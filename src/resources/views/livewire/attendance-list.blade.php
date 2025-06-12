<div>
    <div class="month-selector">
        <button wire:click="previousMonth" class="month-nav prev"><img src="{{ asset('images/arrow.png') }}" alt="前月" class="arrow-icon">
            前月</button>
        <div class="current-month">
            <img src="{{ asset('images/calendar-icon.png') }}" alt="カレンダー" class="calendar-icon">
            {{ $currentMonth->format('Y/m') }}
        </div>
        <button wire:click="nextMonth" class="month-nav next">翌月
            <img src="{{ asset('images/arrow.png') }}" alt="翌月" class="arrow-icon rotate-180"></button>
    </div>

    <div class="attendance-table-wrapper">
        <table class="attendance-table">
            <thead>
                <tr>
                    <th class="attendance-table-header">日付</th>
                    <th class="attendance-table-header">出勤</th>
                    <th class="attendance-table-header">退勤</th>
                    <th class="attendance-table-header">休憩</th>
                    <th class="attendance-table-header">合計</th>
                    <th class="attendance-table-header">詳細</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attendanceData as $data)
                <tr>
                    <td class="attendance-table-data">{{ $data['date'] }}({{ $data['dayOfWeek'] }})</td>
                    <td class="attendance-table-data">{{ $data['beginWork'] }}</td>
                    <td class="attendance-table-data">{{ $data['endWork'] }}</td>
                    <td class="attendance-table-data">{{ $data['breakTime'] }}</td>
                    <td class="attendance-table-data">{{ $data['totalTime'] }}</td>
                    @if(Auth::guard('admin')->check())
                    <td class="attendance-table-data"><a href="{{ route('attendance.detail', ['id' => $data['id'], 'userId' => $userId]) }}"
                            class="detail-btn">詳細</a></td>
                    @else
                    <td class="attendance-table-data"><a href="{{ route('attendance.detail', ['id' => $data['id']]) }}"
                            class="detail-btn">詳細</a></td>
                    @endif
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(Auth::guard('admin')->check())
    <button wire:click="exportCsv" class="export-btn">CSV出力</button>
    @endif
</div>