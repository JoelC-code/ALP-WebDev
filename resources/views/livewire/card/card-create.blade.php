<div class="card p-2 shadow-sm mb-2" style="width: 100%;">
    <input type="text" wire:model="card_title" placeholder="Enter card title" wire:target="createCard" wire:loading.attr="disabled">
    <div class="d-flex flex-row gap-2 mt-2">
        <button class="btn btn-primary btn-sm w-100" wire:click="createCard" wire:loading.attr="disabled" wire:target="createCard" wire:loading.class="disabled">
            <span wire:loading.remove wire:target="createCard">
                Add
            </span>
            <span wire:loading wire:target="createCard">
                Adding...
            </span>
        </button>
        <button wire:click="cancelCreateCard">&#215;</button>
    </div>
</div>
