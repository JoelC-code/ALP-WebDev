    <form wire:submit.prevent="createBoard" class="d-flex flex-column">
        <label for="board_name">Board Name:</label>
        <input type="text" class="form-control my-3" wire:model="board_name" placeholder="Board #1" required>
        <button type="submit" class="btn btn-primary btn-size">Create Board</button>
    </form>
