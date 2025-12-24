<div>
    @if($show)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="background-color: rgba(0, 0, 0, 0.5); z-index: 1050;">
            <div class="card shadow d-flex flex-column" style="width: 400px; max-height: 80vh;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Members</h4>
                    <button class="btn btn-close" wire:click="$dispatch('close-modal-members')"></button>
                </div>
                <div class="card-body" style="overflow-y: auto;">
                    @foreach($members as $member)
                        <div class="d-flex flex-row justify-content-between align-items-center">
                            <div class="flex-row d-flex gap-2">
                                <p>{{ $loop->iteration }}.</p>
                                <p>{{ $member->name }}</p>
                            </div>
                            <p class="text-secondary">{{ ucfirst($member->pivot?->role ?? 'N/A') }}</p>
                        </div>
                        <hr class="mb-2">
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>