@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/attendance-detail.css') }}">
@endsection

@section('livewire')
@livewireStyles
@endsection

@section('navigation')
@include('components.admin-navigation')
@endsection

@section('content')
<div class="content-container">
    <div class="section-header">
        <h1 class="section-title">勤怠詳細</h1>
    </div>

    <table class="attendance-details">
        <tr class="detail-row">
            <th class="label">名前</th>
            <td class="value">{{ $attendance_correct_request->user->name }}</td>
        </tr>
        <tr class="detail-row">
            <th class="label">日付</th>
            <td class="value">{{ Carbon\Carbon::parse($attendance_correct_request->date)->format('Y') }}年</td>
            <td class="separator"></td>
            <td class="value">{{ Carbon\Carbon::parse($attendance_correct_request->date)->format('m月d日') }}</td>
            <td class="double-separator"></td>
        </tr>
        <tr class="detail-row">
            <th class="label">出勤・退勤</th>
            <td class="value"><input type="text" name="startWork" value="{{ $attendance_correct_request->corrections->where('stamp_type', '出勤')->first()->corrected_at->format('H:i') }}" disabled class="time-input">
            </td>
            <td class="separator">～</td>
            <td class="value"><input type="text" name="endWork" value="{{ $attendance_correct_request->corrections->where('stamp_type', '退勤')->first()->corrected_at->format('H:i') }}" disabled class="time-input">
            </td>
        </tr>

        @foreach($attendance_correct_request->corrections->whereIn('stamp_type', ['休憩入', '休憩戻']) as $key => $break)
        @if($key % 2 === 0)
        <tr class="detail-row">
            <th class="label">休憩{{ $key === 2 ? '': $key - 2 }}</th>
            <td class="value">
                <input type="text" name="breakStart[]" value="{{ $break->corrected_at->format('H:i') }}" disabled class="time-input">
            </td>
            @else
            <td class="separator">～</td>
            <td class="value">
                <input type="text" name="breakEnd[]" value="{{ $break->corrected_at->format('H:i') }}" disabled class="time-input">
            </td>
        </tr>
        @endif
        @endforeach
        <tr class="detail-row">
            <th class="label">備考</th>
            <td class="notes-value" colspan="3"><textarea class="notes" name="notes" disabled>{{ $attendance_correct_request->notes }}</textarea>
            </td>
        </tr>
    </table>
    <div class="button-container">
        @livewire('approve', ['correctionRequestId' => $attendance_correct_request->id])
    </div>
    </form>
</div>

@livewireScripts

@endsection