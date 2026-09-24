<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allQuestion()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeQuestion($data = [])
    {
        return self::create($data);
    }
    public function findQuestion($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteQuestion($id)
    {
        return self::findOrFail($id)->delete();
    }
}
