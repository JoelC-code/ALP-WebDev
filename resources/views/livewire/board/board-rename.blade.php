<div>
    @if ($edit)
        <input
            type="text"
            wire:model.defer="board_name"
            wire:keydown.enter="updateBoardName"
            wire:keydown.escape="$set('edit', false)"
            class="form-control"
        >
    @else
        <span wire:click="$set('edit', true)">
            {{ $board_name }}
        </span>
    @endif
</div>

