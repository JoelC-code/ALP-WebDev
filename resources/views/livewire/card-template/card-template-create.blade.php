<div class="card mb-3">
    <div class="card-header">
        <h6 class="mb-0">{{ $templateId ? 'Edit Template' : 'Create Template' }}</h6>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save">
            <!-- Card Title -->
            <div class="mb-3">
                <label class="form-label">Template Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control @error('cardTitle') is-invalid @enderror" wire:model="cardTitle"
                    placeholder="e.g., Bug Report Template, Feature Request" maxlength="255">
                @error('cardTitle')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Description -->
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea class="form-control @error('description') is-invalid @enderror" wire:model="description" rows="3"
                    placeholder="Template description..." maxlength="1000"></textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Due Date -->
            <div class="mb-3">
                <label class="form-label">Default Due Date</label>
                <input type="date" class="form-control @error('dates') is-invalid @enderror" wire:model="dates">
                @error('dates')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
                <small class="text-muted">This will be the default due date for cards created from this template</small>
            </div>

            <!-- Image Upload -->
            <div class="mb-3">
                <label class="form-label">Template Image</label>

                @if ($templateId && !$image)
                    @php
                        $existingTemplate = \App\Models\CardTemplate::find($templateId);
                    @endphp
                    @if ($existingTemplate && $existingTemplate->image)
                        <div class="mb-2">
                            <img src="{{ Storage::url($existingTemplate->image) }}" alt="Current image"
                                class="img-thumbnail" style="max-width: 200px; max-height: 150px;">
                        </div>
                    @endif
                @endif

                @if ($image)
                    <div class="mb-2">
                        @if (is_object($image))
                            <img src="{{ $image->temporaryUrl() }}" alt="Preview" class="img-thumbnail"
                                style="max-width: 200px; max-height: 150px;">
                        @endif
                    </div>
                @endif

                <input type="file" class="form-control @error('image') is-invalid @enderror" wire:model="image"
                    accept="image/*">
                @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror

                <div wire:loading wire:target="image" class="mt-2">
                    <div class="spinner-border spinner-border-sm" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <span class="ms-2">Uploading...</span>
                </div>

                <small class="text-muted d-block mt-1">Max size: 2MB. Formats: JPG, PNG, GIF</small>
            </div>

            <!-- Labels (Single Selection) -->
            @if ($availableLabels->count() > 0)
                <div class="mb-3">
                    <label class="form-label">Label (Select One)</label>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach ($availableLabels as $label)
                            <button type="button"
                                class="btn {{ $selectedLabel === $label->id ? 'border-dark border-3' : 'border-secondary' }}"
                                style="background-color: {{ $label->color }}; color: white; border: 2px solid;"
                                wire:click="selectLabel({{ $label->id }})">
                                {{ $label->title }}
                                @if ($selectedLabel === $label->id)
                                    <i class="fas fa-check ms-1"></i>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    <small class="text-muted d-block mt-1">Click to select a label (only one can be selected)</small>
                </div>
            @endif

            <!-- Custom Fields -->
            @if ($customFields->count() > 0)
                <div class="mb-3">
                    <label class="form-label">Custom Fields (Default Values)</label>
                    @foreach ($customFields as $field)
                        <div class="mb-2">
                            <label class="form-label small">{{ $field->field_name }}</label>
                            @if ($field->field_type === 'text')
                                <input type="text" class="form-control form-control-sm"
                                    wire:model="fieldValues.{{ $field->id }}"
                                    placeholder="Default value for {{ $field->field_name }}">
                            @elseif($field->field_type === 'number')
                                <input type="number" class="form-control form-control-sm"
                                    wire:model="fieldValues.{{ $field->id }}"
                                    placeholder="Default value for {{ $field->field_name }}">
                            @elseif($field->field_type === 'date')
                                <input type="date" class="form-control form-control-sm"
                                    wire:model="fieldValues.{{ $field->id }}">
                            @elseif($field->field_type === 'dropdown')
                                <select class="form-select form-select-sm"
                                    wire:model="fieldValues.{{ $field->id }}">
                                    <option value="">-- Select --</option>
                                    @if ($field->options)
                                        @foreach (json_decode($field->options) as $option)
                                            <option value="{{ $option }}">{{ $option }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            @endif
                        </div>
                    @endforeach
                    <small class="text-muted">These values will be pre-filled when creating a card from this
                        template</small>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="save">
                        <i class="fas fa-save"></i> {{ $templateId ? 'Update' : 'Create' }}
                    </span>
                    <span wire:loading wire:target="save">
                        <i class="fas fa-spinner fa-spin"></i> Saving...
                    </span>
                </button>
                <button type="button" class="btn btn-secondary" wire:click="cancel">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
