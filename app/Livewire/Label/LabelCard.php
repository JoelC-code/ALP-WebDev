<?php

namespace App\Livewire\Label;

use App\Events\Card\CardActions;
use App\Models\Card;
use App\Models\Label;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LabelCard extends Component
{
    public $card;
    public $boardId;
    public $availableLabels = [];
    public $selectedLabelId = null;
    public $showLabelDropdown = false;

    protected $listeners = [
        'label-saved' => 'refreshLabels',
        'label-deleted' => 'refreshLabels',
    ];

    public function mount(Card $card)
    {
        $this->card = $card;
        $this->boardId = $card->list->board->id;
        $this->refreshLabels();
        
        // Get current label if exists
        $currentLabel = $card->labels()->first();
        $this->selectedLabelId = $currentLabel?->id;
    }

    public function refreshLabels()
    {
        $this->availableLabels = Label::where('board_id', $this->boardId)->get();
        
        // Refresh current label
        $currentLabel = $this->card->labels()->first();
        $this->selectedLabelId = $currentLabel?->id;
    }

    public function toggleDropdown()
    {
        $this->showLabelDropdown = !$this->showLabelDropdown;
    }

    public function attachLabel($labelId)
    {
        $label = Label::findOrFail($labelId);
        
        // Check authorization
        $board = $this->card->list->board;
        if ($board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        // Remove any existing labels (one label per card)
        $this->card->labels()->detach();

        // Attach new label
        $this->card->labels()->attach($labelId);
        $this->selectedLabelId = $labelId;

        // Create log
        Log::create([
            'board_id' => $board->id,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $this->card->id,
            'details' => 'Label "' . $label->title . '" attached to card "' . $this->card->card_title . '"',
        ]);

        // Broadcast the change
        broadcast(new CardActions($board->id));

        $this->showLabelDropdown = false;
        $this->dispatch('card-inside-refresh');
        
        session()->flash('message', 'Label attached successfully');
    }

    public function removeLabel()
    {
        $board = $this->card->list->board;
        
        // Check authorization
        if ($board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $currentLabel = $this->card->labels()->first();
        
        if ($currentLabel) {
            $this->card->labels()->detach();
            $this->selectedLabelId = null;

            // Create log
            Log::create([
                'board_id' => $board->id,
                'user_id' => Auth::id(),
                'loggable_type' => Card::class,
                'loggable_id' => $this->card->id,
                'details' => 'Label "' . $currentLabel->title . '" removed from card "' . $this->card->card_title . '"',
            ]);

            // Broadcast the change
            broadcast(new CardActions($board->id));

            $this->dispatch('card-inside-refresh');
            
            session()->flash('message', 'Label removed successfully');
        }

        $this->showLabelDropdown = false;
    }

    public function render()
    {
        $currentLabel = $this->selectedLabelId ? Label::find($this->selectedLabelId) : null;
        
        return view('livewire.label.label-card', [
            'currentLabel' => $currentLabel
        ]);
    }
}