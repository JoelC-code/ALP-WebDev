<div>
    @if($member)
    <div class="d-flex flex-column w-100">
        <div class="d-flex flex-row justify-content-between">
            <p>{{ $member->name }}</p>
            <p class="text-secondary">{{ ucfirst($member->pivot?->role ?? 'N/A') }}</p>
        </div>
        <br>
        <div class="d-flex flex-row justify-content-between align-items-center gap-5">
            <p>Role Update:</p>
            <select class="form-select form-select-sm w-auto" wire:model="role">
                <option value="admin">Admin</option>
                <option value="member">Member</option>
            </select>
        </div>

        <div class="d-flex flex-row gap-3">
            <button class="btn btn-primary w-100 mt-2" wire:click="updateMemberRole" wire:loading.attr="disabled" wire:click.prevent="$set('isLoading', true)">
                <span wire:loading.remove>Update</span>
                <span wire:loading>Loading...</span>
            </button>
            <button class="btn btn-danger w-100 mt-2"
                wire:click="disconnectMemberFromBoard({{ $member->id }})" wire:loading.attr="disabled" wire:click.prevent="$set('isLoading', true)">
                <span wire:loading.remove>Remove</span>
                <span wire:loading>Loading...</span>
            </button>
        </div>
    </div>
    @else 
        <div class="d-flex flex-column justify-content-center align-items-center w-100">
            <div> 
                <p class="text-muted">404 | User not found</p>
            </div>
        </div>
    @endif
</div>
