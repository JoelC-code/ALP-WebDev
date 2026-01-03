<?php

namespace App\Livewire\CardTemplate;

use App\Events\Card\CardActions;
use App\Models\Card;
use App\Models\CardTemplate;
use App\Models\ListCard;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class CardTemplateUse extends Component
{
    public $board;
    public $template;
    public $lists = [];
    public $selectedListId = null;
    public $showModal = false;

    public function mount($board, CardTemplate $template)
    {
        $this->board = $board;
        $this->template = $template;
        
        // FIX: Load lists properly with the relationship
        $this->lists = ListCard::where('board_id', $board->id)
                              ->orderBy('position')
                              ->get();
    }

    public function openModal()
    {
        $this->showModal = true;
        
        // Refresh lists when opening modal (in case lists were added/removed)
        $this->lists = ListCard::where('board_id', $this->board->id)
                              ->orderBy('position')
                              ->get();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedListId = null;
    }

    public function createCardFromTemplate()
    {
        $this->validate([
            'selectedListId' => 'required|exists:list_cards,id',
        ]);

        // Check authorization
        if ($this->board->members->pluck('id')->doesntContain(Auth::id())) {
            session()->flash('error', 'Unauthorized access');
            return;
        }

        $list = ListCard::findOrFail($this->selectedListId);

        // Check if list belongs to the board
        if ($list->board_id !== $this->board->id) {
            session()->flash('error', 'List does not belong to this board');
            return;
        }

        // Get the highest position in the list
        $maxPosition = Card::where('list_id', $this->selectedListId)->max('position') ?? 0;

        // Copy image if exists
        $imagePath = null;
        if ($this->template->image && Storage::disk('public')->exists($this->template->image)) {
            $extension = pathinfo($this->template->image, PATHINFO_EXTENSION);
            $newImageName = 'cards/images/' . uniqid() . '_' . time() . '.' . $extension;
            Storage::disk('public')->copy($this->template->image, $newImageName);
            $imagePath = $newImageName;
        }

        // Create the card
        $card = Card::create([
            'card_title' => $this->template->card_title,
            'description' => $this->template->description,
            'dates' => $this->template->dates,
            'image' => $imagePath,
            'list_id' => $this->selectedListId,
            'position' => $maxPosition + 1,
        ]);

        // Copy labels
        if ($this->template->labels && $this->template->labels->count() > 0) {
            $labelIds = $this->template->labels->pluck('id')->toArray();
            $card->labels()->attach($labelIds);
        }

        // Copy custom field values
        if ($this->template->customFields && $this->template->customFields->count() > 0) {
            foreach ($this->template->customFields as $field) {
                $card->customFields()->attach($field->id, [
                    'value' => $field->pivot->value
                ]);
            }
        }

        // Create log
        Log::create([
            'user_id' => Auth::id(),
            'board_id' => $this->board->id,
            'loggable_type' => Card::class,
            'loggable_id' => $card->id,
            'details' => 'Created card "' . $card->card_title . '" from template "' . $this->template->card_title . '"',
        ]);

        // Broadcast the change
        broadcast(new CardActions($this->board->id));

        $this->closeModal();
        
        session()->flash('message', 'Card created from template successfully');
        
        $this->dispatch('template-used');
        $this->dispatch('card-refreshed'); // Add this to refresh the board
    }

    public function render()
    {
        return view('livewire.card-template.card-template-use');
    }
}