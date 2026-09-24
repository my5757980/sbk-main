<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'ip_address',
        'user_agent',
        'title'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (auth()->check()) {
                $model->user_id = auth()->user()->id;
            }
            $model->ip_address = request()->ip();
            $model->user_agent = app('request')->header('User-Agent');
        });
    }



    public static function store($title)
    {
        self::create(['title' => $title]);
    }
}
