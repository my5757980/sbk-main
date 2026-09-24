<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertySafety extends Model
{
    use HasFactory;

    protected $guarded;

    protected $appends = ['icon_url'];

    public function getIconUrlAttribute()
    {
        return asset("uploads/" . $this->icon);
    }
}
