<button 
    type="button" 
    class="btn btn-outline-danger" 
    wire:click="deleteLabel"
    onclick="return confirm('Are you sure you want to delete this label? It will be removed from all cards.')"
    wire:loading.attr="disabled">
    <i class="fas fa-trash"></i>
</button>