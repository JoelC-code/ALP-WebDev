<div class="card">
    <div class="card-header">
        <h6 class="mb-0">{{ $labelId ? 'Edit Label' : 'Create Label' }}</h6>
    </div>
    <div class="card-body">
        <form wire:submit.prevent="saveData">
            <!-- Title Input -->
            <div class="mb-3">
                <label class="form-label">Label Name</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" wire:model="title"
                    placeholder="e.g., Urgent, In Progress, Done" maxlength="50">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Color Picker -->
            <div class="mb-3">
                <label class="form-label">Label Color</label>

                <!-- Color Preview and Picker -->
                <div class="mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <!-- Preview Badge -->
                        <div
                            style="width: 100px; height: 40px; background-color: {{ $color }}; border-radius: 4px; border: 2px solid #ddd;">
                        </div>

                        <!-- HTML5 Color Picker -->
                        <input type="color" class="form-control form-control-color" wire:model.live="color"
                            style="width: 60px; height: 40px; cursor: pointer;">

                        <!-- Hex Input -->
                        <input type="text" class="form-control form-control-sm @error('color') is-invalid @enderror"
                            wire:model.live="color" placeholder="#000000" maxlength="7" style="width: 100px;">
                    </div>
                    @error('color')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Preset Colors -->
                <label class="form-label small text-muted">Quick Pick:</label>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($colorOptions as $colorOption)
                        <button type="button"
                            class="btn p-0 border {{ $color === $colorOption ? 'border-dark border-3' : 'border-secondary' }}"
                            style="width: 40px; height: 40px; background-color: {{ $colorOption }}; border-radius: 4px;"
                            wire:click="selectColor('{{ $colorOption }}')" title="{{ $colorOption }}">
                        </button>
                    @endforeach
                </div>

                <small class="text-muted d-block mt-2">
                    <i class="fas fa-info-circle"></i> Click the color square to open the color picker, or select from
                    presets below
                </small>
            </div>

            <!-- Preview -->
            @if ($title)
                <div class="mb-3">
                    <label class="form-label">Preview</label>
                    <div>
                        <span class="badge"
                            style="background-color: {{ $color }}; font-size: 14px; padding: 8px 12px;">
                            {{ $title }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Action Buttons -->
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="saveData">
                        <i class="fas fa-save"></i> {{ $labelId ? 'Update' : 'Create' }}
                    </span>
                    <span wire:loading wire:target="saveData">
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
