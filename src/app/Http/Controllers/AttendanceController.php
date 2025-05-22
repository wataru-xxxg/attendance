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
        $startWork = Stamp::where('user_id', Auth::id())
            ->whereDate('stamped_at', '=', $date)
            ->where('stamp_type', '出勤')
            ->first();
        $endWork = Stamp::where('user_id', Auth::id())
            ->whereDate('stamped_at', '=', $date)
            ->where('stamp_type', '退勤')
            ->first();

        $break = [];
        Stamp::where('user_id', Auth::id())
            ->whereDate('stamped_at', '=', $date)
            ->whereIn('stamp_type', ['休憩入', '休憩戻'])
            ->orderBy('stamped_at', 'asc')
            ->chunk(2, function ($stamps) use (&$break) {
                $breakArray = [];

                foreach ($stamps as $stamp) {
                    $breakArray[] = $stamp->stamped_at;
                }

                $break[] = $breakArray;
            });

        $attendanceData = [
            'id' => $id,
            'year' => Carbon::parse($id)->format('Y年'),
            'date' => Carbon::parse($id)->format('m月d日'),
            'start_work' => $startWork ? $startWork->stamped_at->format('H:i') : '',
            'end_work' => $endWork ? $endWork->stamped_at->format('H:i') : '',
            'break' => $break
        ];

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
            'corrected_time' => $request->start_work,
        ]);
        Correction::create([
            'user_id' => $userId,
            'request_id' => $correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_time' => $request->end_work,
        ]);

        $breakStart = $request->break_start;
        $breakEnd = $request->break_end;

        foreach ($breakStart as $key => $value) {
            Correction::create([
                'user_id' => $userId,
                'request_id' => $correctionRequest->id,
                'stamp_type' => '休憩入',
                'corrected_time' => $breakStart[$key],
            ]);
        }
        foreach ($breakEnd as $key => $value) {
            Correction::create([
                'user_id' => $userId,
                'request_id' => $correctionRequest->id,
                'stamp_type' => '休憩戻',
                'corrected_time' => $breakEnd[$key],
            ]);
        }
        return redirect()->route('attendance.index');
    }
}
