<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class UserRegistered extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'sharable_type' => 'User', // Set the sharable type if necessary
            'user_id' => $this->user_id, // ID of the user
//            'send_to' => $notifiable->id, // Assuming notifiable is the recipient
//            'type' => 'User Registration', // Notification type
//            'title' => 'Welcome!',
//            'description' => 'Welcome ' . $this->user->first_name . '! You have successfully registered.',
        ];
    }
}
