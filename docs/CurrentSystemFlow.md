# Current System Flow (Testing Branch)

## Purpose of This Document
This document explains the current runtime flow after the refactor from file-based user storage (`OjtUserStorage`) to database-backed authentication and profile management.

It covers:
- What changed in architecture.
- Which files now own each responsibility.
- How each important function is used.
- How data flows from request to database to UI.

## High-Level Architecture
The system now uses Laravel's standard auth/session stack and the `users` table as the single source of truth for identity and profile data.

Core layers:
- Livewire UI layer: handles forms, validation, and navigation.
- Action layer: contains reusable business logic for registration and profile completion.
- Middleware layer: protects routes and enforces profile completion rules.
- Support/service layer: computes dashboard stats from DTR records.
- Persistence layer: MySQL/SQLite via Eloquent models and migrations.

Removed legacy approach:
- `app/Services/OjtUserStorage.php` was deleted.
- Browser `localStorage` user snapshot usage was removed from layout templates.

## Data Model (Users)
`database/migrations/0001_01_01_000000_create_users_table.php`

Key auth/profile fields used by the flow:
- `username` (nullable unique, used for login)
- `email` (unique)
- `password` (hashed by model cast)
- `first_name`, `middle_name`, `last_name`
- `gender`, `date_of_birth`
- `address` (JSON)
- `contact_number`, `school_attended`, `course`
- `number_of_hours` (required OJT hours)
- `profile_completed` (boolean gate for dashboard access)
- `status` (`pending`, `approved`, `rejected`)

`app/Models/User.php`
- `fillable` includes new profile/auth fields.
- `casts` includes:
  - `address` as array
  - `password` as hashed
  - `profile_completed` boolean
  - `number_of_hours` integer
- Existing student code auto-generation on approved students remains intact.

## Route and Middleware Flow
`routes/web.php`

Public routes:
- `GET /` -> splash
- `GET /terms` -> terms page

Guest-only routes:
- `GET /login`
- `GET /signup`

Authenticated profile setup route:
- `GET /profile/setup` with middleware `ojt.user`

Authenticated + profile-completed routes:
- `GET /home`
- `GET /account/settings`
- `GET /monthly-dtr`
- `GET /terms/dashboard`
- `POST /logout`

Attendance routes:
- `GET /attendance/livewire`
- `POST /attendance/mark`

`bootstrap/app.php`
- Middleware aliases:
  - `ojt.user` -> `EnsureOjtUser`
  - `profile.completed` -> `EnsureProfileCompleted`

`app/Http/Middleware/EnsureOjtUser.php`
- `handle()`: redirects to `login` when user is not authenticated.

`app/Http/Middleware/EnsureProfileCompleted.php`
- `handle()`: if authenticated user has `profile_completed = false`, redirects to `profile.setup`.
- Allows `profile.setup` route itself.

## Auth and Profile Flow

### 1. Sign Up Flow
Files:
- `app/Livewire/Auth/SignUp.php`
- `app/Actions/Auth/RegisterStudent.php`

How it works:
1. User submits email, username, password.
2. `SignUp::rules()` enforces format and uniqueness (`users.email`, `users.username`).
3. `SignUp::signUp()` calls `RegisterStudent::execute()`.
4. `RegisterStudent::execute()` creates a student user in a DB transaction with defaults:
   - `status = pending`
   - `profile_completed = false`
   - profile fields null/empty defaults
5. Component logs user in using `Auth::login($user)`.
6. Session is regenerated for security.
7. User is redirected to `profile.setup`.

Why this is clean/scalable:
- Livewire component remains thin.
- Creation logic is centralized in an Action class for reuse and testing.

### 2. Login Flow
File:
- `app/Livewire/Auth/Login.php`

How it works:
1. User submits username and password.
2. `Login::login()` validates required fields.
3. `Auth::attempt(['username' => ..., 'password' => ...])` authenticates against DB.
4. On success, session is regenerated.
5. Redirect target is dynamic:
   - `home` if `profile_completed = true`
   - `profile.setup` if not completed

Why this is better than old flow:
- Uses standard Laravel auth guards and session hardening.
- No custom password check against JSON file.

### 3. Profile Setup Flow
Files:
- `app/Livewire/Auth/SetUpProfile.php`
- `app/Actions/Profile/CompleteProfile.php`

