<?php

namespace App\Http\Controllers;

use App\Transfer;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    //
    public function import(Request $rq)
    {
        if(isSet($rq['start'])&&isSet($rq['end']))
            return Transfer::where('catalog',1)->where('ship_at','>',$rq['start'])->where('ship_at','>',$rq['end']);
        else
            return response()->json(
                [
                    'code'=>200,
                    'message'=>"无数据",
                ]
            );
    }
}
