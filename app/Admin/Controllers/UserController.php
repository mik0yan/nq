<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Tools\UserRole;
use App\Admin_user as User;
use App\Admin_user;
use App\Role;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class UserController extends Controller
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

            $content->header('用户列表');
            $content->description('维护管理用户');
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/'],
                ['text' => '用户管理', 'url' => '/admin/users']
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
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->tools(function ($tools) {
                $tools->append(new UserRole());
            });
            $grid->id('ID')->sortable();
            $grid->name('姓名');
            $grid->avatar('头像')->image("", 100, 100);
            $grid->work_id('工号');
            $grid->phone('手机号')->editable();
            $grid->email('邮箱')->editable();
            $grid->roles('角色')->pluck('name')->map('ucwords')->implode(',');
            $grid->is_sale('前后台')->editable('select', [1 => '销售', 0 => '职能', 3 => '管理员']);
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
        return Admin::form(User::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('username','用户代码');
            $form->text('name','姓名');
            $form->image('avatar','头像')->uniqueName();
            $form->text('work_id','工号');
            $form->text('phone','手机');
            $form->text('email','邮箱');
            $form->switch('is_sale','业务职能')->states([
                'on'  => ['value' => 1, 'text' => '业务', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '职能', 'color' => 'info'],
            ]);
            $form->multipleSelect('roles','角色')->options(Role::all()->pluck('name', 'id'));
            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    public function list()
    {
        return Admin_user::all()->map(function($user){
            return [
                'id'=>$user->id,
                'name'=>$user->name,
            ];
        });
    }
}
