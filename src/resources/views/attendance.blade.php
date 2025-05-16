@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
@endsection

@section('navigation')
@include('components.navigation')
@endsection

@section('content')
<div class="container">
    <div class="status-badge">勤務外</div>
    <h2 class="date-display">2023年6月1日(木)</h2>
    <div class="time-display">08:00</div>
    <button class="attendance-button">出勤</button>
</div>
@endsection