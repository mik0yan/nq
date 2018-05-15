<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Admin_user extends Model
{
    //
    protected $table = 'admin_users';

    protected $guarded = ['password','remember_token'];

    public function roles()
    {
        return $this->belongsToMany(Role::class,'admin_role_users','user_id');
    }

    public function hasOrder()
    {
        $users = Order::groupby('user_id')->distinct()->pluck('user_id');
        return Admin_user::whereIn('id',$users)->pluck('name','id');
    }

    public function stocks()
    {
        return $this->hasMany(Stock::class,'user_id','id');
    }

}
