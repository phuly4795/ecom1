<?php

namespace App\Events;

use App\Models\Contact;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewContactMessage implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $contact;

    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    public function broadcastOn()
    {
        return new Channel('contact-messages'); // tên channel
    }

    public function broadcastWith()
    {
        return [
            'contact' => [
                'name' => $this->contact->name,
                'email' => $this->contact->email,
                'content' => $this->contact->content,
            ]
        ];
    }
}
