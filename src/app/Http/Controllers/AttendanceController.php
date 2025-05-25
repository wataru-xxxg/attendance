<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use App\Models\Correction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\CorrectRequest;
use App\Models\CorrectionRequest;

class AttendanceController extends Controller
{
    public function index()
    {
        $id = Auth::user()->id;
        $lastStamp = Stamp::lastStamp($id);
        return view('attendance', compact('lastStamp'));
    }

    public function store(Request $request)
    {
        $id = Auth::user()->id;

        Stamp::create([
            'user_id' => $id,
            'stamped_at' => $request->stamped_at,
            'stamp_type' => $request->stamp_type,
        ]);

        return redirect()->route('attendance.index');
    }

    public function attendanceList()
    {
        $id = Auth::user()->id;
        $stamps = Stamp::where('user_id', $id)
            ->whereMonth('stamped_at', now()->month)
            ->get();
        return view('attendance-list', compact('stamps'));
    }

    public function attendanceDetail($id)
    {
        $date = Carbon::parse($id)->format('Y-m-d');
        $correctionRequest = CorrectionRequest::where('user_id', Auth::id())
            ->where('date', $date)
            ->first();

        $attendanceData = [
            'id' => $id,
            'year' => Carbon::parse($id)->format('Y年'),
            'date' => Carbon::parse($id)->format('m月d日'),
            'notes' => '',
        ];

        $break = [];

        if ($correctionRequest) {
            $startWork = $correctionRequest->corrections()->where('stamp_type', '出勤')->first();
            $endWork = $correctionRequest->corrections()->where('stamp_type', '退勤')->first();

            $attendanceData['start_work'] = $startWork->corrected_at->format('H:i');
            $attendanceData['end_work'] = $endWork->corrected_at->format('H:i');

            $correctionRequest->corrections()->whereIn('stamp_type', ['休憩入', '休憩戻'])->orderBy('corrected_at', 'asc')->chunk(2, function ($corrections) use (&$break) {
                $breakArray = [];

                foreach ($corrections as $correction) {
                    $breakArray[] = $correction->corrected_at->format('H:i');
                }

                $break[] = $breakArray;
            });

            $attendanceData['request_exists'] = true;
            $attendanceData['approved'] = $correctionRequest->approved;
            $attendanceData['notes'] = $correctionRequest->notes;
        } else {
            $startWork = Stamp::where('user_id', Auth::id())
                ->whereDate('stamped_at', '=', $date)
                ->where('stamp_type', '出勤')
                ->first();
            $endWork = Stamp::where('user_id', Auth::id())
                ->whereDate('stamped_at', '=', $date)
                ->where('stamp_type', '退勤')
                ->first();

            $attendanceData['start_work'] = $startWork ? $startWork->stamped_at->format('H:i') : '';
            $attendanceData['end_work'] = $endWork ? $endWork->stamped_at->format('H:i') : '';

            Stamp::where('user_id', Auth::id())
                ->whereDate('stamped_at', '=', $date)
                ->whereIn('stamp_type', ['休憩入', '休憩戻'])
                ->orderBy('stamped_at', 'asc')
                ->chunk(2, function ($stamps) use (&$break) {
                    $breakArray = [];

                    foreach ($stamps as $stamp) {
                        $breakArray[] = $stamp->stamped_at->format('H:i');
                    }

                    $break[] = $breakArray;
                });

            $attendanceData['request_exists'] = false;
            $attendanceData['approved'] = false;
        }

        $attendanceData['break'] = $break;

        return view('attendance-detail', compact('attendanceData'));
    }

    public function attendanceRequest(CorrectRequest $request)
    {
        $userId = Auth::user()->id;
        $id = Carbon::parse($request->id)->format('Y-m-d');
        $correctionRequest = CorrectionRequest::create([
            'user_id' => $userId,
            'date' => $id,
            'notes' => $request->notes,
        ]);

        Correction::create([
            'user_id' => $userId,
            'request_id' => $correctionRequest->id,
            'stamp_type' => '出勤',
            'corrected_at' => Carbon::parse($request->start_work),
        ]);
        Correction::create([
            'user_id' => $userId,
            'request_id' => $correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_at' => Carbon::parse($request->end_work),
        ]);

        $breakStart = $request->break_start;
        $breakEnd = $request->break_end;

        foreach ($breakStart as $key => $value) {
            Correction::create([
                'user_id' => $userId,
                'request_id' => $correctionRequest->id,
                'stamp_type' => '休憩入',
                'corrected_at' => Carbon::parse($breakStart[$key]),
            ]);
        }
        foreach ($breakEnd as $key => $value) {
            Correction::create([
                'user_id' => $userId,
                'request_id' => $correctionRequest->id,
                'stamp_type' => '休憩戻',
                'corrected_at' => Carbon::parse($breakEnd[$key]),
            ]);
        }
        return redirect()->route('attendance.index');
    }

    public function stampCorrectionRequestList()
    {
        $userId = Auth::user()->id;
        $correctionRequests = CorrectionRequest::where('user_id', $userId)->where('approved', 0)->get();
        return view('request-list', compact('correctionRequests'));
    }
}
