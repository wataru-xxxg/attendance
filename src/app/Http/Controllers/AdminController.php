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

    public function stampCorrectionRequestApprove(CorrectionRequest $attendance_correct_request)
    {
        return view('admin.request-approve', compact('attendance_correct_request'));
    }
}
