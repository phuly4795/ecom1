<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;

    /**
     * Create a new message instance.
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Chào mừng bạn đến với bản tin của chúng tôi!')
            ->view('emails.newsletter_welcome')
            ->with([
                'email' => $this->email,
            ]);
    }
}
