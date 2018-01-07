<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = ['product_id', 'quantity'];


    public function transfer()
    {

        return $this->belongsTo(Transfer::class,'transfer_id');
    }
}
