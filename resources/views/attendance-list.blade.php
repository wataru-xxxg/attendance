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
<h1 class="page-title">勤怠一覧</h1>
<div class="container">
    @livewire('attendance-list')
</div>
@endsection

@section('scripts')
@livewireScripts
@endsection