<?php

namespace App\Livewire\Card;

use App\Models\Board;
use App\Models\ListCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardDelete extends Component
{

    public $listId;
    public $list;
    public $boardId;
    public $board;
    public $cardId;

    public function mount($boardId, $listId, $cardId) {
        $this->boardId = $boardId;
        $this->board = Board::findOrFail($boardId);
        $this->listId = $listId;
        $this->list = ListCard::findOrFail($listId);
    }

    public function deleteCard() {
        $pivot = $this->board->members()->where('user_id', Auth::user()->id)->first()?->pivot;

        if(! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $card = $this->list->cards()->where('id', $this->cardId)->findOrFail();

        $card->delete();
        $this->dispatch('card-delete');
    }

    public function render() {
        return view('livewire.card.card-delete');
    }
}
