<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Product;
use App\Stock;
use App\Transfer;
use App\Order;
use Illuminate\Support\Facades\Request;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use App\Admin\Extensions\Tools\TransferCatalog;

class TransferAdminController extends Controller
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

            $content->header('进销存');
            $content->description('进销存后台管理界面');

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

            $content->header('编辑');
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

            $content->header('新建');
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
            $grid->tools(function ($tools) {
                $tools->append(new TransferCatalog());
            });

            $grid->actions(function ($actions) {
//                $actions->disableDelete();
//                $actions->disableEdit();
                $actions->append('<a href="transfer/list/'.$actions->getKey().'"><i class="fa fa-list"></i>货品清单</a>');
                if(Request::get('catalog')==1)
                    $actions->prepend('<a href="document/purchase/'.$actions->getKey().'" target = "_blank" ><i class="fa fa-file-word-o"></i>下载</a>');
                if(Request::get('catalog')==3)
                    $actions->prepend('<a href="document/ship/'.$actions->getKey().'" target = "_blank" ><i class="fa fa-file-word-o"></i>下载</a>');
            });
            $grid->model()->orderBy('updated_at','desc');
            if(is_numeric( Request::get('catalog')))
                $grid->model()->where('catalog',Request::get('catalog'));
            else
                $grid->catalog('分类')->display(function ($t){
                    switch ($t){
                        case 1:
                            return "入库";
                            break;
                        case 2:
                            return "调拨";
                            break;
                        case 3:
                            return "出库";
                            break;
                        case 4:
                            return "借出";
                            break;
                        case 5:
                            return "返还";
                            break;
                        case 6:
                            return "损耗";
                            break;
                        case 7:
                            return "改配";
                            break;
                    }
                });
            $grid->id('ID')->sortable();

//            T1:采购,T2:移库,T3:发货,T4:借出,T5:返还,T6.损耗,T7:改配
            if(in_array(Request::get('catalog'),[2,3,4,6,7]))
                $grid->stock2()->name('出库仓');
            if(in_array(Request::get('catalog'),[1,2,5,7]))
                $grid->stock()->name('入库仓');


            if(in_array(Request::get('catalog'),[3]))
                $grid->order()->name('销售员');

            $grid->invoiceno('发票号');
            $grid->contractno('合同编号');
            $grid->user()->name('录入人员');
            if(in_array(Request::get('catalog'),[2,3,4,5,6]))
                $grid->ship_at('发货日期')->editable();
            $grid->arrival_at('到货日期')->editable('date');
            $grid->comment('备注')->editable('text');
//            $grid->product_stocks('商品清单')->display(function ($products){
//                $rows = [];
//                foreach($products as $product){
//                    $p = Product::find($product['product_id']);
//                    $line = [
//                        Product::find($product['product_id'])->item,
//                        Product::find($product['product_id'])->desc,
//                        '<a href="/serials?product_id='.$p->id.'&transfer_id='.$this->getKey().'">'.$product['amount'].'</a>'
//
//                    ];
//                    $rows[] = $line;
//                }
//                $headers = ['型号','规格','数量'];
//
//                $table = new Table($headers, $rows);
//
//                return $table->render();
//            });
//                ->display(function ($c){
//                return str_limit($c,20,"..") ?? "" ;
//            });

            $grid->product_stocks('商品清单')->display(function ($products){
                $rows = [];
                foreach($products as $product){
                    if($p = Product::find($product['product_id']))
                    {
                        if($p->core == 1)
                            $line = [
                                isSet($p->item)?$p->item:"",
                                isSet($p->desc)?$p->desc:"",
                                '<a href="/serials?product_id='.$p->id.'&transfer_id='.$this->getKey().'">'.$product['amount'].'</a>',
                                count(json_decode($product['remark'],1))>0 ? "<p>".implode(json_decode($product['remark'],1),'</p><p>')."</p>": "",
                            ];
                        else
                            $line = [
                                isSet($p->item)?$p->item:"",
                                isSet($p->desc)?$p->desc:"",
                                '<a href="/productline?product_id='.$p->id.'">'.$product['amount'].'</a>',
                                count(json_decode($product['remark'],1))>0 ? "<p>".implode(json_decode($product['remark'],1),'</p><p>')."</p>": "",
                            ];
                        $rows[] = $line;
                    }

                }
                $headers = ['型号','规格','数量','清单'];
                $table = new Table($headers, $rows);
                return $table->render();
            });
            $grid->filter(function ($filter) {

                // 设置created_at字段的范围查询
//                $filter->in('catalog', '类型')->multipleSelect(['key' => 'value']);
                $filter->equal('user_id','录入人员')->select(Admin_user::all()->pluck('name','id'));
                $filter->like('contractno','合同')->select(Product::all()->pluck('name','id'));
                $filter->like('products','产品');
                $filter->equal('to_stock_id','入库仓库')->select(Stock::all()->pluck('name','id'));
                $filter->equal('from_stock_id','出库仓库')->select(Stock::all()->pluck('name','id'));
                $filter->between('ship_at', '发货时间')->date();

                $filter->between('arrival_at', '到达时间')->date();
                $filter->where(function ($query) {

                    $query->whereRaw("`arrival_at` IS NULL or `ship_at` IS NULL ");

                }, '未填日期');
//                $filter->where(function ($query) {
//
//                    $query->whereRaw("`ship_at` IS NULL");
//
//                }, '无发货日期');
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
            $form->date('ship_at','发货日期');
            $form->date('arrival_at','到货日期');
            $form->text('invoiceno','发票编号');
            $form->text('contractno','协议编号');
            $form->text('track_id','运单号');
            $form->select('order_id',"绑定订单")->options(Order::all()->pluck('comment','id'))->default('order_id');
            $form->select('user_id',"录入人员")->options(Admin_user::all()->pluck('name','id'))->default('user_id');
            $form->textarea('comment','备注');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
