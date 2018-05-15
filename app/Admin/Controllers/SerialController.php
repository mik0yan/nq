<?php

namespace App\Admin\Controllers;

use App\Serials;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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

            $content->header('序列号列表');
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

            $content->header('编辑序列号');
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

            $content->header('新建序列');
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
        return Admin::grid(Serials::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
            });
            $grid->id('ID')->sortable();
            $grid->product()->name('产品名');
            $grid->purchase()->invoiceno('发票号');
            $grid->purchase()->ship_at('出厂时间');
            $grid->purchase()->arrival_at('入库时间');
            $grid->purchase_detail('入库仓库')->display(function($purchase){
                if($s = Stock::find($purchase['to_stock_id']))
                {
                    return $s->name;
                } else
                    return null;
            });
            $grid->stock()->name('当前仓库');
            $grid->serial_no('序列号')->editable();
            $grid->comment('备注信息')->editable();
            $grid->dd('发货信息备注')->display(function(){
                $serial = Serials::find($this->id);
                $transfer = $serial->transfer;
                return "<a href='transfer2/$transfer->id/edit'>$transfer->comment</a>";
            });
//            comment('发货信息备注');
            $grid->filter(function($filter){
//                $filter->disableIdFilter();

                $filter->in('stock_id', '当前仓库')->select(Stock::all()->pluck('name','id'));

                $filter->in('product_id', '产品')->select(Product::all()->pluck('item','id'));

                $filter->in('purchase_id', '采购单')->select(Transfer::where('catalog','1')->pluck('invoiceno','id'));

                $filter->in('transfer_id', '销售发货单')->select(Transfer::where('catalog','3')->pluck('invoiceno','id'));

                $filter->where(function ($query) {

                    $query->where('serial_no', 'like', "%{$this->input}%")

                ;
                }, '查询序列号');
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
        return Admin::form(Serials::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('product_id','产品')->options(function ($id) {
                $product = Product::find($id);

                if ($product) {
                    return [$product->id => $product->name];
                }
            })->ajax('/api/products');
            $form->select('purchase_id','采购单')->options(function ($id) {
                $purchase = Purchase::find($id);

                if ($purchase) {
                    return [$purchase->id => $purchase->name];
                }
            })->ajax('/api/purchases');
            $form->select('ship_id','发货单')->options(function ($id) {
                $ship  = Transfer::find($id);

                if ($ship) {
                    return [$ship->id => $ship->name];
                }
            })->ajax('/api/ships');

            $form->text('serial_no','序列号');
            $form->text('comment','备注信息');
            // $form->text('inviceno','发票号');
            // $form->text('inviceno','发票号');
            // $form->text('inviceno','发票号');

            // $form->display('created_at', 'Created At');
            // $form->display('updated_at', 'Updated At');
        });
    }

    public function serial_dup(Request $rq)
    {
        $data = $rq->all();
        Log::info("serial duplicate check: ——————".$rq->input('pid').$rq->input('serial'));
        if(isSet($data['stock_id']))
        {
            if($ss = Serials::where('stock_id',$data['stock_id'])->where('serial_no',$data['serial'])->first())
            {
                if($ss->product_id == $data['pid'])
                    return 1;
                else
                    return 2;
//                Serials::where('stock_id',$data['stock_id'])
//                    ->where('product_id',$data['pid'])
//                    ->where('serial_no',$data['serial'])
//                    ->get()->isNotEmpty();
            }
            else
                return 0;
        }
        else
            return (int) Serials::where('product_id',$data['pid'])->where('serial_no',$data['serial'])->get()->isNotEmpty();
    }

    public function serialInStock(Request $rq)
    {
        Log::info("serial  check: ——————".$rq->input('pid').'   '.$rq->input('serial'));
        return (int) Serials::where('stock_id',$rq->input('pid'))->where('serial_no',$rq->input('serial'))->get()->isNotEmpty();
    }
}
