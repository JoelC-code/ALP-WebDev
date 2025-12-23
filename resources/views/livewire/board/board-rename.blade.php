<div class="header-board">
    @if ($edit)
    <div 
    class="input-group position-relative"
    style="background-color: #003BFF; padding: 15px; border-top-left-radius: 8px; border-top-right-radius: 8px;">
        <input
            type="text"
            wire:model.defer="board_name"
            wire:keydown.enter="updateBoardName"
            wire:keydown.escape="$set('edit', false)"
            class="form-control"
            autofocus
        >
        <button
            class="btn text-muted position-absolute top-50 end-5 translate-middle-y me-2 p-0 border-0 bg-transparent"
            style="z-index: 999"
            wire:click="$set('edit', false)"
        >✕</button>
    </div>
    @else
        <div 
            class="d-flex flex-row align-items-center justify-content-between"
            style="background-color: #003BFF; padding: 15px; border-top-left-radius: 8px; border-top-right-radius: 8px;"
        >
            <p class="fs-5 text-white fw-semibold" wire:click="$set('edit', true)">
                {{ $board_name }}
            </p>
            <div id="button-group" class="d-flex flex-row gap-2">
                @if(auth()->user()->memberBoards->firstWhere('id', $board->id)->pivot->role == "admin")
                    <button class="btn fw-bold" style="background-color:white; border-radius:20px; color:#003BFF;" wire:click="$dispatch('open-invite-modal')">Invite</button>
                @endif
                <button class="btn fw-bold" style="background-color:white; border-radius:20px; color:#003BFF;">Members</button>
                <button class="btn fw-bold" style="background-color:white; border-radius:20px; color:#003BFF;">Logs</button>
            </div>
        </div>
    @endif
</div>

