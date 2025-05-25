@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/request-list.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.navigation')
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