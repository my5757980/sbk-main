<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutOrderItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function order()
    {
        return $this->belongsTo(CheckoutOrder::class, 'checkout_order_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
