<?php

namespace App\Admin\Controllers;

use App\Transfer;
use App\User;
use App\Stock;
use App\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ShipController extends Controller
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
            $grid->model()->where('catalog',3);
            $grid->id('ID')->sortable();
            $grid->stock2()->name('出库仓');
            $grid->inviceno('发票号')->editable('text');
            $grid->contractno('合同编号')->editable('text');
            // $grid->comment('备注')->editable('text');
            $grid->ship_at('发货日期')->editable('date');
            $grid->arrival_at('到货日期')->editable('date');
            $grid->user_id('发货人员')->display(function($user_id){
                if($user_id)
                    return User::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('备注')->editable('text');
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
            $form->select('user_id','出库员')->options(function ($id) {
                $user = User::find($id);

                if ($user) {
                    return [$user->id => $user->name];
                }
            })->ajax('/admin/api/users');
            $form->select('from_stock_id','出库仓')->options(function($id){
                $stock = Stock::find($id);
                if($stock){
                    return [$stock->id => $stock->name];
                }
            })->ajax('/admin/api/stocks')->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机');
            $form->text('inviceno','发票号');
            $form->text('contractno','合同编号');
            $form->text('comment','备注');
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
        return  view('ship.new',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]]);
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
            $t = Transfer::find($request['transfer_id']);
            if($s){
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = null;
                $s->save();
            }
            else{
                $s = new serials;
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
        return redirect('admin/ship/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }
}