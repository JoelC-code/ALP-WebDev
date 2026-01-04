<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LabelCardsAction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $cardId;

    public function __construct(int $cardId)
    {
        $this->cardId = $cardId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('card.'.$this->cardId);
    }

    public function broadcastAs() {
        return "LabelCardsAction";
    }
}
