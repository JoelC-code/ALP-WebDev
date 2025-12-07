<?php

namespace App\Livewire;

use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListView extends Component
{
    public Board $board;
    public $lists = [];

    protected $listeners = [
        'list_created' => 'refreshLists',
        'list_deleted' => 'refreshLists'
    ];

    public function mount(Board $board) {
        $this->board = $board;
        $this->refreshLists();
    }

    public function refreshLists() {
        $pivot = $this->board->members()->where('user_id', Auth::user()->id)->first()?->pivot;

        if (! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->lists = $this->board->lists()->orderBy('position')->get();
    }

    public function render()
    {
        return view('livewire.list.list-view');
    }
}
