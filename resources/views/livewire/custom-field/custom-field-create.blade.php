<div>
    <div class="custom-field-form mb-3">
        @if(!$showForm)
            <button class="btn btn-sm btn-outline-primary" wire:click="toggleForm()">
                + Add Custom Field
            </button>
        @else
            <div class="card p-3 mb-3">
                <h6>Create Custom Field</h6>
                
                <div class="mb-2">
                    <label class="form-label small">Field Name</label>
                    <input 
                        type="text" 
                        wire:model="fieldTitle" 
                        class="form-control form-control-sm"
                        placeholder="e.g., Priority, Status, Effort"
                    >
                </div>

                <div class="mb-3">
                    <label class="form-label small">Field Type</label>
                    <select wire:model.live="fieldType" class="form-control form-control-sm">
                        <option value="text">Text</option>
                        <option value="select">Select (Dropdown)</option>
                        <option value="number">Number</option>
                        <option value="checkbox">Checkbox</option>
                    </select>
                </div>

                @if($fieldType === 'select')
                    <div class="mb-3">
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
                    <button class="btn btn-primary btn-sm" wire:click="createField()">Create</button>
                    <button class="btn btn-secondary btn-sm" wire:click="toggleForm()">Cancel</button>
                </div>
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