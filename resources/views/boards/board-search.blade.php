@extends('layouts.nav')
@section('title', $sitename)
@section('mainContent')
    <div class="container">
        <div class="d-flex d-md-none flex-column text-center bg-color-main-one mb-4">
            <p class="fw-bold fs-3">Searching For:</p>
            <form action="/search-board" method="get" id="searchForm" class="d-flex flex-column w-100 gap-2">
                <input type="text" class="form-control w-100" name="searchBoard" value="{{ $searchData }}"
                    placeholder="Find a Board">
                <button class="btn btn-primary w-100">Search</button>
            </form>
        </div>

        <div class="container d-none d-md-flex flex-md-column text-center mb-4">
            <p class="fw-bold fs-3">You Are Searching For:</p>
            <p class="fw-bold fs-5">{{ $searchData }}</p>
        </div>

        <div class="board-cards d-grid gap-2" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));">
            @forelse($boards as $board)
                <a href="{{ route('board.access', $board->id) }}"
                    class="text-decoration-none card p-4 bg-card-color shadow rounded">
                    <p class="text-truncate fw-semibold">{{ $board->board_name }}</p>
                </a>
            @empty
                <p class="text-muted text-center">
                    Make a board or ask someone to invite you!
                </p>
            @endforelse
        </div>
    </div>
@endsection
