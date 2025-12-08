<?php

namespace App\Livewire\BoardList;

use App\Events\BoardUpdateBroadcast;
use App\Events\List\ListDeleteBroadcast;
use App\Models\Board;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListDelete extends Component
{

    public $listId;
    public $boardId;
    public $board;

    public function mount($boardId, $listId) {
        $this->boardId = $boardId;
        $this->listId = $listId;
        $this->board = Board::findOrFail($boardId);
    } 

    public function deleteList()
    {

        $list = $this->board->lists()->where('id', $this->listId)->firstOrFail();

        $pivot = $this->board->members()->where('user_id', Auth::user()->id)->first()?->pivot;

        if(! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $list->delete();

        event(new BoardUpdateBroadcast($this->board->id, 'list-deleted', [
            'list_id' => $list->id
        ]));

        $this->dispatch('board-update');
    }

    public function render()
    {
        return view('livewire.list.list-delete');
    }
}
