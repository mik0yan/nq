<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Product;
use App\Product_stock;
use App\Serials;
use Illuminate\Support\Facades\Request;

use App\Transfer;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductLineController extends Controller
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
        return Admin::grid(Product_stock::class, function (Grid $grid) {
            if($stock = Request::get('stock'))
            {
                $tranfers = Transfer::where('to_stock_id',$stock)->orwhere('from_stock_id',$stock)->pluck('id');
                $grid->model()->wherein('transfer_id',$tranfers)->orderBy('transfer_id','desc');
            } else
            {
                $grid->model()->orderBy('transfer_id','desc');
            }
            $grid->id('ID')->sortable();
            $grid->transfer()->catalog('类型')->editable('select', [
//                T1:采购,T2:移库,T3:发货,T4:借出,T5:返还,T6.损耗,T7:改配
                1 => '采购',
                2 => '调拨',
                3 => '发货',
                4 => '借出',
                5 => '返还',
                6 => '损耗',
                7 => '改配',
            ]);
            optional($grid->transfer())->id('发票号');
            $grid->column('a','入库')->display(function(){
                $transferId = Product_stock::find($this->id)->transfer_id;
                if($stock =optional(Transfer::find($transferId))->stock)
                    return $stock->name;
                else
                    return "";
            });


            $grid->column('b','出库')->display(function(){
                $transferId = Product_stock::find($this->id)->transfer_id;
                if($stock = optional(Transfer::find($transferId))->stock2)
                    return $stock->name;
                else
                    return "";
            });
            $grid->column('c','发货时间')->display(function(){
                $transferId = Product_stock::find($this->id)->transfer_id;
                if($transfer = optional(Transfer::find($transferId)))
                    return $transfer->ship_at;
                else
                    return "";
            });
            $grid->column('d','操作员')->display(function(){
                $transferId = Product_stock::find($this->id)->transfer_id;
                if($user =optional(Transfer::find($transferId))->user)
                    return $user->name;
                else
                    return "";
            });
            $grid->product()->name('产品名');
            $grid->transfer()->comment('备注');
            $grid->product()->sku('物料');
//            $grid->remark('详情');
            $grid->amount('数量')->editable();
            $grid->column('e','序列号')->display(function(){
                $transferId = Product_stock::find($this->id)->transfer_id;
                $productId = Product_stock::find($this->id)->product_id;
                $result = "";
                if($transfer = Transfer::find($transferId))
                {
                    $product = Product::find($productId);
                    if($transfer->catalog == 1)
                    {
                        $ss = Serials::where('purchase_id',$transfer->id)->where('product_id',$product->id)->pluck('serial_no');

                        foreach ($ss as $s)
                        {
                            $result .= "<p>{$s}</p>";
                        }
                    }
//                        return "<p>".implode('</p><p>',Serials::where('purchase_id',$transfer->id)->pluck('serial_no'))."<p>";
                    else
                    {
                        $ss =  Serials::where('transfer_id',$transfer->id)->where('product_id',$product->id)->pluck('serial_no');
                        foreach ($ss as $s)
                        {
                            $result .= "<p>{$s}</p>";
                        }
                    }
//                        return "<p>".implode('</p><p>',Serials::where('transfer_id',$transfer->id)->pluck('serial_no'))."<p>";

                }
                    return $result;
            });
//            $grid->created_at();
//            $grid->updated_at();
            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->equal('product_id','产品')->select(Product::pluck('name','id'));
                $filter->equal('user_id','管理员')->select(Admin_user::where('is_sale',0)->pluck('name','id'));
                // 在这里添加字段过滤器


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
        return Admin::form(Product_stock::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
