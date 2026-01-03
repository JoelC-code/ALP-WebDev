<?php

namespace App\Livewire\Card;

use App\Models\Board;
use App\Models\Card;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CardDueDateSidebar extends Component
{
    public $board;
    public $upcomingCards = [];
    public $limit = 5;

    protected $listeners = [
        'card-refreshed' => 'loadUpcomingCards',
        'card-action-refresh' => 'loadUpcomingCards',
    ];

    public function mount(Board $board)
    {
        $this->board = $board;
        
        // Check authorization
        if ($board->members->pluck('id')->doesntContain(Auth::id())) {
            abort(403, 'Unauthorized access');
        }
        
        $this->loadUpcomingCards();
    }

    public function loadUpcomingCards()
    {
        // Get all cards from the board's lists with due dates
        $this->upcomingCards = Card::whereHas('list', function($query) {
                $query->where('board_id', $this->board->id);
            })
            ->whereNotNull('dates')
            ->where('is_completed', false) // Only show incomplete cards
            ->orderBy('dates', 'asc')
            ->limit($this->limit)
            ->with(['list', 'labels'])
            ->get();
    }

    public function updateLimit($newLimit)
    {
        $this->limit = max(1, min(20, $newLimit)); // Between 1 and 20
        $this->loadUpcomingCards();
    }

    public function render()
    {
        return view('livewire.card.card-due-date-sidebar');
    }
}