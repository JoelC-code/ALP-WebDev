<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardCreatBroadcast;
use App\Events\BoardUpdateBroadcast;
use App\Models\Board;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateBoard extends Component
{
    public $board_name;

    public function createBoard()
    {
        //Validate incoming data
        $this->validate([
            'board_name' => 'required|min:1'
        ]);

        $board = Board::create([
            'board_name' => $this->board_name
        ]);

        $board->members()->attach(Auth::id(), [
            'role' => 'admin',
            'isGuest' => false,
        ]);

        event(new BoardUpdateBroadcast($board));

        $this->reset('board_name');

        $this->redirect('/dashboard');
    }

    //In livewire library, render is used to render the data
    public function render()
    {
        return view('livewire.board.create-board');
    }
}
