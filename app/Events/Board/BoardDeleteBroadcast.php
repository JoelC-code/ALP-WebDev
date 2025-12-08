<?php

namespace App\Events\Board;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardDeleteBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $boardId;

    public function __construct($boardId)
    {
        $this->boardId = $boardId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('boards');
    }

    public function broadcastWith()
    {
        return [
            'board' => $this->boardId
        ];
    }
}
