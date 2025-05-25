@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin/attendance-list.css') }}">
@endsection

@section('navigation')
@include('components.admin-navigation')
@endsection

@section('content')
<div class="container">
    @livewire('admin-attendance-list')
</div>

@livewireScripts

@endsection