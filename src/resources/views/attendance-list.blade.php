@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.navigation')
@endsection

@section('content')
<div class="container">
    <h1 class="page-title">勤怠一覧</h1>
    @livewire('attendance-list')
</div>

@livewireScripts

@endsection