<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }
    public function outProducts()
    {
        // return $this->hasMany('App\Transfer','from_stock_id');
        return $this->hasManyThrough(
            'App\Transfer',
            'App\Product_stock',
            'transfer_id',
            'to_stock_id',
            'id',
            'id'
            );
    }
}
