<div class="d-flex justify-content-between align-items-center w-100">
    @if ($editList)
    <div class="input-group d-flex gap-2">
        <input
            type="text"
            wire:model.defer="list_name"
            wire:keydown.enter="updateListName"
            wire:keydown.escape="$set('editList', false)"
            class="form-control"
            autofocus
        >
        <button
            class="btn text-muted position-absolute top-50 end-0 translate-middle-y me-2 p-0 border-0 bg-transparent"
            style="z-index: 999"
            wire:click="$set('editList', false)"
        >✕</button>
    </div>
    @else
        <h2 wire:click="$set('editList', true)">
            {{ $list_name }}
        </h2>
        
        <livewire:board-list.list-delete
            :board-id="$board->id"
            :list-id="$list->id"
            :key="'list-delete-' . $list->id"
        />
    @endif
</div>