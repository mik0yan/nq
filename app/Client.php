<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    protected $table = "clients";

    public function area()
    {
        return $this->belongsTo(area::class,'area_code','areacode');
    }

    public function hasOrder()
    {
        $clients = Order::groupby('client_id')->distinct()->pluck('client_id');
        return Client::whereIn('id',$clients)->pluck('corp','id');
    }
}
