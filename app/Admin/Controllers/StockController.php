<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Serials;
use App\Stock;
use App\User;
use App\Product;
use App\Transfer;
use App\Product_stock;
use App\Jobs\UpdateStorage;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Form as Form2;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Tab;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class StockController extends Controller
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

            $content->header('仓库列表');
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

            $content->header('编辑仓库信息');
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

            $content->header('新建仓库');
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
        return Admin::grid(Stock::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
              // append an action.
//              $actions->append('<a href=""><i class="fa fa-eye"></i>查看</a>');

              // prepend an action.
//                $actions->append('编辑');
                $actions->append('<a href="/stocks/'.$actions->getKey().'/edit"><i class="fa fa-edit"></i>编辑</a>');
                $actions->append('<a href="/stock/'.$actions->getKey().'"><i class="fa fa-eye"></i>盘库</a>');
                $actions->append('<a href="/purchase/create?stock='.$actions->getKey().'"><i class="fa fa-shopping-cart"></i>采购</a>');
                $actions->append('<a href="/ship/create?stock='.$actions->getKey().'"><i class="fa fa-truck"></i>发货</a>');
                $actions->append('<a href="/transfer/create?stock='.$actions->getKey().'"><i class="fa fa-arrow-circle-right"></i>调拨</a>');
                $actions->append('<a href="/rent/create?stock='.$actions->getKey().'"><i class="fa fa-undo"></i>借用</a>');
                $actions->append('<a href="/recycle/create?stock='.$actions->getKey().'"><i class="fa fa-recycle"></i>核销</a>');
            });
//            $grid->model()->where('user_id',Admin::user()->id);
            $grid->id('ID')->sortable();
            $stock_type = [1=>'海外', 2=>'海关', 3=>'常规', 4=>'返修', 5=>'损耗', 6=>'借机展机'];
            $grid->type('类型')->editable('select',$stock_type);
            $grid->name('仓库名')->editable();
            $grid->user()->name('库管');
            $grid->address('地址')->editable('textarea');
//            $grid->postal_code('邮编')->editable();
            $states = [
                'on'  => ['value' => 1, 'text' => '加密', 'color' => 'danger'],
                'off' => ['value' => 2, 'text' => '开放', 'color' => 'success'],
            ];
            $grid->privated('私有')->switch($states)->sortable();
            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->equal('type','仓库类型')->select([1 => '海外', 2 => '海关', 3 => '常规', 4 => '返修', 5 => '损耗', 6 => '借机']);
                $filter->equal('user_id','管理员')->select(Admin_user::where('is_sale',0)->pluck('name','id'));
                // 在这里添加字段过滤器


            });
            // $grid->user_id('责任人')->editable('select','/api/users');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Stock::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('type', '类型')->options([1 => '海外', 2 => '海关', 3 => '常规', 4 => '返修', 5 => '损耗', 6 => '借机']);
            $form->text('name', '仓库名');
            $form->select('user_id','库管')->options(Admin_user::all()->pluck('name','id'));
            $form->text('address','地址')->rules('min:3')->placeholder('填写仓库收寄地址');
            $form->text('postal_code','邮编')->placeholder('填写邮政编码');
            $states = [
                'on'  => ['value' => 1, 'text' => '加密', 'color' => 'danger'],
                'off' => ['value' => 2, 'text' => '开放', 'color' => 'success'],
            ];
            $form->switch('privated','私有')->states($states);
        });
    }

    public function user()
    {
        // $q = $request->get('q');
        return User::select(['id', 'name as text'])->get();
    }

//    取一个仓库的库存
    public function products($id)
    {
        return Admin::content(function (Content $content) use($id) {


            $content->header(Stock::find($id)->name);
            $content->description('库存记录');


            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '供应链', 'url' => '/stocks'],
                ['text' => Stock::find($id)->name, 'url' => '']
            );
            $content->row(function ($row) use($id){


                $row->column(6,new box("快速操作","<a href='/purchase/create'>采购</a>&nbsp;&nbsp;"."<a href='/transfer/create'>调拨</a>&nbsp;&nbsp;"."<a href='/ship/create'>出货</a>&nbsp;&nbsp;"."<a href='/return/create'>返修</a>"));
            });
            $headers = ['名称','物料号','规格','型号','数量','序列号'];
            UpdateStorage::dispatch($id);

            $fetchb = Redis::ZRANGE("stock:{$id}:b",0,-1);
            $b = [];
            foreach ($fetchb as $item) {
                $b[] = json_decode($item);
            }

            $fetcha = Redis::ZRANGE("stock:{$id}:a",0,-1);
            $a = [];
            foreach ($fetcha as $item) {
                $a[] = json_decode($item);
            }

            $fetchc = Redis::ZRANGE("stock:{$id}:c",0,-1);
            $c = [];
            foreach ($fetchc as $item) {
                $c[] = json_decode($item);
            }

            $tab = new Tab();

            $tab->add('主机', new Table($headers, $a));
            $tab->add('配件', new Table($headers, $b));
            $tab->add('零库存', new Table($headers, $c));

            $content->body($tab);
        });

        // $products = $stock->outProducts();

