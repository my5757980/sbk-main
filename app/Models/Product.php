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

    public static function normalizeBodyType(?string $type): ?string
    {
        $type = preg_replace('/\s+/u', ' ', trim((string) $type));
        if ($type === '') {
            return null;
        }

        $key = strtolower(preg_replace('/\s*\/\s*/', '/', $type));
        foreach (self::BODY_TYPES as $option) {
            if (strtolower(str_replace(' / ', '/', $option)) === $key) {
                return $option;
            }
        }

        return match ($key) {
            'mpv', 'minivan' => 'MPV / Minivan',
            'wagon', 'estate' => 'Wagon / Estate',
            'convertible', 'cabriolet', 'convertible/roadster' => 'Convertible / Cabriolet',
            'pickup', 'double cab', 'truck/double cab' => 'Pickup Truck',
            'microvan', 'kei van' => 'Microvan / Kei Van',
            'bus', 'mini bus' => 'Minibus',
            default => $type,
        };
    }

    public function setBodyTypeAttribute($value): void
    {
        $this->attributes['body_type'] = self::normalizeBodyType($value);
    }

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
