<div>
    @if ($editList)
        <input
            type="text"
            wire:model.defer="list_name"
            wire:keydown.enter="updateListName"
            wire:keydown.escape="$set('editList', false)"
            class="form-control"
            autofocus
        >
    @else
        <h2 wire:click="$set('editList', true)">
            {{ $list_name }}
        </h2>
    @endif
</div>



{{-- $editList --}}