<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class BoardList extends Component
{
    public $boards;

    protected $listeners = ['board_created' => 'refreshBoards'];

    public function mount()
    {
        $this->boards = Auth::user()->memberBoards()->get();    
    }

    public function refreshBoards() {
        $this->boards = Auth::user()->memberBoards()->get();
    }

    public function render() {
        return view('livewire.board.board-list');
    }
}
