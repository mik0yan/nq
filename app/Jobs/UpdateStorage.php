<?php

namespace App\Jobs;

use App\Stock;
use App\Serials;
use App\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;



class UpdateStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $stock_id;
    private $p_id;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($stock_id)
    {
        $this->stock_id = $stock_id;
//        $this->p_id = $p_id;
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::del('stock:'.$this->stock_id.':a');
        Redis::del('stock:'.$this->stock_id.':b');
        Redis::del('stock:'.$this->stock_id.':c');
        Redis::del('stock:'.$this->stock_id);

        $ss = Stock::find($this->stock_id)->amountProducts();
        arsort($ss);
        foreach ($ss as $k=>$l)
        {
            $p = Product::find($k);
            if($sv = Serials::where('stock_id',$this->stock_id)->where('product_id',$k)->pluck('serial_no'));
//            $ssr= implode('&emsp;', $ss);
            $serialss = "";

            for($i=0;$i<count($sv);$i++)
            {
                if($i%5==0)
                    $serialss .= "<p>".(string) $sv[$i];
                elseif($i%5==4)
                    $serialss .= (string) $sv[$i]."</p>";
                else
                    $serialss .= "&emsp;".(string) $sv[$i]."&emsp;";
            }
            if(isSet($p)) {
                if ($p->core == 1) {
                    $a = [
                        "<a href='../productline?stock={$this->stock_id}&product_id={$k}'>{$p->name}</a>",
                        $p->item,
                        $p->desc,
                        $p->sku,
                        $l>3||$l==0?
                            "<a href='../serials?stock_id={$this->stock_id}&product_id={$k}'>{$l}</a>"
                                :"<a href='../serials?stock_id={$this->stock_id}&product_id={$k}'>{$l}  <i class=\"fa fa-battery-quarter\"></i></a>",
                        $serialss,
                    ];
                    Redis::Zadd('stock:'.$this->stock_id,$k,json_encode($a));
                    if($l==0)
                        Redis::Zadd('stock:'.$this->stock_id.":c",$k,json_encode($a));
                    else
                        Redis::Zadd('stock:'.$this->stock_id.":a",$k,json_encode($a));

                } else {

                    $b = [
                        "<a href='../productline?stock={$this->stock_id}&product_id={$k}'>{$p->name}</a>",
                        $p->item,
                        $p->desc,
                        $p->sku,
                        $l>3||$l==0?$l:$l."  <i class=\"fa fa-battery-quarter\"></i>",
                        ""
                    ];
                    Redis::Zadd('stock:'.$this->stock_id,$k,json_encode($b));
                    if($l==0)
                        Redis::Zadd('stock:'.$this->stock_id.":c",$k,json_encode($b));
                    else
                        Redis::Zadd('stock:'.$this->stock_id.":b",$k,json_encode($b));

                }
            }

        }
    }
}
