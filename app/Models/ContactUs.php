<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactUs extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function allContact()
    {
        return self::orderBy('id','DESC')->get();
    }
    public function findContact($id)
    {
        return self::find($id);
    }
    public function deleteContact($id)
    {
        return self::findOrFail($id)->delete();
    }
}
