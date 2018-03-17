<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product_stock extends Model
{
    protected $table ='product_stock';

    protected $fillable = ['product_id', 'transfer_id', 'stock_id', 'amount', 'status'];

    public function product()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function fromStock($id)
    {

    }
}
