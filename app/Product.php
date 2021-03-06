<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'products';


    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function packages()
    {
        return $this->hasMany(Product_package::class,'product_id','id');
    }

    public function transfer()
    {
        return $this->belongsToMany(Transfer::class,'product_stock','transfer_id','product_id','id','id')->withPivot('amount', 'status','remark');
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class,'order_product','order_id','product_id','id','id')->withPivot('amount', 'sub_total','package_price','package_code');
    }

    public static function getprice($id)
    {
      $product = Product::where('id',$id)->first();
      return $product->price;
    }



    public static function getbonus($id)
    {
      $product = Product::where('id',$id)->first();
      return $product->bonus;
    }

}
