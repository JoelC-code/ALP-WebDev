<?php

namespace App\Livewire\Label;

use App\Events\Label\LabelSetting;
use App\Models\Label;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LabelDelete extends Component
{
    public $label;
    
    public function mount(Label $label) {
        $this->label = $label;
    }

    public function deleteLabel() {
        if(!$this->label) {
            abort(404, 'Label not found');
        }

        // Check authorization
        if ($this->label->board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $labelTitle = $this->label->title;
        $boardId = $this->label->board_id;

        // Detach from all cards first
        $this->label->cards()->detach();

        // Delete the label
        $this->label->delete();

        // Create log
        Log::create([
            'board_id' => $boardId,
            'user_id' => Auth::id(),
            'loggable_type' => Label::class,
            'loggable_id' => $this->label->id,
            'details' => 'Deleted label: "' . $labelTitle . '"',
        ]);

        // Broadcast the change
        broadcast(new LabelSetting($boardId));

        $this->dispatch('label-deleted');
        
        session()->flash('message', 'Label deleted successfully');
    }

    public function render()
    {
        return view('livewire.label.label-delete');
    }
}