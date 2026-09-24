<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $guarded = [];
    /* @NOTIFICATION_TYPES */
    static public $CHAT = "CHAT";
    static public $MESSAGE = "MESSAGE";
    static public $ORDER = "ORDER";
    /* @NOTIFICATION_TYPES */

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function sender()
    {
        return $this->hasOne(User::class,'id','sender_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class,'id','order_id');
    }
    public function notifications()
    {
        return $this->hasMany(Notification::class, 'user_id'); // 'user_id' is the foreign key in the notifications table
    }


}
