<?php

namespace App\Livewire\Comment;

use App\Models\Card;
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
        'comment-created' => 'refreshComments',
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

        $this->card->comments()->create([
            'user_id' => Auth::id(),
            'comment_content' => $this->commentContent,
        ]);

        $this->commentContent = '';
        $this->refreshComments();
    }

    public function deleteComment($commentId)
{
    $comment = $this->card->comments()->find($commentId);
    
    if ($comment->user_id !== Auth::id()) {
        abort(403, 'Unauthorized');
    }
    
    $comment->delete();
    $this->refreshComments();
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
    $this->refreshComments();
}

    public function render()
    {
        return view('livewire.comment.comment-view');
    }
}