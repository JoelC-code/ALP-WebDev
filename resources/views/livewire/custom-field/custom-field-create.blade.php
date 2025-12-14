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
                <select wire:model="fieldType" class="form-control form-control-sm">
                    <option value="text">Text</option>
                    <option value="select">Select (Dropdown)</option>
                    <option value="number">Number</option>
                    <option value="checkbox">Checkbox</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-sm" wire:click="createField()">Create</button>
                <button class="btn btn-secondary btn-sm" wire:click="toggleForm()">Cancel</button>
            </div>
        </div>
    @endif
</div>