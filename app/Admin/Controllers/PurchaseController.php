<?php

namespace App\Admin\Controllers;

use App\Transfer;
use App\User;
use App\Product;
use App\Stock;
use App\serials;
use App\Admin_user;
use App\Product_stock;
use Illuminate\Http\Request;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Table;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class PurchaseController extends Controller
{
    use ModelForm;

    private $stock_id = 0;
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('采购列表');
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

            $content->header('编辑采购单');
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
        if($this->stock_id = $rq->get('stock'))
            return Admin::content(function (Content $content) {

                $content->header('新建采购');
                $content->description('description');

                $content->body($this->form2());
            });
        else
            return Admin::content(function (Content $content) {

                $content->header('新建采购');
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
              $actions->prepend('<a href="purchase/list/'.$actions->getKey().'"><i class="fa fa-paper-plane"></i></a>');
            });
            $grid->model()->where('catalog',1)->orderBy('id','desc');
            $grid->id('ID')->sortable();
            $grid->stock()->name('入库仓库');
            $grid->invoiceno('发票号')->editable('text');
            $grid->contractno('合同编号')->editable('text');
            // $grid->comment('备注')->editable('text');
            $grid->ship_at('发货日期')->sortable();
            $grid->arrival_at('到货日期')->sortable();
            $grid->user_id('采购人员')->display(function($user_id){
                if($user_id)
                    return User::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('备注')->editable('text');
            $grid->product_stocks('商品清单')->display(function ($products){
                        $rows = [];
                        foreach($products as $product){
                        //     $line = "<span class='label label-success'>".$product['amount'].":".Product::find($product['product_id'])->name."</span>";
                        //     print $line;
                        //     $dis = $dis + $line;
                            $p = Product::find($product['product_id']);
                            $line = [
                                $p->item,
                                $p->desc,
                                '<a href="/admin/serials?product_id='.$p->id.'">'.$product['amount'].'</a>'
                                // $product['amount']
                            ];
                            $rows[] = $line;
                        }
                        // return $dis;
                        $headers = ['型号','规格','数量'];
                        // $headers = ['Id', 'Email', 'Name', 'Company'];

                        // $rows = [
                        //     [1, 'labore21@yahoo.com', 'Ms. Clotilde Gibson', 'Goodwin-Watsica'],
                        //     [2, 'omnis.in@hotmail.com', 'Allie Kuhic', 'Murphy, Koepp and Morar'],
                        //     [3, 'quia65@hotmail.com', 'Prof. Drew Heller', 'Kihn LLC'],
                        //     [4, 'xet@yahoo.com', 'William Koss', 'Becker-Raynor'],
                        //     [5, 'ipsa.aut@gmail.com', 'Ms. Antonietta Kozey Jr.'],
                        // ];

                        $table = new Table($headers, $rows);

                        return $table->render();
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
                $form->hidden('catalog')->default(1);
                $form->display('id', 'ID');
                $form->select('user_id','采购员')->options(
                    Admin_user::All()->pluck('name', 'id')
                )->default(Admin::user()->id);
                $form->select('to_stock_id','入库仓')->options(
                    Stock::All()->pluck('name', 'id')
                )->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机');
                $form->text('invoiceno','发票号');
                $form->text('contractno','合同编号');
                $form->text('comment','备注');
                $form->dateRange('ship_at','arrival_at','货期')->help('请输入发货日期和到货日期');
            });
            // $form->date('arrival_at','到货日期')->help('请输入到货日期');
        });
    }

    protected function form2()
    {
        return Admin::form(Transfer::class, function (Form $form) {
            $form->tab('基本信息', function ($form) {
                $form->hidden('catalog')->default(1);
//                $form->display('id', 'ID');
                $form->select('user_id','采购员')->options(
                    Admin_user::All()->pluck('name', 'id')
                )->default(Admin::user()->id);
                $form->select('to_stock_id','入库仓')->options(
                    Stock::All()->pluck('name', 'id')
                )->default($this->stock_id);
                $form->text('invoiceno','发票号');
                $form->text('contractno','合同编号');
                $form->text('comment','备注');
                $form->dateRange('ship_at','arrival_at','货期')->help('请输入发货日期和到货日期');
            });
            // $form->date('arrival_at','到货日期')->help('请输入到货日期');
        });
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
        $title = '进货单';
        // $products = $items->product->name;
        // return view('transfer.list',compact($items));
        return  view('purchase.list',[
            'items'=>$items,
            'title'=>$title,
            'transfer'=>$transfer,
            'from_stock'=>$from_stock,
            'to_stock'=>$to_stock,
        ]);
        // return $items;
    }

    public function quickline($stock_id = 3, Request $request)
    {
        $transfer = new Transfer;
        $data = preg_split ('/[,\s]+/', trim($request['data']));

        $transfer->comment = array_shift($data);
        if(count($data)>2)
            $transfer->invoiceno = array_shift($data);
        if(count($data)>1)
            $transfer->ship_at = array_shift($data);
        $transfer->arrival_at = array_shift($data);
        $transfer->to_stock_id = $stock_id;
        $transfer->user_id = 43;
        $transfer->catalog = 1;
        $transfer->save();

        return redirect('/admin/purchase/list/'.$transfer->id);
        // return $data;
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
        return  view('purchase.new',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]);
    }

    public function storeline(Request $request)
    {
        $product_stock =  Product_stock::where('product_id',$request['product_id'])
            ->where('transfer_id',$request['transfer_id'])
            ->first();

        $serials = explode("\r\n",$request['serials']);



        $serials = explode("\r\n",$request['serials']);
        $amount = 0;
        foreach ($serials as $k => $serial) {
            $s = serials::where('serial_no',$serial)->where('product_id',$request['product_id'])->first();
            $t = Transfer::find($request['transfer_id']);
            if($s){
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
                $amount++;
                // echo $serial."</br>";
            }
        }
        if($product_stock)
        {
            $product_stock->product_id = $request['product_id'];
            $product_stock->transfer_id = $request['transfer_id'];
            $product_stock->amount = $request['amount']>$amount?$request['amount']:$amount;
            $product_stock->save();
        }
        else {
            $product_stock = new Product_stock;
            $product_stock->product_id = $request['product_id'];
            $product_stock->transfer_id = $request['transfer_id'];
            $product_stock->amount = $request['amount']>$amount?$request['amount']:$amount;
            $product_stock->save();
        }
        return redirect('admin/purchase/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }
}
