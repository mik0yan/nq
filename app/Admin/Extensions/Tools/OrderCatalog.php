<?php
namespace App\Admin\Extensions\Tools;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class OrderCatalog extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['status' => '_status_']);

        return <<<EOT

$('input:radio.order-status').change(function () {

    var url = "$url".replace('_status_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;

    }
    public function render()
    {
        Admin::script($this->script());

        $options = [
            'all'   => 'All',
            1     => '暂存',
            2     => '新建',
            3     => '会签',
            4     => '盖章',
            5     => '收款',
            6     => '备货',
            7     => '发货',
            8     => '收货',
            9     => '结算',
            0     => '取消',
//            10     => '待授权',
//            11     => '授权',
        ];
//        S10:待授权,S11:授权,S12:授权拒绝,S1,暂存,S2:新建,S3:会签,S4:签章,S5:收款,S6:备货,S7:发货,S8:收货,S9:完成,S0:取消
        return view('admin.tools.ordercatalog', compact('options'));
    }
}
