<?php

namespace App\Livewire\Board;

use App\Events\Board\BoardMemberActions;
use App\Events\Board\BoardMemberToast;
use App\Models\Board;
use App\Models\Log;
use App\Models\User;
use App\Support\ToastMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\On;
use Livewire\Component;

class BoardMemberAction extends Component
{

    public Board $board;
    public $member;
    public $role;
    public bool $isLoading;

    public function mount(Board $board, int $userId)
    {
        $this->board = $board;
        $this->member = $board->members()->where('user_id', $userId)->firstOrFail();
        $this->role = $this->member->pivot->role;
    }

    public function isAdmin($userId = null): bool
    {
        return $this->board->members()
            ->where('user_id', $userId ?? $this->userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function isMember(int $memberId): bool
    {
        return $this->board->members()
            ->whereKey($memberId)
            ->exists();
    }

    public function checkSameRole(int $memberId, string $newRole): bool
    {
        return $this->board->members()
            ->whereKey($memberId)
            ->wherePivot('role', $newRole)
            ->exists();
    }

    public function updateMemberRole()
    {
        if (! $this->isAdmin(Auth::id())) {
            $this->addError('general', 'Only admins can update roles of a member to this board.');
            return;
        }

        if (! $this->isMember($this->member->id)) {
            $this->addError('general', 'This user is not exist on this board.');
            return;
        }

        $this->member = $this->board->members()
            ->where('user_id', $this->member->id)
            ->firstOrFail();

        if ($this->checkSameRole($this->member->id, $this->role)) {
            $this->addError('general', 'Role of the user is the same.');
            return;
        }

        $this->board->members()->updateExistingPivot(
            $this->member->id,
            ['role' => $this->role]
        );

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => User::class,
            'loggable_id' => $this->member->id,
            'details' => 'Changed role of user ' . $this->member->name . ' to ' . $this->newRole . '.',
        ]);

        $toast = [
                'type' => 'role_changed',
                'board_id' => $this->board->id,
                'board_name' => $this->board->board_name,
                'role' => $this->role,
                'actor_id' => Auth::id(),
                'actor_name' => Auth::user()->name,
                'created_at' => now()->toISOString()
        ];

        Log::info('BoardMemberAction: toast cached', [
            'cache_contents' => Cache::get('toast:user:' . $this->member->id)
        ]);

        $toast['message'] = ToastMessage::resolve($toast);

        broadcast(new BoardMemberActions($this->board, $this->member));
        broadcast(new BoardMemberToast($this->member->id, $toast));

        $this->member = null;
    }

    public function disconnectMemberFromBoard($userId)
    {
        if (! $this->isAdmin(Auth::id())) {
            $this->addError('general', 'Only admins can update roles of a member to this board.');
            return;
        }

        if (! $this->isMember($this->member->id)) {
            $this->addError('general', 'This user is not exist on this board.');
            return;
        }

        $this->board->members()->detach($userId);

        Log::create([
            'board_id' => $this->board->id,
            'user_id' => Auth::id(),
            'loggable_type' => User::class,
            'loggable_id' => $this->member->id,
            'details' => 'Removed user ' . $this->member->name . ' from the board',
        ]);

        $toast = [
                'type' => 'board_removed',
                'board_id' => $this->board->id,
                'board_name' => $this->board->board_name,
                'actor_id' => Auth::id(),
                'actor_name' => Auth::user()->name,
                'created_at' => now()->toISOString()
        ];

        $toast['message'] = ToastMessage::resolve($toast);

        broadcast(new BoardMemberActions($this->board, $this->member));
        broadcast(new BoardMemberToast($userId, $toast));
    }

    #[On('member_action_done')]
    public function resetMember()
    {
        $this->member = null;
    }


    public function render()
    {
        return view('livewire.board.board-member-action');
    }
}
