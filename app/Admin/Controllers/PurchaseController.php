<?php

namespace App\Admin\Controllers;

use App\Transfer;
//use App\User;
use App\Product;
use App\Stock;
use App\Serials;
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
//        T1:采购,T2:调拨,T3:发货,T4.出借,T6.损耗,T7.返修
        return Admin::grid(Transfer::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                $actions->prepend('<a href="purchase/list/'.$actions->getKey().'"><i class="fa fa-list"></i>货品</a>');
                $actions->prepend('<a href="document/purchase/'.$actions->getKey().'" target = "_blank" ><i class="fa fa-file-word-o"></i>下载</a>');
            });
            $grid->model()->where('catalog',1)->where('user_id',Admin::user()->id)->orderBy('updated_at','desc');
            $grid->id('ID')->sortable();
            $grid->stock()->name('入库仓库');
            $grid->invoiceno('发票号')->editable('text');
            $grid->contractno('合同编号')->editable('text');
            // $grid->comment('备注')->editable('text');
            $grid->ship_at('发货日期')->sortable();
            $grid->arrival_at('到货日期')->sortable();
            $grid->user_id('采购人员')->display(function($user_id){
                if($user_id)
                    return Admin_user::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('备注')->editable('text');
            $grid->product_stocks('商品清单')->display(function ($products){
                $rows = [];
                foreach($products as $product){
                    if($p = Product::find($product['product_id']))
                    {
                        $line = [
                            isSet($p->item)?$p->item:"",
                            isSet($p->desc)?$p->desc:"",
                            '<a href="/serials?product_id='.$p->id.'&purchase_id='.$this->getKey().'">'.$product['amount'].'</a>'
                            // $product['amount']
                        ];
                        $rows[] = $line;
                    }

                }
                $headers = ['型号','规格','数量'];
                $table = new Table($headers, $rows);
                return $table->render();
            });

            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->between('ship_at','发货日期')->date();
                $filter->between('arrival_at','到货日期')->date();
                $filter->equal('to_stock_id','入库仓库')->select(Stock::all()->pluck('name','id'));
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

        return redirect('/purchase/list/'.$transfer->id);
        // return $data;
    }

    public function newline(Request $rq, $transfer_id)
    {
        $transfer = Transfer::find($transfer_id);
        if(isSet($rq->q))
        {
//            $products = Product::orWhere('name',$rq->q)->orWhere('item',$rq->q)->orWhere('desc',$rq->q)->get();
            $products = Product::where('name','like','%{$rq->q}%')->orwhere('item','like','%'.$rq->q.'%')->orwhere('sku','like','%'.$rq->q.'%')->orwhere('desc','like','%'.$rq->q.'%')->get();
//            return $rq->q;
        } else {
            $products = Product::All();
        }
        // $product = Product::pluck('name','id');
        $groupeds = $products->groupBy('catalog');
        $keys = [];
        $pss =[];
        foreach ($groupeds as $key => $grouped) {
            $ps = [];
            foreach($grouped as $item)
                $ps[$item['id']] = $item['sku'].'----'.$item['name'].'----'.$item['desc'];
            array_push($keys,$key);
            array_push($pss,$ps);
        }
        return  view('purchase.new',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]);
    }

    public function storeline(Request $request)
    {
        $p = Product::find($request->product_id);
        $t = Transfer::find($request['transfer_id']);
        if($p->core==1)
        {
//            return $request->end;
            if(($request->begin > 0) && ($request->end > 0))
            {
                $arrbegin = explode('.',$request->begin);
                $sbegin = array_pop($arrbegin);
                $arrend = explode('.',$request->end);
                $send = array_pop($arrend);
                $prefix = substr($request->begin,0,-strlen($sbegin));
                $serials = [];
                for($i = $sbegin; $i<=$send;$i++)
                {
                    array_push($serials,$prefix.(string) $i);
                }
            }   else    {
                $serials = explode("\r\n",$request['serials']);
            }
            foreach ($serials as $k => $serial) {
                if($s = Serials::where('serial_no',$serial)->where('product_id',$request['product_id'])->first()){
                    $s->purchase_id = $t->id;
                    $s->stock_id = $t->to_stock_id;
                    $s->save();
                }
                else{
                    $s = new Serials;
                    $s->serial_no = $serial;
                    $s->product_id = $request['product_id'];
                    $s->transfer_id = $request['transfer_id'];
                    $s->purchase_id = $request['transfer_id'];
                    $s->product_at = $t->ship_at;
                    $s->storage_at = $t->arrival_at;
                    $s->stock_id = $t->to_stock_id;
                    $s->save();
                }
            }
//            return $amount;
            if($amount = Serials::where('purchase_id',$t->id)->where('product_id',$p->id)->count()){
                if($product_stock = Product_stock::where('product_id',$request['product_id'])->where('transfer_id',$request['transfer_id'])->first()){
                    $product_stock->product_id = $request['product_id'];
                    $product_stock->transfer_id = $request['transfer_id'];
                    $product_stock->amount = $amount;
                    $product_stock->remark = json_encode($serials);
                    $product_stock->save();
                }
                else {
                    $product_stock = new Product_stock;
                    $product_stock->product_id = $request['product_id'];
                    $product_stock->transfer_id = $request['transfer_id'];
                    $product_stock->amount = $amount;
                    $product_stock->remark = json_encode($serials);
                    $product_stock->save();
                }
            }


        }
        else
        {
            if($product_stock = Product_stock::where('product_id',$request['product_id'])->where('transfer_id',$request['transfer_id'])->first()){
                $product_stock->product_id = $request['product_id'];
                $product_stock->transfer_id = $request['transfer_id'];
                $product_stock->amount = $request->amount;
                $product_stock->save();
            }
            else {
                $product_stock = new Product_stock;
                $product_stock->product_id = $request['product_id'];
                $product_stock->transfer_id = $request['transfer_id'];
                $product_stock->amount = $request->amount;
                $product_stock->save();
            }

        }
        return redirect('purchase/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }

    public function deleteline($id)
    {
        $ps = Product_stock::find($id);
        $tid = $ps-> transfer_id;
        Serials::where('purchase_id', $tid)->where('product_id',$ps->product_id)->delete();
        $ps->delete();
        return redirect('purchase/list/'.$tid);
    }

    public function check(Request $rq)
    {
        return (Product::find($rq->id)->core==1)?1:2;
    }
}
