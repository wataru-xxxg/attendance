@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
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
<div class="content-container">
    <div class="section-header">
        <h1 class="section-title">勤怠詳細</h1>
    </div>

    <form action="{{ route('attendance.correct', ['id' => $attendanceData['id'], 'userId' => $attendanceData['user']->id]) }}" method="post">
        @csrf
        <table class="attendance-details">
            <tr class="detail-row">
                <th class="label">名前</th>
                @if(Auth::guard('admin')->check())
                <td class="value">{{ $attendanceData['user']->name }}</td>
                @else
                <td class="value">{{ Auth::user()->name }}</td>
                @endif
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
                <td class="value"><input type="text" name="startWork" value="@if(old('startWork')){{ old('startWork') }}@else{{ $attendanceData['startWork'] }}@endif" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('startWork')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('endWork') && !$errors->has('startWork'))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                <td class="value"><input type="text" name="endWork" value="@if(old('endWork')){{ old('endWork') }}@else{{ $attendanceData['endWork'] }}@endif" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('endWork')
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('startWork') && !$errors->has('endWork'))
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
                    <input type="text" name="breakStart[]" value="@if(old('breakStart.'.$key)){{ old('breakStart.'.$key) }}@else{{ $break[0] }}@endif" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('breakStart.'.$key)
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('breakEnd.'.$key) && !$errors->has('breakStart.'.$key))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                @if(isset($break[1]))
                <td class="value">
                    <input type="text" name="breakEnd[]" value="@if(old('breakEnd.'.$key)){{ old('breakEnd.'.$key) }}@else{{ $break[1] }}@endif" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                    @error('breakEnd.'.$key)
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('breakStart.'.$key) && !$errors->has('breakEnd.'.$key))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                @else
                <td class="value">
                    <input type="text" name="breakEnd[]" value="" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif class="time-input">
                </td>
                @endif
            </tr>
            @endforeach
            @if(!($attendanceData['requestExists']) || $attendanceData['approved'])
            <tr class="detail-row">
                <th class="label">休憩@if(count($attendanceData['break']) > 0)
                    {{ count($attendanceData['break']) + 1 }}
                    @endif
                </th>
                <td class="value">
                    <input type="text" name="breakStart[]" value="@if(old('breakStart.'.count($attendanceData['break']))){{ old('breakStart.'.count($attendanceData['break'])) }}@endif" class="time-input">
                    @error('breakStart.'.count($attendanceData['break']))
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('breakEnd.'.count($attendanceData['break'])) && !$errors->has('breakStart.'.count($attendanceData['break'])))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
                <td class="separator">～</td>
                <td class="value">
                    <input type="text" name="breakEnd[]" value="@if(old('breakEnd.'.count($attendanceData['break']))){{ old('breakEnd.'.count($attendanceData['break'])) }}@endif" class="time-input">
                    @error('breakEnd.'.count($attendanceData['break']))
                    <div class="time-error">
                        {{ $message }}
                    </div>
                    @enderror
                    @if($errors->has('breakStart.'.count($attendanceData['break'])) && !$errors->has('breakEnd.'.count($attendanceData['break'])))
                    <div class="time-error">
                    </div>
                    @endif
                </td>
            </tr>
            @endif
            <tr class="detail-row">
                <th class="label">備考</th>
                <td class="notes-value" colspan="3"><textarea class="notes" name="notes" @if($attendanceData['requestExists'] && !$attendanceData['approved']) disabled @endif>@if(old('notes')){{ old('notes') }}@else{{ $attendanceData['notes'] }}@endif</textarea>
                    @error('notes')
                    <div class="notes-error">
                        {{ $message }}
                    </div>
                    @enderror
                </td>
            </tr>
        </table>
        @if($attendanceData['requestExists'] && !$attendanceData['approved'])
        <p class="caution-message">*承認待ちのため修正はできません。</p>
        @else
        <div class="button-container">
            <button class="edit-button">修正</button>
        </div>
        @endif

    </form>
</div>

@livewireScripts

@endsection