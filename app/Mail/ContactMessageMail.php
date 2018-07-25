<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    private $message;
    private $reply;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($message,$reply)
    {
        $this->message = $message;
        $this->reply = $reply;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('الشامل')->markdown('emails.contact_message',['message' => $this->message , 'reply' => $this->reply]);
    }
}
