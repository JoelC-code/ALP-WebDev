<div>
    <div class="card-sortable" data-list-id="{{ $list->id }}">
        @foreach ($cards as $ca)
            <div class="card mb-2 shadow-sm card-items {{ $ca->is_completed ? 'card-completed' : '' }}"
                data-card-id="{{ $ca->id }}" wire:key="card-{{ $ca->id }}" style="cursor: pointer;">

                {{-- Image Cover --}}
                @if ($ca->image)
                    <img src="{{ \Storage::url($ca->image) }}" alt="{{ $ca->card_title }}" class="card-img-top"
                        style="height: 150px; object-fit: cover; cursor: pointer; {{ $ca->is_completed ? 'opacity: 0.6;' : '' }}"
                        wire:click="openCard({{ $ca->id }})">
                @endif

                <div class="card-body p-2 d-flex justify-content-between align-items-center">
                    {{-- Checkbox --}}
                    <div class="form-check" onclick="event.stopPropagation()">
                        <input class="form-check-input" type="checkbox" id="card-complete-{{ $ca->id }}"
                            @if ($ca->is_completed) checked @endif
                            wire:click="toggleComplete({{ $ca->id }})" style="cursor: pointer;">
                    </div>

                    {{-- Card Title --}}
                    <span wire:click="openCard({{ $ca->id }})" class="flex-grow-1 ms-2" style="cursor: pointer;">
                        {{ $ca->card_title }}
                    </span>

                    {{-- Label Badge --}}
                    @if ($ca->labels->first())
                        <span class="badge me-2" style="background-color: {{ $ca->labels->first()->color }};">
                            {{ $ca->labels->first()->title }}
                        </span>
                    @endif

                    {{-- Due Date Badge (if exists) --}}
                    @if ($ca->dates)
                        <span
                            class="badge {{ $ca->isOverdue() ? 'bg-danger' : ($ca->isDueSoon() ? 'bg-warning text-dark' : 'bg-secondary') }} ms-2">
                            <i class="fas fa-clock"></i>
                            {{ \Carbon\Carbon::parse($ca->dates)->format('M d') }}
                        </span>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div wire:ignore.self>
        @if (!$showCreateCardForm)
            <button class="btn add-card btn-sm btn-outline-primary w-100 mt-2 no-sort" wire:click="showForm">Add
                Card</button>
        @else
            <livewire:card.card-create :list="$list" :key="'card-create-' . $list->id" />
        @endif
    </div>

    @if ($showCardModal && $selectedCard)
        <div class="modal d-block"
            style="background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999;">
            <div class="modal-dialog"
                style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100000; width: 500px;">
                <div class="modal-content">
                    <div class="modal-header">
                        {{-- Checkbox in Modal Header --}}
                        <div class="form-check me-2">
                            <input class="form-check-input" type="checkbox"
                                {{ $selectedCard->is_completed ? 'checked' : '' }}
                                wire:click="toggleComplete({{ $selectedCard->id }})" style="cursor: pointer;">
                        </div>

                        @if ($editingTitle)
                            <div class="d-flex gap-2 w-100">
                                <input type="text" wire:model="cardTitle" class="form-control"
                                    placeholder="Card title" wire:change="toggleEditTitle()" autofocus>
                                <button class="btn btn-secondary btn-sm" wire:click="toggleEditTitle()">Cancel</button>
                            </div>
                        @else
                            <h5 class="modal-title" wire:click="toggleEditTitle()"
                                style="cursor: pointer; padding: 5px; border-radius: 4px;">
                                {{ $cardTitle }}
                            </h5>
                        @endif
                        <button type="button" class="btn-close" wire:click="closeCard()"></button>
                    </div>

                    <div class="modal-body" style="max-height: 70vh; overflow-y:auto">
                        <!-- Completion Status Badge -->
                        @if ($selectedCard->is_completed)
                            <div class="alert alert-success mb-3">
                                <i class="fas fa-check-circle"></i> This card is marked as complete
                            </div>
                        @endif

                        <!-- Flash Messages -->
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Image Section -->
                        <div class="mb-3">
                            <label class="form-label"><strong><i class="fas fa-image"></i> Image</strong></label>

                            @if ($editingImage)
                                <!-- Upload Form -->
                                <div class="upload-section">
                                    @if ($newImage)
                                        <div class="mb-2">
                                            <img src="{{ $newImage->temporaryUrl() }}" alt="Preview"
                                                class="img-thumbnail" style="max-width: 100%; max-height: 300px;">
                                        </div>
                                    @endif

                                    <div class="input-group mb-2">
                                        <input type="file"
                                            class="form-control @error('newImage') is-invalid @enderror"
                                            wire:model="newImage" accept="image/*">
                                        @error('newImage')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div wire:loading wire:target="newImage" class="mb-2">
                                        <div class="progress">
                                            <div class="progress-bar progress-bar-striped progress-bar-animated"
                                                style="width: 100%">
                                                Loading image...
                                            </div>
                                        </div>
                                    </div>

                                    <small class="text-muted d-block mb-2">Max size: 2MB. Formats: JPG, PNG, GIF</small>

                                    <div class="d-flex gap-2">
                                        <button type="button" class="btn btn-primary btn-sm" wire:click="uploadImage"
                                            wire:loading.attr="disabled" wire:target="newImage, uploadImage"
                                            @if (!$newImage) disabled @endif>
                                            <span wire:loading.remove wire:target="uploadImage">
                                                <i class="fas fa-upload"></i> Upload
                                            </span>
                                            <span wire:loading wire:target="uploadImage">
                                                <i class="fas fa-spinner fa-spin"></i> Uploading...
                                            </span>
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            wire:click="toggleEditImage">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            @else
                                <!-- Display Image -->
                                @if ($cardImage)
                                    <div class="image-display">
                                        <div class="position-relative d-inline-block mb-2">
                                            <img src="{{ \Storage::url($cardImage) }}" alt="Card image"
                                                class="img-thumbnail"
                                                style="max-width: 100%; max-height: 400px; cursor: pointer;"
                                                onclick="window.open('{{ \Storage::url($cardImage) }}', '_blank')">
                                        </div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                wire:click="toggleEditImage">
                                                <i class="fas fa-edit"></i> Change Image
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="removeImage"
                                                onclick="return confirm('Remove this image?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3"
                                        style="background-color: #f8f9fa; border-radius: 4px;">
                                        <p class="text-muted mb-2">No image attached</p>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            wire:click="toggleEditImage">
                                            <i class="fas fa-plus"></i> Add Image
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <hr>

                        <div class="mb-3">
                            <label class="form-label"><strong><i class="fas fa-clock"></i> Due Date</strong></label>

                            @if ($editingDueDate)
                                <!-- Edit Due Date Form -->
                                <form wire:submit.prevent="saveDueDate">
                                    <div class="mb-2">
                                        <input type="date"
                                            class="form-control @error('cardDueDate') is-invalid @enderror"
                                            wire:model="cardDueDate" required>
                                        @error('cardDueDate')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-save"></i> Save
                                        </button>
                                        <button type="button" class="btn btn-secondary btn-sm"
                                            wire:click="toggleEditDueDate">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            @else
                                <!-- Display Due Date -->
                                @if ($cardDueDate)
                                    <div class="due-date-display p-2 border rounded">
                                        @if ($selectedCard->isOverdue())
                                            <span class="badge bg-danger mb-2">
                                                <i class="fas fa-exclamation-triangle"></i> Overdue
                                            </span>
                                        @elseif($selectedCard->isDueSoon())
                                            <span class="badge bg-warning text-dark mb-2">
                                                <i class="fas fa-clock"></i> Due Soon
                                            </span>
                                        @else
                                            <span class="badge bg-success mb-2">
                                                <i class="fas fa-check"></i> On Track
                                            </span>
                                        @endif

                                        <div class="mb-2">
                                            <strong>Due:
                                                {{ \Carbon\Carbon::parse($cardDueDate)->format('M d, Y') }}</strong>
                                        </div>

                                        <div class="small text-muted mb-2">
                                            {{ \Carbon\Carbon::parse($cardDueDate)->diffForHumans() }}
                                        </div>

                                        <div>
                                            <button type="button" class="btn btn-sm btn-outline-secondary"
                                                wire:click="toggleEditDueDate">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger"
                                                wire:click="removeDueDate"
                                                onclick="return confirm('Remove due date?')">
                                                <i class="fas fa-trash"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center py-3"
                                        style="background-color: #f8f9fa; border-radius: 4px;">
                                        <p class="text-muted mb-2">No due date set</p>
                                        <button type="button" class="btn btn-sm btn-primary"
                                            wire:click="toggleEditDueDate">
                                            <i class="fas fa-plus"></i> Add Due Date
                                        </button>
                                    </div>
                                @endif
                            @endif
                        </div>

                        <hr>

                        <!-- Label Section -->
                        <div class="mb-3">
                            <livewire:label.label-card :card="$selectedCard" :key="'label-card-' . $selectedCard->id" />
                        </div>

                        <!-- Description Section -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Description</strong></label>
                            @if ($editingDescription)
                                <div>
                                    <textarea wire:model="cardDescription" class="form-control mb-2" placeholder="Add a description..." rows="4"
                                        wire:change="toggleEditDescription()" autofocus></textarea>
                                    <div class="d-flex flex-row align-items-center">
                                        <button wire:click="toggleEditDescription()" wire:loading.attr="disabled">
                                            <span class="btn btn-primary btn-sm" wire:loading.remove
                                                wire:target="toggleEditDescription">Save</span>
                                            <span class="btn btn-secondary btn-sm" wire:loading
                                                wire:target="toggleEditDescription">Saving...</span>
                                        </button>
                                        <span class="text-muted ml-2" style="font-size: 12px">Description will be
                                            saved
                                            automatically when exit</span>
                                    </div>
                                </div>
                            @else
                                <div wire:click="toggleEditDescription()"
                                    style="cursor: pointer; padding: 8px; border-radius: 4px; min-height: 40px; background-color: #f8f9fa;">
                                    {{ $cardDescription ?: 'Add a description...' }}
                                </div>
                            @endif
                        </div>

                        <hr>
                        <livewire:custom-field.custom-field-view :card="$selectedCard" :key="'custom-field-' . $selectedCard->id . '-' . now()->timestamp" />

                        <hr>
                        <livewire:comment.comment-view :card="$selectedCard" :key="'comment-' . $selectedCard->id" />
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" wire:click="deleteCard()"
                            wire:loading.attr="disabled" wire:target="deleteCard">Delete</button>
                        <button type="button" wire:loading wire:target="deleteCard"
                            class="btn btn-secondary">Deleting...</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
