<div>
    <div class="card-sortable" data-list-id="{{ $list->id }}">
    @foreach ($cards as $ca)
        <div 
            class="card mb-2 shadow-sm card-items" 
            data-card-id="{{ $ca->id }}" 
           wire:key="card-{{ $ca->id }}"
            style="cursor: pointer;"
        >
            <div class="card-body p-2 d-flex justify-content-between align-items-center">
                <span wire:click="openCard({{ $ca->id }})">{{ $ca->card_title }}</span>
            </div>
        </div>
    @endforeach
    </div>
    <div wire:ignore.self>
        @if (! $showCreateCardForm)
            <button class="btn add-card btn-sm btn-outline-primary w-100 mt-2 no-sort" wire:click="showForm">Add Card</button>
        @else
            <livewire:card.card-create :list="$list" :key="'card-create-' . $list->id" />
        @endif
        </div>

    @if($showCardModal && $selectedCard)
        <div class="modal d-block" style="background-color: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 99999;">
            <div class="modal-dialog" style="position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 100000; width: 500px;">
                <div class="modal-content">
                    <div class="modal-header">
                        @if($editingTitle)
                            <div class="d-flex gap-2 w-100">
                                <input 
                                    type="text" 
                                    wire:model="cardTitle" 
                                    class="form-control"
                                    placeholder="Card title"
                                    wire:change="toggleEditTitle()"
                                    autofocus
                                >
                                <button class="btn btn-secondary btn-sm" wire:click="toggleEditTitle()">Cancel</button>
                            </div>
                        @else
                            <h5 
                                class="modal-title" 
                                wire:click="toggleEditTitle()"
                                style="cursor: pointer; padding: 5px; border-radius: 4px;"
                            >
                                {{ $cardTitle }}
                            </h5>
                        @endif
                        <button type="button" class="btn-close" wire:click="closeCard()"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Description Section -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Description</strong></label>
                            @if($editingDescription)
                                <div>
                                    <textarea 
                                        wire:model="cardDescription" 
                                        class="form-control mb-2"
                                        placeholder="Add a description..."
                                        rows="4"
                                        wire:change="toggleEditDescription()"
                                        autofocus
                                    ></textarea>
                                    <button class="btn btn-secondary btn-sm" wire:click="toggleEditDescription()">Cancel</button>
                                </div>
                            @else
                                <div 
                                    wire:click="toggleEditDescription()"
                                    style="cursor: pointer; padding: 8px; border-radius: 4px; min-height: 40px; background-color: #f8f9fa;"
                                >
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
                        <button type="button" class="btn btn-danger" wire:click="deleteCard()">Delete</button>
                        <button type="button" class="btn btn-secondary" wire:click="closeCard()">Close</button>
                    </div>
            </div>
        </div>
    @endif
</div>