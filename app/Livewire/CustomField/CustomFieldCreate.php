<?php

namespace App\Livewire\CustomField;

use App\Events\CustomField\CustomFieldBoard;
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
    public $customOptions = [];
    public $newOptionLabel = '';
    public $editingOptionIndex = null;
    public $editingOptionLabel = '';

    public function mount(Board $board)
    {
        $this->board = $board;
        $this->resetCustomOptions();
    }

    public function toggleForm()
    {
        $this->showForm = !$this->showForm;
        if ($this->showForm) {
            $this->resetForm();
        }
    }

    public function resetForm()
    {
        $this->fieldTitle = '';
        $this->fieldType = 'text';
        $this->newOptionLabel = '';
        $this->editingOptionIndex = null;
        $this->editingOptionLabel = '';
        $this->resetCustomOptions();
    }

    public function resetCustomOptions()
    {
        $this->customOptions = CustomField::getDefaultOptions();
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
        // Prevent removing if only one option left
        if (count($this->customOptions) <= 1) {
            return;
        }

        unset($this->customOptions[$index]);
        $this->customOptions = array_values($this->customOptions);
    }

    public function updatedFieldType($value)
    {
        if ($value === 'select') {
            $this->resetCustomOptions();
        }
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

        $data = [
            'title' => $this->fieldTitle,
            'type' => $this->fieldType,
        ];

        // Add options if type is select
        if ($this->fieldType === 'select') {
            $data['options'] = $this->customOptions;
        }

        $customField = $this->board->customFields()->create($data);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $customField->id,
            'details' => 'Created custom field "' . $customField->title . '" of type ' . $customField->type,
        ]);

        $this->resetForm();
        $this->showForm = false;
        
        broadcast(new CustomFieldBoard($this->board->id))->toOthers();
        $this->dispatch('field-updated');
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-create');
    }
}