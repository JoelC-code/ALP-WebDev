<?php

namespace App\Livewire\Inbox;

use Livewire\Component;

class InboxActions extends Component
{
    public array $inboxes = [];
    public int $boardId;
    public string $inboxName = '';

    public function mount(int $boardId) {
        $this->boardId = $boardId;
        $all = session()->get('inboxes', []);
        $this->inboxes = $all[$boardId] ?? [];
    }

    public function addInbox() {
        if(trim($this->inboxName) === '') return;

        $this->inboxes[] = [
            'id' => uniqid('inbox_'),
            'title' => $this->inboxName,
        ];

        $all = session()->get('inboxes', []);
        $all[$this->boardId] = $this->inboxes;

        session()->put('inboxes', $all);

        $this->inboxName = '';
    }

    public function removeInbox($index) {
        $this->inboxes = array_values(
            array_filter($this->inboxes, fn($i) => $i['id'] !== $index)
        );

        $all = session()->get('inboxes', []);
        $all[$this->boardId] = $this->inboxes;

        session()->put('inboxes', $all);
    }

    public function render()
    {
        return view('livewire.inbox.inbox-actions');
    }
}
