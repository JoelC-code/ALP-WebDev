<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardMemberActions;
use App\Models\Board;
use App\Models\Card;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BoardDisconnect extends Component
{
    
    public $boardId;

    public function leave() {
        $user = Auth::user();
        $board = Board::findOrFail($this->boardId);

        $pivot = $board->members()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot;

        if(! $pivot || ! $pivot->isGuest) {
            abort(403);
        }

        $board->members()->detach($user->id);

        Log::create([
            'board_id' => $board->id,
            'user_id' => $user->id,
            'loggable_type' => Board::class,
            'loggable_id' => $board->id,
            'details' => 'User ' . $user->name . ' left the board',
        ]);

        broadcast(new BoardMemberActions($board, $user));
    }

    public function render()
    {
        return view('livewire.board.board-disconnect');
    }
}
