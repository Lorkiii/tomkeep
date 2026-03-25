# Dashboard Architecture Guide

Owner: Frontend and Backend Team  
Version: v1.0  
Last Updated: 2026-03-08

## Goal

Explain how the student dashboard is structured so future changes stay simple, reusable, and easy to maintain.

## High-Level Rule

- The route prepares dashboard data.
- Support classes shape business and reporting data.
- The layout provides shared dashboard shell behavior.
- The sidebar is a reusable Blade component.
- The floating attendance action is a dedicated Livewire component.
- The attendance service remains the only place that writes attendance records.

## Main Files

### Route Entry

File:
- [routes/web.php](../routes/web.php)

Responsibility:
- Protect the dashboard behind middleware.
- Resolve the authenticated user.
- Ask `UserDashboardStats` for computed dashboard values.
- Pass user context and stats into the dashboard view.

Key output:
- `currentOjtUser`
- `progressPercent`
- `remainingHours`
- `requiredHours`
- `activityLogs`
- `hoursThisDay`
- `hoursThisWeek`
- `hoursThisMonth`

## Layout Structure

### Dashboard Layout

File:
- [resources/views/components/layouts/dashboard.blade.php](../resources/views/components/layouts/dashboard.blade.php)

Responsibility:
- Provide the full dashboard page shell.
- Own responsive sidebar behavior.
- Hold Alpine state for:
  - `sidebarOpen` for mobile drawer visibility
  - `sidebarCollapsed` for desktop compact mode
- Render the page content through `{{ $slot }}`

Important boundary:
- This file should handle shared layout behavior only.
- Page-specific content belongs in each dashboard page view.

### Sidebar Component

File:
- [resources/views/components/dashboard/sidebar.blade.php](../resources/views/components/dashboard/sidebar.blade.php)

Responsibility:
- Display student identity summary.
- Render shared dashboard navigation links.
- Support both desktop and mobile because it is reused by the layout.
- Keep navigation changes in one file only.

Why this was extracted:
- Prevents duplication between desktop and mobile markup.
- Makes the main layout shorter and easier to understand.
- Gives future developers one place to edit nav labels, links, and sidebar UI.

## Dashboard Page

### Home Dashboard View

File:
- [resources/views/dashboard/home.blade.php](../resources/views/dashboard/home.blade.php)

Responsibility:
- Render the student-facing dashboard page.
- Show progress card.
- Show daily, weekly, and monthly hour summaries.
- Show recent logs for today only.
- Mount the floating attendance quick-action Livewire component.

Important rule:
- This view should stay mostly presentational.
- It should not contain attendance business rules or heavy reporting logic.

## Reporting and Dashboard Data

### UserDashboardStats

File:
- [app/Support/UserDashboardStats.php](../app/Support/UserDashboardStats.php)

Responsibility:
- Read daily time records for one user.
- Compute completed hours.
- Compute remaining hours and progress percentage.
- Compute hour summaries for day, week, and month.
- Build the dashboard recent log list.

Important dashboard rule:
- `activityLogs` on the dashboard are intentionally limited to today.
- Historical log browsing belongs in Monthly DTR, not in the home dashboard.

Important business rule:
- Net worked time is based on `time_in` to `time_out`.
- If both `lunch_out` and `lunch_in` exist, the lunch interval is subtracted.

## Attendance Action Flow

### AttendanceActionState

File:
- [app/Support/AttendanceActionState.php](../app/Support/AttendanceActionState.php)

Responsibility:
- Convert today's DTR record into a UI-friendly "next action" state.
- Decide which action the floating button should show.

Supported action order:
1. `time_in`
2. `lunch_out`
3. `lunch_in`
4. `time_out`

If all actions are already recorded:
- The state becomes complete.
- The floating widget stops offering another action.

Why this class exists:
- Prevents conditional attendance logic from spreading into Blade templates.
- Makes the next-action behavior easy to test independently.

### DashboardQuickAction Livewire Component

Files:
- [app/Livewire/Attendance/DashboardQuickAction.php](../app/Livewire/Attendance/DashboardQuickAction.php)
- [resources/views/livewire/attendance/dashboard-quick-action.blade.php](../resources/views/livewire/attendance/dashboard-quick-action.blade.php)

Responsibility:
- Show the floating attendance button.
- Show the current label, icon, color tone, and help text.
- Ask for confirmation before submitting an attendance action.
- Submit the next allowed action.
- Refresh the dashboard after success.

Important boundary:
- Livewire handles interaction.
- `AttendanceActionState` decides what action is next.
- `AttendanceService` performs the actual write.

## Attendance Write Layer

### AttendanceService

File:
- [app/Services/AttendanceService.php](../app/Services/AttendanceService.php)

Responsibility:
- Enforce the attendance sequence.
- Create or update today's record.
- Prevent invalid or duplicate transitions.
- Write matching audit logs.

Important rule:
- No UI component should bypass this service when recording attendance.

## Data Flow Summary

### Dashboard Page Load

1. The user visits `/home`.
2. The route in [routes/web.php](../routes/web.php) resolves the authenticated user.
3. The route asks `UserDashboardStats` for computed values.
4. The route returns [resources/views/dashboard/home.blade.php](../resources/views/dashboard/home.blade.php).
5. The view is wrapped by [resources/views/components/layouts/dashboard.blade.php](../resources/views/components/layouts/dashboard.blade.php).
6. The layout renders [resources/views/components/dashboard/sidebar.blade.php](../resources/views/components/dashboard/sidebar.blade.php).
7. The page mounts the floating Livewire attendance component.

### Attendance Button Click

1. The floating button is rendered by `DashboardQuickAction`.
2. The button state comes from `AttendanceActionState`.
3. The user confirms the action.
4. `DashboardQuickAction` calls `AttendanceService`.
5. `AttendanceService` validates and records the transition.
6. The component redirects back to the dashboard.
7. The route rebuilds stats and today's logs.

## Maintainability Rules

- Keep attendance business rules in service or support classes, not in Blade.
- Keep shared shell behavior in the layout.
- Keep reusable navigation in the sidebar component.
- Keep the dashboard page focused on presentation.
- Keep historical attendance exploration in Monthly DTR, not the home dashboard.
- Prefer adding small support classes over growing one large Blade file.

## Suggested Change Strategy

When adding a new dashboard feature:

1. Decide whether it is layout, page, interaction, or business logic.
2. Place it in the smallest correct layer.
3. If the feature changes derived dashboard values, update `UserDashboardStats`.
4. If the feature changes attendance action flow, update `AttendanceActionState` and `AttendanceService`.
5. If the feature changes shared navigation, update the sidebar component.
6. Add or update tests for support classes first when possible.

## Related Files

- [resources/views/dashboard/monthly-dtr.blade.php](../resources/views/dashboard/monthly-dtr.blade.php)
- [tests/Unit/AttendanceActionStateTest.php](../tests/Unit/AttendanceActionStateTest.php)
- [tests/Unit/UserDashboardStatsTest.php](../tests/Unit/UserDashboardStatsTest.php)
- [tests/Feature/AttendanceFlowTest.php](../tests/Feature/AttendanceFlowTest.php)