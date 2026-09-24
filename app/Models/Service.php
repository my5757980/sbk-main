<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $fillable = [
        'title',
        'menu_title',
        'slug',
        'excerpt',
        'primary_cta_label',
        'secondary_cta_label',
        'image',
        'content',
        'meta_title',
        'meta_description',
        'sort_order',
        'status',
    ];

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? asset(uploadsDir('front') . $this->image) : asset('assets/front/images/about/about-10.jpg');
    }

    public function allService()
    {
        return self::orderBy('sort_order')->orderBy('id', 'DESC')->get();
    }

    public function storeService($data = [])
    {
        return self::create($data);
    }

    public function findService($id)
    {
        return self::find($id);
    }

    public function deleteService($id)
    {
        return self::findOrFail($id)->delete();
    }
}
