<?php

namespace App\Livewire\Card;

use App\Events\Card\CardReordered;
use App\Models\Card;
use App\Models\ListCard;
use App\Models\Log;
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

    public string $labelView = 'list';
    public ?int $activeLabelId = null;


    protected $listeners = [
        'card-deleted' => 'refreshCards',
        'card-created' => 'refreshCards',
        'hideCreateFormCard' => 'createCancel',
        'cards-reordered' => 'reorderCards',
        'card-refreshed' => 'refreshCards',

        //Labels
        'create-label'  => 'openCreateLabel',
        'edit-label'    => 'openEditLabel',
        'label-saved'   => 'backToLabelList',
        'label-deleted' => 'backToLabelList',
        'cancel-label'  => 'backToLabelList',
    ];

    public function mount(ListCard $list)
    {
        $this->listId = $list->id;
        $this->list = ListCard::find($this->listId);
        $this->refreshCards();
    }

    public function refreshCards()
    {
        $this->list = ListCard::with('cards')->find($this->listId);

        if ($this->list && $this->list->board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->cards = $this->list->cards()->orderBy('position')->get();
    }

    public function reorderCards(int $cardId, int $fromListId, int $toListId, array $orderedIds)
    {
        foreach ($orderedIds as $index => $id) {
            Card::where('id', $id)->update([
                'list_id' => $toListId,
                'position' => $index + 1
            ]);
        }

        broadcast(new CardReordered($toListId, $orderedIds, $this->list->board->id))->toOthers();

        $this->refreshCards();
    }

    public function showForm()
    {
        $this->showCreateCardForm = true;
    }

    public function createCancel()
    {
        $this->showCreateCardForm = false;
    }

    public function openCard($cardId)
    {
        $this->selectedCard = Card::find($cardId);
        $this->showCardModal = true;
        $this->editMode = false;
        $this->cardTitle = $this->selectedCard->card_title;
        $this->cardDescription = $this->selectedCard->description;
    }

    public function closeCard()
    {
        $this->showCardModal = false;
        $this->selectedCard = null;
        $this->editMode = false;
        $this->dispatch('reset-fields');
    }

    public function deleteCard($cardId = null)
    {
        $card = $cardId ? Card::find($cardId) : $this->selectedCard;

        $cardTitle = $card->card_title;
        $cardIdVal = $card->id;
        $boardId = $card->list->board->id;

        $card->delete();

        Log::create([
            'board_id'      => $boardId,
            'user_id'       => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id'   => $cardIdVal,
            'details'       => 'Deleted card: "' . $cardTitle . '"',
        ]);

        if(! $cardId) $this->closeCard();

        $this->refreshCards();
    }

    public function toggleEditTitle()
    {
        if ($this->editingTitle && $this->cardTitle) {
            $oldTitle = $this->selectedCard->card_title;
            $this->selectedCard->update([
                'card_title' => $this->cardTitle,
            ]);

            Log::create([
                'board_id'      => $this->selectedCard->list->board->id,
                'user_id'       => Auth::id(),
                'loggable_type' => Card::class,
                'loggable_id'   => $this->selectedCard->id,
                'details'       => 'Changed card title: "' . $oldTitle . '" → "' . $this->cardTitle . '"',
            ]);
        }
        $this->editingTitle = !$this->editingTitle;
    }

    public function toggleEditDescription()
    {
        if ($this->editingDescription) {
            $oldDesc = $this->selectedCard->description;
            $this->selectedCard->update([
                'description' => $this->cardDescription,
            ]);

            Log::create([
                'board_id'      => $this->selectedCard->list->board->id,
                'user_id'       => Auth::id(),
                'loggable_type' => Card::class,
                'loggable_id'   => $this->selectedCard->id,
                'details'       => 'Description has been updated for ' . $this->selectedCard->cardTitle,
            ]);
        }
        $this->editingDescription = !$this->editingDescription;
    }


    public function openCreateLabel()
    {
        $this->activeLabelId = null;
        $this->labelView = 'form';
    }

    public function openEditLabel($labelId)
    {
        $this->activeLabelId = $labelId;
        $this->labelView = 'form';
    }

    public function backToLabelList()
    {
        $this->activeLabelId = null;
        $this->labelView = 'list';
    }

    public function render()
    {
        return view('livewire.card.card-list');
    }
}
