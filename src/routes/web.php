<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\VerificationController;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');

    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');

    Route::get('/attendance/list', [AttendanceController::class, 'attendanceList'])->name('attendance.list');

    Route::get('/attendance/export', [AttendanceController::class, 'exportCsv'])->name('attendance.export');

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/admin/login', [AdminController::class, 'index'])->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login.post');

Route::get('/attendance/{id}', [AttendanceController::class, 'attendanceDetail'])->name('attendance.detail');

Route::post('/attendance/{id}', [AttendanceController::class, 'attendanceCorrect'])->name('attendance.correct');

Route::get('/stamp_correction_request/list', [AttendanceController::class, 'stampCorrectionRequestList'])->name('request.list');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/attendance/list', [AdminController::class, 'attendanceList'])->name('admin.attendance.list');

    Route::get('/admin/staff/list', [AdminController::class, 'staffList'])->name('admin.staff.list');

    Route::get('/admin/attendance/staff/{id}', [AdminController::class, 'staffAttendance'])->name('admin.attendance.staff');

    Route::get('/stamp_correction_request/approve/{attendance_correct_request}', [AdminController::class, 'stampCorrectionRequestApprove'])->name('admin.request.approve');

    Route::post('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');
});

Route::middleware('auth')->group(function () {
    Route::get('/verify/guidance', [VerificationController::class, 'show'])
        ->name('verification.notice');

    Route::get('/verify/otp', function () {
        return view('auth.verify-otp');
    })->name('verification.otp');

    Route::post('/verify/otp', [VerificationController::class, 'verify'])
        ->name('verification.verify');

    Route::post('/verify/otp/resend', [VerificationController::class, 'resend'])
        ->name('verification.resend');
});
