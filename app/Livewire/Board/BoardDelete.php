<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BoardDelete extends Component
{
    public $boardId;

    public function delete()
    {
        $board = Board::findOrFail($this->boardId);
        $user = Auth::user();

        $pivot = $board->members()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if (! $pivot || (bool) $pivot->isGuest) {
            abort(403, 'Unauthorize Action, cannot delete another user board');
        }

        $board->delete();

        $this->dispatch('board_deleted');
    }

    public function render()
    {
        return view('livewire.board.board-delete');
    }
}
