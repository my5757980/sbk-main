<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeling extends Model
{
    use HasFactory;
    use HasFactory;
    protected $guarded = [];

    public function allFeeling()
    {
        return self::orderBy('id', 'DESC')->get();
    }
    public function storeFeeling($data = [])
    {
        return self::create($data);
    }
    public function findFeeling($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [], $data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteFeeling($id)
    {
        return self::findOrFail($id)->delete();
    }
    // public function resource()
    // {
    //     return $this->hasMany(Resource::class);
    // }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'resource_feelings');
    }
}
