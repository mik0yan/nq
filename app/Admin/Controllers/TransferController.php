<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Transfer;
use App\Purchase;
use App\User;
use App\Stock;
use App\Product;
use App\Serials;
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
use App\Jobs\UpdateStorage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

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

            $content->header('调拨清单');
            $content->description('调拨列表');

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

            $content->header('编辑调拨单');
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

            $content->header('新建调拨单');
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
//                $actions->disableDelete();
//                $actions->disableEdit();
              // append an action.
              $actions->append('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-list"></i>货品清单</a>');

              // prepend an action.
//              $actions->prepend('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-paper-plane"></i></a>');
            });
//            $grid->model()->where('catalog',2)->where('user_id',Admin::user()->id)->orderBy('updated_at','desc');
            $grid->model()->where('catalog',2)->orderBy('updated_at','desc');
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
            $grid->filter(function ($filter) {

                // 设置created_at字段的范围查询
//                $filter->in('catalog', '类型')->multipleSelect(['key' => 'value']);
                $filter->equal('user_id','经办人员')->select(Admin_user::all()->pluck('name','id'));
                $filter->like('comment','备注信息');

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
//            $form->hidden('catalog')->default(2);
            $form->display('id', 'ID');
            $form->select('catalog','类别')->options([
                1 =>'采购',
                2 =>'调拨',
                3 =>'发货',
            ])->default(2)->rules('required');
            $form->select('from_stock_id','出库仓')->options(Stock::all()
                    ->pluck('name','id'))
                    ->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')
//                    ->default($this->from_stock_id)
                    ->rules('required');
            $form->select('to_stock_id','入库仓')->options(Stock::all()->pluck('name','id'))->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')->rules('required');
            // $form->select('from_stock_id','出库仓')->options('/api/stocks');
            $form->select('user_id','操作员')->options(Admin_user::All()->pluck('name','id'))->rules('required');
            $form->text('track_id','运单信息');
            $form->date('arrival_at','到货日期')->rules('required');
//            $form->hidden('catalog')->value(2);
            $form->textarea('comment','备注')->rows(6)->placeholder('填写备注信息');

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

    public function newline2($transfer_id)
    {
        $transfer = Transfer::find($transfer_id);
        $stock = Stock::find($transfer->from_stock_id);
        $products = Product::whereIn('id',array_keys($stock->amountProducts()))->get();

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
        return  view('transfer.options',['product'=>array_combine($keys,$pss),'id'=>$transfer_id]);
    }

    public function item(Request $rq, $transfer_id)
    {
        $transfer = Transfer::find($transfer_id);
        $stock = Stock::find($transfer->from_stock_id);
        return $stock->stockProduct($rq->id);
//        return  $rq->id;
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
//            $product_stock->amount = 3;

            $product_stock->save();
        }
        else {
            $product_stock = new Product_stock;
            $product_stock->product_id = $request['product_id'];
            $product_stock->transfer_id = $request['transfer_id'];
            $product_stock->amount = $request['amount'];
            $serials = explode("\r\n",$request['serials']);
//            $product_stock->amount = 3;
            $product_stock->remark = implode(',',$serials);
            $product_stock->save();
        }

        $serials = explode("\r\n",$request['serials']);
        foreach ($serials as $k => $serial) {
            if($s = Serials::where('serial_no',$serial)->where('product_id',$request['product_id'])->first()){
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = Transfer::find($request['transfer_id'])->to_stock_id;
                $s->save();
            }
            else{
                $s = new Serials();
                $s->serial_no = $serial;
                // $s->comment = $request['product_id'];
                $s->product_id = $request['product_id'];
                $s->transfer_id = $request['transfer_id'];
                $s->stock_id = Transfer::find($request['transfer_id'])->to_stock_id;
                $s->save();
                // echo $serial."</br>";

            }


        }
        return redirect('transfer/list/'.$request['transfer_id']);
        // return explode("\r\n",$request['serials']);
    }

    public function storeline2(Request $rq)
    {
        $transfer = Transfer::find($rq->transfer_id);
        $p = Product::find($rq->product_id);
        if($p->core==1)
        {
            $serialnos =  array_filter(array_keys($rq->toArray()),'is_int');
            if($ps = Product_stock::where('product_id',$rq->product_id)->where('transfer_id',$rq->transfer_id)->first()){
                $ps->amount = count($serialnos);
                $ps->save();
            } else {
                Product_stock::create([
                    'product_id' => $rq->product_id,
                    'transfer_id' => $rq->transfer_id,
                    'amount' => count($serialnos),
                    'status' => 3,
                    'remark' => json_encode($serialnos),
                ]);
            }
            foreach ($serialnos as $s)
            {
                $serial = Serials::find($s);
                $serial->transfer_id = $rq->transfer_id;
                $serial->stock_id = $transfer->to_stock_id;
                $serial->save();
            }
        } else {
            if($ps = Product_stock::where('product_id',$rq->product_id)->where('transfer_id',$rq->transfer_id)->first()) {
                $ps->amount = $rq->amount;
                $ps->save();
            } else
            {
                Product_stock::create([
                    'product_id' => $rq->product_id,
                    'transfer_id' => $rq->transfer_id,
                    'amount' => $rq->amount,
                    'status' => 3,
                ]);
            }
        }
        UpdateStorage::dispatch($transfer->from_stock_id);
        return redirect('transfer/list/'.$rq['transfer_id']);


    }

    public function check(Request $rq)
    {
        return (Product::find($rq->id)->core==1)?1:2;
    }

//    物流信息 基本信息+商品列表
    public function detail($id)
    {
        $t = Transfer::find($id);
        $h = $t->product_stocks();
        return view('transfer.detail',["transfer"=>$t,"lists"=>$h]);
    }

//    物流信息 基本信息+商品列表
    public function productlist(Request $rq, $id)
    {

    }

//  确认序列号
    public function checkserial(Request $rq, $id)
    {
        $serials = explode("\r\n",$rq['serials']);
        $result = [];
        foreach (array_unique($serials) as $key=>$serial)
        {
            if(count($serial)>3){
                $result[] = [
                    "check"=>Serials::where('serial_no',$serial)->first()?1:0,
                    "sn"=> $serial,
                ];
            }

        }

        return $result;
    }

    public function checkrange(Request $rq, $id)
    {
//        $serials =
    }

//  查询序列号
    public function queryserial(Request $rq, $id)
    {
        $titles = [null,"采购","调拨","出货","借出","归还","返修","修理","合格","损耗"];
        $t = Transfer::find($id);
//        T1:采购,T2:移库,T3:发货,T4:借出,T5:返还,T6.损耗,T7:改配
        return [
            "title" =>$titles[$t->catalog],
            "from"=>$t->from_stock_id ? [
                'id'=>$t->stock2->id,
                'name'=>$t->stock2->name,
                'address'=>$t->stock2->address,
                'user'=>$t->stock2->user->name,
            ]:null,
            "to"=>$t->to_stock_id ? [
                'id'=>$t->stock->id,
                'name'=>$t->stock->name,
                'address'=>$t->stock->address,
                'user'=>$t->stock->user->name,
            ]:null,
            "order"=>$t->order_id ?[
                'id'=>$t->order->id,
                'user'=>$t->order->user->name,
                'client' =>$t->order->client->corp,
                'agent' =>$t->order->agent->corp,
                'orderno' =>$t->order->ordno,
            ]:null,
            "user"=>$t->user->name,
            "track"=>['invoice'=>$t->invoiceno,'contract'=>$t->contractno,'track'=>$t->track_id,],
            "comment"=>$t->comment,
            "date" => ['ship'=>$t->ship_at,'arrival'=>$t->arrival_at,],
            "detail" =>$t-> product_stocks->transform(function ($product){
                return [
                    'id' => $product->id,
                    'amount' =>  $product->amount,
                    'product_id' => $product->product->id,
                    'product_name' => $product->product->name.$product->product->desc,
                    'core' => ($product->product->core ==1),
                    'serials' => $product->transfer->serials($product->product_id)->get()->map(function($serial){
                        return [
                            'id'=>$serial->id,
                            'serial'=>$serial->serial_no,
                            'comment'=>$serial->comment,
                        ];
                    }),
                ];
            }),
        ];

//        return $t->stock2()->get();

    }

//    存储数据
    public function productstore(Request $rq, $id)
    {

    }

//    删除产品数据
    public function productdelete(Request $rq, $id)
    {

    }

    public function updateSerialTransfer()
    {
        $ss = Serials::all();
        foreach ($ss as $s) {
            if($s->purchase_id && Transfer::find($s->purchase_id))
                $s->transfers()->attach($s->purchase_id);
            if($s->transfer_id && Transfer::find($s->transfer_id) && $s->transfer_id!=$s->purchase_id)
                $s->transfers()->attach($s->transfer_id);
        }
    }

    public function postPurchase(Request $rq)
    {
        $data =  $rq->json()->all();
        if(isSet($data['to_stock_id']))
        {
            $t = new Transfer;
            $t->catalog = 1;
            $t->invoiceno = $data['invoiceno'];
            $t->contractno = $data['contractno'];
            $t->comment = $data['comment'];
            $t->track_id = $data['track_id'];
            $t->to_stock_id = $data['to_stock_id'];
            $t->ship_at = $data['ship_at'];
            $t->arrival_at = $data['arrival_at'];
            $t->save();
            foreach ($data['list'] as $item) {
                Log::info("make item  ". json_encode($item));
                $ps = new Product_stock;
                $ps->product_id = $item['id'];
                $ps->transfer_id = $t->id;
                $ps->amount = $item['num'];
                $ps->remark = $item['comment'];
                $ps->status = 3;
                $ps->save();
                if($item['core'])
                {
                    foreach ($item['seriallist'] as $serial)
                    {
                        $ss = new Serials;
                        $ss->product_id = $item['id'];
                        $ss->purchase_id = $t->id;
                        $ss->stock_id = $data['to_stock_id'];
                        $ss->serial_no = $serial;
                        $ss->transfer_id = $t->id;
                        $ss->product_at = $data['ship_at'];
                        $ss->storage_at = $data['arrival_at'];
                        $ss->save();
                    }
                }
            }
        }

        return [
            "code" => 200,
            "message" => "success",
            "data"=>$data
        ];
    }



}
