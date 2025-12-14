<?php

namespace App\Events\List;

use App\Models\ListCard;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ListRenamed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $list;
    public $boardId;

    public function __construct(ListCard $list)
    {
        $list->load('board');
        $this->list = $list->toArray();
        $this->boardId = $list->board_id;
    }

    public function broadcastOn() {
        return new PrivateChannel('board.' . $this->boardId);
    }

    public function broadcastAs() {
        return 'ListRenamed';
    }
}
