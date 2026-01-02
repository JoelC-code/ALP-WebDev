<?php

namespace App\Events\Card;

use App\Models\Card;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

//!HARUS PAKE `implements ShouldBroadcast` 
//!JIKA INGIN BISA GANTI DATA SECARA DYNAMIC
class CardActions implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $boardId;

    public function __construct($boardId)
    {
        $this->boardId = $boardId;
    }

    //Broadcast ini mirip mirip nama yang akan dipakai di app.js
    //(but after this, go to the Livewire [controller])
    public function broadcastOn()
    {
        return new PrivateChannel('board.' . $this->boardId);
    }

    public function broadcastAs()
    {
        return 'CardActions';
    }
}
