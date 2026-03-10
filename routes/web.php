<?php

use App\Actions\Admin\UpdateManagedUserProfile;
use App\Actions\Admin\UpdateManagedUserStatus;
use App\Http\Controllers\AttendanceController;
use App\Livewire\Auth\ApplicationRejected;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\SetUpProfile;
use App\Livewire\Auth\SignUp;
use App\Livewire\Auth\WaitingForApproval;
use App\Models\User;
use App\Livewire\SplashScreen;
use App\Livewire\TermsAndConditions;
use App\Support\AdminDashboardStats;
use App\Support\UserDashboardStats;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\Rule;

// Web routes for onboarding, auth, student dashboard pages, admin pages, and attendance endpoints.


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

// Holding pages for students whose accounts are not yet approved or were rejected.
Route::middleware('ojt.user')->group(function (): void {
    Route::get('/waiting-approval', WaitingForApproval::class)->name('waiting-approval');
    Route::get('/application-rejected', ApplicationRejected::class)->name('application-rejected');
});

// Admin-only area.
Route::middleware(['ojt.user', 'admin'])->group(function (): void {
    Route::get('/admin', function (Request $request, AdminDashboardStats $stats) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $dashboardStats = $stats->overview();

        // The admin dashboard now renders from the dedicated admin page namespace.
        return view('pages.admin.dashboard.home', [
            'currentAdminUser' => $user->toArray(),
            ...$dashboardStats,
        ]);
    })->name('admin.dashboard');

    Route::get('/admin/student-approvals', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.dashboard.student-approvals', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.student-approvals');

    Route::get('/admin/users', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.users.index', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.users.index');

    Route::get('/admin/users/{managedUser}', function (Request $request, User $managedUser) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $managedUser->load('approvedBy');

        return view('pages.admin.users.show', [
            'currentAdminUser' => $user->toArray(),
            'managedUser' => $managedUser,
        ]);
    })->name('admin.users.show');

    Route::get('/admin/users/{managedUser}/edit', function (Request $request, User $managedUser) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.users.edit', [
            'currentAdminUser' => $user->toArray(),
            'managedUser' => $managedUser,
        ]);
    })->name('admin.users.edit');

    Route::patch('/admin/users/{managedUser}', function (Request $request, User $managedUser, UpdateManagedUserProfile $updateManagedUserProfile) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'username' => ['nullable', 'string', 'max:50', Rule::unique('users', 'username')->ignore($managedUser->id)],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($managedUser->id)],
            'role' => ['required', Rule::in(['admin', 'student'])],
            'first_name' => ['nullable', 'string', 'max:30'],
            'middle_name' => ['nullable', 'string', 'max:30'],
            'last_name' => ['nullable', 'string', 'max:30'],
            'gender' => ['nullable', Rule::in(['Male', 'Female', 'Other'])],
            'date_of_birth' => ['nullable', 'date'],
            'contact_number' => ['nullable', 'string', 'max:11'],
            'school_attended' => ['nullable', 'string', 'max:255'],
            'course' => ['nullable', 'string', 'max:255'],
            'number_of_hours' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'province' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'street_house_number' => ['nullable', 'string', 'max:255'],
            'admin_notes' => ['nullable', 'string'],
            'profile_completed' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updateManagedUserProfile->execute($managedUser, [
            'username' => $validated['username'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'first_name' => $validated['first_name'] ?? null,
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'date_of_birth' => $validated['date_of_birth'] ?? null,
            'contact_number' => $validated['contact_number'] ?? null,
            'school_attended' => $validated['school_attended'] ?? null,
            'course' => $validated['course'] ?? null,
            'number_of_hours' => (int) ($validated['number_of_hours'] ?? 0),
            'address' => [
                'province' => $validated['province'] ?? null,
                'municipality' => $validated['municipality'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
                'street_house_number' => $validated['street_house_number'] ?? null,
            ],
            'admin_notes' => $validated['admin_notes'] ?? null,
            'profile_completed' => $request->boolean('profile_completed'),
            'is_active' => $request->boolean('is_active'),
        ], $user);

        return redirect()
            ->route('admin.users.show', $managedUser)
            ->with('admin_notice', 'User details updated successfully.');
    })->name('admin.users.update');

    Route::patch('/admin/users/{managedUser}/status', function (Request $request, User $managedUser, UpdateManagedUserStatus $updateManagedUserStatus) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
            'admin_notes' => ['nullable', 'string'],
        ]);

        $updateManagedUserStatus->execute(
            managedUser: $managedUser,
            status: $validated['status'],
            adminNotes: $validated['admin_notes'] ?? null,
            actor: $user,
        );

        return redirect()
            ->route('admin.users.show', $managedUser)
            ->with('admin_notice', 'User status updated successfully.');
    })->name('admin.users.status');
});

// Shared logout route used by both student and admin sidebars.
Route::middleware('ojt.user')->group(function (): void {
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});


// Student dashboard area.
// These pages require a logged-in user, a finished profile, and admin approval.
Route::middleware(['ojt.user', 'profile.completed', 'approved.student'])->group(function (): void {
    // Student dashboard home.
    Route::get('/home', function (Request $request, UserDashboardStats $stats) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        // This service prepares the summary cards and activity list shown on the student home screen.
        $dashboardStats = $stats->forUser($user);

        return view('pages.student.dashboard.home', [
            'currentOjtUser' => $user->toArray(),
            ...$dashboardStats,
        ]);
    })->name('home');

    // Student account settings page.
    Route::get('/account/settings', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.student.dashboard.account-settings', ['currentOjtUser' => $user->toArray()]);
    })->name('account.settings');

    // Student monthly DTR page.
    Route::get('/monthly-dtr', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.student.dashboard.monthly-dtr', ['currentOjtUser' => $user->toArray()]);
    })->name('monthly.dtr');

    // Student terms page inside the dashboard shell.
    // This keeps the sidebar visible while the user is logged in.
    Route::get('/terms/dashboard', function (Request $request) {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.student.dashboard.terms', ['currentOjtUser' => $user->toArray()]);
    })->name('terms.dashboard');
});


Route::post('/attendance/mark', [AttendanceController::class, 'mark']);
