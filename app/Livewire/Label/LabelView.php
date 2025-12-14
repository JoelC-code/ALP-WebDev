<?php

namespace App\Livewire\Label;

use App\Models\Label;
use Livewire\Component;

class LabelView extends Component
{

    public $board;
    public $labels = [];
    protected $listeners = [
        'label-created' => 'refreshLabel',
        'label-deleted' => 'refreshLabel'
    ];

    public function mount($board) {
        $this->board = $board;
        $this->loadLabels();
    }

    public function loadLabels() {
        $this->labels = Label::where('board_id', $this->board->id)->get();
    }

    public function render()
    {
        return view('livewire.label.label-view');
    }
}
