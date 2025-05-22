<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Stamp;
use Illuminate\Support\Facades\Auth;

class AttendanceList extends Component
{
    public $currentMonth;
    public $attendanceData = [];

    public function mount()
    {
        $this->currentMonth = Carbon::now();
        $this->loadAttendanceData();
    }

    public function loadAttendanceData()
    {
        $daysInMonth = $this->currentMonth->daysInMonth;
        $this->attendanceData = [];

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($this->currentMonth->year, $this->currentMonth->month, $i);
            $dateKey = $date->format('Y-m-d');

            $beginWork = Stamp::where('user_id', Auth::id())
                ->whereDate('stamped_at', '=', $dateKey)
                ->where('stamp_type', '出勤')
                ->first();
            $endWork = Stamp::where('user_id', Auth::id())
                ->whereDate('stamped_at', '=', $dateKey)
                ->where('stamp_type', '退勤')
                ->first();

            $breakTime = $this->getBreakTimeAttribute($dateKey);
            $totalTime = $this->getTotalTimeAttribute($dateKey) - $breakTime;

            if ($totalTime < 0) {
                $totalTime = 0;
            }

            $this->attendanceData[] = [
                'id' => $date->format('Ymd'),
                'date' => $date->format('m/d'),
                'day_of_week' => $date->locale('ja')->isoFormat('ddd'),
                'begin_work' => $beginWork ? $beginWork->stamped_at->format('H:i') : '',
                'end_work' => $endWork ? $endWork->stamped_at->format('H:i') : '',
                'break_time' => $breakTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i') : '',
                'total_time' => $totalTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($totalTime)->format('H:i') : ''
            ];
        }
    }

    public function getBreakTimeAttribute($date)
    {
        $id = Auth::id();
        $stamps = Stamp::where('user_id', $id)
            ->whereDate('stamped_at', '=', $date)
            ->whereIn('stamp_type', ['休憩入', '休憩戻'])
            ->orderBy('stamped_at', 'desc')
            ->get();

        if (count($stamps) % 2 === 1) {
            $stamps = $stamps->skip(1);
        }

        $breakTime = 0;
        $previousStamp = null;
        foreach ($stamps as $stamp) {
            if (is_null($previousStamp)) {
                $previousStamp = $stamp;
                continue;
            }

            if ($previousStamp->stamp_type === '休憩戻') {
                $breakTime += $stamp->stamped_at->diffInMinutes($previousStamp->stamped_at);
            }

            $previousStamp = $stamp;
        }
        return $breakTime;
    }

    public function getTotalTimeAttribute($date)
    {
        $id = Auth::id();
        $stamps = Stamp::where('user_id', $id)
            ->whereDate('stamped_at', '=', $date)
            ->whereIn('stamp_type', ['出勤', '退勤'])
            ->orderBy('stamped_at', 'desc')
            ->get();

        $totalTime = 0;
        $previousStamp = null;
        foreach ($stamps as $stamp) {
            if ($previousStamp) {
                $totalTime += $stamp->stamped_at->diffInMinutes($previousStamp->stamped_at);
            }
            $previousStamp = $stamp;
        }
        return $totalTime;
    }

    public function previousMonth()
    {
        $this->currentMonth = $this->currentMonth->subMonth();
        $this->loadAttendanceData();
    }

    public function nextMonth()
    {
        $this->currentMonth = $this->currentMonth->addMonth();
        $this->loadAttendanceData();
    }

    public function render()
    {
        return view('livewire.attendance-list');
    }
}
