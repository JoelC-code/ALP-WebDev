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

class ListCreateBroadcast implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $list; 
    public $boardId;

    public function __construct(ListCard $list) 
    {
        $this->list = $list;
        $this->boardId = $list->board_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn()
    {
        return new Channel('board.' . $this->boardId);
    }

    public function broadcastWith() {
        return [
            'list' => $this->list->toArray()
        ];
    }
}
