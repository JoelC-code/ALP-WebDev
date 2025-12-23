<?php

namespace App\Livewire\Board;

use App\Models\Board;
use Livewire\Attributes\On;
use Livewire\Component;

class BoardMemberList extends Component
{
    public Board $board;
    public bool $show = false;

    public function mount(Board $board) {
        $this->board = $board;
        $this->loadMember();
    }

    public function loadMember() {
        $this->board->load('members');
    }

    #[On('open-modal-members')]
    public function toggleDropdownOpen() {
        $this->show = true;
    }

    #[On('close-modal-members')]
    public function toggleDropdownClose() {
        $this->show = false;
    }

    #[On('member_added')]
    public function memberAddUpdate() {
        $this->loadMember();
    }

    #[On('member_removed')]
    public function memberDeleteUpdate() {
        $this->loadMember();
    }

    public function render()
    {
        return view('livewire.board.board-member-list', [
            'members' => $this->board->members()->get()
        ]);
    }
}
