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
    public $selectedCard = null;
    public $showCardModal = false;
    public $editMode = false;
    public $cardTitle;
    public $cardDescription;
    public $editingTitle = false;
    public $editingDescription = false;

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

    public function openCard($cardId) {
        $this->selectedCard = Card::find($cardId);
        $this->showCardModal = true;
        $this->editMode = false;
        $this->cardTitle = $this->selectedCard->card_title;
        $this->cardDescription = $this->selectedCard->description;
    }

    public function closeCard() {
        $this->showCardModal = false;
        $this->selectedCard = null;
        $this->editMode = false;
        $this->dispatch('reset-fields');
    }

    public function deleteCard($cardId = null) {
    if ($cardId) {
        Card::find($cardId)->delete();
    } else {
        $this->selectedCard->delete();
        $this->closeCard();
    }
    $this->refreshCards();
    }

    public function toggleEditTitle()
    {
        if ($this->editingTitle && $this->cardTitle) {
            $this->selectedCard->update([
                'card_title' => $this->cardTitle,
            ]);
        }
        $this->editingTitle = !$this->editingTitle;
    }

    public function toggleEditDescription()
    {
        if ($this->editingDescription) {
            $this->selectedCard->update([
                'description' => $this->cardDescription,
            ]);
        }
        $this->editingDescription = !$this->editingDescription;
    }

    public function render()
    {
        return view('livewire.card.card-list');
    }
}