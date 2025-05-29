@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-list.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.admin-navigation')
@endsection

@section('content')
<div class="container">
    <h1 class="page-title">{{ $user->name }}さんの勤怠</h1>
    @livewire('attendance-list', ['userId' => $user->id])
</div>

@livewireScripts

@endsection