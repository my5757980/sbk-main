<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SituationQuestion extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allSituation()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeSituation($data = [])
    {
        return self::create($data);
    }
    public function findSituation($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteSituation($id)
    {
        return self::findOrFail($id)->delete();
    }
}

