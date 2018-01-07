<?php

namespace App\Admin\Controllers;

use App\serials;
use App\Transfer;
use App\Purchase;
use App\User;
use App\Stock;
use App\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class SerialController extends Controller
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
        return Admin::grid(serials::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->product()->name('产品名');
            $grid->purchase()->invoiceno('发票号');
            $grid->purchase()->ship_at('出厂时间');
            $grid->purchase()->arrival_at('入库时间');
            $grid->purchase_detail('入库仓库')->display(function($purchase){
                return Stock::find($purchase['to_stock_id'])->name;
            });
            $grid->stock()->name('当前仓库');
            $grid->serial_no('序列号')->editable();
            $grid->comment('备注信息')->editable();
            $grid->filter(function($filter){
                $filter->disableIdFilter();

                $filter->in('stock_id', '仓库')->multipleSelect('/admin/api/stocks');

                $filter->in('product_id', '产品')->multipleSelect('/admin/api/products');

                $filter->in('purchase_id', '采购单')->multipleSelect('/admin/api/purchases');

                $filter->in('ship_id', '销售发货单')->multipleSelect('/admin/api/ships');

                $filter->where(function ($query) {

                    $query->where('serial_no', 'like', "%{$this->input}%")

                ;}, '查询序列号');
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
        return Admin::form(serials::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('product_id','产品')->options(function ($id) {
                $product = Product::find($id);

                if ($product) {
                    return [$product->id => $product->name];
                }
            })->ajax('/admin/api/products');
            $form->select('purchase_id','采购单')->options(function ($id) {
                $purchase = Purchase::find($id);

                if ($purchase) {
                    return [$purchase->id => $purchase->name];
                }
            })->ajax('/admin/api/purchases');
            $form->select('ship_id','发货单')->options(function ($id) {
                $ship  = Transfer::find($id);

                if ($ship) {
                    return [$ship->id => $ship->name];
                }
            })->ajax('/admin/api/ships');

            $form->text('serial_no','序列号');
            $form->text('comment','备注信息');
            // $form->text('inviceno','发票号');
            // $form->text('inviceno','发票号');
            // $form->text('inviceno','发票号');

            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }
}