//        $transfers1 = Transfer::where('from_stock_id',$id)->get();
//        $transfers2 = Transfer::where('to_stock_id',$id)->get();
        // return $transfers2;

    }

    private function getProductStock($transfers1,$transfers2,$stock_id)
    {
        $a = [];
        $b = [];
        foreach ($transfers2 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmount($ps));
            }
        }

        foreach ($transfers1 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmountNegative($ps));
            }
        }

        $grouped = Collection::make($a)->groupBy('product_id')->sortBy('product_id');;
        // $grouped = Collection::make($a);
//         return $grouped;
        foreach ($grouped as $key=>$product)
        {
            $sum = 0;
            foreach($product as $ps)
            {
                $sum += $ps['amount'];
                // print $ps.'</br>';
            }
            // array_push($b,array('product_id'=>$key,'sum'=>$sum));
            if($sum>0)
            {
                if($product = Product::find($key))
                {
                    $ss = Serials::where('product_id',$key)->where('stock_id',$stock_id)->pluck('serial_no');
                    $serialss = "";

                    for($i=0;$i<count($ss);$i++)
                    {
                        if($i%5==0)
                            $serialss .= "<p>".(string) $ss[$i];
                        elseif($i%5==4)
                            $serialss .= (string) $ss[$i]."</p>";
                        else
                            $serialss .= "&emsp;".(string) $ss[$i]."&emsp;";
                    }

                    $line = [$product->name,$product->sku,$product->desc,'<a href="/serials?stock_id='.$stock_id.'&product_id='.$key.'">'.$sum.'</a>'.count($ss),$serialss];
                    $b[] = $line;

                    Redis::Zadd("stock:".$stock_id,$key,json_encode($line));

                }

            }
        }
//        return $b;
        $headers = ['名称','物料号','规格','数量','序列号'];

        $table = new Table($headers, $b);

        return $table->render();

        // return $b;
    }

    private function getProductStock1($transfers1,$transfers2,$stock_id)
    {
        $pids = Product::where('core',2)->pluck('id');
        $a = [];
        $b = [];
        foreach ($transfers2 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->whereNotIn('product_id',$pids)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmount($ps));
            }
        }

        foreach ($transfers1 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->whereNotIn('product_id',$pids)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmountNegative($ps));
            }
        }

        $grouped = Collection::make($a)->groupBy('product_id')->sortBy('product_id');;
        // $grouped = Collection::make($a);
//         return $grouped;
        foreach ($grouped as $key=>$product)
        {
            $sum = 0;
            foreach($product as $ps)
            {
                $sum += $ps['amount'];
                // print $ps.'</br>';
            }
            // array_push($b,array('product_id'=>$key,'sum'=>$sum));
            if($sum>0)
            {
                if($product = Product::find($key))
                {
                    $ss = Serials::where('product_id',$key)->where('stock_id',$stock_id)->pluck('serial_no');
                    $serialss = "";

                    for($i=0;$i<count($ss);$i++)
                    {
                        if($i%5==0)
                            $serialss .= "<p>".(string) $ss[$i];
                        elseif($i%5==4)
                            $serialss .= (string) $ss[$i]."</p>";
                        else
                            $serialss .= "&emsp;".(string) $ss[$i]."&emsp;";
                    }

                    $line = [$product->name,$product->sku,$product->desc,'<a href="/serials?stock_id='.$stock_id.'&product_id='.$key.'">'.$sum.'</a>',$serialss];
                    $b[] = $line;
                    Redis::Zadd("stock:".$stock_id.":a",$key,json_encode($line));

                }

            }
        }
