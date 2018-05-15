<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\OrderCatalog;
use App\Admin_user;
use App\Client;
use App\Agent;
use App\Order;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Form as Form2;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Table;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Request;

class OrderSimpleController extends Controller
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
            $content->description('业务部门订单列表');
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '订单', 'url' => '/order2']
            );
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
            $content->description('订单详情');

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
            $content->description('订单详情');

            $content->body($this->createform());
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
            $grid->tools(function ($tools) {
                $tools->append(new OrderCatalog());
            });
//            $grid->model()->where('status','')
            if(is_numeric( Request::get('status')))
                $grid->model()->where('status',Request::get('status'));
            $grid->id('ID')->sortable();
            $grid->user()->name('销售员');
            $grid->client()->corp('医院');
            $grid->agent()->corp('代理商');
            $grid->sum('总价')->sortable();
            $grid->price('货值');
            $grid->package('运营成本');
            $grid->bonus('毛利');
            $grid->comment('备注信息');
            $grid->products('审批记录')->display(function ($products){
                $rows = [];
                foreach($products as $product){
                    $p = Product::find($product['product_id']);
                    $line = [
                        Product::find($product['product_id'])->item,
                        Product::find($product['product_id'])->desc,
                        '<a href="/serials?product_id='.$p->id.'&transfer_id='.$this->getKey().'">'.$product['amount'].'</a>'

                    ];
                    $rows[] = $line;
                }
                $headers = ['型号','规格','数量'];

                $table = new Table($headers, $rows);

                return $table->render();
            });
            $grid->finished_at('到货时间')->display(function ($dt){
                return Carbon::parse($dt)->toDateString();
            });

//            $grid->created_at();
//            $grid->updated_at();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
//                $actions->disableEdit();
            });
            $grid->filter(function($filter) {
                $agent = new Agent();
                $user = new Admin_user();
                $client = new Client();
                $filter->disableIdFilter();
                $filter->equal('user_id', '销售员')->select($user->hasOrder());
                $filter->equal('agent_id', '代理商')->select($agent->hasOrder());
                $filter->equal('client_id', '客户')->select($client->hasOrder());
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
        return Admin::form(Order::class, function (Form $form)  {

            $form->display('id', 'ID');
            $form->select('client_id','客户')->options(Client::all()->pluck('corp','id'));
            $form->select('agent_id','代理商')->options(Agent::all()->pluck('corp','id'));
            $form->select('user_id','销售员')->options(Admin_user::all()->pluck('name','id'));
            $form->text('ordno','订单号');
            $form->text('comment','备注信息');
            $form->switch('status', '状态更新 ')->states($this->states())->default('off');
            $form->number('warranty','质保期');
            $form->datetime('finished_at','到货时间');
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
        });
    }

    protected function createform()
    {
        return Admin::form(Order::class, function (Form $form) {
//            S10:待授权,S11:授权,S12:授权拒绝,S1,暂存,S2:新建,S3:会签,S4:签章,S5:收款,S6:备货,S7:发货,S8:收货,S9:完成,S0:取消
            $form->display('id', 'ID');
            $form->select('client_id','客户')->options(Client::all()->pluck('corp','id'));
            $form->select('agent_id','代理商')->options(Agent::all()->pluck('corp','id'));
            $form->select('user_id','销售员')->options(Admin_user::all()->pluck('name','id'));
            $form->text('ordno','订单号');
            $form->text('comment','备注信息');
            $form->switch('status', '新建')->states([
                'on' => ['value' => 2, 'text' => '新建', 'color' => 'success'],
                'off'  => ['value' => 1, 'text' => '暂存', 'color' => 'info'],
            ])->default('off');
            $form->number('warranty','质保期');
            $form->datetime('finished_at','到货时间');
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
        });
    }

    public function states($status=1)
    {

        $options = [
            'all'   => 'All',
            1     => '暂存',
            2     => '新建',
            3     => '会签',
            4     => '盖章',
            5     => '收款',
            6     => '备货',
            7     => '发货',
            8     => '收货',
            9     => '结算',
            0     => '取消',
            20     => '待授权',
            21     => '授权',
        ];

        if($status ==9)
            $nextStatus = 0;
        else
            $nextStatus = $status +1;

        return [
            'on' => ['value' => $nextStatus, 'text' => $options[$nextStatus], 'color' => 'success'],
            'off'  => ['value' => $status, 'text' => $options[$status], 'color' => 'info'],
        ];
    }
}
