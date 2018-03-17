<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class agent extends Model
{
    //
    protected $table = 'agents';


    public function area()
    {
        return $this->belongsTo(area::class,'area_code','areacode');
    }
}
