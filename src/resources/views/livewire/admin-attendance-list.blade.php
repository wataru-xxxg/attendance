<div>
    <h1 class="page-title">{{ $currentDate->format('Y年m月d日') }}の勤怠</h1>
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

    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="attendance-table-header">名前</th>
                    <th class="attendance-table-header">出勤</th>
                    <th class="attendance-table-header">退勤</th>
                    <th class="attendance-table-header">休憩</th>
                    <th class="attendance-table-header">合計</th>
                    <th class="attendance-table-header">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($attendanceData as $attendance)
                <tr>
                    <td class="attendance-table-data">{{ $attendance['userName'] }}</td>
                    <td class="attendance-table-data">{{ $attendance['beginWork'] }}</td>
                    <td class="attendance-table-data">{{ $attendance['endWork'] }}</td>
                    <td class="attendance-table-data">{{ $attendance['breakTime'] }}</td>
                    <td class="attendance-table-data">{{ $attendance['totalTime'] }}</td>
                    <td class="attendance-table-data"><a href="{{ route('attendance.detail', ['id' => $attendance['id'], 'userId' => $attendance['userId']]) }}"
                            class="detail-btn">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>