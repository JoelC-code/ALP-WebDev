<?php

use Illuminate\Support\Facades\DB;
use App\Models\ListCard;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('boards', function () {
    return true;
});

Broadcast::channel('board.{boardId}', function ($user, $boardId) {
    return $user->memberBoards()->where('board_id', $boardId)->exists();
});

Broadcast::channel('list.{listId}', function ($user, $listId) {
    return ListCard::where('id', $listId)->whereHas('board.members', fn($q) => $q->where('users.id', $user->id))->exists();
});

Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('card.{cardId}', function ($user, $cardId) {
    return DB::table('cards')
        ->join('list_cards', 'cards.list_id', '=', 'list_cards.id')
        ->join('boards', 'list_cards.board_id', '=', 'boards.id')
        ->join('member_boards', 'boards.id', '=', 'member_boards.board_id')
        ->where('cards.id', $cardId)
        ->where('member_boards.user_id', $user->id)
        ->exists();
});
