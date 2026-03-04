<?php

use App\Http\Controllers\AttendanceController;
use App\Livewire\Attendance\MarkAttendance;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\SetUpProfile;
use App\Livewire\Auth\SignUp;
use App\Livewire\SplashScreen;
use App\Livewire\TermsAndConditions;
use Illuminate\Support\Facades\Route;

// Option A flow: Splash → Terms → Login / Sign Up
Route::get('/', SplashScreen::class)->name('splash');
Route::get('/terms', TermsAndConditions::class)->name('terms');
Route::get('/login', Login::class)->name('login');
Route::get('/signup', SignUp::class)->name('signup');
Route::get('/profile/setup', SetUpProfile::class)->name('profile.setup');

// Home (previous welcome)
Route::get('/home', function () {
    return view('welcome');
})->name('home');

Route::get('/attendance/livewire', MarkAttendance::class)->name('attendance.livewire');

Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
