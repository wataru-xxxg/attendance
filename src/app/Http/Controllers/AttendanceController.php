<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stamp;
use Illuminate\Support\Facades\Auth;

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
}
