<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardRenamed;
use App\Models\Board;
use Livewire\Attributes\On;
use Livewire\Component;

class BoardRename extends Component
{
    public $board;
    public $board_name;
    public $edit = false;
    
    public function mount(Board $board) {
        $this->board = $board;
        $this->board_name = $board->board_name;
    }

    public function startEditBoard() {
        $this->edit = true;
    }

    public function updateBoardName() {
        $this->validate([
            'board_name' => 'required|string|max:100'
        ]);

        $this->board->update([
            'board_name' => $this->board_name
        ]);

        $this->edit = false;

        broadcast(new BoardRenamed($this->board))->toOthers();

        $this->dispatch('board-renamed');
        $this->dispatch('global-board-renamed');
    }

    #[On('board-renamed')]
    public function syncBoard(){
        $this->board = $this->board->fresh();
        $this->board_name = $this->board->board_name;
    }

    public function render()
    {
        return view('livewire.board.board-rename');
    }
}
