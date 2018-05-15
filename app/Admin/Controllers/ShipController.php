<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Transfer;
use App\User;
use App\Stock;
use App\Serials;

use App\Product;
use App\Product_stock;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;

class ShipController extends Controller
{
    use ModelForm;
    private $ship_from = 0;
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('发货清单');
            $content->description('发货列表');

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

            $content->header('编辑发货清单');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Request $rq)
    {
        if($this->ship_from = $rq->get('stock'))
            return Admin::content(function (Content $content) {

            $content->header('新建发货清单');
            $content->description('description');

            $content->body($this->form2());
             });
        else
            return Admin::content(function (Content $content) {

                $content->header('新建发货清单');
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
                $actions->prepend('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-list"></i>货品</a>');
            });
            $grid->model()->where('catalog',3)->orderBy('updated_at','desc');
//            $grid->model()->where('catalog',3)->where('user_id',Admin::user()->id)->orderBy('updated_at','desc');

            $grid->id('ID')->sortable();
            $grid->stock2()->name('出库仓');
            $grid->inviceno('发票号')->editable('text');
            $grid->track_id('运单号')->editable('text');
            $grid->contractno('合同编号')->editable('text');
            // $grid->comment('备注')->editable('text');
            $grid->ship_at('发货日期')->editable('date');
            $grid->arrival_at('到货日期')->editable('date');
            $grid->user_id('发货人员')->display(function($user_id){
                if($user_id)
                    return Admin_user::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('收货信息')->editable('text');
            $grid->product_stocks('商品清单')->display(function ($products){
                $rows = [];

                foreach($products as $product){
                    $p = Product::find($product['product_id']);

                    $line = [
                                Product::find($product['product_id'])->item,
                                Product::find($product['product_id'])->desc,
                                '<a href="/serials?product_id='.$p->id.'&transfer_id='.$this->getKey().'">'.$product['amount'].'</a>'
                            ];
                            $rows[] = $line;
                        }
                        $headers = ['型号','规格','数量'];

                        $table = new Table($headers, $rows);

                        return $table->render();
                    });
            $grid->filter(function($filter) {
//                $filter->disableIdFilter();
                $filter->between('ship_at','发货日期')->date();
                $filter->between('arrival_at','到货日期')->date();
                $filter->like('comment','备注信息');

                $filter->equal('user_id','经办人员')->select(Admin_user::all()->pluck('name','id'));
                $filter->where(function ($query){
                    $query->whereHas('product_stock',function($query) {
                        $query->whereHas('product',function($query) {
                            $query->where('name', 'like', '%'.$this->input.'%')
                                ->orwhere('desc', 'like', '%'.$this->input.'%')
                                ->orwhere('item', 'like', '%'.$this->input.'%')
                                ->orwhere('sku', 'like', '%'.$this->input.'%');
                        });
                    });
                },"物料号或产品名");
            });
            // $grid->created_at();
            // $grid->updated_at();
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
            $form->hidden('catalog')->default(3);
            $form->display('id', 'ID');
            $form->select('user_id','发货员')->options(
                Admin_user::All()->pluck('name', 'id')
            )->default(Admin::user()->id);
            $form->select('from_stock_id','出库仓')->options(
                Stock::All()->pluck('name', 'id')
            )->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机');
            $form->text('contractno','合同编号');
            $form->text('track_id','运单号');
            $form->textarea('comment','收货信息');
            $form->dateRange('ship_at','arrival_at','货期')->help('请输入发货日期和到货日期');
            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }

    protected function form2()
    {
        return Admin::form(Transfer::class, function (Form $form) {
            $form->hidden('catalog')->default(3);
            $form->display('id', 'ID');
            $form->select('user_id','发货员')->options(
                Admin_user::All()->pluck('name', 'id')
            )->default(Admin::user()->id);
            $form->select('from_stock_id','出库仓')->options(
                Stock::All()->pluck('name', 'id')
            )->default($this->ship_from);
            $form->text('contractno','合同编号');
            $form->text('track_id','运单号');
            $form->textarea('comment','收货信息');
            $form->dateRange('ship_at','arrival_at','货期')->help('请输入发货日期和到货日期');
            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }


    public function list($id)
    {
        $pss = Product_stock::where('transfer_id',$id)->get();
        $items = [];
        $transfer = Transfer::find($id);
        $from_stock = Stock::find($transfer->from_stock_id);
        // $to_stock = Stock::find($transfer->to_stock_id);
        foreach ($pss as $k=>$ps) {
            $product = Product::find($ps['product_id']);
            $serials = Serials::where('transfer_id',$id)
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
        $title = '出货单';
        // $products = $items->product->name;
        // return view('transfer.list',compact($items));
        return  view('ship.list',[
            'items'=>$items,
            'title'=>$title,
            'transfer'=>$transfer,
            'from_stock'=>$from_stock,
            // 'to_stock'=>$to_stock,
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
        return  view('ship.new',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]);
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
            $s = Serials::where('serial_no',$serial)->where('product_id',$request['product_id'])->first();
            $t = Transfer::find($request['transfer_id']);
            if($s){
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = null;
                $s->save();
            }
            else{
                $s = new Serials;
                $s->serial_no = $serial;
                // $s->comment = $request['product_id'];
                $s->product_id = $request['product_id'];
                $s->transfer_id = $request['transfer_id'];
                $s->purchase_id = $request['transfer_id'];
                $s->product_at = $t->ship_at;
                $s->storage_at = $t->arrival_at;
                $s->stock_id = $t->to_stock_id;
                $s->save();
                // echo $serial."</br>";
            }


        }
        return redirect('/ship/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }
}
