<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reward extends Model
{
    //
    public function user()
    {
        $this->belongsTo(Admin_user::Class,'user_id','id');
    }
}
