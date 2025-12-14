<?php

namespace App\Livewire\BoardList;

use App\Events\List\ListRenamed;
use App\Models\ListCard;
use Livewire\Component;

class ListRename extends Component
{
    public $list;
    public $list_name;
    public $editList = false;

    public function mount(ListCard $list) {
        $this->list = $list;
        $this->list_name = $list->list_name;
    }

    public function showEditList() {
        $this->editList = true;
    }

    public function updateListName() {
        $this->validate([
            'list_name' => 'required|string|max:150',
        ]);

        $this->list->update([
            'list_name' => $this->list_name
        ]);

        broadcast(new ListRenamed($this->list));

        $this->dispatch('list-renamed');
        $this->editList = false;
    }

    public function render()
    {
        return view('livewire.list.list-rename');
    }
}
