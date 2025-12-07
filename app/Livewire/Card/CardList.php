<?php

namespace App\Livewire\Card;

use App\Models\Board;
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
        'card-delete' => 'refreshCards',
        'card-created' => 'refreshCards',
        'hideCreateFormCard' => 'createCancel'
    ];

    public function showForm() {
        $this->showCreateCardForm = true;
    }

    public function createCancel() {
        $this->showCreateCardForm = false;
    }

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

    public function render()
    {
        logger('render cards at ' . now());
        return view('livewire.card.card-list');
    }
}
