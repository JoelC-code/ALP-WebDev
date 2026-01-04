<div>
    <div>
        <h6 class="mb-3">Custom Fields</h6>

        @foreach($boardFields as $field)
            @php
                $cardField = $cardFields->firstWhere('id', $field->id);
                $hasField = $cardField !== null;
                $value = $hasField ? $cardField->pivot->value : '';
            @endphp

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label small mb-0">
                        <strong>{{ $field->title }}</strong>
                        <span class="text-muted">({{ $field->type }})</span>
                    </label>
                    
                    @if(!$hasField)
                        <button 
                            class="btn btn-sm btn-outline-primary"
                            wire:click="addField({{ $field->id }})"
                        >
                            + Add
                        </button>
                    @else
                        <button 
                            class="btn btn-sm btn-outline-danger"
                            wire:click="removeField({{ $field->id }})"
                        >
                            Remove
                        </button>
                    @endif
                </div>

                @if($hasField)
                    @if($field->type === 'text')
                        @if($editingFieldId === $field->id)
                            <div class="input-group input-group-sm">
                                <input 
                                    type="text" 
                                    class="form-control"
                                    wire:model="editingValue"
                                    wire:keydown.enter="updateFieldValue({{ $field->id }}, $wire.editingValue)"
                                >
                                <button 
                                    class="btn btn-primary"
                                    wire:click="updateFieldValue({{ $field->id }}, $wire.editingValue)"
                                >
                                    Save
                                </button>
                                <button 
                                    class="btn btn-secondary"
                                    wire:click="cancelEdit"
                                >
                                    Cancel
                                </button>
                            </div>
                        @else
                            <div 
                                class="form-control form-control-sm cursor-pointer"
                                wire:click="startEdit({{ $field->id }})"
                            >
                                {{ $value ?: 'Click to edit...' }}
                            </div>
                        @endif

                    @elseif($field->type === 'number')
                        @if($editingFieldId === $field->id)
                            <div class="input-group input-group-sm">
                                <input 
                                    type="number" 
                                    class="form-control"
                                    wire:model="editingValue"
                                    wire:keydown.enter="updateFieldValue({{ $field->id }}, $wire.editingValue)"
                                >
                                <button 
                                    class="btn btn-primary"
                                    wire:click="updateFieldValue({{ $field->id }}, $wire.editingValue)"
                                >
                                    Save
                                </button>
                                <button 
                                    class="btn btn-secondary"
                                    wire:click="cancelEdit"
                                >
                                    Cancel
                                </button>
                            </div>
                        @else
                            <div 
                                class="form-control form-control-sm cursor-pointer"
                                wire:click="startEdit({{ $field->id }})"
                            >
                                {{ $value ?: 'Click to edit...' }}
                            </div>
                        @endif

                    @elseif($field->type === 'select')
                        <select 
                            class="form-control form-control-sm"
                            wire:change="updateFieldValue({{ $field->id }}, $event.target.value)"
                        >
                            <option value="">-- Select --</option>
                            @foreach($field->getSelectOptions() as $option)
                                <option 
                                    value="{{ $option['value'] }}" 
                                    {{ $value === $option['value'] ? 'selected' : '' }}
                                >
                                    {{ $option['label'] }}
                                </option>
                            @endforeach
                        </select>

                    @elseif($field->type === 'checkbox')
                        <div class="form-check">
                            <input 
                                class="form-check-input" 
                                type="checkbox"
                                {{ $value === 'true' ? 'checked' : '' }}
                                wire:change="toggleCheckbox({{ $field->id }}, $event.target.checked)"
                            >
                            <label class="form-check-label">
                                {{ $value === 'true' ? 'Checked' : 'Unchecked' }}
                            </label>
                        </div>
                    @endif
                @endif
            </div>
        @endforeach

        @if($boardFields->isEmpty())
            <p class="text-muted small">No custom fields available for this board.</p>
        @endif
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer:hover {
            background-color: #f8f9fa;
        }
    </style>
</div>