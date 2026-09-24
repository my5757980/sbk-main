<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends = ['image_url'];
    public function getImageUrlAttribute()
    {
        return asset('uploads/front/' . $this->image);
    }
    public function allCategory()
    {
        return self::orderBy('id', 'DESC')->get();
    }
    public function storeCategory($data = [])
    {
        return self::create($data);
    }
    public function findCategory($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteCategory($id)
    {
        return self::findOrFail($id)->delete();
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_categories');
    }
}
