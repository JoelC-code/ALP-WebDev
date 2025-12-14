<?php

namespace App\Livewire\Card;

use App\Events\Card\CardReordered;
use App\Models\Card;
use App\Models\ListCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardList extends Component
{
    
    public $list;
    public $listId;
    public $showCreateCardForm = false;
    public $cards = [];

    protected $listeners = [
        'card-deleted' => 'refreshCards',
        'card-created' => 'refreshCards',
        'hideCreateFormCard' => 'createCancel',
        'cards-reordered' => 'reorderCards',
        'card-refreshed' => 'refreshCards',
    ];

    public function mount(ListCard $list) {
        $this->listId = $list->id;
        $this->list = ListCard::find($this->listId);
        $this->refreshCards();
    }

    public function refreshCards() {
        $this->list = ListCard::with('cards')->find($this->listId);

        if ($this->list && $this->list->board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->cards = $this->list->cards()->orderBy('position')->get();
    }

    public function reorderCards(int $cardId, int $fromListId, int $toListId, array $orderedIds) {
        foreach($orderedIds as $index => $id) {
            Card::where('id', $id)->update([
                'list_id' => $toListId,
                'position' => $index + 1
            ]);
        }

        broadcast(new CardReordered($toListId, $orderedIds, $this->list->board->id))->toOthers();

        $this->refreshCards();
    }

    public function showForm() {
        $this->showCreateCardForm = true;
    }

    public function createCancel() {
        $this->showCreateCardForm = false;
    }

    public function render()
    {
        return view('livewire.card.card-list');
    }
}
