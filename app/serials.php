<?php

namespace App;
use App\Product;
use App\serials;

use Illuminate\Database\Eloquent\Model;

class serials extends Model
{
    protected $table = "serials";

    protected $fillable = ['produtc_id','purchase_id','stock_id','ship_id','serial_no','product_at','storage_at','expire_at'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Transfer::class,'purchase_id','id');
    }

    public function purchase_detail()
    {
        return $this->belongsTo(Transfer::class,'purchase_id','id');
    }

    public function transfer()
    {
        return $this->belongsTo(Transfer::class,'transfer_id','id');
    }
}
