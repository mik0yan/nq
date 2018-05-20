<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    protected $table = "transfers";

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
        return $this->belongsTo('App\Admin_user','user_id','id');
    }

    public function product_stocks()
    {
        return $this->hasMany(Product_stock::class);
    }

    public function lot()
    {
        return $this->hasMany(Lot::class, 'transfer_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class,'order_id',"id");
    }

    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }


    public function product_stock()
    {
        return $this->hasMany(Product_stock::class,'transfer_id','id');
    }

    public function productlist()
    {
        $pss = $this->product_stocks;
        return $pss;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class,'product_stock','transfer_id','product_id','id','id')->withPivot('amount', 'status','remark');
    }

    public function serials($product_id)
    {
        return $this->belongsToMany(Serials::class,'serial_transfer','transfer_id','serial_id','id','id')->where('serials.product_id',$product_id);
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class,'contractno','id');
    }

}
