<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardMemberActions;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BoardDisconnect extends Component
{
    
    public $boardId;

    public function leave() {
        $user = Auth::user();
        logger("Done got the UserId");
        $board = Board::findOrFail($this->boardId);
        logger("Done catching both UserId & Board");

        $pivot = $board->members()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;
        logger("Checking the pivot");

        if(! $pivot || ! $pivot->isGuest) {
            abort(403);
        }

        logger("Prepping for the disconnect");
        $board->members()->detach($user->id);

        logger("Done and being sent to the broadcast");
        broadcast(new BoardMemberActions($board, $user));
    }

    public function render()
    {
        return view('livewire.board.board-disconnect');
    }
}
