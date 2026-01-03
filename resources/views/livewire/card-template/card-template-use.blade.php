<div>
    <!-- Use Template Button -->
    <button type="button" class="btn btn-sm btn-success" wire:click="openModal">
        <i class="fas fa-plus-circle"></i> Use
    </button>

    <!-- Modal -->
    @if($showModal)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5);" wire:click="closeModal">
            <div class="modal-dialog" wire:click.stop>
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Create Card from Template</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="text-muted mb-3">
                            <strong>Template:</strong> {{ $template->card_title }}
                        </p>

                        <form wire:submit.prevent="createCardFromTemplate">
                            <div class="mb-3">
                                <label class="form-label">Select List <span class="text-danger">*</span></label>
                                <select 
                                    class="form-select @error('selectedListId') is-invalid @enderror" 
                                    wire:model="selectedListId">
                                    <option value="">-- Choose a list --</option>
                                    @foreach($lists as $list)
                                        <option value="{{ $list->id }}">{{ $list->list_name }}</option>
                                    @endforeach
                                </select>
                                @error('selectedListId')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">The card will be added to the selected list</small>
                            </div>

                            <!-- Template Preview -->
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title mb-2">Card Preview:</h6>
                                    <ul class="list-unstyled mb-0">
                                        <li><strong>Title:</strong> {{ $template->card_title }}</li>
                                        @if($template->description)
                                            <li><strong>Description:</strong> {{ Str::limit($template->description, 50) }}</li>
                                        @endif
                                        @if($template->dates)
                                            <li><strong>Due Date:</strong> {{ \Carbon\Carbon::parse($template->dates)->format('M d, Y') }}</li>
                                        @endif
                                        @if($template->labels->count() > 0)
                                            <li>
                                                <strong>Labels:</strong>
                                                @foreach($template->labels as $label)
                                                    <span class="badge me-1" style="background-color: {{ $label->color }};">
                                                        {{ $label->title }}
                                                    </span>
                                                @endforeach
                                            </li>
                                        @endif
                                        @if($template->customFields->count() > 0)
                                            <li><strong>Custom Fields:</strong> {{ $template->customFields->count() }} field(s) with values</li>
                                        @endif
                                        @if($template->image)
                                            <li><strong>Image:</strong> ✓ Included</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal">
                            Cancel
                        </button>
                        <button 
                            type="button" 
                            class="btn btn-primary" 
                            wire:click="createCardFromTemplate"
                            wire:loading.attr="disabled"
                            wire:target="createCardFromTemplate">
                            <span wire:loading.remove wire:target="createCardFromTemplate">
                                <i class="fas fa-plus"></i> Create Card
                            </span>
                            <span wire:loading wire:target="createCardFromTemplate">
                                <i class="fas fa-spinner fa-spin"></i> Creating...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>