<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Agent extends Model
{
    //
    protected $table = 'agents';


    public function area()
    {
        return $this->belongsTo(area::class,'area_code','areacode');
    }

    public function hasOrder()
    {
        $agents = Order::groupby('agent_id')->distinct()->pluck('agent_id');
        return Agent::whereIn('id',$agents)->pluck('corp','id');
    }
}
