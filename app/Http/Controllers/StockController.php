<?php

namespace App\Http\Controllers;
use App\Product;
use App\Stock;
use App\Transfer;
use App\Product_stock;

use Encore\Admin\Widgets\Table;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function products($id)
    {
        // $products = $stock->outProducts();

        $transfers1 = Transfer::where('from_stock_id',$id)->get();
        $transfers2 = Transfer::where('to_stock_id',$id)->get();

        return $this->getProductStock($transfers1,$transfers2,$id);
    }

    private function getProductStock($transfers1,$transfers2,$stock_id)
    {
        $a = [];
        $b = [];
        foreach ($transfers1 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmount($ps));
            }
        }

        foreach ($transfers2 as $transfer) {
            $pss =  Product_stock::where('transfer_id',$transfer->id)->get();
            foreach($pss as $ps)
            {
                // array_push($a,getProductAmount($ps));
                array_push($a,$this->getProductAmountNegative($ps));
            }
        }

        $grouped = Collection::make($a)->groupBy('product_id')->sortBy('product_id');;
        // $grouped = Collection::make($a);
        foreach ($grouped as $key=>$product)
        {
            $sum = 0;
            foreach($product as $ps)
            {
                $sum += $ps['amount'];
                // print $ps.'</br>';
            }
            // array_push($b,array('product_id'=>$key,'sum'=>$sum));
            if($sum>0)
              $b[] = [Product::find($key)->item,Product::find($key)->desc,'<a href="http://39.106.152.130/admin/serials?stock_id='.$stock_id.'&product_id='.$key.'">'.$sum.'</a>'];
            // $rows[] = $line;

        }
        $headers = ['型号','规格','数量'];

        $table = new Table($headers, $b);

        return $table->render();

        // return $b;
    }

    private function getProductAmount($product_stock)
    {
        return [
            'product_id' => $product_stock['product_id'],
            'amount' => $product_stock['amount'],
        ];
    }
    private function getProductAmountNegative($product_stock)
    {
        return [
            'product_id' => $product_stock['product_id'],
            'amount' => -1*$product_stock['amount'],
        ];
    }

}
