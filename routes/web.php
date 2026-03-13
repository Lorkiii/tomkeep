<?php

use App\Actions\Admin\CreateManagedSite;
use App\Actions\Admin\UpdateManagedUserProfile;
use App\Actions\Admin\UpdateManagedSite;
use App\Actions\Admin\UpdateManagedSiteState;
use App\Actions\Admin\UpdateManagedUserStatus;
use App\Http\Controllers\Admin\AttendanceReportExportController;
use App\Http\Controllers\AttendanceController;
use App\Livewire\Admin\AttendanceReportsTable;
use App\Livewire\Admin\TodayAttendanceTable;
use App\Livewire\Auth\ApplicationRejected;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\SetUpProfile;
use App\Livewire\Auth\SignUp;
use App\Livewire\Auth\WaitingForApproval;
use App\Models\Site;
use App\Models\User;
use App\Livewire\SplashScreen;
use App\Livewire\TermsAndConditions;
use App\Support\AdminDashboardStats;
use App\Support\AttendancePolicy;
use App\Support\SiteLocationData;
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

    // Phase 5: Admin Attendance Monitoring Routes
    // Today's attendance view - real-time observation of current day's attendance status
    Route::get('/admin/attendance/today', function (Request $request) {
        $user = $request->user();

        // Redirect unauthenticated users to login
        if (! $user) {
            return redirect()->route('login');
        }

        // Render today's attendance page with admin context
        return view('pages.admin.attendance.today', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.attendance.today');

    // Reports page - period-based analysis with filtering, charting, and export
    Route::get('/admin/attendance/reports', function (Request $request) {
        $user = $request->user();

        // Redirect unauthenticated users to login
        if (! $user) {
            return redirect()->route('login');
        }

        // Render reports page with admin context
        return view('pages.admin.attendance.reports', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.attendance.reports');

    Route::get('/admin/settings/attendance', function (Request $request, AttendancePolicy $attendancePolicy) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.settings.attendance', [
            'currentAdminUser' => $user->toArray(),
            'wfhAnchorLimit' => $attendancePolicy->wfhAnchorLimitMeters(),
        ]);
    })->name('admin.settings.attendance.edit');

    Route::patch('/admin/settings/attendance', function (Request $request, AttendancePolicy $attendancePolicy) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'wfh_anchor_limit_m' => ['required', 'integer', 'min:1', 'max:5000'],
        ]);

        $attendancePolicy->updateWfhAnchorLimitMeters((int) $validated['wfh_anchor_limit_m'], $user);

        return redirect()
            ->route('admin.settings.attendance.edit')
            ->with('admin_notice', 'Attendance policy updated successfully.');
    })->name('admin.settings.attendance.update');

    // CSV export endpoint - streams filtered attendance records as downloadable file
    Route::get('/admin/attendance/reports/export', AttendanceReportExportController::class)
        ->name('admin.attendance.reports.export');

    Route::get('/admin/users', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.users.index', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.users.index');

    Route::get('/admin/sites', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.sites.index', [
            'currentAdminUser' => $user->toArray(),
        ]);
    })->name('admin.sites.index');

    Route::get('/admin/sites/create', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.sites.create', [
            'currentAdminUser' => $user->toArray(),
            'siteCoordinates' => ['latitude' => null, 'longitude' => null],
        ]);
    })->name('admin.sites.create');

    Route::post('/admin/sites', function (Request $request, CreateManagedSite $createManagedSite) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // The site form keeps address fields separate for readability, then stores them as one JSON structure.
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'allowed_radius_m' => ['required', 'integer', 'min:1', 'max:5000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'enforce_geofence' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $site = $createManagedSite->execute([
            'company_name' => $validated['company_name'],
            'address' => [
                'street_address' => $validated['street_address'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
                'municipality' => $validated['municipality'] ?? null,
                'province' => $validated['province'] ?? null,
            ],
            'allowed_radius_m' => (int) $validated['allowed_radius_m'],
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'enforce_geofence' => $request->boolean('enforce_geofence', true),
            'is_active' => $request->boolean('is_active', true),
        ], $user);

        return redirect()
            ->route('admin.sites.edit', $site)
            ->with('admin_notice', 'Site created successfully.');
    })->name('admin.sites.store');

    Route::get('/admin/sites/{managedSite}/edit', function (Request $request, Site $managedSite, SiteLocationData $siteLocationData) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        return view('pages.admin.sites.edit', [
            'currentAdminUser' => $user->toArray(),
            'managedSite' => $managedSite,
            'siteCoordinates' => $siteLocationData->coordinatesFor($managedSite),
        ]);
    })->name('admin.sites.edit');

    Route::patch('/admin/sites/{managedSite}', function (Request $request, Site $managedSite, UpdateManagedSite $updateManagedSite) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'street_address' => ['nullable', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:100'],
            'municipality' => ['nullable', 'string', 'max:100'],
            'province' => ['nullable', 'string', 'max:100'],
            'allowed_radius_m' => ['required', 'integer', 'min:1', 'max:5000'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'enforce_geofence' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $updateManagedSite->execute($managedSite, [
            'company_name' => $validated['company_name'],
            'address' => [
                'street_address' => $validated['street_address'] ?? null,
                'barangay' => $validated['barangay'] ?? null,
                'municipality' => $validated['municipality'] ?? null,
                'province' => $validated['province'] ?? null,
            ],
            'allowed_radius_m' => (int) $validated['allowed_radius_m'],
            'latitude' => (float) $validated['latitude'],
            'longitude' => (float) $validated['longitude'],
            'enforce_geofence' => $request->boolean('enforce_geofence', true),
            'is_active' => $request->boolean('is_active'),
        ], $user);

        return redirect()
            ->route('admin.sites.edit', $managedSite)
            ->with('admin_notice', 'Site details updated successfully.');
    })->name('admin.sites.update');

    Route::patch('/admin/sites/{managedSite}/status', function (Request $request, Site $managedSite, UpdateManagedSiteState $updateManagedSiteState) {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $updateManagedSiteState->execute(
            managedSite: $managedSite,
            isActive: (bool) $validated['is_active'],
            actor: $user,
        );

        return redirect()
            ->route('admin.sites.edit', $managedSite)
            ->with('admin_notice', 'Site status updated successfully.');
    })->name('admin.sites.status');

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
            'position' => ['nullable', 'string', 'max:120'],
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
            'position' => $validated['position'] ?? 'OJT Trainee',
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
