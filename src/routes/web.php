<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Foundation\Http\EmailVerificationRequest;
use Illuminate\Http\Request;

Route::middleware('auth')->group(function () {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
    Route::get('/attendance/list', [AttendanceController::class, 'attendanceList'])->name('attendance.list');
    Route::get('/attendance/{id}', [AttendanceController::class, 'attendanceDetail'])->name('attendance.detail');
    Route::post('/attendance/{id}', [AttendanceController::class, 'attendanceRequest'])->name('attendance.request');
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'stampCorrectionRequestList'])->name('attendance.stampCorrectionRequestList');
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});

Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::post('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');

Route::get('/admin/attendance/list', function () {
    return view('admin.attendance-list');
})->middleware('auth:admin');

Route::post('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');

Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');
