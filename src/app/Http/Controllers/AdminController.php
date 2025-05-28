<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\CorrectionRequest;
use App\Models\Stamp;
use App\Models\Correction;
use App\Http\Requests\CorrectRequest;
use App\Models\User;

class AdminController extends Controller
{
    public function adminLogin(LoginRequest $request)
    {
        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];
        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('admin.attendance.list');
        }

        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません.',
        ]);
    }

    public function adminLogout(Request $request)
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/admin/login');
    }

    public function attendanceDetail(Request $request, $id)
    {
        $date = Carbon::parse($id)->format('Y-m-d');
        $userId = $request->userId;
        $user = User::find($userId);
        $correctionRequest = CorrectionRequest::where('user_id', $userId)
            ->where('date', $date)
            ->orderBy('id', 'desc')
            ->first();

        $attendanceData = [
            'id' => $id,
            'user' => $user,
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

        $attendanceData['admin'] = true;

        return view('attendance-detail', compact('attendanceData'));
    }

    public function attendanceCorrect(CorrectRequest $request, $id)
    {
        $userId = $request->userId;
        $date = Carbon::parse($id)->format('Y-m-d');
        $correctionRequest = CorrectionRequest::create([
            'user_id' => $userId,
            'date' => $date,
            'approved' => true,
            'notes' => $request->notes,
        ]);

        Correction::create([
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '出勤',
            'corrected_at' => Carbon::parse($request->startWork),
        ]);
        Correction::create([
            'correction_request_id' => $correctionRequest->id,
            'stamp_type' => '退勤',
            'corrected_at' => Carbon::parse($request->endWork),
        ]);

        $breakStart = $request->breakStart;
        $breakEnd = $request->breakEnd;

        foreach ($breakStart as $key => $value) {
            Correction::create([
                'correction_request_id' => $correctionRequest->id,
                'stamp_type' => '休憩入',
                'corrected_at' => Carbon::parse($breakStart[$key]),
            ]);
        }
        foreach ($breakEnd as $key => $value) {
            Correction::create([
                'correction_request_id' => $correctionRequest->id,
                'stamp_type' => '休憩戻',
                'corrected_at' => Carbon::parse($breakEnd[$key]),
            ]);
        }
        return redirect()->route('admin.attendance.list');
    }

    public function staffList()
    {
        $users = User::all();
        return view('admin.staff-list', compact('users'));
    }

    public function staffAttendance($id)
    {
        $user = User::find($id);
        return view('admin.staff-attendance', compact('user'));
    }

    public function stampCorrectionRequestList()
    {
        $correctionRequests = CorrectionRequest::where('approved', 0)->get();
        return view('request-list', compact('correctionRequests'));
    }
}
