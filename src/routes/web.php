<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;

Route::get('/', function () {
    return view('attendance');
})->middleware('auth');

Route::get('/admin/login', function () {
    return view('admin.login');
});
Route::post('/admin/login', [AdminController::class, 'adminLogin'])->name('admin.login');

Route::get('/admin/attendance/list', function () {
    return view('admin.attendance-list');
})->middleware('auth:admin');

Route::post('/admin/logout', [AdminController::class, 'adminLogout'])->name('admin.logout');
