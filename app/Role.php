<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    //
    protected $table = "admin_roles";

    public function users()
    {
        return $this->belongsToMany(Admin_user::class,'admin_role_users','user_id','role_id');
    }
}
