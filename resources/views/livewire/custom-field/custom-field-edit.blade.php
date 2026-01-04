<div>
    <div class="custom-field-item d-flex justify-content-between align-items-center p-2 border mb-2">
        @if($editMode)
            <div class="w-100">
                <input 
                    type="text" 
                    wire:model="fieldTitle" 
                    class="form-control form-control-sm mb-2"
                >
                <select wire:model.live="fieldType" class="form-control form-control-sm mb-2">
                    <option value="text">Text</option>
                    <option value="select">Select (Dropdown)</option>
                    <option value="number">Number</option>
                    <option value="checkbox">Checkbox</option>
                </select>

                @if($fieldType === 'select')
                    <div class="mb-2">
                        <label class="form-label small">Dropdown Options</label>
                        
                        <div class="mb-2">
                            @foreach($customOptions as $index => $option)
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    @if($editingOptionIndex === $index)
                                        <input 
                                            type="text" 
                                            wire:model="editingOptionLabel" 
                                            class="form-control form-control-sm flex-grow-1"
                                            wire:keydown.enter="saveEditOption"
                                            wire:keydown.escape="cancelEditOption"
                                        >
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-success"
                                            wire:click="saveEditOption"
                                        >
                                            ✓
                                        </button>
                                        <button 
                                            type="button" 
                                            class="btn btn-sm btn-secondary"
                                            wire:click="cancelEditOption"
                                        >
                                            ✕
                                        </button>
                                    @else
                                        <span 
                                            class="badge bg-secondary flex-grow-1 text-start cursor-pointer"
                                            wire:click="startEditOption({{ $index }})"
                                            title="Click to edit"
                                        >
                                            {{ $option['label'] }}
                                        </span>
                                        @if(count($customOptions) > 1)
                                            <button 
                                                type="button" 
                                                class="btn btn-sm btn-outline-danger"
                                                wire:click="removeOption({{ $index }})"
                                            >
                                                ×
                                            </button>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <div class="input-group input-group-sm">
                            <input 
                                type="text" 
                                wire:model="newOptionLabel" 
                                class="form-control"
                                placeholder="Add new option..."
                                wire:keydown.enter="addOption"
                            >
                            <button 
                                class="btn btn-outline-primary" 
                                type="button"
                                wire:click="addOption"
                            >
                                Add
                            </button>
                        </div>
                    </div>
                @endif

                <div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm" wire:click="updateField()">Save</button>
                    <button class="btn btn-secondary btn-sm" wire:click="toggleEditMode()">Cancel</button>
                </div>
            </div>
        @else
            <div>
                <strong>{{ $fieldTitle }}</strong>
                <small class="text-muted">({{ $fieldType }})</small>
                @if($fieldType === 'select' && !empty($customOptions))
                    <div class="mt-1">
                        @foreach($customOptions as $option)
                            <span class="badge bg-light text-dark me-1">{{ $option['label'] }}</span>
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-outline-secondary" wire:click="toggleEditMode()">Edit</button>
                <livewire:custom-field.custom-field-delete :field="$field" :key="'delete-'.$field->id" />
            </div>
        @endif
    </div>

    <style>
        .cursor-pointer {
            cursor: pointer;
        }
        .cursor-pointer:hover {
            opacity: 0.8;
        }
    </style>
</div>