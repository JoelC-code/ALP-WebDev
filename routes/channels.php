<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('boards', function () {
    return true;
});

Broadcast::channel('board.{boardId}', function ($user, $boardId) {
    return $user->memberBoards()->where('board_id', $boardId)->exists();
});
