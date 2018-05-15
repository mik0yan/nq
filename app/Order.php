<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    //
    protected $table = 'orders';

    public function user()
    {
        return $this->belongsTo(Admin_user::class,'user_id','id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'order_product','order_id','product_id','id','id')->withPivot('amount', 'subtotal','package_id','bonus');
    }

    public function approves()
    {
        return $this->belongsToMany(Admin_user::class,'order_approves','order_id','approver_id','id','id')->withPivot('type','status','memo')->withTimestamps();
    }

    public function states()
    {

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
            20     => '待授权',
            21     => '授权',
        ];

        if($this->status ==9)
            $nextStatus = 0;
        else
            $nextStatus = $this->status +1;

        return [
            'on' => ['value' => $nextStatus, 'text' => $options[$nextStatus], 'color' => 'success'],
            'off'  => ['value' => $this->status, 'text' => $options[$this->status], 'color' => 'info'],
        ];
    }
}
