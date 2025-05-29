@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request-list.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@if(Auth::guard('admin')->check())
@include('components.admin-navigation')
@else
@include('components.navigation')
@endif
@endsection

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">申請一覧</h1>
    </div>

    @livewire('request-list')
</div>

@livewireScripts
@endsection