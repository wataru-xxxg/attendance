<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use App\Models\Correction;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\CorrectRequest;
use App\Models\CorrectionRequest;
use App\Models\User;

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

    public function attendanceDetail(Request $request, $id)
    {
        if (Auth::guard('admin')->check()) {
            $userId = $request->userId;
        } elseif (Auth::check()) {
            $userId = Auth::id();
        } else {
            return redirect()->route('login');
        }

        $date = Carbon::parse($id)->format('Y-m-d');
        $correctionRequest = CorrectionRequest::where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('id', 'desc')
            ->first();

        $attendanceData = [
            'id' => $id,
            'user' => User::find($userId),
            'year' => Carbon::parse($id)->format('Y年'),
            'date' => Carbon::parse($id)->format('m月d日'),
            'notes' => '',
        ];

        $break = [];

        if ($correctionRequest) {
            $startWork = $correctionRequest->corrections()->where('stamp_type', '出勤')->first();
            $endWork = $correctionRequest->corrections()->where('stamp_type', '退勤')->first();

            $attendanceData['startWork'] = $startWork->corrected_at->format('H:i');
            $attendanceData['endWork'] = $endWork->corrected_at->format('H:i');

            $correctionRequest->corrections()->whereIn('stamp_type', ['休憩入', '休憩戻'])->orderBy('corrected_at', 'asc')->chunk(2, function ($corrections) use (&$break) {
                $breakArray = [];

                foreach ($corrections as $correction) {
                    $breakArray[] = $correction->corrected_at->format('H:i');
                }

                $break[] = $breakArray;
            });

            $attendanceData['requestExists'] = true;
            $attendanceData['approved'] = $correctionRequest->approved;
            $attendanceData['notes'] = $correctionRequest->notes;
        } else {
            $startWork = Stamp::where('user_id', $userId)
                ->whereDate('stamped_at', '=', $date)
                ->where('stamp_type', '出勤')
                ->first();
            $endWork = Stamp::where('user_id', $userId)
                ->whereDate('stamped_at', '=', $date)
                ->where('stamp_type', '退勤')
                ->first();

            $attendanceData['startWork'] = $startWork ? $startWork->stamped_at->format('H:i') : '';
            $attendanceData['endWork'] = $endWork ? $endWork->stamped_at->format('H:i') : '';

            Stamp::where('user_id', $userId)
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

            $attendanceData['requestExists'] = false;
            $attendanceData['approved'] = false;
        }

        $attendanceData['break'] = $break;

        $attendanceData['admin'] = false;

        return view('attendance-detail', compact('attendanceData'));
    }

    public function attendanceCorrect(CorrectRequest $request)
    {
        if (Auth::guard('admin')->check()) {
            $userId = $request->userId;

            $correctionRequestParams['user_id'] = $userId;
            $correctionRequestParams['approved'] = 1;

            $redirectRoute = redirect()->route('admin.attendance.list');
        } elseif (Auth::check()) {
            $userId = Auth::id();

            $correctionRequestParams['user_id'] = $userId;
            $correctionRequestParams['approved'] = 0;

            $redirectRoute = redirect()->route('attendance.index');
        } else {
            return redirect()->route('login');
        }

        $id = Carbon::parse($request->id)->format('Y-m-d');
        $year = Carbon::parse($request->id)->year;
        $month = Carbon::parse($request->id)->month;
        $day = Carbon::parse($request->id)->day;

        $startWork = $request->startWork;
        $startWorkHour = Carbon::parse($startWork)->hour;
        $startWorkMinute = Carbon::parse($startWork)->minute;
        $endWork = $request->endWork;
        $endWorkHour = Carbon::parse($endWork)->hour;
        $endWorkMinute = Carbon::parse($endWork)->minute;

        $correctionRequestParams = [
            'date' => $id,
            'notes' => $request->notes,
        ];

        $correctionRequest = CorrectionRequest::create($correctionRequestParams);

        Correction::create([
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '出勤',
            'corrected_at' => Carbon::create($year, $month, $day, $startWorkHour, $startWorkMinute),
        ]);
        Correction::create([
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_at' => Carbon::create($year, $month, $day, $endWorkHour, $endWorkMinute),
        ]);

        $breakStart = $request->breakStart;
        $breakEnd = $request->breakEnd;

        if (is_null($breakStart) && is_null($breakEnd)) {
            return $redirectRoute;
        }

        foreach ($breakStart as $key => $value) {
            if (is_null($breakStart[$key])) {
                continue;
            }
            $breakStartHour = Carbon::parse($breakStart[$key])->hour;
            $breakStartMinute = Carbon::parse($breakStart[$key])->minute;
            Correction::create([
                'correction_request_id' => $correctionRequest->id,
                'stamp_type' => '休憩入',
                'corrected_at' => Carbon::create($year, $month, $day, $breakStartHour, $breakStartMinute),
            ]);
        }
        foreach ($breakEnd as $key => $value) {
            if (is_null($breakEnd[$key])) {
                continue;
            }
            $breakEndHour = Carbon::parse($breakEnd[$key])->hour;
            $breakEndMinute = Carbon::parse($breakEnd[$key])->minute;
            Correction::create([
                'correction_request_id' => $correctionRequest->id,
                'stamp_type' => '休憩戻',
                'corrected_at' => Carbon::create($year, $month, $day, $breakEndHour, $breakEndMinute),
            ]);
        }
        return $redirectRoute;
    }

    public function stampCorrectionRequestList()
    {
        if (Auth::guard('admin')->check()) {
            $correctionRequests = CorrectionRequest::where('approved', 0)->get();
        } elseif (Auth::check()) {
            $userId = Auth::user()->id;
            $correctionRequests = CorrectionRequest::where('user_id', $userId)->where('approved', 0)->get();
        } else {
            return redirect()->route('login');
        }
        return view('request-list', compact('correctionRequests'));
    }
}
