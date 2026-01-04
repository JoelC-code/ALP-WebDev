<?php

namespace App\Livewire\CustomField;

use App\Events\CustomField\CustomFieldBoard;
use App\Models\CustomField;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CustomFieldDelete extends Component
{
    public $field;
    public $board;

    public function mount(CustomField $field)
    {
        $this->field = $field;
        $this->board = $field->board;
    }

    public function deleteField()
    {
        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;
        if (!$pivot) {
            abort(403, 'Unauthorized');
        }

        $customFieldName = $this->field->title;
        $customFieldId = $this->field->id;
        $boardId = $this->board->id;

        // Delete the field
        $this->field->delete();

        Log::create([
            'board_id' => $boardId,
            'user_id' => Auth::id(),
            'loggable_type' => CustomField::class,
            'loggable_id' => $customFieldId,
            'details' => 'Deleted custom field "' . $customFieldName . '"',
        ]);

        // Dispatch to parent to remove this component from DOM
        $this->dispatch('field-deleted', fieldId: $customFieldId);
        
        // Then broadcast to others
        broadcast(new CustomFieldBoard($boardId))->toOthers();
        
        session()->flash('message', 'Field deleted successfully');
    }

    public function render()
    {
        return view('livewire.custom-field.custom-field-delete');
    }
}