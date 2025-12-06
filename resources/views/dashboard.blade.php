@extends('layouts.nav')
@section('title', 'Dashboard, Welcome')
@section('mainContent')
    <div class="container text-center bg-color-main-one">
        <h1 class="fw-bold">Welcome, {{ strtok(auth()->user()->name, ' ') }}</h1>
        <p>What are you working for today?</p>
    </div>

    <livewire:board.board-list />
@endsection