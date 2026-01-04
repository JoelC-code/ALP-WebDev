<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CardTemplateUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $boardId;
    public $action;
    public $template;

    public function __construct($boardId, $action, $template = null)
    {
        $this->boardId = $boardId;
        $this->action = $action;
        $this->template = $template;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('board.' . $this->boardId);
    }

    public function broadcastAs()
    {
        return 'CardTemplateUpdated';
    }

    public function broadcastWith()
    {
        return [
            'action' => $this->action,
            'template' => $this->template,
            'timestamp' => now()->toISOString()
        ];
    }
}