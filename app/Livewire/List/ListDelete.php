<?php

namespace App\Livewire\List;

use App\Models\ListCard;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ListDelete extends Component
{
    public function deleteList($listId)
    {

        $list = $this->board->lists()->where('id', $listId)->firstOrFail();

        $pivot = $this->board->members()->where('user_id', Auth::user()->id)->first()?->pivot;

        if(! $pivot) {
            abort(403, 'Unauthorized access, you are not part of the board');
        }

        $list->delete();

        $this->dispatch('list_deleted');
    }

    public function render()
    {
        return view('livewire.list.list-delete');
    }
}
