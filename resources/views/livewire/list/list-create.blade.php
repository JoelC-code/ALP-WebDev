<div class="card p-3 shadow-sm" style="width: 300px; height: fit-content;">
    <input type="text" wire:model="list_name" placeholder="Enter a title">
    <div class="d-flex flex-row gap-2">
        <button class="btn btn-primary" wire:click="createList">Add</button>
        <button wire:click="cancelCreateList">&#215</button>
    </div>
</div>
