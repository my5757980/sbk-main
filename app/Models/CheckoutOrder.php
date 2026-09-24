<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function items()
    {
        return $this->hasMany(CheckoutOrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
