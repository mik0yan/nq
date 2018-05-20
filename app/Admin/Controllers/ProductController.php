<?php

namespace App\Admin\Controllers;

use App\Product;

use App\Stock;
use App\Vendor;
use App\Serials;
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
use Symfony\Component\HttpFoundation\Request;

class ProductController extends Controller
{
    use ModelForm;
    private $catalog = ['静态心电图机','运动心电图仪','运动心肺','康复系统类','运动踏车类','康讯类','动态血压','运动血压','小肺','运动平板类','急救系列','核磁监护仪','动态心电','软件类','监护仪','国产类','运动肺'];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('产品列表');
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

            $content->header('编辑产品');
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

            $content->header('新建产品');
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
            $grid->actions(function ($actions) {
                $actions->disableDelete();
            });
            $grid->Model()->orderBy('id','desc');
            $grid->id('ID')->display(function ($id){
                return "<a href='productline?product_id={$id}'>{$id}</a>";
            })->sortable();
            $grid->name('名称')->editable();
            $grid->sku('物料号')->editable();
            $grid->item('型号')->editable();
//            $catalog = ['静态心电图机','运动心电图仪','运动心肺','康复系统类','运动踏车类','康讯类','动态血压','运动血压','小肺','运动平板类','急救系列','核磁监护仪','动态心电','软件类','监护仪','国产类','运动肺'];
            $grid->vendor()->name('供应商');
            $grid->catalog('分类');
            // $grid->catalog('分类')->sortable();
            $grid->desc('商品描述')->editable('textarea');
//            $grid->price('商品价格')->editable();
//            $grid->cert_no('注册号');
            // $grid->license('合规')->checkbox([1 => '合规',2 => '不合规']);
            $states = [
                'on'  => ['value' => 1, 'text' => '序列号', 'color' => 'success'],
                'off' => ['value' => 2, 'text' => '小件', 'color' => 'info'],
            ];
            $grid->core('核心物料')->switch($states)->sortable();
            $grid->filter(function($filter){
                $filter->disableIdFilter();
//                $filter->in('core', '序列号？')->multipleSelect([1 => '产品',2 => '配件']);

//                $catalog = ['静态心电图机','运动心电图仪','运动心肺','康复系统类','运动踏车类','康讯类','动态血压','运动血压','小肺','运动平板类','急救系列','核磁监护仪','动态心电','软件类','监护仪','国产类','运动肺'];
                $filter->equal('vendor_id','供应商')->select(Vendor::all()->pluck('name','id'));
                $filter->equal('catalog','类型')->select(array_combine($this->catalog,$this->catalog));
                // $filter->like('name', '名称');
                $filter->where(function ($query) {

                    $query->where('name', 'like', "%{$this->input}%")
                        ->orWhere('sku', 'like', "%{$this->input}%")
                        ->orWhere('desc', 'like', "%{$this->input}%")
                        ->orWhere('item', 'like', "%{$this->input}%");
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
//            $catalog = ['静态心电图机','运动心电图仪','运动心肺','康复系统类','运动踏车类','康讯类','动态血压','运动血压','小肺','运动平板类','急救系列','核磁监护仪','动态心电','软件类','监护仪','国产类','运动肺'];

            $form->select('catalog','类型')->options(array_combine($this->catalog,$this->catalog))->rules('required');
            $form->select('vendor_id','供应商')->options(Vendor::all()->pluck('name','id'))->rules('required');

            $form->text('name','产品名')->rules('required|min:2');
            $form->text('sku','物料号')->placeholder('商品唯一编号,数字字母及.')->rules(function ($form) {

                // 如果不是编辑状态，则添加字段唯一验证
                if (!$id = $form->model()->id) {
                    return 'required|regex:/^[a-zA-Z0-9.-]+$/|unique:products,sku,'.$form->model()->id;
                }
                else {
                    return 'required|regex:/^[a-zA-Z0-9.-]+$/|unique:products,sku,'.$form->model()->id;
                }

            });
            $form->textarea('item','型号')->rules('required|min:2');
            $form->textarea('desc','商品描述')->rows(3)->placeholder('商品的详细说明');
            $form->currency('price','价格')->symbol('￥');
            $states = [
                'on'  => ['value' => 1, 'text' => '序列号', 'color' => 'success'],
                'off' => ['value' => 2, 'text' => '小件', 'color' => 'info'],
            ];
            $form->switch('core','核心物料')->states($states)->default(1);
            $form->text('cert_no','注册证号')->placeholder('CFDA注册号');
            $form->text('cert_url','认证网址');
            $form->date('certified_at','证有效期')->placeholder('证上有效期限');
            $form->hasMany('packages', function (Form\NestedForm $form) {
                $form->select('catalog')->options([1 =>'商务包',2=>'培训包', 3=>'维保包']);
                $form->text('name','名称');
                $form->text('code','代码');
                $form->currency('add_price','附加价格')->symbol('￥');
                $form->text('desc','描述');
            });

        });
    }


    public function  list()
    {
        return Product::all()->map(function($product){
            return [
                'id' => $product->id,
                'core'=> $product->core,
                'name'=> $product->name.$product->desc,
            ];
        });
    }

    public function stocklist($id)
    {
        $res = collect(Stock::find($id)->amountProducts())->filter(function($num){
            return $num>0;
        })->keys();
        return Product::whereIn('id',$res)->get()->map(function($product){
            return [
                'id' => $product->id,
                'core'=> $product->core,
                'name'=> $product->name.$product->desc,
            ];
        });
    }

    public function api_reserve($id)
    {
        $res = collect(Stock::find($id)->amountProducts())->filter(function($num){
            return $num>0;
        })->keys();
        return Product::whereIn('id',$res)->get()->map(function($product){
            return [
                'id' => $product->id,
                'core'=> $product->core,
                'name'=> $product->name.$product->desc,
            ];
        });
    }

    public function api_show(Request $rq , $id)
    {
        $data = $rq->all();
        if(isSet($data['stock_id']))
        {
            $num =  Stock::find($data['stock_id'])->amountProducts();
            $p = Product::find($id);
            $p['num'] = $num[$id];
            if($p->core == 1)
                $p['stocklist']=Serials::where('product_id',$id)->where('stock_id',$data['stock_id'])->pluck('serial_no');
            return $p;
        }
        else
            return Product::find($id);
    }

//    查找该产品在库存量
    public function api_count(Request $rq, $id)
    {
        $data = $rq->all();
        if(isSet($data['stock_id']))
        {
            $list = Stock::find($data['stock_id'])->amountProducts();
            if(isSet($list[$id]))
                return $list[$id];
            else
                return 0;
        }
        else
            return 0;
    }

    public function api_stock(Request $rq, $id)
    {
        $data = $rq->all();
        if(isSet($data['stock_id']))
        {
            $p = Product::find($id);
            $list = Stock::find($data['stock_id'])->amountProducts();
            if(isSet($list[$id]))
            {
                $ss = Serials::where('stock_id',$data['stock_id'])->where('product_id',$id)->pluck('serial_no');
                $p['max'] = $list[$id];
                $p['stocklist'] = $ss->combine(array_fill(0,count($ss),false));
                return $p;
            }

            else
            {
                $p['max'] = 0;
                return $p;
            }

        }
        else
            return null;
    }
}
