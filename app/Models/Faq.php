<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function allFaq()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeFaq($data = [])
    {
        return self::create($data);
    }
    public function findFaq($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteFaq($id)
    {
        return self::findOrFail($id)->delete();
    }
}
