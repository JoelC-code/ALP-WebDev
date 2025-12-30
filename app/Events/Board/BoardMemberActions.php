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

class BoardMemberActions implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $board;
    public $member;

    public function __construct(Board $board, $member)
    {
        $this->board = $board;
        $this->member = $member;
    }

    public function broadcastOn()
    {
        return[ 
            new PrivateChannel('board.' . $this->board->id),
            new Channel('boards'),
        ];
    }

    public function broadcastAs() {
        return 'BoardMemberActions';
    }
}
