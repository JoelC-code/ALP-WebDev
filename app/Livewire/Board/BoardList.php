<?php

namespace App\Livewire\Board;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class BoardList extends Component
{
    public $myWorkspaces;
    public $otherWorkspaces;
    public $boards;

    public function mount()
    {
        $this->refreshBoards();
    }

    #[On('board_deleted')]
    public function refreshBoards() {
        logger("Livewire refreshed boards after Echo event.");
        $this->loadBoards();
    }

    public function loadBoards()
    {
        $user = Auth::user();

        if ($user instanceof User) {
            $this->boards = $user->memberBoards()->get();

            $this->myWorkspaces = $this->boards->filter(
                fn ($board) => $board->pivot->isGuest == false
            );

            $this->otherWorkspaces = $this->boards->filter(
                fn ($board) => $board->pivot->isGuest === true
            );
        } else {
            $this->myWorkspaces = collect([]);
            $this->otherWorkspaces = collect([]);
        }
    }

    public function render()
    {
        return view('livewire.board.board-list');
    }
}
