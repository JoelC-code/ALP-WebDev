<div>
    <div class="text-center bg-color-main-one">
        <h1 class="fw-bold">Make a board</h1>
        <p>Make a new board by putting the name on the input</p>
    </div>
    <form wire:submit.prevent="createBoard" class="d-flex flex-column">
        <label for="board_name">Board Name:</label>
        <input type="text" class="form-control my-3" wire:model="board_name" placeholder="Board #1" required>
        <button type="submit" class="btn btn-primary btn-size">Create Board</button>
    </form>
</div>
