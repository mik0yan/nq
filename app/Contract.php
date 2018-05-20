<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    //
    protected $fillable = ['name','comment','client_id','agent_id','user_id'];

    public function Transfer()
    {
        return $this->hasMany(Transfer::class,'contractno','id');
    }


    public function Client()
    {
        return $this->belongsTo(Client::class);
    }

    public function Agent()
    {
        return $this->belongsTo(Agent::class);
    }

    public function User()
    {
        return$this->belongsTo(Admin_user::class,'user_id','id');
    }
}
