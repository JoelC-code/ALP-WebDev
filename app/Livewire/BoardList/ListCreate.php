<?php

namespace App\Livewire\BoardList;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListCreate extends Component
{
    public $list_name;
    public $position;
    public $board_id;
    public $board;

    public function mount($board)
    {
        $this->board = $board;
        $this->board_id = $board->id;
    }

    public function createList()
    {

        $pivot = $this->board->members()->where('user_id', Auth::user()->id)->first()?->pivot;

        if (! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->validate([
            'list_name' => 'required|string|min:1'
        ]);

        $position = $this->position ?? $this->board->lists()->count() + 1;

        $this->board->lists()->create([
            'list_name' => $this->list_name,
            'position' => $position
        ]);

        $this->reset('list_name');

        $this->dispatch('list-created');

        $this->dispatch('hideCreateFormFromParent');
    }

    public function cancelCreateList()
    {
        // Just tell parent to close the form
        $this->dispatch('hide-create-form');
    }

    public function render()
    {
        return view('livewire.list.list-create');
    }
}
