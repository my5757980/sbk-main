<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HabitQuestion extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allHabit()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeHabit($data = [])
    {
        return self::create($data);
    }
    public function findHabit($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteHabit($id)
    {
        return self::findOrFail($id)->delete();
    }
}
