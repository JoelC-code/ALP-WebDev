<div class="label-card-component">
    <label class="form-label"><strong><i class="fas fa-tag"></i> Label</strong></label>
    
    <div class="position-relative">
        @if($currentLabel)
            <!-- Show current label -->
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="badge" style="background-color: {{ $currentLabel->color }}; font-size: 14px; padding: 8px 12px;">
                    {{ $currentLabel->title }}
                </span>
                <button type="button" class="btn btn-sm btn-outline-danger" wire:click="removeLabel">
                    <i class="fas fa-times"></i> Remove
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="toggleDropdown">
                    <i class="fas fa-edit"></i> Change
                </button>
            </div>
        @else
            <!-- No label attached -->
            <button type="button" class="btn btn-sm btn-outline-primary" wire:click="toggleDropdown">
                <i class="fas fa-plus"></i> Add Label
            </button>
        @endif

        <!-- Dropdown for selecting label -->
        @if($showLabelDropdown)
            <div class="card shadow-sm mt-2" style="position: absolute; z-index: 1000; min-width: 250px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Select a Label</span>
                    <button type="button" class="btn-close" wire:click="toggleDropdown"></button>
                </div>
                <div class="card-body p-2" style="max-height: 300px; overflow-y: auto;">
                    @forelse($availableLabels as $label)
                        <button 
                            type="button" 
                            class="btn w-100 mb-2 text-start d-flex align-items-center justify-content-between {{ $selectedLabelId == $label->id ? 'border border-primary' : '' }}"
                            style="background-color: {{ $label->color }}; color: white;"
                            wire:click="attachLabel({{ $label->id }})">
                            <span>{{ $label->title }}</span>
                            @if($selectedLabelId == $label->id)
                                <i class="fas fa-check"></i>
                            @endif
                        </button>
                    @empty
                        <p class="text-muted text-center mb-0">No labels available. Create one first!</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>