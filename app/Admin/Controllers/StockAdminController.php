<?php

namespace App\Admin\Controllers;

use App\Stock;
use App\Admin_user;
use App\Serials;
use App\User;
use App\Product;
use App\Transfer;
use App\Product_stock;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//class StockAdminController extends Controller
//{
//    use ModelForm;
//
//    /**
//     * Index interface.
//     *
//     * @return Content
//     */
//    public function index()
//    {
//        return Admin::content(function (Content $content) {
//
//            $content->header('header');
//            $content->description('description');
//
//            $content->body($this->grid());
//        });
//    }
//
//    /**
//     * Edit interface.
//     *
//     * @param $id
//     * @return Content
//     */
//    public function edit($id)
//    {
//        return Admin::content(function (Content $content) use ($id) {
//
//            $content->header('header');
//            $content->description('description');
//
//            $content->body($this->form()->edit($id));
//        });
//    }
//
//    /**
//     * Create interface.
//     *
//     * @return Content
//     */
//    public function create()
//    {
//        return Admin::content(function (Content $content) {
//
//            $content->header('header');
//            $content->description('description');
//
//            $content->body($this->form());
//        });
//    }
//
//    /**
//     * Make a grid builder.
//     *
//     * @return Grid
//     */
//    protected function grid()
//    {
//        return Admin::grid(Stock::class, function (Grid $grid) {
//
//            $grid->id('ID')->sortable();
//
//            $grid->created_at();
//            $grid->updated_at();
//        });
//    }
//
//    /**
//     * Make a form builder.
//     *
//     * @return Form
//     */
//    protected function form()
//    {
//        return Admin::form(Stock::class, function (Form $form) {
//
//            $form->display('id', 'ID');
//
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
//        });
//    }
//}


class StockAdminController extends Controller
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
                $actions->append('<a href="/document/stock/'.$actions->getKey().'"target = "_blank"><i class="fa fa-file"></i>库存表</a>');
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


    public function products($id)
    {
        // $products = $stock->outProducts();

//        $transfers1 = Transfer::where('from_stock_id',$id)->get();
//        $transfers2 = Transfer::where('to_stock_id',$id)->get();
        // return $transfers2;
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

//        return $b;


        $tab = new Tab();

        $tab->add('主机', new Table($headers, $a));
        $tab->add('配件', new Table($headers, $b));

//        return $this->getProductStock($transfers1,$transfers2,$id);
        return $tab->render();
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

                    $line = [$product->name,$product->sku,$product->desc,'<a href="/serials?stock_id='.$stock_id.'&product_id='.$key.'">'.$sum.'</a>',$serialss];
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

}
