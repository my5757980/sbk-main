<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $appends = ['unread'];
    protected $hidden = ['unread'];

    public function unreadChat()
    {
        $chat = Chat::find($this->id);
        if (!$chat)
            return $this->respondBadRequest([], false, __('responseMessages.dataNotFound'));

//        if($this->is_group == 0){
            $fieldToUpdate = '';
            if ($chat->first_user_id == request()->user()->id) {
                $fieldToUpdate = 'is_read_by_first_user';
            } elseif ($chat->second_user_id == request()->user()->id) {
                $fieldToUpdate = 'is_read_by_second_user';
            }
            $chatCount = ChatMessage::where('chat_id', $this->id)
                ->where($fieldToUpdate, 0)
                ->count();

        return $chatCount;
    }

    public function getUnreadAttribute()
    {
//        $getChatData = Chat::where(function ($query) {
//            $query->where('first_user_id', request()->user()->id)
//                ->whereHas('messages', function ($query) {
//                    $query->where(function ($query) {
//                        $query->where('is_read_by_first_user', '0');
//                    });
//                });
//        })->orWhere(function ($query) {
//            $query->where('second_user_id', request()->user()->id)
//                ->whereHas('messages', function ($query) {
//                    $query->where(function ($query) {
//                        $query->where('is_read_by_second_user', '0');
//                    });
//                });
//        })
//            // ->withCount('unreadChat as unread_chats')
//            ->where(function ($query) {
//                $query->where('is_deleted_by', '!=', request()->user()->id)
//                    ->orWhereNull('is_deleted_by');
//            })
//            ->orderBy('last_message_date_time', 'DESC')
//            ->count();
        $chat = Chat::find($this->id);
        if (!$chat)
            return $this->respondBadRequest([], false, __('responseMessages.dataNotFound'));

//        if($this->is_group == 0){
            $fieldToUpdate = '';
            if ($chat->first_user_id == request()->user()->id) {
                $fieldToUpdate = 'is_read_by_first_user';
            } elseif ($chat->second_user_id == request()->user()->id) {
                $fieldToUpdate = 'is_read_by_second_user';
            }
            $chatCount = ChatMessage::where('chat_id', $this->id)
                ->where('receiver_id', request()->user()->id)
                ->where('is_read_by_second_user', 0)
                ->count();
//        }elseif ($this->is_group == 1){
//            $fieldToUpdate = 'is_read_by_first_user';
//            $chatCount = ChatMessage::where('chat_id', $this->id)
//                ->where($fieldToUpdate, 0)
//                ->count();
//        }
        return $chatCount;
    }

    public static function findIfChatExits($user_id, $request_user_id, $request)
    {
        return self::where(function ($query) use ($request) {
            $query->where('first_user_id', $request->user()->id)
                ->where('second_user_id', $request->user_id);
        })
            ->orWhere(function ($query) use ($request) {
                $query->where('first_user_id', $request->user_id)
                    ->where('second_user_id', $request->user()->id);
            });
    }

    // public static function findIfChatExitsAsGroup($user_id, $request_user_id, $request)
    // {
    //     return self::where(function ($query) use ($request) {
    //         $query->where('first_user_id', $request->user()->id)
    //             ->where('second_user_id', null)
    //             ->where('is_group', $request->is_group);
    //     })
    //         ->orWhere(function ($query) use ($request) {
    //             $query->where('first_user_id', $request->user_id)
    //                 ->where('second_user_id', null)
    //                 ->where('is_group', $request->is_group);
    //         });
    // }

    public static function findIfChatExitsStory($user_id, $request_user_id, $request)
    {
        return self::where(function ($query) use ($user_id, $request_user_id) {
            $query->where('first_user_id', $user_id)
                ->where('second_user_id', $request_user_id);
        })
            ->orWhere(function ($query) use ($user_id, $request_user_id) {
                $query->where('first_user_id', $request_user_id)
                    ->where('second_user_id', $user_id);
            });
    }

    public static function creatChat($user_id, $req_user_id)
    {
        return self::create([
            'first_user_id' => $user_id,
            'second_user_id' => $req_user_id
        ]);
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_id', 'id')->orderBy('created_at', 'DESC');
    }
}
