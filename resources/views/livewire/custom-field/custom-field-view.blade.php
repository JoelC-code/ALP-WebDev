<div>
    <div class="custom-fields-section mb-3">
        <h6 class="mb-2">Custom Fields</h6>

        <!-- Display card's custom fields -->
        <div class="card-fields mb-3">
            @forelse($cardFields as $field)
                <div class="custom-field-item p-2 border-bottom d-flex justify-content-between align-items-center" wire:key="field-{{ $field->id }}">
                    <div class="flex-grow-1">
                        <small class="text-muted">{{ $field->title }}</small>
                        
                        @if($editingFieldId === $field->id)
                            <!-- EDIT MODE -->
                            <div class="d-flex gap-2 mt-2">
                                @if($field->type === 'select')
                                    <select 
                                        wire:change="updateFieldValue({{ $field->id }}, $event.target.value)"
                                        class="form-control form-control-sm"
                                        autofocus
                                    >
                                        <option value="">-- Select --</option>
                                        <option value="Low" @selected($editingValue === 'Low')>Low</option>
                                        <option value="Medium" @selected($editingValue === 'Medium')>Medium</option>
                                        <option value="High" @selected($editingValue === 'High')>High</option>
                                    </select>
                                @elseif($field->type === 'number')
                                    <input 
                                        type="number" 
                                        wire:change="updateFieldValue({{ $field->id }}, $event.target.value)"
                                        class="form-control form-control-sm"
                                        value="{{ $editingValue }}"
                                        autofocus
                                    >
                                @else
                                    <input 
                                        type="text" 
                                        wire:change="updateFieldValue({{ $field->id }}, $event.target.value)"
                                        class="form-control form-control-sm"
                                        value="{{ $editingValue }}"
                                        autofocus
                                    >
                                @endif
                                <button 
                                    class="btn btn-secondary btn-sm" 
                                    wire:click="cancelEdit()"
                                >
                                    Cancel
                                </button>
                            </div>
                        @else
                            <!-- VIEW MODE -->
                            @if($field->type === 'checkbox')
                                <div class="mt-2">
                                    <input 
                                        type="checkbox" 
                                        wire:change="toggleCheckbox({{ $field->id }}, $event.target.checked)"
                                        @checked($field->pivot->value === 'true' || $field->pivot->value === '1')
                                    >
                                </div>
                            @else
                                <div 
                                    wire:click="startEdit({{ $field->id }})"
                                    style="cursor: pointer; padding: 5px; border-radius: 4px;"
                                    class="hover-highlight"
                                >
                                    @if($field->type === 'select')
                                        <span class="badge bg-secondary">{{ $field->pivot->value ?: 'Not set' }}</span>
                                    @elseif($field->type === 'number')
                                        <strong>{{ $field->pivot->value ?: '0' }}</strong>
                                    @else
                                        <strong>{{ $field->pivot->value ?: '—' }}</strong>
                                    @endif
                                </div>
                            @endif
                        @endif
                    </div>
                    
                    <button 
                        class="btn btn-sm btn-outline-danger"
                        wire:click="removeField({{ $field->id }})"
                    >
                        ✕
                    </button>
                </div>
            @empty
                <p class="text-muted small">No fields added yet</p>
            @endforelse
        </div>

        @if($boardFields->count() > $cardFields->count())
            <div class="available-fields">
                <small class="text-muted">Add fields:</small>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    @foreach($boardFields as $field)
                        @if(!$cardFields->contains('id', $field->id))
                            <button 
                                class="btn btn-sm btn-outline-primary"
                                wire:click="addField({{ $field->id }})"
                            >
                                + {{ $field->title }}
                            </button>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <style>
        .hover-highlight:hover {
            background-color: #f0f0f0;
        }
    </style>
</div>