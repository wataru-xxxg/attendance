<div>
    <div class="title-bar">
        <h1>{{ $currentDate->format('Y年m月d日') }}の勤怠</h1>
    </div>

    <div class="date-navigation">
        <button wire:click="previousDay" class="day-nav prev"><img src="{{ asset('images/arrow.png') }}" alt="前日" class="arrow-icon">
            前日</button>
        <div class="date">
            <img src="{{ asset('images/calendar-icon.png') }}" alt="カレンダー" class="calendar-icon">
            {{ $currentDate->format('Y/m/d') }}
        </div>
        <button wire:click="nextDay" class="day-nav next"><img src="{{ asset('images/arrow.png') }}" alt="翌日" class="arrow-icon rotate-180">
            翌日</button>
    </div>

    <div class="attendance-table">
        <table>
            <thead>
                <tr>
                    <th>名前</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $attendance)
                <tr>
                    <td>{{ $attendance['user_name'] }}</td>
                    <td>{{ $attendance['begin_work'] }}</td>
                    <td>{{ $attendance['end_work'] }}</td>
                    <td>{{ $attendance['break_time'] }}</td>
                    <td>{{ $attendance['total_time'] }}</td>
                    <td class="attendance-table-data"><a href="{{ route('attendance.detail', ['id' => $attendance['id'], 'user_id' => $attendance['user_id']]) }}"
                            class="detail-btn">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>