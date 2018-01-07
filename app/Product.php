<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ["name","sku","item","desc"];

    public function product_package()
    {
        return $this->hasMany(Product_package::class, 'product_id');
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }

    public function product_stocks()
    {
        return $this->hasMany(Product_stock::class);
    }
}
