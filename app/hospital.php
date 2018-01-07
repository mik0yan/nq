<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class hospital extends Model
{
    //
    protected $table = 'hospitals';

    public static function getareacode($id)
    {
      $hospital = hospital::where('id',$id)->first();
      return $hospital->area_code;
    }

    public static function getname($id)
    {
      $hospital = hospital::where('id',$id)->first();
      return $hospital->name;
    }

}
