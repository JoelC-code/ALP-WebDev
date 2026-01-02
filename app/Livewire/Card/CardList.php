<?php

namespace App\Livewire\Card;

use App\Events\Card\CardActions;
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
        'card-inside-refresh' => 'refreshSelectedCard',
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

        if ($fromListId !== $toListId) {
            $fromList = ListCard::findOrFail($fromListId);
            $toList = ListCard::findOrFail($toListId);

            Log::create([
                'board_id' => $this->list->board->id,
                'user_id' => Auth::id(),
                'loggable_type' => Card::class,
                'loggable_id' => $cardId,
                'details' => 'Card moved from "' . $fromList->list_name . '" to "' . $toList->list_name . '"',
            ]);
        }

        broadcast(new CardReordered($toListId, $orderedIds, $this->list->board->id))->toOthers();

        $this->refreshCards();
    }

    public function refreshSelectedCard() {
        if(! $this->selectedCard) return;

        $this->selectedCard = Card::with('list.board', 'comments', 'customFields')->find($this->selectedCard->id);

        $this->cardTitle = $this->selectedCard->card_title;
        $this->cardDescription = $this->selectedCard->description;
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

        logger('The card id opened is '. $cardId);

        $this->dispatch('card-entering', cardId: $cardId);
    }

    public function closeCard()
    {
        if($this->selectedCard) {
            $this->dispatch('card-leaving');
        }

         logger('The card id closed is '. $this->selectedCard->id);
         
        $this->showCardModal = false;
        $this->selectedCard = null;
        $this->editMode = false;
        $this->dispatch('reset-fields');
    }

    public function deleteCard($cardId = null)
    {
        $card = $cardId ? Card::find($cardId) : $this->selectedCard;

        $cardTitle = $card->card_title;
        $cardIdVal = $card->getKey();
        $boardId = $card->list->board->id;

        $card->delete();

        Log::create([
            'board_id'      => $boardId,
            'user_id'       => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id'   => $cardIdVal,
            'details'       => 'Deleted card: "' . $cardTitle . '"',
        ]);

        broadcast(new CardActions($boardId));

        if (! $cardId) $this->closeCard();

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

            broadcast(new CardActions($this->selectedCard->list->board->id));
        }
        $this->editingTitle = !$this->editingTitle;
    }

    public function toggleEditDescription()
    {
        if ($this->editingDescription) {
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

            broadcast(new CardActions($this->selectedCard->list->board->id));
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
