<?php

namespace App\Livewire\Inbox;

use App\Events\Card\CardCreated;
use App\Models\ListCard;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
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
            'id' => (string) Str::uuid(),
            'title' => $this->inboxName,
        ];

        $all = session()->get('inboxes', []);
        $all[$this->boardId] = $this->inboxes;

        session()->put('inboxes', $all);

        $this->inboxName = '';
    }

    #[On('inbox-dropped')]
    public function inboxDropped($title, $listId, $inboxId) {
        logger()->info('Inbox dropped', compact('title', 'listId'));

        $this->inboxToCard($title, $listId, $inboxId);
    }

    public function inboxToCard(string $title, int $listId, string $inboxId) {
        logger()->info('Inbox -> Card', [
            'title' => $title,
            'listId' => $listId,
            'boardId' => $this->boardId,
        ]);

        $list = ListCard::where('id', $listId)
            ->where('board_id', $this->boardId)
            ->first();

        if(! $list) {
            logger()->warning("No list id detected, you shouldn't see this in UI so CHEVAL GRAND!!!!!");
            return;
        }

        $pivot = $list->board->members()->where('user_id', Auth::id())->first()?->pivot;

        if(! $pivot) {
            abort(403, "You shouldn't see this message but here we are... [Unauthorized Access]");
        }

        $position = $list->cards()->count() + 1;

        $card = $list->cards()->create([
            'card_title' => $title,
            'position' => $position
        ]);

        logger()->info('Inbox has been succefully become card', ['card_id' => $card->id]);

        $this->removeInbox($inboxId);

        broadcast(new CardCreated($card));
    }

    public function removeInbox(string $inboxId) {
        $this->inboxes = array_values(
            array_filter($this->inboxes, fn($i) => $i['id'] !== $inboxId)
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
