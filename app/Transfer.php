<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    public function stock()
    {
        return $this->belongsTo('App\Stock','to_stock_id');
    }

    public function stock2()
    {
        return $this->belongsTo('App\Stock','from_stock_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }

    public function product_stocks()
    {
        return $this->hasMany(Product_stock::class);
    }

    public function lot()
    {
        return $this->hasMany(Lot::class, 'transfer_id');
    }


    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }
}
