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

class RentController extends Controller
{
    use ModelForm;
    private $stock_id;
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('租赁清单');
            $content->description('');

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

            $content->header('编辑租赁信息');
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
        if($this->stock_id = $rq->get('stock')){
            return Admin::content(function (Content $content) {

                $content->header('新建租赁');
                $content->description('description');

                $content->body($this->form2());
            });
        }
        else
        {
            return Admin::content(function (Content $content) {

                $content->header('新建租赁');
                $content->description('description');

                $content->body($this->form());
            });
        }

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
                $actions->append('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-list"></i>货品清单</a>');
            });
            $grid->model()->where('catalog','4')->orderBy('arrival_at')->orderBy('ship_at','desc');
            $grid->id('ID')->sortable();
            $grid->track_id('运单号')->editable('text');
            $grid->contractno('协议编号');
            $grid->stock2()->name('出库仓');
            $grid->stock()->name('入库仓');
            $grid->user_id('操作人')->display(function($user_id){
                if($user_id)
                    return Admin_user::findOrFail($user_id)->name;
                else
                    return '未分配';
            });
            $grid->comment('备注')->editable('textarea');
            $grid->ship_at('借出时间')->editable('date');
            $grid->arrival_at('归还时间')->editable('date');
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
            $grid->filter(function ($filter) {

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

            $form->display('id', 'ID');
            $form->hidden('catalog','类别')->value(4);
            $form->select('from_stock_id','出库仓')->options(Stock::all()
                ->pluck('name','id'))
                ->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')
//                    ->default($this->from_stock_id)
                ->rules('required');
            $form->select('to_stock_id','入库仓')->options(Stock::where('type',6)->pluck('name','id'))->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')->rules('required');
            // $form->select('from_stock_id','出库仓')->options('/api/stocks');
            $form->select('user_id','操作员')->options(Admin_user::where('is_sale',0)->pluck('name','id'))->rules('required');
            $form->text('track_id','运单信息');
            $form->date('ship_at','发货日期')->rules('required');
            $form->textarea('comment','备注')->rows(6)->placeholder('填写备注信息');
        });
    }

    protected function form2()
    {
        return Admin::form(Transfer::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('catalog','类别')->value(4);
            $form->select('from_stock_id','出库仓')->options(Stock::all()
                ->pluck('name','id'))
                ->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')
                ->default($this->stock_id);
//                ->rules('required');
            $form->select('to_stock_id','入库仓')->options(Stock::where('type',6)->pluck('name','id'))->help('先输入仓库类型:1.海外,2.海关,3.常规,4.返修,5.损耗,6.借机')->rules('required');
            // $form->select('from_stock_id','出库仓')->options('/api/stocks');
            $form->select('user_id','操作员')->options(Admin_user::all()->pluck('name','id'))->default(4);
            $form->text('track_id','运单信息');
            $form->date('ship_at','发货日期')->rules('required');
            $form->textarea('comment','备注')->rows(6)->placeholder('填写备注信息');
        });
    }
}
