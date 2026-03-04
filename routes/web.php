<?php

use App\Http\Controllers\AttendanceController;
use App\Livewire\Attendance\MarkAttendance;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/attendance/livewire', MarkAttendance::class)->name('attendance.livewire');

Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
