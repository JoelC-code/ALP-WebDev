<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardInvited;
use App\Events\Board\BoardMemberToast;
use App\Models\Board;
use App\Models\Log;
use App\Models\User;
use App\Support\ToastMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class BoardInvite extends Component
{
    public Board $board;
    public string $inviteId = "";
    public bool $show = false;

    public function mount(Board $board)
    {
        $this->board = $board;
    }

    public function isAdmin($userId = null): bool {
        return $this->board->members()
            ->where('user_id', $userId ?? $this->userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    #[On('open-invite-modal')]
    public function open() {
        $this->resetErrorBag();
        $this->show = true;
    }

    #[On('close-invite-modal')]
    public function close() {
        $this->show = false;
    }

    public function inviteByCode() {
        if(!$this->isAdmin(Auth::id())) {
            $this->addError('general', 'Only admins can invite members to this board.');
            return;
        }

        $user = User::where('invite_id', $this->inviteId)->first();

        if(! $user) {
            $this->addError('inviteId', 'No user found with that invite code.');
            return;
        }

        if($user->id == Auth::id()) {
            $this->addError('inviteId', 'You cannot invite yourself to the board.');
            return;
        }

        if($this->board->members()->where('user_id', $user->id)->exists()) {
            $this->addError('inviteId', 'User is already a member of this board.');
            return;
        }

        $this->board->members()->attach($user->id, [
            'role' => 'member',
            'isGuest' => true,
        ]);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => User::class,
            'loggable_id' => $user->id,
            'details' => 'Invited user ' . $user->name . ' to the board',
        ]);

        $toast = [
                'type' => 'board_added',
                'board_id' => $this->board->id,
                'board_name' => $this->board->board_name,
                'actor_id' => Auth::id(),
                'actor_name' => Auth::user()->name,
                'created_at' => now()->toISOString()
        ];

        $this->reset('inviteId');

        $toast['message'] = ToastMessage::resolve($toast);

        event(new BoardInvited($this->board));
        event(new BoardMemberToast($user->id, $toast));
        
        $this->dispatch('member_added');
    }

    public function render()
    {
        return view('livewire.board.board-invite');
    }
}
