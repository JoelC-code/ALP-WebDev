<div class="custom-field-item d-flex justify-content-between align-items-center p-2 border mb-2">
    @if($editMode)
        <div class="w-100">
            <input 
                type="text" 
                wire:model="fieldTitle" 
                class="form-control form-control-sm mb-2"
            >
            <select wire:model="fieldType" class="form-control form-control-sm mb-2">
                <option value="text">Text</option>
                <option value="select">Select (Dropdown)</option>
                <option value="number">Number</option>
                <option value="checkbox">Checkbox</option>
            </select>
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-sm" wire:click="updateField()">Save</button>
                <button class="btn btn-secondary btn-sm" wire:click="toggleEditMode()">Cancel</button>
            </div>
        </div>
    @else
        <div>
            <strong>{{ $fieldTitle }}</strong>
            <small class="text-muted">({{ $fieldType }})</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-sm btn-outline-secondary" wire:click="toggleEditMode()">Edit</button>
            <livewire:custom-field.custom-field-delete :field="$field" />
        </div>
    @endif
</div>