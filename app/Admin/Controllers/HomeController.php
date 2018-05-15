<?php

namespace App\Admin\Controllers;

use App\Admin_user;
use App\Http\Controllers\Controller;
use App\Stock;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Alert;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Widgets\Form as Form2;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Collapse;

use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('首页');
            $content->description('仪表盘');
            $content->breadcrumb(
                ['text' => '首页', 'url' => '/admin'],
                ['text' => '用户管理', 'url' => '/admin/users']
            );

//            $content->row(function ($row) {
//                $row->column(4, new InfoBox('New Users', 'users', 'aqua', '/demo/users', '1024'));
//                $row->column(4, new InfoBox('New Orders', 'shopping-cart', 'green', '/demo/orders', '150%'));
//                $row->column(4, new InfoBox('Articles', 'book', 'yellow', '/demo/articles', '2786'));
//            });
            //库管权限: 查看本人的库房.全部操作权限
            if(Admin::user()->isRole('keeper'))
            {
                $ss =  Admin_user::find(Admin::user()->id)->stocks;

                foreach ($ss as $s)
                {
                    $content->row(function ($row) use($s){
                        $collapse = new Collapse();
                        $line = "<button class='btn btn-success' type='button' onclick= \"window.location.href='/stock/{$s->id}'\">库存</button>&nbsp;&nbsp;";
                        foreach ($s->stockMethods() as $m) {
                            $line .=  "<button class='btn btn-primary' type='button' onclick= \"window.location.href='/transfer/{$s->id}/{$m['title']}'\">{$m['transfer']} <span class='badge'></span></button>&nbsp&nbsp";
//                            $line .=  "<a type='button' onclick= \"window.location.href = '/transfer2?catalog={$m['type']}&to_stock_id={$s->id}'\"><i class=\"fa fa-\"></i></a>&nbsp&nbsp";
                        }
                        $collapse->add($s->name, $line);
                        $row->column(6,$collapse);

                    });
                }
                $content->body('hello world',['style'=>'info']);
            }
            //商务权限权限: 查看全部库房.查询权限
            elseif (Admin::user()->isRole('service'))
            {
                $content->row(function ($row) {
                    $ss = Stock::all();
                    foreach ($ss as $s)
                    {
                        $collapse = new Collapse();
                        $line = "<button class='btn btn-success' type='button' onclick= \"window.location.href='/stock/{$s->id}'\">库存</button>&nbsp;&nbsp;";
                        foreach ($s->stockMethods() as $m) {
                            $line .=  "<button class='btn btn-primary' type='button' onclick= \"window.location.href='/transfer/{$s->id}/{$m['title']}'\">{$m['transfer']} <span class='badge'></span></button>&nbsp&nbsp";
                        }

                        $collapse->add($s->name, $line);
                        $row->column(6,$collapse);

                    }
                });

            }

            $content->body('首页',['style'=>'info']);
            $content->row(function ($row) {
                $row->column(4, new InfoBox('New Users', 'users', 'aqua', '/demo/users', '1024'));
                $row->column(4, new InfoBox('Documents', 'file', 'red', '/demo/files', '698726'));
                $row->column(4, new InfoBox('Articles', 'book', 'yellow', '/demo/articles', '2786'));
            })->body('hello world',['style'=>'info']);




        });
    }
}
