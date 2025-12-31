@extends('layouts.nav')
@section('title', "$code - $message")
@section('mainContent')
    <div class="container-fluid d-flex flex-column align-items-center justify-content-center" style="min-height: 75vh">
        <div class="d-flex flex-column align-items-center mb-5">
            <p class="fw-bold" style="font-size: 2em">{{ $code }}</p>
            <p style="font-size: 1.2em">{{ $message }}</p>
        </div>
        <div class="d-flex flex-column align-items-center w-100">
            <a class="btn btn-primary w-50 mb-2" href="/login">Login</a>
            <a class="btn btn-secondary w-50" href="/">Return</a>
        </div>
    </div>
@endsection
