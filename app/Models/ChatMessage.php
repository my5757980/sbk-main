<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sender() {
        return $this->belongsTo(User::class,'sender_id');
    }

    // protected $appends = ['attachment_url'];

    // public function getAttachmentUrlAttribute()
    // {
    //     // return asset('uploads/profile/' . $this->profile_picture);
    //     // if ($this->attachment == null || $this->attachment == "") {
    //     //     return url();
    //     // }
    //     return asset('storage/'.$this->attachment);
    // }
}
