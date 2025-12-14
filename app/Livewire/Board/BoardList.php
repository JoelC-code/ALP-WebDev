<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class BoardList extends Component
{
    public $myWorkspaces;
    public $otherWorkspaces;
    public $boards;
    public $board;
    public $boardId;

    public function mount()
    {
        $this->refreshBoards();
    }

    #[On('board_deleted')]
    public function refreshBoards()
    {
        $this->loadBoards();
    }

    public function loadBoards()
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->boards = $user->memberBoards()->get();

            $this->myWorkspaces = $this->boards->filter(
                fn($board) => $board->pivot->isGuest == false
            );

            $this->otherWorkspaces = $this->boards->filter(
                fn($board) => $board->pivot->isGuest === true
            );
        } else {
            $this->myWorkspaces = collect([]);
            $this->otherWorkspaces = collect([]);
        }
    }

    #[On('global-board-renamed')]
    public function handleGlobalBoardRename() {
        $this->loadBoards();
    }


    public function render()
    {
        return view('livewire.board.board-list');
    }
}
