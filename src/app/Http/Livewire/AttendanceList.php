<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Stamp;
use Illuminate\Support\Facades\Auth;
use App\Models\CorrectionRequest;
use App\Models\Correction;

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
        $id = Auth::id();

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($this->currentMonth->year, $this->currentMonth->month, $i);
            $dateKey = $date->format('Y-m-d');

            $correctionRequests = CorrectionRequest::where('user_id', $id)
                ->where('approved', true)
                ->where('date', $dateKey)
                ->first();

            if ($correctionRequests) {
                $beginWorkCorrection = $correctionRequests->corrections->where('stamp_type', '出勤')->first();
                $beginWork = $beginWorkCorrection->corrected_at->format('H:i');

                $endWorkCorrection = $correctionRequests->corrections->where('stamp_type', '退勤')->first();
                $endWork = $endWorkCorrection->corrected_at->format('H:i');

                $corrections = Correction::where('correction_request_id', $correctionRequests->id)->orderBy('corrected_at', 'desc')->get();

                $breakTime = $this->getCorrectedBreakTimeAttribute($corrections);
                $totalTime = $this->getCorrectedTotalTimeAttribute($beginWorkCorrection, $endWorkCorrection) - $breakTime;
            } else {
                $beginWorkStamp = Stamp::where('user_id', Auth::id())
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '出勤')
                    ->first();
                $beginWork = $beginWorkStamp ? $beginWorkStamp->stamped_at->format('H:i') : '';

                $endWorkStamp = Stamp::where('user_id', Auth::id())
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '退勤')
                    ->first();
                $endWork = $endWorkStamp ? $endWorkStamp->stamped_at->format('H:i') : '';

                $stamps = Stamp::where('user_id', $id)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->whereIn('stamp_type', ['休憩入', '休憩戻'])
                    ->orderBy('stamped_at', 'desc')
                    ->get();

                $breakTime = $this->getBreakTimeAttribute($stamps);
                $totalTime = $this->getTotalTimeAttribute($beginWorkStamp, $endWorkStamp) - $breakTime;
            }

            if ($totalTime < 0) {
                $totalTime = 0;
            }

            $this->attendanceData[] = [
                'id' => $date->format('Ymd'),
                'date' => $date->format('m/d'),
                'day_of_week' => $date->locale('ja')->isoFormat('ddd'),
                'begin_work' => $beginWork,
                'end_work' => $endWork,
                'break_time' => $breakTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i') : '',
                'total_time' => $totalTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($totalTime)->format('H:i') : ''
            ];
        }
    }

    public function getBreakTimeAttribute($stamps)
    {
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

    public function getTotalTimeAttribute($beginWorkStamp, $endWorkStamp)
    {
        if (is_null($beginWorkStamp) || is_null($endWorkStamp)) {
            return 0;
        }

        $totalTime = $endWorkStamp->stamped_at->diffInMinutes($beginWorkStamp->stamped_at);
        return $totalTime;
    }

    public function getCorrectedBreakTimeAttribute($corrections)
    {
        $breakTime = 0;
        $previousStamp = null;
        foreach ($corrections as $correction) {
            if (is_null($previousStamp)) {
                $previousStamp = $correction;
                continue;
            }

            if ($previousStamp->stamp_type === '休憩戻') {
                $breakTime += $correction->corrected_at->diffInMinutes($previousStamp->corrected_at);
            }

            $previousStamp = $correction;
        }
        return $breakTime;
    }

    public function getCorrectedTotalTimeAttribute($beginWorkCorrection, $endWorkCorrection)
    {
        $totalTime = $endWorkCorrection->corrected_at->diffInMinutes($beginWorkCorrection->corrected_at);

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
