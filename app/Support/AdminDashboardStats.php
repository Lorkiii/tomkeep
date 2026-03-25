<?php

namespace App\Support;

use App\Models\User;
use Carbon\Carbon;

/**
 * Shapes admin-facing approval metrics for the overview dashboard.
 */
class AdminDashboardStats
{
    /**
     * Build the overview payload for the admin dashboard home.
     *
     * @return array<string, mixed>
     */
    public function overview(): array
    {
        // Use one shared reference point so all "today" calculations stay consistent.
        $today = now();

        // Top-line KPI cards for the admin overview.
        $totalStudents = User::query()->where('role', 'student')->count();
        $pendingApprovals = User::query()->where('role', 'student')->where('status', 'pending')->count();
        $approvedToday = User::query()
            ->where('role', 'student')
            ->where('status', 'approved')
            ->whereDate('approved_at', $today->toDateString())
            ->count();
        $activeNow = User::query()
            ->where('role', 'student')
            ->where('status', 'approved')
            ->where('is_active', true)
            ->count();

        // Pending requests feed the queue preview shown on the home page.
        $recentRequests = User::query()
            ->where('role', 'student')
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'profile_completed',
                'created_at',
            ])
            ->map(fn (User $student): array => [
                'id' => $student->id,
                'name' => $this->displayName($student),
                'email' => $student->email,
                'profile_completed' => (bool) $student->profile_completed,
                'submitted_at' => optional($student->created_at)?->format('M j, Y g:i A'),
            ])
            ->all();

        // Recent decisions give admins quick context on the latest approvals and rejections.
        $recentDecisions = User::query()
            ->where('role', 'student')
            ->whereIn('status', ['approved', 'rejected'])
            ->latest('updated_at')
            ->limit(5)
            ->get([
                'id',
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'status',
                'approved_at',
                'updated_at',
                'admin_notes',
            ])
            ->map(function (User $student): array {
                $decisionAt = $student->status === 'approved'
                    ? $student->approved_at
                    : $student->updated_at;

                return [
                    'id' => $student->id,
                    'name' => $this->displayName($student),
                    'email' => $student->email,
                    'status' => $student->status,
                    'decision_at' => optional($decisionAt)?->format('M j, Y g:i A'),
                    'note' => $student->admin_notes,
                ];
            })
            ->all();

        // These counters power the alert strip that warns about queue quality, not just size.
        $incompletePendingProfiles = User::query()
            ->where('role', 'student')
            ->where('status', 'pending')
            ->where('profile_completed', false)
            ->count();

        $stalePending = User::query()
            ->where('role', 'student')
            ->where('status', 'pending')
            ->where('created_at', '<=', $today->copy()->subDays(2))
            ->count();

        return [
            'totalStudents' => $totalStudents,
            'pendingApprovals' => $pendingApprovals,
            'approvedToday' => $approvedToday,
            'activeNow' => $activeNow,
            'recentRequests' => $recentRequests,
            'recentDecisions' => $recentDecisions,
            'alerts' => $this->buildAlerts(
                pendingApprovals: $pendingApprovals,
                stalePending: $stalePending,
                incompletePendingProfiles: $incompletePendingProfiles,
            ),
        ];
    }

    /**
     * Convert raw queue counts into small alert messages for the overview screen.
     *
     * @return array<int, array<string, string>>
     */
    private function buildAlerts(int $pendingApprovals, int $stalePending, int $incompletePendingProfiles): array
    {
        $alerts = [];

        if ($pendingApprovals > 0) {
            $alerts[] = [
                'tone' => 'blue',
                'title' => 'Pending approvals need review',
                'description' => $pendingApprovals . ' student account' . ($pendingApprovals === 1 ? ' is' : 's are') . ' waiting in the approval queue.',
            ];
        }

        if ($stalePending > 0) {
            $alerts[] = [
                'tone' => 'amber',
                'title' => 'Some requests are aging in the queue',
                'description' => $stalePending . ' pending request' . ($stalePending === 1 ? ' has' : 's have') . ' been waiting for at least two days.',
            ];
        }

        if ($incompletePendingProfiles > 0) {
            $alerts[] = [
                'tone' => 'slate',
                'title' => 'Pending profiles are still incomplete',
                'description' => $incompletePendingProfiles . ' pending student profile' . ($incompletePendingProfiles === 1 ? ' is' : 's are') . ' missing required setup details.',
            ];
        }

        if ($alerts === []) {
            $alerts[] = [
                'tone' => 'emerald',
                'title' => 'Approval queue is under control',
                'description' => 'There are no urgent approval issues right now.',
            ];
        }

        return $alerts;
    }

    /**
     * Build a readable student label even when the profile is only partially filled in.
     */
    private function displayName(User $student): string
    {
        $name = trim(collect([
            $student->first_name,
            $student->middle_name,
            $student->last_name,
        ])->filter()->implode(' '));

        return $name !== '' ? $name : 'Student Profile Incomplete';
    }
}