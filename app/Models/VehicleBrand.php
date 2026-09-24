<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleBrand extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset(uploadsDir('front') . $this->logo) : null;
    }
}
