<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mood extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends=['light_image_url','dark_image_url'];
    public function getLightImageUrlAttribute()
    {
        return asset('uploads/front/' . $this->light_image);
    }
    public function getDarkImageUrlAttribute()
    {
        return asset('uploads/front/' . $this->dark_image);
    }
    public function allMood()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeMood($data = [])
    {
        return self::create($data);
    }
    public function findMood($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteMood($id)
    {
        return self::findOrFail($id)->delete();
    }
}
