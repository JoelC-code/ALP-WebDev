<?php

namespace App\Livewire\BoardList;

use App\Events\List\ListReordered as ListListReordered;
use App\Models\Board;
use App\Models\ListCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListView extends Component
{
    public $board;
    public $boardId;
    public $lists = [];
    public $showCreateForm = false;

    //Listeners structure:
    // 'dispatch_sent' -> 'refreshfunction'
    protected $listeners = [
        'list-created' => 'refreshLists',
        'hideCreateFormFromParent' => 'createCancel',
        'list-deleted' => 'refreshLists',
        'list-renamed' => 'refreshLists',
        'lists-reordered' => 'reorderLists',
        'list-reordered' => 'refreshLists',
    ];

    
    public function showForm()
    {
        $this->showCreateForm = true;
    }

    public function createCancel()
    {
        $this->showCreateForm = false;
    }
    

    public function mount(Board $board) {
        $this->boardId = $board->id;
        $this->board = Board::find($this->boardId);
        $this->refreshLists();
    }

    public function refreshLists() {
        $this->board = Board::with('lists')->find($this->boardId);

        $pivot = $this->board->members()->where('user_id', Auth::id())->first()?->pivot;

        if (! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $this->lists = $this->board->lists()->orderBy('position')->get();
    }

    public function reorderLists($boardId, $orderedIds){
        foreach($orderedIds as $index => $listId) {
            ListCard::where('id', $listId)->update(['position' => $index + 1]);
        }

        broadcast(new ListListReordered($this->boardId, $orderedIds))->toOthers();

        $this->refreshLists();
    }

    public function render()
    {
        logger('render fired at ' . now());
        return view('livewire.list.list-view');
    }
}
