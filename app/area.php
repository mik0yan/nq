<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class area extends Model
{
    //
    protected $table = 'sh_area';

    protected $primaryKey = 'areacode';

    public static function getareacode()
    {
      $seed = rand(1,3200);
      $area = area::where('areacode','not like','%00')->inRandomOrder()->first();

      return $area->areacode;
    }

    public function address()
    {
        return implode('-',array_slice(explode(',',$this->merger_name),1));
    }
}
