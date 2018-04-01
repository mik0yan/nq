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
            5     => '返修',
            6     => '损耗',
            7     => '改配',
        ];

        return view('admin.tools.transfercatalog', compact('options'));
    }
}
