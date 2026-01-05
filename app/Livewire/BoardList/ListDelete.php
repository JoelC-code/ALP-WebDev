<?php

namespace App\Livewire\BoardList;

use App\Events\List\ListDeleted;
use App\Models\Board;
use App\Models\ListCard;
use App\Models\Log;
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

        $listName = $list->list_name;
        $listIdVal = $list->id;

        $list->delete();

        Log::create([
            'board_id' => $this->boardId,
            'user_id' => Auth::id(),
            'loggable_type' => ListCard::class,
            'loggable_id' => $listIdVal,
            'details' => 'List ' . $listName . ' has been deleted.',
        ]);

        $this->dispatch('list-deleted');

        broadcast(new ListDeleted($list->id, $this->boardId));
    }

    public function render()
    {
        return view('livewire.boardlist.list-delete');
    }
}
