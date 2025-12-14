<?php

namespace App\Events\Card;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardReordered implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $listId;
    public array $orderedIds;
    public int $boardId;

    public function __construct($listId, $orderedIds, $boardId)
    {
        $this->listId = $listId;
        $this->orderedIds = $orderedIds;
        $this->boardId = $boardId;  
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new PrivateChannel("board.{$this->boardId}");
    }

    public function broadcastAs() {
        return 'CardReordered';
    }
}
