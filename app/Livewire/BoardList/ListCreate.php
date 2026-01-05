<?php

namespace App\Livewire\BoardList;

use App\Events\List\ListCreated;
use App\Models\ListCard;
use App\Models\Log;
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

        $list = $this->board->lists()->create([
            'list_name' => $this->list_name,
            'position' => $position
        ]);

        Log::create([
            'board_id' => $this->board_id,
            'user_id' => Auth::id(),
            'loggable_type' => ListCard::class,
            'loggable_id' => $list->id,
            'details' => 'New list has been created with the name ' . $this->list_name,
        ]);

        $this->reset('list_name');

        broadcast(new ListCreated($list));

        $this->cancelCreateList();
    }

    public function cancelCreateList()
    {
        $this->dispatch('hideCreateFormFromParent');
    }

    public function render()
    {
        return view('livewire.board-list.list-create');
    }
}
