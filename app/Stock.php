<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stocks';

    public function user()
    {
        return $this->belongsTo('App\Admin_user');
    }
    public function purchases()
    {
        return $this->hasMany('App\Purchase');
    }
    public function outProducts()
    {
        // return $this->hasMany('App\Transfer','from_stock_id');
        return $this->hasManyThrough(
            'App\Product_stock',
            'App\Transfer',
            'from_stock_id',
            'transfer_id',
            'id',
            'id'
            );
    }
    public function inProducts()
    {
        // return $this->hasMany('App\Transfer','from_stock_id');
        return $this->hasManyThrough(
            'App\Product_stock',
            'App\Transfer',
            'to_stock_id',
            'transfer_id',
            'id',
            'id'
        );
    }

    public function amountProducts()
    {
        $pss1 = $this->inProducts->groupBy('product_id');
        $list1  =  $pss1 -> map(function ($ps,$key){
            return $ps->sum('amount');
        });
        $pss2 = $this->outProducts->groupBy('product_id');
        $list2  =  $pss2 -> map(function ($ps,$key){
            return $ps->sum('amount');
        });

        $list = [];
        foreach ($list1 as $k=>$v) {
            $list[$k] = $v;
        }
        foreach ($list2 as $k=>$v) {
            if(array_key_exists($k,$list)){
                if($list[$k]==$v)
//                    unset($list[$k]);
                    $list[$k] = $list[$k] - $v;

            else
                    $list[$k] = $list[$k] - $v;
            } else {
                $list[$k] = -$v;
            }
        }
        return $list;
    }

    public function storageTable()
    {
        $ll = $this->amountProducts();
        $a = [];
        $b = [];
        foreach ($ll as $k=>$l)
        {
            $p = Product::find($k);
//            $a[] = $p->core;
            $ss = Serials::where('stock_id',$this->id)->where('product_id',$k)->pluck('serial_no');
            $ssr= implode('&emsp;',$ss);
            if(isSet($p)) {
                if ($p->core == 1) {
                    $a[] = [
                        $p->name,
                        $p->item,
                        $p->desc,
                        $p->sku,
                        $l,
                        $ssr,
                    ];
                } else {
                    $b[] = [
                        $p->name,
                        $p->item,
                        $p->desc,
                        $p->sku,
                        $l,
                        ""
                    ];
                }
            }

        }
        return \Response::json($a);
//        return [$a , $b];
    }


    public function stockProduct($id)
    {
        $pss1 = $this->inProducts->where('product_id',$id)->sum('amount');
        $pss2 = $this->outProducts->where('product_id',$id)->sum('amount');

        if(Product::find($id)->core==1){
            $ss = Serials::where('product_id',$id)->where('stock_id',$this->id)->get()->pluck('serial_no','id');
            return [
                'amount'=>$pss1-$pss2,
                'serials'=>$ss->toArray(),
                ];
        } else {
            return [
                'amount'=>$pss1-$pss2,
            ];
        }
    }

//    不同种类的仓库可执行的操作
    public function stockMethods()
    {
        if($this->type == 3)  //常规库
        {
            return [
                [
                    'type'=>1,
                    'transfer' => "采购",
                    'title'=>'purchase'
                ],
                [
                    'type'=>2,
                    'transfer' => "调拨",
                    'title'=>'trans'
                ],
                [
                    'type'=>3,
                    'transfer' => "出货",
                    'title'=>'ship'
                ],
                [
                    'type'=>6,
                    'transfer' => "退货(换机)",
                    'title'=>'rework'
                ],
            ];
        }
        elseif($this->type == 6) //借出、展机库
        {
            return [
                [
                    'type' => 2,
                    'transfer' => "调拨",
                    'title'=>'trans'
                ],
                [
                    'type' => 3,
                    'transfer' => "出货",
                    'title'=>'ship'
                ],
                [
                    'type' => 4,
                    'transfer' => "借出",
                    'title'=>'lend'
                ],
                [
                    'type' => 5,
                    'transfer' => "归还",
                    'title'=>'return'
                ]
            ];
        }
        elseif($this->type == 4)  //维修库
        {
            return [
                [
                    'type' => 2,
                    'transfer' => "调拨",
                    'title'=>'trans'
                ],
                [
                    'type' => 3,
                    'transfer' => "出货",
                    'title'=>'ship'
                ],
                [
                    'type' => 4,
                    'transfer' => "借出",
                    'title'=>'lend'
                ],
                [
                    'type' => 5,
                    'transfer' => "换机",
                    'title'=>'return'
                ]
            ];
        }
        elseif($this->type == 2)  //海关库
        {
            return [
                [
                    'type'=>1,
                    'transfer' => "采购",
                    'title'=>'purchase'
                ],
                [
                    'type' => 2,
                    'transfer' => "调拨",
                    'title'=>'trans'
                ],

            ];
        }
        else
            return [];
    }
}
