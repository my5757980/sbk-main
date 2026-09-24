<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('uploads/front/' . $this->image);
    }
    public function allResource()
    {
        return self::with('categories', 'activities')->orderBy('id', 'DESC')->get();
    }
    public function storeResource($data = [])
    {
        // Create the main resource record
        return self::create($data);
    }
    public function findResource($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteResource($id)
    {
        return self::findOrFail($id)->delete();
    }
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }

    // // Define the relationship with Activity
    // public function activity()
    // {
    //     return $this->belongsTo(Activity::class);
    // }
    public function mood()
    {
        return $this->belongsTo(Mood::class);
    }
    // public function feeling()
    // {
    //     return $this->belongsTo(Feeling::class);
    // }

    // Relationship with categories
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'resource_categories');
    }

    // Relationship with activities
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'resource_activities');
    }

    // Relationship with feelings
    public function feelings()
    {
        return $this->belongsToMany(Feeling::class, 'resource_feelings');
    }
}
