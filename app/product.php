<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    //
    protected $table = 'products';

    public static function getprice($id)
    {
      $product = product::where('id',$id)->first();
      return $product->price4;
    }

    public static function getbonus($id)
    {
      $product = product::where('id',$id)->first();
      return $product->bonus;
    }

}
