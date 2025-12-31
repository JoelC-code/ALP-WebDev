<?php

namespace App\Livewire\CustomField;

use App\Models\Board;
use App\Models\CustomField;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomFieldCreate extends Component
{
    public $board;
    public $fieldTitle = '';
    public $fieldType = 'text';
    public $showForm = false;

    public function mount(Board $board)
    {
        $this->board = $board;
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
    }

    public function createField()
    {
        $this->validate([
            'fieldTitle' => 'required|string|max:255',
            'fieldType' => 'required|in:text,select,number,checkbox',
        ]);

        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;
        if (!$pivot) {
            abort(403, 'Unauthorized');
        }

        $customField = $this->board->customFields()->create([
            'title' => $this->fieldTitle,
            'type' => $this->fieldType,
        ]);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $customField->id,
            'details' => 'Created custom field "' . $customField->title . '" of type ' . $customField->type,
        ]);

        $this->fieldTitle = '';
        $this->fieldType = 'text';
        $this->showForm = false;
        $this->dispatch('field-created');
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-create');
    }
}