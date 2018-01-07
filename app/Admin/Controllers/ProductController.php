<?php

namespace App\Admin\Controllers;

use App\Product;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ProductController extends Controller
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
            // $content->row('hello');
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
        return Admin::grid(Product::class, function (Grid $grid) {
            $grid->Model()->orderBy('sku','desc');
            $grid->id('ID')->sortable();
            $grid->name('名称')->editable();
            $grid->sku('物料号')->editable();
            $grid->item('型号')->editable();
            $vendor = [1=>'法国席勒', 2=>'瑞士席勒', 3=>'德国席勒', 4=>'席勒天津', 5=>'上海希丽'];
            $catalog = ['软件','小肺','心电','除颤','肺功能','运动血压','运动踏车'];
            // $grid->vendor()->corp('供应商');
            $grid->vendor_id('生产商')->editable('select',$vendor);
            $grid->catalog('分类')->editable('select',array_combine($catalog,$catalog));
            // $grid->catalog('分类')->sortable();
            $grid->desc('商品描述')->editable('textarea');
            $grid->price('商品价格')->editable();
            $grid->cert_no('注册号');
            // $grid->license('合规')->checkbox([1 => '合规',2 => '不合规']);
            $states = [
                'on'  => ['value' => 1, 'text' => '序列号', 'color' => 'success'],
                'off' => ['value' => 2, 'text' => '小件', 'color' => 'info'],
            ];
            $grid->core('核心物料')->switch($states)->sortable();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
                $filter->in('core', '序列号？')->multipleSelect([1 => '产品',2 => '配件']);

                $vendor = [1=>'法国席勒', 2=>'瑞士席勒', 3=>'德国席勒', 4=>'席勒天津', 5=>'上海希丽'];
                $catalog = ['软件','小肺','心电','除颤','肺功能','运动血压','运动踏车'];
                $filter->equal('vendor_id','供应商')->select($vendor);
                $filter->equal('catalog','类型')->select(array_combine($catalog,$catalog));
                // $filter->like('name', '名称');
                $filter->where(function ($query) {

                    $query->where('name', 'like', "%{$this->input}%")
                          ->orWhere('name', 'like', "{$this->input}%")
                          ->orWhere('desc', 'like', "%{$this->input}%")
                          ->orWhere('desc', 'like', "{$this->input}%");

                }, '关键词');
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
        return Admin::form(Product::class, function (Form $form) {

            $form->display('id', 'ID');
            $vendor = [1=>'法国席勒', 2=>'瑞士席勒', 3=>'德国席勒', 4=>'席勒天津', 5=>'上海希丽'];
            $catalog = ['软件','小肺','心电','除颤','肺功能','运动血压','运动踏车'];

            $form->select('catalog','类型')->options(array_combine($catalog,$catalog));
            $form->select('vendor_id','供应商')->options($vendor);
            $form->text('name','产品名');
            $form->text('sku','物料号')->placeholder('商品唯一编号,数字字母及.');
            $form->textarea('item','型号');
            $form->textarea('desc','商品描述')->rows(3)->placeholder('商品的详细说明');
            $form->currency('price','价格')->symbol('￥');
            $states = [
                'on'  => ['value' => 1, 'text' => '序列号', 'color' => 'success'],
                'off' => ['value' => 2, 'text' => '小件', 'color' => 'info'],
            ];
            $form->switch('core','核心物料')->states($states);
            $form->text('cert_no','注册证号')->placeholder('CFDA注册号');
            $form->text('cert_url','认证网址');
            $form->date('certified_at','证有效期')->placeholder('证上有效期限');


        });
    }
}
