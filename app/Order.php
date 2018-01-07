<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(client::class);
    }

    public function agent()
    {
        return $this->belongsTo(agent::class);
    }
}
