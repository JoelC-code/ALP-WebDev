<?php

namespace App\Livewire\CustomField;

use App\Models\CustomField;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomFieldEdit extends Component
{
    public $field;
    public $board;
    public $fieldTitle;
    public $fieldType;
    public $editMode = false;

    protected $listeners = [
        'field-updated' => 'refresh',
    ];

    public function mount(CustomField $field)
    {
        $this->field = $field;
        $this->board = $field->board;
        $this->fieldTitle = $field->title;
        $this->fieldType = $field->type;
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
    }

    public function updateField()
    {
        // Check if user is board member
        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;
        if (!$pivot) {
            abort(403, 'Unauthorized');
        }

        $this->validate([
            'fieldTitle' => 'required|string|max:255',
            'fieldType' => 'required|in:text,select,number,checkbox',
        ]);

        $customFieldUpdate = $this->field->update([
            'title' => $this->fieldTitle,
            'type' => $this->fieldType,
        ]);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $this->field->id,
            'details' => 'Changed field: "' . $this->fieldTitle . ' with the types of ' . $this->fieldType,
        ]);

        $this->editMode = false;
        $this->dispatch('field-updated');
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-edit');
    }
}