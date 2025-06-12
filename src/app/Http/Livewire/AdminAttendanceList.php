<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\Stamp;
use App\Models\User;
use App\Models\CorrectionRequest;
use App\Models\Correction;

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

        foreach ($users as $user) {
            $correctionRequest = CorrectionRequest::where('user_id', $user->id)
                ->where('approved', true)
                ->where('date', $dateKey)
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

                $beginWorkStamp = Stamp::where('user_id', $user->id)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '出勤')
                    ->first();
                $beginWork = $beginWorkStamp ? $beginWorkStamp->stamped_at->format('H:i') : '';

                $endWorkStamp = Stamp::where('user_id', $user->id)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->where('stamp_type', '退勤')
                    ->first();
                $endWork = $endWorkStamp ? $endWorkStamp->stamped_at->format('H:i') : '';

                $stamps = Stamp::where('user_id', $user->id)
                    ->whereDate('stamped_at', '=', $dateKey)
                    ->whereIn('stamp_type', ['休憩入', '休憩戻'])
                    ->orderBy('created_at', 'desc')
                    ->get();

                $breakTime = $this->getBreakTimeAttribute($stamps);
                $totalTime = $this->getTotalTimeAttribute($beginWorkStamp, $endWorkStamp) - $breakTime;
            }

            if ($totalTime < 0) {
                $totalTime = 0;
            }

            $this->attendanceData[] = [
                'userId' => $user->id,
                'userName' => $user->name,
                'id' => $date->format('Ymd'),
                'date' => $date->format('m/d'),
                'dayOfWeek' => $date->locale('ja')->isoFormat('ddd'),
                'beginWork' => $beginWork,
                'endWork' => $endWork,
                'breakTime' => $breakTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($breakTime)->format('H:i') : '',
                'totalTime' => $totalTime ? Carbon::createFromTime(0, 0, 0)->addMinutes($totalTime)->format('H:i') : ''
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