How it works:
1. `mount()` loads current authenticated user and pre-fills form.
2. Form validates profile fields (name, gender, contact, hours, school, address, etc).
3. `submitProfile()` transforms DOB format (`m/d/Y` -> `Y-m-d`) and calls action.
4. `CompleteProfile::execute()` writes fields in a DB transaction and sets `profile_completed = true`.
5. Success modal appears, then user is redirected to `home`.

Field mapping note:
- UI field `required_hours` maps to DB column `number_of_hours`.

## Shared Current User in Views
File:
- `app/Providers/AppServiceProvider.php`

How it works:
- A global view composer injects `currentOjtUser` into all views.
- It uses authenticated user id (`Auth::id()`) and queries `User`.
- It returns `toArray()` because Blade views currently use array-style access (`$currentOjtUser['first_name']`).
- Includes `Schema::hasTable('users')` guard for environments/tests that boot views before migrations.

## Dashboard Stats Computation
File:
- `app/Support/UserDashboardStats.php`

Purpose:
- Compute dashboard values from `daily_time_records` instead of storing transient counters in `users`.

`forUser(User $user)` returns:
- `progressPercent`
- `remainingHours`
- `requiredHours` (from `number_of_hours`)
- `activityLogs` (latest time in/out entries)
- `hoursThisDay`, `hoursThisWeek`, `hoursThisMonth`

Implementation details:
- Reads all DTR rows for the user ordered by date desc.
- `workedSeconds()` calculates effective duration:
  - requires `time_in` and `time_out`
  - subtracts lunch break (`lunch_out` -> `lunch_in`) when available
- Converts seconds to hour counts via floor.

Used in:
- `/home` route closure in `routes/web.php`.

## Attendance API Flow
Files:
- `app/Http/Controllers/AttendanceController.php`
- `app/Services/AttendanceService.php`

How it works:
1. `POST /attendance/mark` validates request payload.
2. Controller delegates to `AttendanceService::mark()`.
3. Service runs in DB transaction with locking and enforces sequence rules:
   - `time_in`
   - `lunch_out`
   - `lunch_in`
   - `time_out`
4. Rejects invalid order and duplicate actions via `AttendanceException`.
5. Writes audit logs for each action.

## Logout Flow
File:
- `routes/web.php`

`POST /logout`:
- Calls `Auth::logout()`.
- Invalidates session.
- Regenerates CSRF token.
- Redirects to `login`.

## Terms and Splash Flow
Files:
- `app/Livewire/SplashScreen.php`
- `app/Livewire/TermsAndConditions.php`

Current behavior:
- Splash component redirects to terms.
- Terms page sets `session('terms_agreed', true)` and redirects to login.
- `terms_agreed` is currently stored but not enforced by middleware on protected routes.

## Seeder and Local Testing
Files:
- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`

What was updated:
- Seed users now include `username`, `gender`, `number_of_hours`, `profile_completed`.
- Factory now provides `username` by default.

Local reset command used:
- `php artisan migrate:fresh --seed`

## Refactor Outcome Summary
Before:
- User auth/profile data persisted in local JSON and mirrored in browser localStorage.

After:
- User auth/profile data is DB-backed and session-based via Laravel Auth.
- Livewire components orchestrate UI only.
- Action classes encapsulate write operations.
- Middleware controls access and profile completion gate.
- Dashboard metrics are computed from DTR records.

## File Inventory of Key Refactor Additions
Added:
- `app/Actions/Auth/RegisterStudent.php`
- `app/Actions/Profile/CompleteProfile.php`
- `app/Http/Middleware/EnsureProfileCompleted.php`
- `app/Support/UserDashboardStats.php`

Removed:
- `app/Services/OjtUserStorage.php`

Updated (major):
- `app/Livewire/Auth/SignUp.php`
- `app/Livewire/Auth/Login.php`
- `app/Livewire/Auth/SetUpProfile.php`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Middleware/EnsureOjtUser.php`
- `routes/web.php`
- `database/migrations/0001_01_01_000000_create_users_table.php`
- `database/seeders/DatabaseSeeder.php`
- `database/factories/UserFactory.php`
- `resources/views/components/layouts/app.blade.php`
- `resources/views/components/layouts/dashboard.blade.php`
- `resources/views/components/layouts/guest.blade.php`
- `resources/views/components/layouts/terms.blade.php`
