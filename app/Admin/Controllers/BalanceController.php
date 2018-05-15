<?php

namespace App\Admin\Controllers;
use App\Admin_user;
use App\Order;
use App\Refund;
use App\Reward;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class BalanceController extends Controller
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

            $content->header('销售员余额表');
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

            $content->header('销售员');
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
        return Admin::grid(Admin_user::class, function (Grid $grid) {
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
//              $actions->append('<a href=""><i class="fa fa-eye"></i>查看</a>');

                // prepend an action.
//                $actions->append('编辑');

//                $actions->append('<a href="/order?&user_id='.$actions->getKey().'"><i class="fa fa-list"></i>订单清单</a>');
//                $actions->append('<a href="/refund?&user_id='.$actions->getKey().'"><i class="fa fa-list"></i>报销清单</a>');
//                $actions->append('<a href="/reward?&user_id='.$actions->getKey().'"><i class="fa fa-list"></i>额度清单</a>');
            });
            $grid->model()->where('is_sale',1);
            $grid->id('ID')->sortable();
            $grid->name('销售员');
            $grid->column('a','订单总额')->display(function (){
                $os = Order::where('user_id',$this->id)->sum('sum');

                return '<a href="/order?&user_id='.$this->getkey().'">'.$os.'</a>';
            });
            $grid->column('c','额度总额')->display(function (){
                $os = Reward::where('user_id',$this->id)->sum('sum');
//                return $os;
                return '<a href="/reward?&user_id='.$this->id.'">'.$os.'</a>';
            });
            $grid->column('b','报销总额')->display(function (){
                $os = Refund::where('user_id',$this->id)->sum('sum');
//                return $os;
                return '<a href="/refund?&user_id='.$this->id.'">'.$os.'</a>';

            });
            $grid->column('d','余额')->display(function (){
                $os = Reward::where('user_id',$this->id)->sum('sum') - Refund::where('user_id',$this->id)->sum('sum');
                return $os;
            });
//            $grid->created_at();
//            $grid->updated_at();

            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->equal('id', '销售员')->select(Admin_user::where('is_sale',1)->pluck('name','id'));
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
        return Admin::form(Admin_user::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
