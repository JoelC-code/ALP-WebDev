@extends('layouts.nav')
@section('title', 'Dashboard, Welcome')
@section('mainContent')
    <div class="text-center bg-color-main-one">
        <h1 class="fw-bold">Welcome, {{ strtok(auth()->user()->name, ' ') }}</h1>
        <p class="fw-bold">What are you working for today?</p>
    </div>
@endsection
