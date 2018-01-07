<?php

namespace App\Admin\Controllers;

use App\Stock;
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

use Encore\Admin\Widgets\Table;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

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
        return Admin::grid(Stock::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
              $actions->disableDelete();
              // append an action.
              $actions->append('<a href=""><i class="fa fa-eye"></i></a>');

              // prepend an action.
              $actions->prepend('<a href="/admin/stock/'.$actions->getKey().'"><i class="fa fa-paper-plane"></i></a>');
            });

            $grid->id('ID')->sortable();
            $stock_type = [1=>'海外', 2=>'海关', 3=>'常规', 4=>'返现', 5=>'损耗', 6=>'借机展机'];
            $grid->type('类型')->editable('select',$stock_type);
            $grid->name('仓库名')->editable();
            $grid->user()->name('库管');
            $grid->address('地址')->editable('textarea');
            $grid->postal_code('邮编')->editable();
            $states = [
                'on'  => ['value' => 1, 'text' => '加密', 'color' => 'danger'],
                'off' => ['value' => 2, 'text' => '开放', 'color' => 'success'],
            ];
            $grid->privated('私有')->switch($states)->sortable();
            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->equal('type')->select([1 => '海外', 2 => '海关', 3 => '常规', 4 => '返修', 5 => '损耗', 6 => '借机']);
                // 在这里添加字段过滤器


            });
            // $grid->user_id('责任人')->editable('select','/admin/api/users');
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
            $form->select('user_id','库管')->options(function ($id) {
                $user = User::find($id);
                if ($user) {
                    return [$user->id => $user->name];
                }
            })->ajax('/admin/api/users');
                 // $form->ckeditor('address');
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

        $transfers1 = Transfer::where('from_stock_id',$id)->get();
        $transfers2 = Transfer::where('to_stock_id',$id)->get();
        // return $transfers2;
        return $this->getProductStock($transfers1,$transfers2,$id);
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
        // return $grouped;
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
                $product = Product::find($key);
            }
              $b[] = [$product->item,$product->desc,'<a href="/admin/serials?stock_id='.$stock_id.'&product_id='.$key.'">'.$sum.'</a>'];
        }
        $headers = ['型号','规格','数量'];

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

}
