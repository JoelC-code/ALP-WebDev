<?php

namespace App\Livewire\Card;

use App\Events\Card\CardDeleted;
use App\Models\Board;
use App\Models\Card;
use App\Models\ListCard;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardDelete extends Component
{

    public $listId;
    public $list;
    public $boardId;
    public $board;
    public $cardId;

    public function mount($boardId, $listId) {
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

        $cardName = $card->card_title;

        $card->delete();
        
        Log::create([
            'board_id' => $this->boardId,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $card->id,
            'details' => $cardName . ' has been deleted',
        ]);
        $this->dispatch('card-deleted');

        broadcast(new CardDeleted($card->id, $this->boardId));
    }

    public function render() {
        return view('livewire.card.card-delete');
    }
}
