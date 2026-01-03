<?php

namespace App\Livewire\CardTemplate;

use App\Models\CardTemplate;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CardTemplateCreate extends Component
{
    use WithFileUploads;

    public $board;
    public $templateId = null;
    public $cardTitle;
    public $description;
    public $dates;
    public $image;
    public $selectedLabel = null; // CHANGE: from array to single value
    public $availableLabels = [];

    // For custom fields
    public $customFields = [];
    public $fieldValues = [];

    public function mount($board, $templateId = null)
    {
        $this->board = $board;
        $this->availableLabels = $board->labels;
        $this->customFields = $board->customFields;

        if ($templateId) {
            $this->loadTemplate($templateId);
        }
    }

    public function loadTemplate($templateId)
    {
        $template = CardTemplate::with(['labels', 'customFields'])->findOrFail($templateId);

        // Check authorization
        if ($template->board_id !== $this->board->id) {
            abort(403, 'Unauthorized access');
        }

        $this->templateId = $template->id;
        $this->cardTitle = $template->card_title;
        $this->description = $template->description;

        // Fix: Check if dates is already a string or Carbon instance
        if ($template->dates) {
            $this->dates = $template->dates instanceof \Carbon\Carbon
                ? $template->dates->format('Y-m-d')
                : $template->dates;
        }

        // CHANGE: Load only the first label (single label)
        $this->selectedLabel = $template->labels->first()?->id;

        // Load custom field values
        foreach ($template->customFields as $field) {
            $this->fieldValues[$field->id] = $field->pivot->value;
        }
    }

    // CHANGE: Update method for single label selection
    public function selectLabel($labelId)
    {
        // If clicking the same label, deselect it
        if ($this->selectedLabel === $labelId) {
            $this->selectedLabel = null;
        } else {
            // Otherwise, select this label (replacing any previous selection)
            $this->selectedLabel = $labelId;
        }
    }

    public function save()
    {
        $this->validate([
            'cardTitle' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'dates' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
            'selectedLabel' => 'nullable|exists:labels,id', // CHANGE: validation for single label
        ]);

        // Check authorization
        if ($this->board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $imagePath = null;

        // Handle image upload
        if ($this->image && is_object($this->image)) {
            $imagePath = $this->image->store('templates/images', 'public');
        } elseif ($this->templateId) {
            // Keep existing image if editing
            $existingTemplate = CardTemplate::find($this->templateId);
            $imagePath = $existingTemplate->image;
        }

        // Create or update template
        $template = CardTemplate::updateOrCreate(
            ['id' => $this->templateId],
            [
                'card_title' => $this->cardTitle,
                'description' => $this->description,
                'dates' => $this->dates,
                'image' => $imagePath,
                'board_id' => $this->board->id,
            ]
        );

        // CHANGE: Sync single label (or clear if none selected)
        if ($this->selectedLabel) {
            $template->labels()->sync([$this->selectedLabel]);
        } else {
            $template->labels()->sync([]);
        }

        // Sync custom fields
        $customFieldData = [];
        foreach ($this->fieldValues as $fieldId => $value) {
            if ($value !== null && $value !== '') {
                $customFieldData[$fieldId] = ['value' => $value];
            }
        }
        $template->customFields()->sync($customFieldData);

        // Create log
        $action = $this->templateId ? 'Updated' : 'Created';
        Log::create([
            'user_id' => Auth::id(),
            'board_id' => $this->board->id,
            'loggable_type' => CardTemplate::class,
            'loggable_id' => $template->id,
            'details' => $action . ' card template: "' . $this->cardTitle . '"',
        ]);

        $this->dispatch('template-saved');

        session()->flash('message', 'Template ' . ($this->templateId ? 'updated' : 'created') . ' successfully');
    }

    public function cancel()
    {
        $this->dispatch('template-saved');
    }

    public function render()
    {
        return view('livewire.card-template.card-template-create');
    }
}
