<?php

namespace App\Events\List;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $boardId;
    public array $orderedIds;
    public function __construct($boardId, $orderedIds)
    {
        $this->boardId = $boardId;
        $this->orderedIds = $orderedIds;
    }

    public function broadcastOn()
    {
        return new PrivateChannel("board.$this->boardId");
    }

    public function broadcastAs(){
        return "ListReordered";
    }
}
