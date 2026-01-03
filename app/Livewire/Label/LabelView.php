<?php

namespace App\Livewire\Label;

use App\Models\Label;
use Livewire\Component;

class LabelView extends Component
{
    public $board;
    public $labels = [];
    public string $labelView = 'list'; // 'list' or 'form'
    public ?int $activeLabelId = null;

    protected $listeners = [
        'label-saved' => 'backToList',
        'label-deleted' => 'backToList',
        'label-setting' => 'loadLabels',
    ];

    public function mount($board) {
        $this->board = $board;
        $this->loadLabels();
    }

    public function loadLabels() {
        $this->labels = $this->board->labels()->get();
    }

    public function backToList() {
        $this->labelView = 'list';
        $this->activeLabelId = null;
        $this->loadLabels();
    }

    public function createLabel() {
        $this->labelView = 'form';
        $this->activeLabelId = null;
    }

    public function editLabel($labelId) {
        $this->labelView = 'form';
        $this->activeLabelId = $labelId;
    }

    public function render()
    {
        return view('livewire.label.label-view');
    }
}