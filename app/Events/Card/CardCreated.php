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
class CardCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $card;
    public $boardId;
    //Kita buat file broadcast
    public function __construct(Card $card)
    {
        //Kenapa array? lihat di view, numpuk kayak array
        $card->load('list');        
        $this->card = $card->toArray();
        $this->boardId = $card->list->board_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */

    //Broadcast ini mirip mirip nama yang akan dipakai di app.js
    //(but after this, go to the Livewire [controller])
    public function broadcastOn()
    {
        return new PrivateChannel('board.' . $this->boardId);
    }

    public function broadcastAs()
    {
        return 'CardCreated';
    }
}
