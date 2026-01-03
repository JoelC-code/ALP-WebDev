<div>
    @if($labelView === 'list')
        <!-- Label List View -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0"><i class="fas fa-tags"></i> Board Labels</h6>
                <button type="button" class="btn btn-sm btn-primary" wire:click="createLabel">
                    <i class="fas fa-plus"></i> New Label
                </button>
            </div>

            @if($labels->count() > 0)
                <div class="list-group">
                    @foreach($labels as $label)
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-2 flex-grow-1">
                                <span class="badge" style="background-color: {{ $label->color }}; min-width: 100px; padding: 8px;">
                                    {{ $label->title }}
                                </span>
                                <small class="text-muted">{{ $label->cards->count() }} card(s)</small>
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button type="button" class="btn btn-outline-secondary" wire:click="editLabel({{ $label->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <livewire:label.label-delete :label="$label" :key="'label-delete-' . $label->id" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No labels yet. Create your first label!
                </div>
            @endif
        </div>
    @elseif($labelView === 'form')
        <!-- Label Form View -->
        <livewire:label.label-settings 
            :board="$board" 
            :labelId="$activeLabelId" 
            :key="'label-form-' . ($activeLabelId ?? 'new')" />
    @endif
</div>