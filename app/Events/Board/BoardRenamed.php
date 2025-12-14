<?php

namespace App\Events\Board;

use App\Models\Board;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BoardRenamed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board;

    public function __construct(Board $board) 
    {
        $this->board = $board;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return[ 
            new Channel('boards'),
            new PrivateChannel('board.' . $this->board->id)
        ];
    }

    public function broadcastAs()
    {
        return 'BoardRenamed';
    }
}
