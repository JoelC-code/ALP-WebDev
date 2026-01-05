<?php

namespace App\Livewire\BoardList;

use App\Events\List\ListRenamed;
use App\Models\ListCard;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListRename extends Component
{
    public $list;
    public $list_name;
    public $editList = false;
    public $board;

    public function mount(ListCard $list, $board)
    {
        $this->list = $list;
        $this->list_name = $list->list_name;
        $this->board = $board;
    }

    public function showEditList()
    {
        $this->editList = true;
    }

    public function updateListName()
    {
        $this->validate([
            'list_name' => 'required|string|max:150',
        ]);

        $this->list->update([
            'list_name' => $this->list_name
        ]);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => ListCard::class,
            'loggable_id' => $this->list->id,
            'details' => 'List has changed name to ' . $this->list_name,
        ]);

        broadcast(new ListRenamed($this->list));

        $this->dispatch('list-renamed');
        $this->editList = false;
    }

    public function render()
    {
        return view('livewire.boardlist.list-rename');
    }
}
