@extends('layouts.nav')
@section('title', 'Make a board')
@section('mainContent')
    <div class="board-layout d-flex flex-row">

        {{-- Sidebar --}}
        <aside id="sidebar" class="p-3 border-end bg-light">
            <h5 class="mb-3">Inbox</h5>

            <div class="inbox-form mb-3 d-flex flex-row gap-1">
                <input type="text" class="form-control w-75" placeholder="inbox name">
                <button class="btn btn-primary">Add</button>
            </div>

            <div class="inbox-list">
                No inbox
            </div>
        </aside>

        {{-- Board --}}
        <main id="boardContent" class="p-4">
            <livewire:board.board-rename :board="$board" :key="'board-renamed-' . $board->id . '-' . $board->updated_at" />
            @push('scripts')
            <script>
                window.boardId = {{ $board->id }};
            </script>
            @endpush
            <livewire:boardlist.list-view :board="$board" />
        </main>
    </div>

@endsection
