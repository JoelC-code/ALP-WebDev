@extends('layouts.nav')
@section('title', 'Dashboard, Welcome')
@section('mainContent')
    <div class="container text-center bg-color-main-one">
        <h1 class="fw-bold fs-3">Welcome, {{ strtok(auth()->user()->name, ' ') }}</h1>
        <p class="fs-6">What are you working for today?</p>
    </div>

    <livewire:board.board-list />
@endsection