<?php

namespace App\Livewire\Card;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardCreate extends Component
{

    public $card_title;
    public $position;
    public $board_id;
    public $board;
    public $list;
    public $list_id;

    public function mount($list) {
        $this->list = $list;
        $this->list_id = $list->id;
        $this->board = $list->board()->with('members')->first();;
    }

    public function createCard(){
        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->validate([
            'card_title' => 'required|string|min:1'
        ]);

        $position = $this->position ?? $this->list->cards()->count() + 1;

        $this->list->cards()->create([
            'card_title' => $this->card_title,
            'position' => $position
        ]);

        $this->reset('card_title');
        $this->dispatch('card-created');
        $this->dispatch('hideCreateFormCard');
    }

    public function cancelCreateCard() {
        $this->dispatch('hideCreateFormCard');
    }

    public function render()
    {
        return view('livewire.card.card-create');
    }
}
