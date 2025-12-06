<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

//LIVEWIRE
//use wire:click"function(params)" to call a public
//function of a livewire file
class BoardDelete extends Component
{

    public $boardId;

    public function delete() {
        $board = Board::findOrFail($this->boardId);
        $user = Auth::user();
        $pivot = $board->members()->where('user_id', $user->id)->first()?->pivot;
        //Check salah satu, pivot ada ATAU di pivot itu diflag true untuk isGuest
        if(! $pivot || (bool)$pivot->isGuest) {
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