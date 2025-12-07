<?php

namespace App\Livewire\Card;

use Livewire\Component;

class CardCreate extends Component
{

    public $card_title;
    public $position;
    public $board_id;
    public $board;
    public $list;
    public $list_id;

    public function mount($board, $list) {
        $this->board = $board;
        $this->board_id = $board->id;
    }

    public function render()
    {
        return view('livewire.card.card-create');
    }
}
