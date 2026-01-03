<?php

namespace App\Events\CustomField;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomFieldCard implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $cardId;

    public function __construct($cardId)
    {
        $this->cardId = $cardId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('card.'.$this->cardId);
    }

    public function broadcastAs() {
        return "CustomFieldCard";
    }
}
