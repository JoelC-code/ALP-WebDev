<div style="z-index: 999">
    @if ($show)
        <div class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center"
            style="background-color: rgba(0, 0, 0, 0.5); z-index: 1050;">
            <div class="card shadow" style="width: 400px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>Add Member</h4>
                    <button class="btn btn-close" wire:click="$dispatch('close-invite-modal')"></button>
                </div>
                <div class="card-body">
                    @error('general')
                        <div class="alert alert-danger py-1 mb-2">
                            {{ $message }}
                        </div>
                    @enderror
                    @error('inviteId')
                        <div class="alert alert-danger py-1 mb-2">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="mb-3">
                        <label class="form-label">Invite Code</label>
                        <input type="number" wire:model.defer="inviteId"
                            class="form-control @error('inviteId') is-invalid @enderror" placeholder="Enter Invite ID">
                    </div>

                    <button class="btn btn-primary w-100" wire:click="inviteByCode" wire.loading.attr="disabled">
                        <span wire:loading.remove>Invite</span>
                        <span wire:loading>Loading...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
