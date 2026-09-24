<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    protected $guarded = [];

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_permission_admin_role');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class);
    }
}
