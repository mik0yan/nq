<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Transfer;
use App\Purchase;
use App\User;
use App\Stock;
use App\Product;
use App\serials;
use App\Product_stock;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TransferController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Transfer::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
              $actions->disableDelete();
              // append an action.
              $actions->append('<a href=""><i class="fa fa-eye"></i></a>');

              // prepend an action.
              $actions->prepend('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-paper-plane"></i></a>');
            });
            $grid->model('catalog','>',2);
            $grid->id('ID')->sortable();

            $grid->stock2()->name('出库仓');
            $grid->stock()->name('入库仓');

            $grid->track_id('运单号')->editable('text');
            $grid->user_id('操作人')->display(function($user_id){
                if($user_id)
                    return Admin_user::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('备注')->editable('textarea');
            $grid->ship_at('发货时间')->editable('date');
            $grid->arrival_at('到达时间')->editable('date');
            // $grid->created_at();
            // $grid->updated_at();
            $grid->product_stocks('商品清单')->display(function ($products){
                        $rows = [];
                        foreach($products as $product){
                            $line = [
                                Product::find($product['product_id'])->item,
                                Product::find($product['product_id'])->desc,
                                $product['amount']
                            ];
                            $rows[] = $line;
                        }
                        $headers = ['型号','规格','数量'];

                        $table = new Table($headers, $rows);

                        return $table->render();
                    });
            $grid->filter(function ($filter) {

                // 设置created_at字段的范围查询
                $filter->in('catalog', '类型')->multipleSelect(['key' => 'value']);

                $filter->between('arrival_at', '到达时间')->date();
            });


        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Transfer::class, function (Form $form) {
            $form->tab('基本信息', function ($form) {
                // $form->hidden('catalog')->default(2);
                $form->display('id', 'ID');
                $form->select('catalog','类别')->options([
                    1 =>'采购',
                    2 =>'发货',
                    3 =>'移库',
                ])->default(3);
                $form->select('from_stock_id','出库仓')->options(function($id){
                    $stock = Stock::find($id);
                    if($stock){
                        return [$stock->id => $stock->name];
                    }
                })->ajax('/admin/api/stocks')->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机');
                $form->select('to_stock_id','入库仓')->options(function($id){
                    $stock = Stock::find($id);
                    if($stock){
                        return [$stock->id => $stock->name];
                    }
                })->ajax('/admin/api/stocks')->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机');
                // $form->select('from_stock_id','出库仓')->options('/admin/api/stocks');
                $form->select('user_id','操作员')->options(function ($id) {
                    $user = Admin_user::findorFail($id);

                    if ($user) {
                        return [$user->id => $user->name];
                    }
                })->ajax('/admin/api/users');
                $form->text('track_id','运单信息');
                $form->date('arrival_at','到货日期');
                $form->textarea('comment','备注')->rows(6)->placeholder('填写备注信息');
                })->tab('主机信息', function ($form) {
                    $form->hasMany('product_stocks', function (Form\NestedForm $form) {
                    // $form->number('measure_id');
                        $form->select('product_id')->options(function($id){
                            $product = Product::find($id);
                            if($product){
                                return [$product->id => $product->name];
                            }
                        })->ajax('/admin/api/products');
                      // $form->select('measure_id','测量项')->options('/api/measures');
                        $form->number('amount','数量');
                    });
                });

        });
    }
    public function users(Request $request)
    {
        $q = $request->get('q');
        return Admin_user::where('name', 'like', "%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function stocks(Request $request)
    {
        $q = $request->get('q');
        return Stock::where('type',$q)->paginate(null, ['id', 'name as text']);
    }

    public function products(Request $request)
    {
        $q = $request->get('q');
        return Product::where('name','like',"%$q%")->paginate(null, ['id', 'name as text']);
    }

    public function ships()
    {

        return Transfer::where('catalog',2)
            ->orderBy('arrival_at','desc')
            ->paginate(null, ['id', 'comment as text']);

    }


    public function transfer()
    {

        return Transfer::where('catalog',3)
            ->orderBy('arrival_at','desc')
            ->paginate(null, ['id', 'comment as text']);

    }

    public function purchases()
    {
        return Transfer::where('catalog',1)
            ->orderBy('arrival_at','desc')
            ->paginate(null, ['id', 'comment as text']);

    }

    public function list($id)
    {
        $pss = Product_stock::where('transfer_id',$id)->get();
        $items = [];
        $transfer = Transfer::find($id);
        $from_stock = Stock::find($transfer->from_stock_id);
        $to_stock = Stock::find($transfer->to_stock_id);
        foreach ($pss as $k=>$ps) {
            $product = Product::find($ps['product_id']);
            $serials = serials::where('transfer_id',$id)
                            ->where('product_id',$ps['product_id'])
                            ->get();
            array_push($items,[
                'id'=>$ps['id'],
                'product_id'=>$ps['product_id'],
                'sku'=>$product['sku'],
                'name'=>$product['name'],
                'amount'=>$ps['amount'],
                'serials'=>$serials,
            ]);
        }
        $title = '转运单';
        // $products = $items->product->name;
        // return view('transfer.list',compact($items));
        return  view('transfer.list',[
            'items'=>$items,
            'title'=>$title,
            'transfer'=>$transfer,
            'from_stock'=>$from_stock,
            'to_stock'=>$to_stock,
        ]);
        // return $items;
    }

    public function newline($transfer_id)
    {
        $transfer = Transfer::find($transfer_id);
        $products = Product::All();
        // $product = Product::pluck('name','id');
        $groupeds = $products->groupBy('catalog');
        $keys = [];
        $pss =[];
        foreach ($groupeds as $key => $grouped) {
            $ps = [];
            foreach($grouped as $item)
                $ps[$item['id']] = $item['sku'].'-----'.$item['name'];
            array_push($keys,$key);
            array_push($pss,$ps);
        }
        return  view('transfer.new',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]);
    }

    public function storeline(Request $request)
    {
        $product_stock =  Product_stock::where('product_id',$request['product_id'])
            ->where('transfer_id',$request['transfer_id'])
            ->first();
        if($product_stock)
        {
            $product_stock->product_id = $request['product_id'];
            $product_stock->transfer_id = $request['transfer_id'];
            $product_stock->amount = $request['amount'];
            $product_stock->save();
        }
        else {
            $product_stock = new Product_stock;
            $product_stock->product_id = $request['product_id'];
            $product_stock->transfer_id = $request['transfer_id'];
            $product_stock->amount = $request['amount'];
            $product_stock->save();
        }

        $serials = explode("\r\n",$request['serials']);
        foreach ($serials as $k => $serial) {
            $s = serials::where('serial_no',$serial)->where('product_id',$request['product_id'])->first();
            if($s){
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = Transfer::find($request['transfer_id'])->to_stock_id;
                $s->save();
            }
            else{
                $s = new serials;
                $s->serial_no = $serial;
                // $s->comment = $request['product_id'];
                $s->product_id = $request['product_id'];
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = Transfer::find($request['transfer_id'])->to_stock_id;
                $s->save();
                // echo $serial."</br>";

            }


        }
        return redirect('admin/transfer/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }
}
