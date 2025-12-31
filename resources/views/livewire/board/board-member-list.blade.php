<div>
    @if($show)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="background-color: rgba(0, 0, 0, 0.5); z-index: 9999;">
            <div class="card shadow d-flex flex-column" style="width: 400px; max-height: 80vh; z-index: 10001">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Members</h4>
                    @if(! $selectedUserId)
                        <button class="btn btn-close" wire:click="$dispatch('close-modal-members') "></button>
                    @else
                        <button class="btn" wire:click.stop="backToList">←</button>
                    @endif 
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    @if($selectedUserId !== null)
                        <livewire:board.board-member-action
                            :board="$board"
                            :userId="$selectedUserId"
                            wire:key="member-action-{{ $selectedUserId }}" />
                    @else
                    @foreach($members as $member)
                        @php
                            $isSelf = $member->id === auth()->id();
                            $canInteract = $this->currentUserIsAdmin() && ! $isSelf && ! $this->memberIsProtectedAdmin($member)
                        @endphp
                        <div class="d-flex flex-row justify-content-between align-items-center {{ ! $canInteract ? 'opacity-50' : 'cursor-pointer' }}" 
                            @if($canInteract)
                                wire:click="$set('selectedUserId', {{ $member->id }})"
                            @endif
                            >
                            <div class="flex-row d-flex gap-2">
                                <p>{{ $loop->iteration }}.</p>
                                <p>{{ $member->name }}</p>
                            </div>
                            <p class="text-secondary">{{ ucfirst($member->pivot?->role ?? 'N/A') }}</p>
                        </div>
                        <hr class="mb-2">
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>