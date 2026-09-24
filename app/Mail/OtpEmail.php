<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OtpEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The request instance.
     *
     * @var Request
     */

    public $email;
    public $name;
    public $otp;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $email, $otp)
    {
        $this->name = $name;
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user['name'] = $this->name;
        $user['email'] = $this->email;
        $user['otp'] = $this->otp;

        return $this->from(env('MAIL_FORM_ADDRESS'), env('MAIL_FROM_NAME'))
            ->subject('Otp Verification')
            ->markdown('emails.front.otpemail', ['data' => $user]);
    }
}
