<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
/**
 * Created by PhpStorm.
 * User: mikuan
 * Date: 2018/1/10
 * Time: 下午10:12
 */

class Balance extends Model
{
    //
//    protected $table = 'sh_area';
    public function newquity()
    {
        return ($this->reward1 + $this->reward2 + $this->reward3 - $this->refund1 - $this->refund2 - $this->refund3)/100;
    }
}
