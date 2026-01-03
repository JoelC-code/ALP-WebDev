<?php

namespace App\Livewire\Label;

use App\Events\Label\LabelSetting;
use App\Models\Label;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class LabelSettings extends Component
{
    public $title;
    public $board;
    public ?int $labelId = null;
    public $color = "#3498db";

    // Predefined color options
    public $colorOptions = [
        '#e74c3c', // Red
        '#e67e22', // Orange
        '#f39c12', // Yellow
        '#2ecc71', // Green
        '#3498db', // Blue
        '#9b59b6', // Purple
        '#1abc9c', // Teal
        '#34495e', // Dark Gray
        '#e91e63', // Pink
        '#00bcd4', // Cyan
    ];

    public function mount($board, $labelId = null) {
        $this->board = $board;
        if($labelId) {
            $this->loadLabel($labelId);
        }
    }

    public function loadLabel($labelId) {
        $label = Label::findOrFail($labelId);
        
        // Check authorization
        if ($label->board_id !== $this->board->id) {
            abort(403, 'Unauthorized access');
        }
        
        $this->labelId = $label->id;
        $this->title = $label->title;
        $this->color = $label->color;
    }

    public function selectColor($color) {
        $this->color = $color;
    }

    public function cancel() {
        $this->dispatch('label-saved'); // This will trigger backToList
    }

    public function saveData()
    {
        $this->validate([
            'title' => 'required|string|max:50',
            'color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        // Check authorization
        if ($this->board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $isNew = !$this->labelId;

        $label = Label::updateOrCreate(
            ['id' => $this->labelId],
            [
                'title' => $this->title,
                'color' => $this->color,
                'board_id' => $this->board->id,
            ]
        );

        // Create log
        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => Label::class,
            'loggable_id' => $label->id,
            'details' => $isNew 
                ? 'Created label: "' . $this->title . '"'
                : 'Updated label: "' . $this->title . '"',
        ]);

        // Broadcast the change
        broadcast(new LabelSetting($this->board->id));

        $this->dispatch('label-saved');
        
        session()->flash('message', $isNew ? 'Label created successfully' : 'Label updated successfully');
    }

    public function render()
    {
        return view('livewire.label.label-create');
    }
}