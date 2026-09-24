<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allActivity()
    {
        return self::orderBy('id', 'DESC')->get();
    }
    public function storeActivity($data = [])
    {
        return self::create($data);
    }
    public function findActivity($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteActivity($id)
    {
        return self::findOrFail($id)->delete();
    }
    // public function resource()
    // {
    //     return $this->hasMany(Resource::class);
    // }
    public function category()
    {
        return $this->hasMany(Category::class);
    }

    // Relationship with resources
    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_activities');
    }
}
