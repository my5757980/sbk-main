<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Relationship extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allRelationShip()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function storeRelationShip($data = [])
    {
        return self::create($data);
    }
    public function findRelationShip($id)
    {
        return self::find($id);
    }
    public function whereUpdate($where = [],$data = [])
    {
        return self::where($where)->update($data);
    }
    public function deleteRelationShip($id)
    {
        return self::findOrFail($id)->delete();
    }
}
