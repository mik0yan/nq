<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class order extends Model
{
    //
    protected $table = 'orders';

    public function user()
    {
        return $this->belongsTo(Admin_user::class,'user_id','id');
    }

    public function products()
    {
        return $this->belongsToMany(product::class,'order_product','order_id','product_id','id','id')->withPivot('amount', 'subtotal','package_id');
    }

}
