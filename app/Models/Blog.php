<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $appends=['image_url'];
    protected $fillable = [
        'title',
        'slug',
        'status',
        'image',
        'description',
        'content',
        'meta_title',
        'meta_description',
    ];

    public function getImageUrlAttribute()
    {
        if ($this->image && file_exists(uploadsDir('front') . $this->image)) {
            return asset(uploadsDir('front') . $this->image);
        }

        return asset('assets/front/img/blog-american-japanese.webp');
    }

    public function scopeActive($q)
    {
        return $q->where('status', 'published');
    }
    public function allBlog()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeBlog($data = [])
    {
        return self::create($data);
    }
    public function findBlog($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteBlog($id){
        return self::findOrFail($id)->delete();
    }
}
