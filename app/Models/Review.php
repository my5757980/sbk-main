<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function owner()
    {
        return $this->hasOne(User::class,'id','owner_id');
    }

    public function traveller()
    {
        return $this->hasOne(User::class,'id','traveller_id');
    }
}
