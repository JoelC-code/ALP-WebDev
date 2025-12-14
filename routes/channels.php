<?php

use App\Models\ListCard;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('boards', function () {
    return true;
});

Broadcast::channel('board.{boardId}', function ($user, $boardId) {
    return $user->memberBoards()->where('board_id', $boardId)->exists();
});

Broadcast::channel('list.{listId}', function($user, $listId) {
    return ListCard::where('id', $listId)->whereHas('board.members', fn ($q) => $q->where('users.id', $user->id));
});
