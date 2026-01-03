<?php

namespace App\Livewire\Card;

use App\Events\Card\CardActions;
use App\Events\Card\CardReordered;
use App\Models\Card;
use App\Models\ListCard;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CardList extends Component
{
    use WithFileUploads;

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

    public $cardImage;
    public $newImage;
    public $editingImage = false;
    public $cardDueDate;
    public $editingDueDate = false;
    public string $labelView = 'list';
    public ?int $activeLabelId = null;

    protected $listeners = [
        'card-inside-refresh' => 'refreshSelectedCard',
        'hideCreateFormCard' => 'createCancel',
        'cards-reordered' => 'reorderCards',
        'card-refreshed' => 'refreshCards',
        'card-action-refresh' => 'refreshCards',
        'open-card-modal' => 'openCardFromId', // Add this

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

    public function refreshSelectedCard()
    {
        if (! $this->selectedCard) return;

        $this->selectedCard = Card::with('list.board', 'comments', 'customFields')->find($this->selectedCard->id);

        $this->cardTitle = $this->selectedCard->card_title;
        $this->cardDescription = $this->selectedCard->description;
        $this->cardImage = $this->selectedCard->image;
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
        $this->cardImage = $this->selectedCard->image;
        $this->cardDueDate = $this->selectedCard->dates ? $this->selectedCard->dates->format('Y-m-d') : null;

        logger('The card id opened is ' . $cardId);

        $this->dispatch('card-entering', cardId: $cardId);
    }

    public function closeCard()
    {
        if ($this->selectedCard) {
            $this->dispatch('card-leaving');
        }

        logger('The card id closed is ' . $this->selectedCard->id);

        $this->showCardModal = false;
        $this->selectedCard = null;
        $this->editMode = false;
        $this->editingImage = false;
        $this->newImage = null;
        $this->editingDueDate = false;
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
                'details'       => 'Description has been updated for ' . $this->selectedCard->card_title,
            ]);

            broadcast(new CardActions($this->selectedCard->list->board->id));
        }
        $this->editingDescription = !$this->editingDescription;
    }

    public function toggleEditImage()
    {
        $this->editingImage = !$this->editingImage;

        if (!$this->editingImage) {
            $this->newImage = null;
        }
    }

    public function uploadImage()
    {
        $this->validate([
            'newImage' => 'required|image|max:2048',
        ]);

        $board = $this->selectedCard->list->board;
        $pivot = $board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (!$pivot) {
            session()->flash('error', 'Unauthorized access');
            return;
        }

        if ($this->selectedCard->image && Storage::disk('public')->exists($this->selectedCard->image)) {
            Storage::disk('public')->delete($this->selectedCard->image);
        }

        $imagePath = $this->newImage->store('cards/images', 'public');

        $this->selectedCard->update(['image' => $imagePath]);
        $this->cardImage = $imagePath;

        Log::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $this->selectedCard->id,
            'details' => 'Image uploaded to card "' . $this->selectedCard->card_title . '"',
        ]);

        broadcast(new CardActions($board->id));

        $this->newImage = null;
        $this->editingImage = false;
        $this->selectedCard = $this->selectedCard->fresh();

        session()->flash('message', 'Image uploaded successfully');
    }

    public function removeImage()
    {
        $board = $this->selectedCard->list->board;
        $pivot = $board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (!$pivot) {
            session()->flash('error', 'Unauthorized access');
            return;
        }

        if ($this->selectedCard->image && Storage::disk('public')->exists($this->selectedCard->image)) {
            Storage::disk('public')->delete($this->selectedCard->image);
        }

        $this->selectedCard->update(['image' => null]);
        $this->cardImage = null;

        Log::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $this->selectedCard->id,
            'details' => 'Image removed from card "' . $this->selectedCard->card_title . '"',
        ]);

        broadcast(new CardActions($board->id));

        session()->flash('message', 'Image removed successfully');
    }

    public function toggleEditDueDate()
    {
        $this->editingDueDate = !$this->editingDueDate;

        if (!$this->editingDueDate) {
            // Reset to original value if canceling
            $this->cardDueDate = $this->selectedCard->dates ? $this->selectedCard->dates->format('Y-m-d') : null;
        }
    }

    public function saveDueDate()
    {
        $this->validate([
            'cardDueDate' => 'required|date',
        ]);

        $board = $this->selectedCard->list->board;
        $pivot = $board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (!$pivot) {
            session()->flash('error', 'Unauthorized access');
            return;
        }

        $this->selectedCard->update([
            'dates' => $this->cardDueDate,
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $this->selectedCard->id,
            'details' => "Due date set to: {$this->cardDueDate}",
        ]);

        broadcast(new CardActions($board->id));

        $this->editingDueDate = false;
        $this->selectedCard = $this->selectedCard->fresh();

        session()->flash('message', 'Due date updated successfully');
    }

    public function removeDueDate()
    {
        $board = $this->selectedCard->list->board;
        $pivot = $board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (!$pivot) {
            session()->flash('error', 'Unauthorized access');
            return;
        }

        $this->selectedCard->update([
            'dates' => null,
        ]);

        Log::create([
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $this->selectedCard->id,
            'details' => 'Due date removed from card "' . $this->selectedCard->card_title . '"',
        ]);

        broadcast(new CardActions($board->id));

        $this->cardDueDate = null;
        $this->editingDueDate = false;

        session()->flash('message', 'Due date removed successfully');
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

    public function toggleComplete($cardId)
    {
        $card = Card::findOrFail($cardId);

        $board = $card->list->board;
        if ($board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $card->update([
            'is_completed' => !$card->is_completed
        ]);

        $status = $card->is_completed ? 'completed' : 'marked as incomplete';
        Log::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $card->id,
            'details' => 'Card "' . $card->card_title . '" ' . $status,
        ]);

        broadcast(new CardActions($board->id));

        $this->refreshCards();
    }

    public function render()
    {
        return view('livewire.card.card-list');
    }
}
