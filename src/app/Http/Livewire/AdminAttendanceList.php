<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Stamp;
use App\Models\User;

class AdminAttendanceList extends Component
{
    public $currentDate;
    public $attendanceData = [];

    public function mount()
    {
        $this->currentDate = Carbon::now();
        $this->loadAttendanceData();
    }

    public function loadAttendanceData()
    {
        $this->attendanceData = [];

        $date = Carbon::create($this->currentDate->year, $this->currentDate->month, $this->currentDate->day);
        $dateKey = $this->currentDate->format('Y-m-d');

        $users = User::orderBy('id', 'asc')->get();

        $stamps = Stamp::whereDate('stamped_at', '=', $dateKey)
            ->orderBy('user_id', 'asc')
            ->get();

        foreach ($users as $user) {
            $beginWork = Stamp::where('user_id', $user->id)
                ->whereDate('stamped_at', '=', $dateKey)
                ->where('stamp_type', '出勤')
                ->first();
            $endWork = Stamp::where('user_id', $user->id)
                ->whereDate('stamped_at', '=', $dateKey)
                ->where('stamp_type', '退勤')
                ->first();

            $breakTime = $this->getBreakTimeAttribute($dateKey, $user->id);
            $totalTime = $this->getTotalTimeAttribute($dateKey, $user->id) - $breakTime;

            if ($totalTime < 0) {
                $totalTime = 0;
            }

            $this->attendanceData[] = [
                'user_id' => $user->id,
                'user_name' => $user->name,
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

    public function getBreakTimeAttribute($date, $user_id)
    {
        $stamps = Stamp::where('user_id', $user_id)
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

    public function getTotalTimeAttribute($date, $user_id)
    {
        $stamps = Stamp::where('user_id', $user_id)
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

    public function previousDay()
    {
        $this->currentDate = $this->currentDate->subDay();
        $this->loadAttendanceData();
    }

    public function nextDay()
    {
        $this->currentDate = $this->currentDate->addDay();
        $this->loadAttendanceData();
    }

    public function render()
    {
        return view('livewire.admin-attendance-list');
    }
}
