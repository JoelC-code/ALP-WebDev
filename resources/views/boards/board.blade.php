@extends('layouts.nav')
@section('title', 'Make a board')
@section('mainContent')
<div class="board-layout" class="d-flex flex-column">
    {{-- Side for Inbox --}}
    <aside id="sidebar" class="d-flex w-25 flex-column p-3 border end bg-light">
        <h5 class="mb-3">Inbox</h5>

        <div class="inbox-form mb-3 d-flex flex-row gap-1">
            <input type="text" class="form-control w-75" placeholder="inbox name">
            <button class="btn btn-primary">Add</button>
        </div>

        <div class="inbox-list">
            No inbox
        </div>
    </aside>
    {{-- For the kanban board --}}
    <main id="boardContent" class="p-3">
        <h1 class="mb-4">Board Name</h1>

        {{-- kanban board goes here later --}}
        <p class="text-muted">Put your lists here...</p>
    </main>
</div>
@endsection