<?php

namespace App\Admin\Controllers;

use App\Refund;
use App\Admin_user;
use App\Balance;
use App\Reward;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\DB;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RefundController extends Controller
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

            $content->header('支取表');
            $content->description('销售员支取费用记录表');

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

            $content->header('编辑支取记录');
            $content->description('销售员支取费用记录表');

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

            $content->body($this->form2());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Refund::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->user_id('销售员')->display(function ($user_id) {
                $user = Admin_user::find($user_id);
                return $user->name;
            });
            $grid->author_id('审批员')->display(function ($user_id) {
                $user = Admin_user::find($user_id);
                return $user->name;
            });
            $grid->stage('审批角色')->display(function ($stage){
                $stages = array('人力','出纳','财务');
                return $stages[$stage];
            });
            $grid->catalog('类别')->display(function ($cat){
//                1.工资、2奖金、3.绩效、4.差旅、5.平衡记账
                $cats = [1=>'工资',2=>'奖金',3=>'绩效',4=>'差旅',5=>'平衡记账'];
                return $cats[$cat];
            });
            $grid->sum('金额');
            $grid->updated_at('审批时间');

            $grid->filter(function($filter) {
                $filter->disableIdFilter();
                $filter->equal('user_id', '销售员')->select(Admin_user::where('is_sale',1)->pluck('name','id'));
                $filter->equal('author_id', '审批员')->select(Admin_user::where('is_sale',0)->pluck('name','id'));
                $filter->equal('catalog', '类别')->select([1=>'工资',2=>'奖金',3=>'绩效',4=>'差旅',5=>'平衡记账']);
                $filter->between('sum', '金额');
                $filter->between('updated_at', '审批时间')->date();
            });
//            $grid->created_at();
//            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Refund::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('user_id','销售员')->options(
                Admin_user::where('is_sale',1)->pluck('name', 'id')
            );
            $form->select('author_id','审批员')->options(
                Admin_user::where('is_sale',0)->pluck('name', 'id')
            );
            $form->select('stage','范畴')->options(array('人力','出纳','财务'));
            $form->select('catalog','类别')->options(array('工资','奖金','绩效','差旅','平衡记账'));
            $form->display('user_id','平衡表')->with(function ($user_id) {
//                $refunds = select('sum');
                $a = Refund::where('user_id',$user_id)->sum('sum');
                $b = Reward::where('user_id',$user_id)->sum('sum');
                if(($a-$b)>0)
                    $c = '#FF0000';
                else
                    $c = '#008000';
                return '<style="color:blue;">￥'.$b-$a.'</style>';
            });
            $form->currency('sum','金额')->symbol('￥');
            $form->text('doc_no','凭证号');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');

        });
    }

    protected function form2()
    {
        return Admin::form(Refund::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->select('user_id','销售员')->options(
                Admin_user::where('is_sale',1)->pluck('name', 'id')
            );
            $form->select('author_id','审批员')->options(
                Admin_user::where('is_sale',0)->pluck('name', 'id')
            );
            $form->select('stage','范畴')->options(array('人力','出纳','财务'));
            $form->select('catalog','类别')->options(array('工资','奖金','绩效','差旅','平衡记账'));
            $form->hidden('status')->value(2);
            $form->currency('sum','额度')->symbol('￥');
            $form->text('doc_no','凭证号');
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
