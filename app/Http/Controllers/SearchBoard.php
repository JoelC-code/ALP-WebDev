<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchBoard extends Controller
{
    public function index(Request $request) {
        $user = Auth::user();

        if(! $user instanceof User) {
            abort(403, "You're a goddamn magician to find this...");
        }

        $query = $user->memberBoards();

        if($request->filled('searchBoard')) {
            $query->where('board_name', 'like', '%' . $request->searchBoard . '%');
        }

        return view('boards.board-search', [
            'sitename' => $request->searchBoard ?? 'Search a Board',
            'boards' => $query->paginate(6)->withQueryString(),
            'searchData' => $request->searchBoard ?? '',
        ]);
    }
}
