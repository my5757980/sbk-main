<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public const BODY_TYPES = [
        'Sedan',
        'Hatchback',
        'SUV',
        'Crossover',
        'MPV / Minivan',
        'Wagon / Estate',
        'Coupe',
        'Convertible / Cabriolet',
        'Pickup Truck',
        'Truck',
        'Van',
        'Microvan / Kei Van',
        'Minibus',
        'Roadster',
        'Liftback',
        'Fastback',
    ];

    public const HOME_SECTION_OPTIONS = [
        'best_seller' => 'Best Seller',
        'upcoming_collection' => 'The Exclusive New Stock',
        'new_in_demand_arrivals' => 'Luxury Vehicles',
        'commercial_vehicles' => 'Commercial Vehicles',
    ];

    protected $guarded = [];

    protected $casts = [
        'images' => 'array',
        'vehicle_specifications' => 'array',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
        'discounted_price' => 'decimal:2',
    ];

    public function brand()
    {
        return $this->belongsTo(VehicleBrand::class, 'vehicle_brand_id');
    }

    public function vehicleModel()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function getPrimaryImageAttribute()
    {
        $images = $this->images ?: [];

        return $images[0] ?? null;
    }

    public function getPrimaryImageUrlAttribute()
    {
        if ($this->primary_image && file_exists(uploadsDir('front') . $this->primary_image)) {
            return asset(uploadsDir('front') . $this->primary_image);
        }

        return asset('assets/front/img/car-markx-white.jpg');
    }

    public function getMileageLabelAttribute()
    {
        return $this->mileage !== null && $this->mileage !== '' ? number_format($this->mileage) . ' km' : 'N/A';
    }
}
