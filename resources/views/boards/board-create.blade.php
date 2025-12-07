@extends('layouts.nav')
@section('title', 'Make a board')
@section('mainContent')
    <div class="text-center bg-color-main-one">
        <h1 class="fw-bold">Make a board</h1>
        <p>Make a new board by putting the name on the input</p>
    </div>
    <livewire:board.create-board />
@endsection