<?php

namespace App\Admin\Controllers;

use App\agent;
use App\area;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AgentController extends Controller
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

            $content->header('代理商');
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

            $content->header('编辑代理商');
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

            $content->header('新建代理商');
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
        return Admin::grid(agent::class, function (Grid $grid) {

            $grid->id('ID')->sortable();
            $grid->corp('公司名称');
            $grid->name('联系人');
            $grid->mobile('联系方式');
            $grid->level('等级')->select([1=>'经销商',2=>'总代',3=>'直销']);
//            $grid->area()->merger_name();
            $grid->column('area_code','地区')->display(function ($area){
                return area::find($area)->address();
            });
            $grid->email('联系邮箱');
            $states = [
                'on'  => ['value' => 1, 'text' => '独占', 'color' => 'primary'],
                'off' => ['value' => 0, 'text' => '公开', 'color' => 'default'],
            ];
            $grid->flag('独占性')->switch($states);
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
        return Admin::form(agent::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->text('corp','公司');
            $form->text('name','姓名');
            $form->text('mobile','联系方式');
            $form->text('email','电子邮箱');
            $form->select('level','等级')->options([1=>'经销商',2=>'总代',3=>'直销'])->default(1);
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
        });
    }
}
