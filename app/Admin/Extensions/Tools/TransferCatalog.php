<?php
/**
 * Created by PhpStorm.
 * User: mikuan
 * Date: 2018/2/20
 * Time: 上午7:53
 */

namespace App\Admin\Extensions\Tools;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class TransferCatalog extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['catalog' => '_catalog_']);

        return <<<EOT

$('input:radio.transfer-catalog').change(function () {

    var url = "$url".replace('_catalog_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;

    }
    public function render()
    {
        Admin::script($this->script());

        $options = [
            'all'   => 'All',
            1     => '采购',
            2     => '调拨',
            3     => '出库',
            4     => '借出',
            5     => '归还',
            6     => '返修',
            7     => '修理',
            8     => '合格',
            9     => '损耗',
        ];
//        T1:采购,T2:调拨,T3:出库,T4:借出,T5:归还,T6.返修,T7:修理,T8:合格,T9:损耗
        return view('admin.tools.transfercatalog', compact('options'));
    }
}
