<?php

namespace App\Livewire\CustomField;

use App\Models\Board;
use Livewire\Component;

class CustomFieldList extends Component
{
    public $board;
    public $fields = [];

    protected $listeners = [
        'field-created' => 'loadFields',
        'field-updated' => 'loadFields',
        'field-deleted' => 'loadFields',
    ];

    public function mount(Board $board)
    {
        $this->board = $board;
        $this->loadFields();
    }

    public function loadFields()
    {
        $this->fields = $this->board->customFields;
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-list');
    }
}