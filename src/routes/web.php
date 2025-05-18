<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AttendanceController;

Route::middleware('auth')->group(function () {
    Route::get('/', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('attendance.store');
});

Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::post('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');

Route::get('/admin/attendance/list', function () {
    return view('admin.attendance-list');
})->middleware('auth:admin');

Route::post('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');
