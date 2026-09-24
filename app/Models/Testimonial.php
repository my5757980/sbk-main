<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends=['image_url'];

    public function getImageUrlAttribute()
    {
        return asset('uploads/testimonials/' . $this->image);
    }
    public function allTestimonial()
    {
        return self::orderBy('created_at','DESC')->get();
    }
    public function storeTestimonial($data = [])
    {
        return self::create($data);
    }
    public function findTestimonial($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteTestimonial($id){
        return self::findOrFail($id)->delete();
    }
}
