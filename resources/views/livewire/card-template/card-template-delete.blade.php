<button 
    type="button" 
    class="btn btn-sm btn-outline-danger" 
    onclick="if(confirm('Are you sure you want to delete this template? This action cannot be undone.')) { @this.call('delete') }"
    wire:loading.attr="disabled">
    <span wire:loading.remove wire:target="delete">
        <i class="fas fa-trash"></i>
    </span>
    <span wire:loading wire:target="delete">
        <i class="fas fa-spinner fa-spin"></i>
    </span>
</button>