<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleTrackingEvent extends Model
{
    protected $guarded = ['id'];

    protected $casts = ['occurred_on' => 'date'];

    public const STAGES = [
        'not_started' => 'Not started',
        'reserved' => 'Vehicle Reserved',
        'invoice_generated' => 'Invoice Generated',
        'first_payment' => 'First Payment Received',
        'picked_up' => 'Car Booked',
        'inspection_yard' => 'Inspection/Yard',
        'in_yard' => 'In Yard',
        'final_payment' => 'Final Payment',
        'shipped' => 'Shipped',
        'delivered' => 'Delivered to port',
    ];
}
