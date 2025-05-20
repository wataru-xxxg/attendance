<div>
    <div class="month-selector">
        <button wire:click="previousMonth" class="month-nav prev">
            <img src="{{ asset('images/arrow-left.png') }}" alt="前月" class="arrow-icon rotate-180">
            前月
        </button>
        <div class="current-month">
            <img src="{{ asset('images/calendar-icon.png') }}" alt="カレンダー" class="calendar-icon">
            {{ $currentMonth->format('Y/m') }}
        </div>
        <button wire:click="nextMonth" class="month-nav next">
            翌月
            <img src="{{ asset('images/arrow-right.png') }}" alt="翌月" class="arrow-icon">
        </button>
    </div>

    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th>日付</th>
                    <th>出勤</th>
                    <th>退勤</th>
                    <th>休憩</th>
                    <th>合計</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                <tr>
                    <td>{{ $data['date'] }}({{ $data['day_of_week'] }})</td>
                    <td>{{ $data['clock_in'] }}</td>
                    <td>{{ $data['clock_out'] }}</td>
                    <td>{{ $data['break_time'] }}</td>
                    <td>{{ $data['total_time'] }}</td>
                    <td><a href="#" class="detail-btn">詳細</a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>