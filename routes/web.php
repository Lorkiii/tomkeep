<?php

use App\Http\Controllers\AttendanceController;
use App\Livewire\Attendance\MarkAttendance;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\SetUpProfile;
use App\Livewire\Auth\SignUp;
use App\Livewire\SplashScreen;
use App\Livewire\TermsAndConditions;
use App\Support\UserDashboardStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Web routes for onboarding, auth, dashboard, and attendance endpoints.

// Option A flow: Splash → Terms → Login / Sign Up
Route::get('/', SplashScreen::class)->name('splash');
Route::get('/terms', TermsAndConditions::class)->name('terms');

// Guest-only authentication pages.
Route::middleware('guest')->group(function (): void {
    Route::get('/login', Login::class)->name('login');
    Route::get('/signup', SignUp::class)->name('signup');
});



Route::get('/profile/setup', SetUpProfile::class)
    ->name('profile.setup')
    ->middleware('ojt.user');

// Main dashboard pages require authentication and completed profile details.
Route::middleware(['ojt.user', 'profile.completed'])->group(function (): void {
    // Home (dashboard)
    Route::get('/home', function (Request $request, UserDashboardStats $stats) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $dashboardStats = $stats->forUser($user);

        return view('dashboard.home', [
            'currentOjtUser' => $user->toArray(),
            ...$dashboardStats,
        ]);
    })->name('home');

    // Account Settings (dashboard)
    Route::get('/account/settings', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('dashboard.account-settings', ['currentOjtUser' => $user->toArray()]);
    })->name('account.settings');

    // My Monthly DTR (dashboard)
    Route::get('/monthly-dtr', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('dashboard.monthly-dtr', ['currentOjtUser' => $user->toArray()]);
    })->name('monthly.dtr');

    // Terms and Conditions (dashboard — when logged in, sidebar stays)
    Route::get('/terms/dashboard', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('dashboard.terms', ['currentOjtUser' => $user->toArray()]);
    })->name('terms.dashboard');

    // Log out
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

Route::get('/attendance/livewire', MarkAttendance::class)->name('attendance.livewire');

Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
