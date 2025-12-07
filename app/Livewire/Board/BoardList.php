<?php

namespace App\Livewire\Board;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BoardList extends Component
{
    public $myWorkspaces;
    public $otherWorkspaces;
    public $boards;

    protected $listeners = [
        'board_deleted' => 'refreshBoards'
    ];

    public function mount()
    {
        $this->refreshBoards();
    }

    public function refreshBoards()
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
