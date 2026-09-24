<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    protected $guarded = [];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_permission_admin_role');
    }
}
