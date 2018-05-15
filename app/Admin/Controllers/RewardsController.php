<?php

namespace App\Admin\Controllers;

use App\Reward;

use App\Admin_user;
use App\Order;
use App\Client;
use Carbon\Carbon;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RewardsController extends Controller
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
        Carbon::setLocale('zh');

        return Admin::grid(Reward::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->user_id('销售员')->display(function ($user_id) {
                $user = Admin_user::find($user_id);
                return $user->name;
            });
            $grid->order_id('医院')->display(function ($order_id) {
                $order = Order::find($order_id);
                $client = Client::find($order->client_id);
                return $client->corp;
            });
            $grid->sum('金额');
            $grid->created_at('更新时间')->display(function ($time){
                return Carbon::parse($time)->diffForHumans();
            });

            $grid->actions(function ($actions) {
                $actions->disableEdit();
                $actions->disableDelete();
//            $grid->updated_at();
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
        return Admin::form(Reward::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
