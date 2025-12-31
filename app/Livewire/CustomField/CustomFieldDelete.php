<?php

namespace App\Livewire\CustomField;

use App\Models\CustomField;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomFieldDelete extends Component
{
    public $field;
    public $board;

    protected $listeners = [
        'field-deleted' => 'refreshParent',
    ];

    public function mount(CustomField $field)
    {
        $this->field = $field;
        $this->board = $field->board;
    }

    public function deleteField()
    {
        // Check if user is board member
        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;
        if (!$pivot) {
            abort(403, 'Unauthorized');
        }

        $customFieldName = $this->field->title;
        $customFieldId = $this->field->id;

        $this->field->delete();

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $customFieldId,
            'details' => 'Deleted custom field "' . $customFieldName . '"',
        ]);

        $this->dispatch('field-deleted');
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-delete');
    }
}