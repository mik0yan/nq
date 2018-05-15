<?php

namespace App;
use App\Product;

use Illuminate\Database\Eloquent\Model;

class Serials extends Model
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

    public function transfers()
    {
        return $this->belongsToMany(Transfer::class,'serial_transfer','serial_id','transfer_id','id','id');
    }

    public function serialsForHuman($ss)
    {
        $last_id = null;
        $last_item = null;
        $array = [];
        $array_line = [];
        foreach ($ss as $item)
        {
            $item_array = explode('.',$item);
            $id = array_pop($item_array);
            if($id  == $last_id + 1)
            {
                array_push($array_line,$item);
            } else {
                array_push($array,$array_line);
                $array_line = [];
                $array_line[] = $item;
            }
            $last_id = $id;
        }
        $result = "";
        foreach ($array as $i)
        {
            if(count($i)==0)
            {
            }
            elseif (count($i)==1)
            {
                $result .= $i[0].", ";
            }
            else{
                $result .= $i[0]."~".end($i).", ";
            }
        }

        return $result;
    }
}
