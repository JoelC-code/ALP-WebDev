<?php

namespace App\Livewire\CustomField;

use App\Events\CustomField\CustomFieldBoard;
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
    public $customOptions = [];
    public $newOptionLabel = '';
    public $editingOptionIndex = null;
    public $editingOptionLabel = '';

    // Store original state for cancel
    public $originalFieldTitle;
    public $originalFieldType;
    public $originalOptions = [];

    protected $listeners = [
        'field-updated' => 'refresh',
    ];

    public function mount(CustomField $field)
    {
        $this->field = $field;
        $this->board = $field->board;
        $this->fieldTitle = $field->title;
        $this->fieldType = $field->type;
        $this->loadOptions();
        $this->saveOriginalState();
    }

    public function hydrate()
    {
        if (!$this->field || !$this->field->exists) {
            return;
        }

        $this->fieldTitle = $this->field->title;
        $this->fieldType = $this->field->type;
    }

    public function saveOriginalState()
    {
        $this->originalFieldTitle = $this->fieldTitle;
        $this->originalFieldType = $this->fieldType;
        $this->originalOptions = $this->customOptions;
    }

    public function loadOptions()
    {
        if ($this->fieldType === 'select') {
            $this->customOptions = $this->field->options ?? CustomField::getDefaultOptions();
        } else {
            $this->customOptions = [];
        }
    }

    public function toggleEditMode()
    {
        if ($this->editMode) {
            // Canceling - restore original state
            $this->fieldTitle = $this->originalFieldTitle;
            $this->fieldType = $this->originalFieldType;
            $this->customOptions = $this->originalOptions;
            $this->newOptionLabel = '';
            $this->editingOptionIndex = null;
            $this->editingOptionLabel = '';
        } else {
            // Starting edit - save current state
            $this->saveOriginalState();
            $this->loadOptions();
        }
        
        $this->editMode = !$this->editMode;
    }

    public function updatedFieldType($value)
    {
        if ($value === 'select') {
            $this->customOptions = CustomField::getDefaultOptions();
        } else {
            $this->customOptions = [];
        }
    }

    public function addOption()
    {
        if (empty(trim($this->newOptionLabel))) {
            return;
        }

        $this->customOptions[] = [
            'value' => strtolower(str_replace(' ', '_', trim($this->newOptionLabel))),
            'label' => trim($this->newOptionLabel),
        ];

        $this->newOptionLabel = '';
    }

    public function startEditOption($index)
    {
        $this->editingOptionIndex = $index;
        $this->editingOptionLabel = $this->customOptions[$index]['label'];
    }

    public function saveEditOption()
    {
        if ($this->editingOptionIndex !== null && !empty(trim($this->editingOptionLabel))) {
            $this->customOptions[$this->editingOptionIndex] = [
                'value' => strtolower(str_replace(' ', '_', trim($this->editingOptionLabel))),
                'label' => trim($this->editingOptionLabel),
            ];
        }
        $this->cancelEditOption();
    }

    public function cancelEditOption()
    {
        $this->editingOptionIndex = null;
        $this->editingOptionLabel = '';
    }

    public function removeOption($index)
    {
        if (count($this->customOptions) <= 1) {
            return;
        }

        unset($this->customOptions[$index]);
        $this->customOptions = array_values($this->customOptions);
    }

    public function updateField()
    {
        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;
        if (!$pivot) {
            abort(403, 'Unauthorized');
        }

        $this->validate([
            'fieldTitle' => 'required|string|max:255',
            'fieldType' => 'required|in:text,select,number,checkbox',
        ]);

        $data = [
            'title' => $this->fieldTitle,
            'type' => $this->fieldType,
        ];

        // Add options if type is select
        if ($this->fieldType === 'select') {
            $data['options'] = $this->customOptions;
        } else {
            $data['options'] = null;
        }

        $this->field->update($data);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $this->field->id,
            'details' => 'Updated field: "' . $this->fieldTitle . '" with type ' . $this->fieldType,
        ]);

        $this->editMode = false;
        $this->saveOriginalState();
        
        broadcast(new CustomFieldBoard($this->board->id))->toOthers();
        $this->dispatch('field-updated');
    }

    public function refresh()
    {
        $this->field->refresh();
        $this->fieldTitle = $this->field->title;
        $this->fieldType = $this->field->type;
        $this->loadOptions();
        $this->saveOriginalState();
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-edit');
    }
}