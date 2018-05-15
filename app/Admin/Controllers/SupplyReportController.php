<?php

namespace App\Admin\Controllers;

use App\Transfer;

use App\Vendor;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Form as Form2;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Symfony\Component\HttpFoundation\Request;

class SupplyReportController extends Controller
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
        return Admin::grid(Transfer::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->created_at();
            $grid->updated_at();
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

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function table(Request $rq)
    {
        return Admin::content(function (Content $content) use ($rq){
            $content->header('进销存统计');
            $tab = new Tab();
            // table 1
            $headers = ['Id', '分类', '供应商', '物料号', '产品','规格','数量'];
            $transfers =  Transfer::where('catalog',1)->whereBetween('arrival_at',[$rq['start'],$rq['end']])->get();
            $group = $transfers->transform(function ($transfer){
                return $transfer->products;
            })->sortBy('catalog')->flatten(1)->groupBy('id');

            $result = $group->transform(function ($item,$key){
                $sum = $item->sum('pivot.amount');

                return [
                    $item[0]->id,
                    $item[0]->catalog,
                    $item[0]->vendor->name,
                    $item[0]->sku,
                    $item[0]->item,
                    $item[0]->desc,
//                    'core' => $item[0]->core,
                    $sum,
                ];
            })->values();

            $table1 = new Table($headers, $result->toArray());
            $tab->add('期间进货', $table1);
//            $content->row((new Box('第一季度进货统计', $table1))->style('info')->solid());

            $transfers =  Transfer::where('catalog',3)->whereBetween('ship_at',[$rq['start'],$rq['end']])->get();
            $group = $transfers->transform(function ($transfer){
                return $transfer->products;
            })->flatten(1)->groupBy('id');

            $result = $group->transform(function ($item,$key){
                $sum = $item->sum('pivot.amount');

                return [
                    $item[0]->id,
                    $item[0]->catalog,
                    $item[0]->vendor->name,
                    $item[0]->sku,
                    $item[0]->item,
                    $item[0]->desc,
//                    'core' => $item[0]->core,
                    $sum,
                ];
            })->sortBy('catalog')->values();
            $table2 = new Table($headers, $result->toArray());
            $tab->add('期间出货', $table2);


            $transfers =  Transfer::where('catalog',1)->where('arrival_at','<=',$rq['end'])->get();
            $transfers2 =  Transfer::where('catalog',3)->where('ship_at','<=',$rq['end'])->get();
            $products = $transfers->transform(function ($transfer){
                return $transfer->products;
            })->flatten(1)->transform(function ($item){
                return [
                    'id' => $item->id,
                    'catalog' => $item->catalog,
                    'core' => $item->core,
                    'vendor_id' => $item->vendor_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'item' => $item->item,
                    'desc' => $item->desc,
                    'transfer_id' => $item->pivot->transfer_id,
                    'amount' =>$item->pivot->amount,
                ];
            });
            $products2 = $transfers2->transform(function ($transfer){
                return $transfer->products;
            })->flatten(1)->transform(function ($item){
                return [
                    'id' => $item->id,
                    'catalog' => $item->catalog,
                    'core' => $item->core,
                    'vendor_id' => $item->vendor_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'item' => $item->item,
                    'desc' => $item->desc,
                    'transfer_id' => $item->pivot->transfer_id,
                    'amount' => -1 * $item->pivot->amount,
                ];
            });
            $result = $products
                ->merge($products2)
                ->groupBy('id')
                ->transform(function ($item,$key){
//                    return $item->sum('amount');
                    $sum = $item->sum('amount');
                    $vendor = Vendor::find($item[0]['vendor_id'])->name;
//                    return $item[0];
                    return [
                        $item[0]['id'],
                        $item[0]['catalog'],
                        $vendor,
                        $item[0]['sku'],
                        $item[0]['item'],
                        $item[0]['desc'],
                        $sum,
                    ];
                })->values();
            $table3 = new Table($headers, $result->toArray());
            $tab->add('期末库存', $table3);

//            $content->row((new Box('第一季度出货统计', $table2))->style('success')->solid());
            $content->row($tab);
        });
    }



    public function import(Request $rq)
    {
        if(isSet($rq['start'])&&isSet($rq['end']))
        {
            $transfers =  Transfer::where('catalog',1)->whereBetween('ship_at',[$rq['start'],$rq['end']])->get();
            $transfers2 =  Transfer::where('catalog',3)->whereBetween('ship_at',[$rq['start'],$rq['end']])->get();
            $products = $transfers->transform(function ($transfer){
                return $transfer->products;
            })->flatten(1)->transform(function ($item){
                return [
                    'id' => $item->id,
                    'catalog' => $item->catalog,
                    'core' => $item->core,
                    'vendor_id' => $item->vendor_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'item' => $item->item,
                    'desc' => $item->desc,
                    'transfer_id' => $item->pivot->transfer_id,
                    'amount' =>$item->pivot->amount,
                ];
            });
            $products2 = $transfers2->transform(function ($transfer){
                return $transfer->products;
            })->flatten(1)->transform(function ($item){
                return [
                    'id' => $item->id,
                    'catalog' => $item->catalog,
                    'core' => $item->core,
                    'vendor_id' => $item->vendor_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'item' => $item->item,
                    'desc' => $item->desc,
                    'transfer_id' => $item->pivot->transfer_id,
                    'amount' => -1 * $item->pivot->amount,
                ];
            });
            $group = $products
                ->merge($products2)
                ->groupBy('id')
                ->transform(function ($item,$key){
//                    return $item->sum('amount');
                    $sum = $item->sum('amount');
//                    return $item[0];
                    return [
                        $item[0]['id'],
                        $item[0]['catalog'],
                        $item[0]['vendor_id'],
                        $item[0]['sku'],
                        $item[0]['item'],
                        $item[0]['desc'],
                        $sum,
                    ];
            })->values();


            return response()->json(
                [
                    'code'=>200,
                    'message'=>"success",
                    'data'=> $group,
                ]
            );
        }
        else
            return response()->json(
                [
                    'code'=>200,
                    'message'=>"无数据",
                ]
            );
    }
}
