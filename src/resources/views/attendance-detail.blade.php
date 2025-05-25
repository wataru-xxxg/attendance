@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.navigation')
@endsection

@section('content')
<div class="content-container">
    <div class="section-header">
        <h1 class="section-title">勤怠詳細</h1>
    </div>

    <form action="/attendance/{{ $attendanceData['id'] }}" method="post">
        @csrf
        <table class="attendance-details">
            <tr class="detail-row">
                <th class="label">名前</th>
                <td class="value">{{ Auth::user()->name }}</td>
            </tr>
            <tr class="detail-row">
                <th class="label">日付</th>
                <td class="value">{{ $attendanceData['year'] }}</td>
                <td class="separator"></td>
                <td class="value">{{ $attendanceData['date'] }}</td>
                <td class="double-separator"></td>
            </tr>
            <tr class="detail-row">
                <th class="label">出勤・退勤</th>
                <td class="value"><input type="text" name="start_work" value="@if(old('start_work')){{ old('start_work') }}@else{{ $attendanceData['start_work'] }}@endif" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('start_work')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('end_work') && !$errors->has('start_work'))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                <td class="value"><input type="text" name="end_work" value="@if(old('end_work')){{ old('end_work') }}@else{{ $attendanceData['end_work'] }}@endif" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('end_work')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('start_work') && !$errors->has('end_work'))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
            </tr>

            @foreach($attendanceData['break'] as $key => $break)
            <tr class="detail-row">
                @if($key === 0)
                <th class="label">休憩</th>
                @else
                <th class="label">休憩{{ $key + 1 }}</th>
                @endif
                <td class="value">
                    <input type="text" name="break_start[]" value="@if(old('break_start.'.$key)){{ old('break_start.'.$key) }}@else{{ $break[0] }}@endif" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('break_start.'.$key)
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('break_end.'.$key) && !$errors->has('break_start.'.$key))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                @if(isset($break[1]))
                <td class="value">
                    <input type="text" name="break_end[]" value="@if(old('break_end.'.$key)){{ old('break_end.'.$key) }}@else{{ $break[1] }}@endif" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('break_end.'.$key)
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('break_start.'.$key) && !$errors->has('break_end.'.$key))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                @else
                <td class="value">
                    <input type="text" name="break_end[]" value="" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                </td>
                @endif
            </tr>
            @endforeach
            @if(!($attendanceData['request_exists']))
            <tr class="detail-row">
                <th class="label">休憩@if(count($attendanceData['break']) > 0)
                    {{ count($attendanceData['break']) + 1 }}
                    @endif
                </th>
                <td class="value">
                    <input type="text" name="break_start[]" value="@if(old('break_start.0')){{ old('break_start.0') }}@endif" class="time-input">
                    @error('break_start.0')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('break_end.0') && !$errors->has('break_start.0'))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                <td class="value">
                    <input type="text" name="break_end[]" value="@if(old('break_end.0')){{ old('break_end.0') }}@endif" class="time-input">
                    @error('break_end.0')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('break_start.0') && !$errors->has('break_end.0'))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
            </tr>
            @endif
            <tr class="detail-row">
                <th class="label">備考</th>
                <td class="notes-value" colspan="3"><textarea class="notes" name="notes" @if($attendanceData['request_exists'] && !$attendanceData['approved']) disabled @endif>@if(old('notes')){{ old('notes') }}@else{{ $attendanceData['notes'] }}@endif</textarea>
                    @error('notes')
                    <div class="notes-error">
                        {{ $message }}
                    </div>
                    @enderror
                </td>
            </tr>
        </table>

        @if($attendanceData['request_exists'] && !$attendanceData['approved'])
        <p class="caution-message">*承認待ちのため修正はできません。</p>
        @else
        <div class="button-container">
            <button class="edit-button">修正</button>
        </div>
        @endif
    </form>
</div>
@endsection