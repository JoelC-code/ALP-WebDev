<?php

namespace App\Livewire\Comment;

use App\Events\comment\CommentActions;
use App\Models\Card;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CommentView extends Component
{
    public $card;
    public $comments = [];
    public $commentContent = '';
    public $editingCommentId = null;
    public $editingCommentContent = '';

    protected $listeners = [
        'comment-action' => 'refreshComments',
    ];

    public function mount(Card $card)
    {
        $this->card = $card;
        $this->refreshComments();
    }

    public function refreshComments()
    {
        $this->comments = $this->card->comments()->with('user')->latest()->get();
    }

    public function addComment()
    {
        $this->validate([
            'commentContent' => 'required|string|min:1',
        ]);

        $commentNew = $this->card->comments()->create([
            'user_id' => Auth::id(),
            'comment_content' => $this->commentContent,
        ]);

        $this->commentContent = '';
        broadcast(new CommentActions($commentNew->card->id));
    }

    public function deleteComment($commentId)
    {
        $comment = $this->card->comments()->find($commentId);

        if(! $comment) {
            abort(404, 'Comment is being called twice, causing it to go delete a non existance comment.');
        }

        $actorId = Auth::id();
        $actor = User::findOrFail($actorId);
        $boardId = $comment->card->list->board_id;

        $isAdmin = $actor->memberBoards()->where('board_id', $boardId)->wherePivot('role', 'admin')->exists();

        if ($comment->user_id !== Auth::id() || ! $isAdmin) {
            abort(403, 'Unauthorized');
        }

        $comment->delete();
        broadcast(new CommentActions($comment->card->id));
    }

    public function updateComment($commentId, $newContent)
    {
        $comment = $this->card->comments()->find($commentId);

        if ($comment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        if (empty($newContent) || strlen($newContent) < 1) {
            return;
        }

        $comment->comment_content = $newContent;
        $comment->edited_at = now();
        $comment->save();

        $this->editingCommentId = null;
        $this->editingCommentContent = '';
        broadcast(new CommentActions($comment->card->id));
    }

    public function render()
    {
        return view('livewire.comment.comment-view');
    }
}
