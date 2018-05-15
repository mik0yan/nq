<?php

namespace App\Admin\Controllers;

use App\Order;
use App\Users;
use App\Client;
use App\Reward;
use App\Admin_user;
use App\Agent;
use App\Admin\Extensions\CheckRow;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Form as Form2;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class OrderController extends Controller
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

            $content->header('订单列表');
            $content->description('所有订单列表');

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

            $content->header('编辑订单');
            $content->description('订单信息编辑');

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

            $content->header('新建订单');
            $content->description('订单信息');

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
        return Admin::grid(Order::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->user()->name('销售员');
            $grid->client()->corp('医院');
            $grid->agent()->corp('代理商');
            $grid->sum('总价')->sortable();
            $grid->price('货值');
            $grid->package('运营成本');
            $grid->bonus('毛利');
            $grid->comment('备注信息');
            // $grid->created_at();
            $grid->updated_at('结算时间');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                // 添加操作
                $order = Order::find($actions->getKey());
//                $actions->append(new CheckRow($actions->getKey()));
                if($order->status > 9)
                {
                    $actions->disableEdit();
                }
                elseif($order->status = 9){
                    $actions->prepend('<a href="/order/check/'.$actions->getKey().'"ˆ><i class="fa fa-check-square-o"></i></a>');

                }

                $actions->append('<a href=""><i class="fa fa-outdent"></i></a>');

            });

            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->equal('user_id', '销售员')->select(Admin_user::where('is_sale',1)->pluck('name','id'));
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
        return Admin::form(Order::class, function (Form $form) {

            $form->hidden('id');
            $form->hidden('status')->value(9);
//            $form->hidden('rates')->value(9);
            $form->select('user_id','销售员')->options(
                Admin_user::All()->pluck('name', 'id')
            );
            $form->select('client_id','医院')->options(
                Client::All()->pluck('corp', 'id')
            );
            $form->select('agent_id','代理商')->options(
                Agent::All()->pluck('corp', 'id')
            );
            $form->currency('sum','总价')->symbol('￥');
            $form->currency('price','货值')->symbol('￥');
            $form->currency('package','运营成本')->symbol('￥');
            $form->textarea('comment','备注信息');
            $form->date('finished_at','结算日期')->format('YYYY-MM-DD');
            $form->hidden('bonus');
//            S10:待授权,S11:授权,S12:授权拒绝,S1,暂存,S2:新建,S3:会签,S4:签章,S5:收款,S6:备货,S7:发货,S8:收货,S9:完成,S0:取消,S20完结
            $form->saving(function (Form $form) {

                $form->bonus = $form->sum - $form->package - $form->price ;
            });

            $form->saved(function (Form $form) {

            });

        });
    }

    public function checkOrder($id)
    {
        $order = Order::find($id);
        $order->status = 20;
        $order->save();
        $data = [
            [
                'user_id'   => $order->user_id,
                'stage'     => 1,
                'order_id'  => $order->id,
                'status'    => 2,
                'rate'      => 0.06,
                'sum'       => 0.06 * $order->price,
                'created_at'=> $order->finished_at,
                'updated_at'=> now()
            ],
            [
                'user_id'   => $order->user_id,
                'stage'     => 1,
                'order_id'  => $order->id,
                'status'    => 2,
                'rate'      => 0.2 ,
                'sum'       => 0.2 * $order->bonus,
                'created_at'=> $order->finished_at,
                'updated_at'=> now()
            ]
        ];
        Reward::unguard();
        $rewards = Reward::insert($data);
        Reward::reguard();
        return redirect('order');

    }

    public function list()
    {
        return Order::orderBy('updated_at','desc')->get()->map(function($order){
            return [
                'id' => $order->id,
                'name'=>"销售【". $order->user->name.'】跟进【'. $order->client->corp."】订单".$order->comment,
                'disabled'=> $order->status < 7
            ];
        });
    }


}
