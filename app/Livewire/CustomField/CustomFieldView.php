<?php

namespace App\Livewire\CustomField;

use App\Events\CustomField\CustomFieldCard;
use App\Models\Card;
use Livewire\Component;

class CustomFieldView extends Component
{
    public $cardId;
    public $editingFieldId = null;
    public $editingValue = '';

    protected $listeners = [
        'refresh-fields' => 'refresh',
        'reset-fields' => 'resetEdit',
    ];

    public function mount(Card $card)
    {
        $this->cardId = $card->id;
    }

    public function addField($fieldId)
    {
        $card = Card::find($this->cardId);
        $card->customFields()->attach($fieldId, ['value' => '']);
        broadcast(new CustomFieldCard($card->id));
    }

    public function removeField($fieldId)
    {
        $card = Card::find($this->cardId);
        $card->customFields()->detach($fieldId);
        broadcast(new CustomFieldCard($card->id));
    }

    public function startEdit($fieldId)
    {
        $this->editingFieldId = $fieldId;
        $card = Card::find($this->cardId);
        $field = $card->customFields()->find($fieldId);
        $this->editingValue = $field?->pivot?->value ?? '';
    }

    public function updateFieldValue($fieldId, $value)
    {
        $card = Card::find($this->cardId);
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
        broadcast(new CustomFieldCard($this->cardId));
    }

    public function resetEdit()
    {
        $this->editingFieldId = null;
        $this->editingValue = '';
    }

    public function cancelEdit() {
        $this->resetEdit();
    }


    public function refresh()
    {
        // Force refresh
    }

    public function render()
    {
        $card = Card::with(['customFields' => function ($q) {
            $q->withPivot('value');
        }, 'list.board.customFields'])->find($this->cardId);

        return view('livewire.custom-field.custom-field-view', [
            'card' => $card,
            'cardFields' => $card->customFields,
            'boardFields' => $card->list->board->customFields,
        ]);
    }
}
