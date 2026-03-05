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
Route::get('/profile/setup', SetUpProfile::class)->name('profile.setup')->middleware('ojt.user');

// Home (dashboard) — requires OJT user in session
Route::get('/home', function () {
    $storage = app(\App\Services\OjtUserStorage::class);
    $user = $storage->findById(session('ojt_user_id'));
    if (!$user) {
        return redirect()->route('login');
    }
    $requiredHours = (int) ($user['required_hours'] ?? 0);
    $activityLogs = $user['activity_logs'] ?? [];
    $completedHours = (int) ($user['completed_hours'] ?? 0);
    $progressPercent = $requiredHours > 0 ? min(100, (int) round(($completedHours / $requiredHours) * 100)) : 0;
    $remainingHours = max(0, $requiredHours - $completedHours);
    return view('dashboard.home', [
        'currentOjtUser' => $user,
        'progressPercent' => $progressPercent,
        'remainingHours' => $remainingHours,
        'requiredHours' => $requiredHours,
        'activityLogs' => $activityLogs,
        'hoursThisDay' => (int) ($user['hours_this_day'] ?? 0),
        'hoursThisWeek' => (int) ($user['hours_this_week'] ?? 0),
        'hoursThisMonth' => (int) ($user['hours_this_month'] ?? 0),
    ]);
})->name('home')->middleware('ojt.user');

// Account Settings (dashboard)
Route::get('/account/settings', function () {
    $storage = app(\App\Services\OjtUserStorage::class);
    $user = $storage->findById(session('ojt_user_id'));
    if (!$user) {
        return redirect()->route('login');
    }
    return view('dashboard.account-settings', ['currentOjtUser' => $user]);
})->name('account.settings')->middleware('ojt.user');

// My Monthly DTR (dashboard)
Route::get('/monthly-dtr', function () {
    $storage = app(\App\Services\OjtUserStorage::class);
    $user = $storage->findById(session('ojt_user_id'));
    if (!$user) {
        return redirect()->route('login');
    }
    return view('dashboard.monthly-dtr', ['currentOjtUser' => $user]);
})->name('monthly.dtr')->middleware('ojt.user');

// Terms and Conditions (dashboard — when logged in, sidebar stays)
Route::get('/terms/dashboard', function () {
    $storage = app(\App\Services\OjtUserStorage::class);
    $user = $storage->findById(session('ojt_user_id'));
    if (!$user) {
        return redirect()->route('login');
    }
    return view('dashboard.terms', ['currentOjtUser' => $user]);
})->name('terms.dashboard')->middleware('ojt.user');

// Log out (clear session and redirect to login)
Route::post('/logout', function () {
    session()->forget('ojt_user_id');
    return redirect()->route('login');
})->name('logout')->middleware('ojt.user');

Route::get('/attendance/livewire', MarkAttendance::class)->name('attendance.livewire');

Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
