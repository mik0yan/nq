<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_package extends Model
{
    protected $fillable = ['name','code','product_id', 'catalog','desc', 'add_price'];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id');
    }
}
