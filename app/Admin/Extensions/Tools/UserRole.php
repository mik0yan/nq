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

class UserRole extends AbstractTool
{
    protected function script()
    {
        $url = Request::fullUrlWithQuery(['role' => '_role_']);

        return <<<EOT

$('input:radio.user-role').change(function () {

    var url = "$url".replace('_role_', $(this).val());

    $.pjax({container:'#pjax-container', url: url });

});

EOT;

    }
    public function render()
    {
        Admin::script($this->script());

        $options = [
            'all'   => 'All',
            1     => '管理员',
            2     => '库管',
            3     => '采购',
            4     => '商务',
            5     => '财务',
            6     => '核心团队',
            7     => '销售',
            8     => '出纳',
            9     => '人力',
        ];

        return view('admin.tools.userrole', compact('options'));
    }
}
