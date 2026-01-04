<?php

namespace App\Livewire\CustomField;

use App\Events\CustomField\CustomFieldCard;
use App\Models\Card;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class CustomFieldView extends Component
{
    public $cardId;
    public $editingFieldId = null;
    public $editingValue = '';

    protected $listeners = [
        'refresh-fields' => '$refresh',
        'reset-fields' => 'resetEdit',
        'field-updated' => '$refresh',  // ← ADD THIS to listen for deletions
    ];

    public function mount(Card $card)
    {
        $this->cardId = $card->id;
    }

    public function addField($fieldId)
    {
        $card = Card::findOrFail($this->cardId);
        
        // Check if field still exists before attaching
        if (!\App\Models\CustomField::find($fieldId)) {
            $this->dispatch('$refresh');
            return;
        }
        
        $card->customFields()->attach($fieldId, ['value' => '']);
        broadcast(new CustomFieldCard($card->id));
        $this->dispatch('$refresh');
    }

    public function removeField($fieldId)
    {
        $card = Card::findOrFail($this->cardId);
        $card->customFields()->detach($fieldId);
        broadcast(new CustomFieldCard($card->id));
        $this->dispatch('$refresh');
    }

    public function startEdit($fieldId)
    {
        $this->editingFieldId = $fieldId;
        $card = Card::findOrFail($this->cardId);
        $field = $card->customFields()->find($fieldId);
        $this->editingValue = $field?->pivot?->value ?? '';
    }

    public function updateFieldValue($fieldId, $value)
    {
        $card = Card::findOrFail($this->cardId);
        $card->customFields()->syncWithoutDetaching([
            $fieldId => ['value' => $value]
        ]);
        $this->editingFieldId = null;
        $this->editingValue = '';
        broadcast(new CustomFieldCard($card->id));
    }

    public function toggleCheckbox($fieldId, $checked)
    {
        $value = $checked ? 'true' : 'false';
        $this->updateFieldValue($fieldId, $value);
    }

    public function resetEdit()
    {
        $this->editingFieldId = null;
        $this->editingValue = '';
    }

    public function cancelEdit()
    {
        $this->resetEdit();
    }

    public function render()
    {
        try {
            $card = Card::with([
                'customFields' => function ($q) {
                    $q->withPivot('value');
                },
                'list.board.customFields'
            ])->findOrFail($this->cardId);

            // Filter out any null or deleted fields
            $boardFields = $card->list->board->customFields->filter(function($field) {
                return $field && $field->exists;
            });

            return view('livewire.custom-field.custom-field-view', [
                'card' => $card,
                'cardFields' => $card->customFields ?? collect(),
                'boardFields' => $boardFields,
            ]);
        } catch (\Exception $e) {
            Log::error('CustomFieldView render error: ' . $e->getMessage());

            return view('livewire.custom-field.custom-field-view', [
                'card' => Card::findOrFail($this->cardId),
                'cardFields' => collect(),
                'boardFields' => collect(),
            ]);
        }
    }
}