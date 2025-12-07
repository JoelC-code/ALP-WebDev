<div class="card p-2 shadow-sm mb-2" style="width: 100%;">
    <input type="text" wire:model="card_title" placeholder="Enter card title">
    <div class="d-flex flex-row gap-2 mt-2">
        <button class="btn btn-primary btn-sm" wire:click="createCard">Add</button>
        <button wire:click="cancelCreateCard">&#215;</button>
    </div>
</div>
