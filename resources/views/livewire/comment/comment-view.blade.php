<div class="comments-section">
    <h6 class="mb-3">Comments and activity</h6>

    <!-- Comments List -->
    <div class="comments-list mb-3" style="max-height: 300px; overflow-y: auto;">
        @forelse($comments as $comment)
            <div class="comment-item mb-2 p-2 border-bottom">
                <div class="d-flex justify-content-between align-items-start gap-2">
                    <div>
                        <strong>{{ $comment->user->name }}</strong>
                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                        @if ($comment->edited_at)
                            <small class="text-muted">(edited {{ $comment->edited_at->diffForHumans() }})</small>
                        @endif
                    </div>

                    <!-- Edit/Delete buttons - only show if user is the author -->
                    @if ($comment->user_id === auth()->id())
                        <div class="btn-group btn-group-sm">
                            <button class="btn btn-outline-secondary btn-sm"
                                wire:click="$set('editingCommentId', {{ $comment->id }})">
                                Edit
                            </button>
                            @php
                                $boardId = $comment->card->list->board->id;
                                $userBoard = auth()->user()->memberBoards->firstWhere('id', $boardId);
                            @endphp
                            @if ($userBoard && $userBoard->pivot->role === 'admin' || $comment->user_id === auth()->id())
                                <button class="btn btn-outline-danger btn-sm"
                                    wire:click="deleteComment({{ $comment->id }})">
                                    Delete
                                </button>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- View Mode -->
                @if ($editingCommentId !== $comment->id)
                    <p class="mb-0 mt-1">{{ $comment->comment_content }}</p>
                @else
                    <!-- Edit Mode -->
                    <textarea wire:model.live="editingCommentContent" class="form-control form-control-sm mb-2 mt-2" rows="2"></textarea>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm"
                            wire:click="updateComment({{ $comment->id }}, $wire.editingCommentContent)">
                            Save
                        </button>
                        <button class="btn btn-secondary btn-sm" wire:click="$set('editingCommentId', null)">
                            Cancel
                        </button>
                    </div>
                @endif
            </div>
        @empty
            <p class="text-muted">No comments yet</p>
        @endforelse
    </div>

    <!-- Add Comment Form -->
    <div>
        <textarea wire:model.live="commentContent" class="form-control mb-2" placeholder="Write a comment..." rows="2"></textarea>
        <button class="btn btn-primary btn-sm" wire:click="addComment" @disabled(!$commentContent)>
            Post comment
        </button>
    </div>
</div>
