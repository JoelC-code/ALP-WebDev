<?php

namespace App\Livewire\Label;

use App\Models\Label;
use Livewire\Component;

class LabelCreate extends Component
{
    public $title;
    public $board;
    public $color = "#008cffff";

    public function mount($board) {
        $this->board = $board;
        $this->color = "#008cffff";
    }

    public function createLabel()
    {
        $this->validate([
            'title' => 'required|string|max:50',
            'color' => 'required|string|max:7',
        ]);

        $label = Label::create([
            'title' => $this->title,
            'color' => $this->color,
            'board_id' => $this->board->id
        ]);

        $this->reset(['title', 'color']);
        $this->color = "#008cffff";

        $this->dispatch('label-created');
    }

    public function render()
    {
        return view('livewire.label.label-create');
    }
}
