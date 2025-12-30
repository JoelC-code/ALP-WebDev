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

class BoardInvited implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Board $board;

    public function __construct(Board $board) 
    {
        $this->board = $board;
    }

    public function broadcastOn()
    {
        return [
            new PrivateChannel('board.' . $this->board->id),
            new Channel('boards'),
        ];
    }

    public function broadcastAs() {
        return 'BoardInvited';
    }
}
