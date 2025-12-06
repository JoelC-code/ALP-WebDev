<?php

namespace App\Http\Controllers;

use App\Models\Board;

class DashboardController extends Controller
{
    public function directCreate() {
        return view('boards.board-create');
    }

    public function accessBoard(Board $board) {
        return view('boards.board', [
            'board' => $board
        ]);
    }
}