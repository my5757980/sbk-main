<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $guarded;

    public function traveller()
    {
        return $this->belongsTo(User::class,'traveller_id');
    }

    public function property_owner()
    {
        return $this->belongsTo(User::class,'property_owner_id');
    }

    public function property()
    {
        return $this->belongsTo(PropertyListing::class,'property_id');
    }
}