//        return $b;
        $headers = ['名称','物料号','规格','数量','序列号'];

        $table = new Table($headers, $b);

        return $table->render();

        // return $b;
    }

    private function getProductStock2($transfers1,$transfers2,$stock_id)
    {
        $pids = Product::where('core',2)->pluck('id');

        $a = [];
        $b = [];
        foreach ($transfers2 as $transfer) {

            $pss =  Product_stock::where('transfer_id',$transfer->id)->whereIn('product_id',$pids)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmount($ps));
            }
        }

        foreach ($transfers1 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->whereIn('product_id',$pids)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmountNegative($ps));
            }
        }

        $grouped = Collection::make($a)->groupBy('product_id')->sortBy('product_id');;
        // $grouped = Collection::make($a);
//         return $grouped;
        foreach ($grouped as $key=>$product)
        {
            $sum = 0;
            foreach($product as $ps)
            {
                $sum += $ps['amount'];
                // print $ps.'</br>';
            }
            // array_push($b,array('product_id'=>$key,'sum'=>$sum));
            if($sum>0)
            {
                if($product = Product::find($key))
                {
                    $ss = Serials::where('product_id',$key)->where('stock_id',$stock_id)->pluck('serial_no');
                    $serialss = "";

                    for($i=0;$i<count($ss);$i++)
                    {
                        if($i%5==0)
                            $serialss .= "<p>".(string) $ss[$i];
                        elseif($i%5==4)
                            $serialss .= (string) $ss[$i]."</p>";
                        else
                            $serialss .= "&emsp;".(string) $ss[$i]."&emsp;";
                    }

                    $line = [$product->name,$product->sku,$product->desc,$sum,$serialss];
                    $b[] = $line;

                    Redis::Zadd("stock:".$stock_id.":b",$key,json_encode($line));

                }

            }
        }
//        return $b;
        $headers = ['名称','物料号','规格','数量','序列号'];

        $table = new Table($headers, $b);

        return $table->render();

        // return $b;
    }

    private function getProductAmount($product_stock)
    {
        return [
            'product_id' => $product_stock['product_id'],
            'amount' => $product_stock['amount'],
        ];
    }
    private function getProductAmountNegative($product_stock)
    {
        return [
            'product_id' => $product_stock['product_id'],
            'amount' => -1*$product_stock['amount'],
        ];
    }


    public function test()
    {
        $headers = ['名称','物料号','规格','数量','序列号'];
        UpdateStorage::dispatch(2);
        $fetchD = Redis::ZRANGE('stock:2',0,-1);
        $b = [];
//        $fetchD = json_decode(Redis::ZRANGE('stock:1:b',0,1),1) ;
        foreach ($fetchD as $item) {
            $b[] = json_decode($item);
        }

//        return $b;
        $table = new Table($headers, $b);

        return $table->render();
    }

    public function api_show($id)
    {
        return Stock::Find($id);
    }

    public function list()
    {
        return Stock::All()->map(function($stock){
            return [
                'id' => $stock->id,
                'name' => $stock->name,
            ];
        });
    }


    public function api_store($id)
    {
        $products = Stock::find($id)->amountProducts();
        $core = [];
        $noncore = [];
        $zero = [];
        foreach ($products as $k=>$v)
        {
            $product = Product::find($k);
            if($v == 0)
            {
                $zero[] = [
                    'id' => $k,
                    'name'=>$product->name,
                    'sku'=>$product->sku,
                    'item'=>$product->item,
                    'desc'=>$product->desc,
                    'num' => 0,
                    'serials'=>[],
                ];
            }
            elseif ($product->core == 1)
            {
                $serials = Serials::where('product_id',$k)->where('stock_id',$id)->get()->map(function ($serial){
                    return [
                        "id" => $serial->id,
                        "serial_no" => $serial->serial_no,
                        "comment" => $serial->comment,
                    ];
                });
                $core[] = [
                    'id' => $k,
                    'name'=>$product->name,
                    'sku'=>$product->sku,
                    'item'=>$product->item,
                    'desc'=>$product->desc,
                    'num' => $v,
                    'serials'=>$serials,
                ];
            }
            elseif ($product->core == 2)
            {
                $noncore[] = [
                    'id' => $k,
                    'name'=>$product->name,
                    'sku'=>$product->sku,
                    'item'=>$product->item,
                    'desc'=>$product->desc,
                    'num' => $v,
                    'serials'=>[],
                ];
            }
        }

        return [
            'core' => $core,
            'noncore' => $noncore,
            'zero' => $zero,
        ];
    }


}
