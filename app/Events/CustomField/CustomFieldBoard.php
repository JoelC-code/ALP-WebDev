<?php

namespace App\Events\CustomField;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomFieldBoard implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $boardId;

    public function __construct($boardId)
    {
        $this->boardId = $boardId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('board.'.$this->boardId);
    }

    public function broadcastAs() {
        return "CustomFieldBoard";
    }
}
