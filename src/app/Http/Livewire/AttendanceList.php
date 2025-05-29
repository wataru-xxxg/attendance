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
    public $userId;

    public function mount($userId)
    {
        $this->userId = $userId;
        $this->currentMonth = Carbon::now();
        $this->loadAttendanceData();
    }

    public function loadAttendanceData()
    {
        $daysInMonth = $this->currentMonth->daysInMonth;
        $this->attendanceData = [];
        $userId = $this->userId;

        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = Carbon::create($this->currentMonth->year, $this->currentMonth->month, $i);
            $dateKey = $date->format('Y-m-d');

            $correctionRequest = CorrectionRequest::where('user_id', $userId)
                ->where('approved', true)
                ->where('date', $dateKey)
                ->orderBy('id', 'desc')
                ->first();

            if ($correctionRequest) {
                $beginWorkCorrection = $correctionRequest->corrections->where('stamp_type', '出勤')->first();
                $beginWork = $beginWorkCorrection->corrected_at->format('H:i');

                $endWorkCorrection = $correctionRequest->corrections->where('stamp_type', '退勤')->first();
                $endWork = $endWorkCorrection->corrected_at->format('H:i');

                $corrections = Correction::where('correction_request_id', $correctionRequest->id)->orderBy('corrected_at', 'desc')->get();

                $breakTime = $this->getCorrectedBreakTimeAttribute($corrections);
                $totalTime = $this->getCorrectedTotalTimeAttribute($beginWorkCorrection, $endWorkCorrection) - $breakTime;
            } else {
                $beginWorkStamp = Stamp::where('user_id', $userId)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '出勤')
                    ->first();
                $beginWork = $beginWorkStamp ? $beginWorkStamp->stamped_at->format('H:i') : '';

                $endWorkStamp = Stamp::where('user_id', $userId)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '退勤')
                    ->first();
                $endWork = $endWorkStamp ? $endWorkStamp->stamped_at->format('H:i') : '';

                $stamps = Stamp::where('user_id', $userId)
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
                'dayOfWeek' => $date->locale('ja')->isoFormat('ddd'),
                'beginWork' => $beginWork,
                'endWork' => $endWork,
                'breakTime' => $breakTime === 0 ? '' : Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i'),
                'totalTime' => $totalTime === 0 ? '' : Carbon::createFromTime(0, 0, 0)->addMinutes($totalTime)->format('H:i'),
                'user_id' => $userId
            ];
        }
    }

    public function getBreakTimeAttribute($stamps)
    {
        if ($stamps->isEmpty()) {
            return 0;
        }

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
        if ($corrections->isEmpty()) {
            return 0;
        }

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
