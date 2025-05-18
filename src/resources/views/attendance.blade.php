@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.navigation')
@endsection

@section('content')
<div class="container">
    @if($lastStamp->exists())
    @if($lastStamp->stamp_type == '出勤')
    <div class="status-badge">出勤中</div>
    @elseif($lastStamp->stamp_type == '退勤')
    @if($lastStamp->stamped_at && $lastStamp->stamped_at->format('Y-m-d') == now()->format('Y-m-d'))
    <div class="status-badge">退勤済</div>
    @else
    <div class="status-badge">勤務外</div>
    @endif
    @elseif($lastStamp->stamp_type == '休憩入')
    <div class="status-badge">休憩中</div>
    @elseif($lastStamp->stamp_type == '休憩戻')
    <div class="status-badge">出勤中</div>
    @endif
    @else
    <div class="status-badge">勤務外</div>
    @endif
    <form class="attendance-form" action="/attendance" method="post">
        @csrf
        @livewire('realtime-clock')
        @if($lastStamp->exists())
        @if($lastStamp->stamp_type == '出勤' || $lastStamp->stamp_type == '休憩戻')
        <div class="attendance-buttons">
            <button class="finish-button" name="stamp_type" value="退勤">退勤</button>
            <button class="beginBreak-button" name="stamp_type" value="休憩入">休憩入</button>
        </div>
        @elseif($lastStamp->stamp_type == '退勤')
        @if($lastStamp->stamped_at && $lastStamp->stamped_at->format('Y-m-d') == now()->format('Y-m-d'))
        <p>お疲れさまでした。</p>
        @else
        <button class="begin-button" name="stamp_type" value="出勤">出勤</button>
        @endif
        @elseif($lastStamp->stamp_type == '休憩入')
        <button class="finishBreak-button" name="stamp_type" value="休憩戻">休憩戻</button>
        @endif
        @else
        <button class="begin-button" name="stamp_type" value="出勤">出勤</button>
        @endif
    </form>
</div>

@livewireScripts
<script src="{{ mix('js/app.js') }}" defer></script>
@endsection