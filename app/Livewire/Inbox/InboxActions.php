<?php

namespace App\Livewire\Inbox;

use App\Events\Card\CardActions;
use App\Events\Card\CardCreated;
use App\Models\Card;
use App\Models\ListCard;
use App\Models\Log;
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
        $this->inboxToCard($title, $listId, $inboxId);
    }

    public function inboxToCard(string $title, int $listId, string $inboxId) {
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

        Log::create([
            'board_id' => $this->boardId,
            'user_id' => Auth::id(),
            'loggable_type' => Card::class,
            'loggable_id' => $card->id,
            'details' => 'Card "' . $card->card_title . '" created from inbox',
        ]);

        $this->removeInbox($inboxId);

        broadcast(new CardActions($this->boardId));
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
