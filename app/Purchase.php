<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $table = "transfers";

    public function product_stocks()
    {
        return $this->hasMany(Product_stock::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'product_stock');
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class,'stock_id');
    }
}
