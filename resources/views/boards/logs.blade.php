@extends('layouts.nav')
@section('title', $board->board_name . "'s Activity")
@section('mainContent')
    <div class="container py-4">
        <h3 class="fs-2 fw-bold text-center mb-4">Activity Of {{ $board->board_name }}</h3>

        <form method="GET" action="{{ route('boards.activity', $board->id) }}"
            class="mb-3 d-flex flex-column flex-md-row gap-2">
            <input type="text" name="searchLogs" value="{{ $searchData }}" class="form-control"
                placeholder="Search activity">
            <button class="btn btn-primary" type="submit">Search</button>
            <a href="{{ route('boards.activity', $board->id) }}" class="btn btn-secondary">Refresh</a>
        </form>

        <div class="list-group">
            @forelse($logs as $log)
                <div class="list-group-item">
                    <div>
                        <strong>{{ $log->user->name }}</strong> - {{ $log->details }}
                    </div>
                    <small class="text-muted">{{ $log->created_at->format('d M Y, H:i') }}</small>
                </div>
            @empty
                <div class="list-group-item d-flex align-items-center justify-content-center" style="min-height: 75vh;">
                    <p class="fs-6 text-muted">No Logs Found...</p>
                </div>
            @endforelse
        </div>
        <div class="mt-3">
            {{ $logs->links() }}
        </div>
        <div class="mt-3 text-end">
            <a href="{{ route('board.access', $board->id)}}" class="btn btn-secondary">Return</a>
        </div>
    </div>
@endsection
