<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsLetter extends Model
{
    use HasFactory;
    protected $guarded = [];
    public function allNewsletter()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function findNewsletter($id)
    {
        return self::find($id);
    }
    public function deleteNewletter($id){
        return self::findOrFail($id)->delete();
    }
}
