<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CreateBoard extends Component
{
    public $board_name;

    public function create()
    {
        //Validate incoming data
        $this->validate([
            'board_name' => 'required|min:1'
        ]);

        $board = Board::create([
            'board_name' => $this->board_name
        ]);

        $board->users()->attach(Auth::id(), [
            'role' => 'admin',
            'isGuest' => false,
        ]);

        $this->reset('board_name');

        $this->dispatch('board_created');
    }

    //In livewire library, render is used to render the data
    public function render()
    {
        return view('livewire.board.create-board');
    }
}
