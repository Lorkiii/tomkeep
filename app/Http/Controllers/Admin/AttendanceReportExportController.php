<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\AttendanceMetrics;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Phase 5: Attendance Report CSV Export Controller
 * Streams attendance records filtered by date range and search term as a downloadable CSV file.
 * Used by the Reports page to enable bulk export for external systems and partner institutions.
 */
class AttendanceReportExportController extends Controller
{
    /**
     * Handle CSV export request and stream response to browser as download.
     * Validates date range, applies search filters, and computes worked hours for each record.
     *
     * @return StreamedResponse CSV file download response
     */
    public function __invoke(Request $request, AttendanceMetrics $metrics): StreamedResponse
    {
        // Validate request parameters: from, to (date range), search (optional)
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
            'search' => ['nullable', 'string', 'max:120'],
        ]);

        // Normalize date strings to YYYY-MM-DD format
        $from = Carbon::parse($validated['from'])->toDateString();
        $to = Carbon::parse($validated['to'])->toDateString();
        // Trim search term to remove extra whitespace
        $search = trim((string) ($validated['search'] ?? ''));

        // Build query: join daily_time_records with users table
        $records = DB::table('daily_time_records')
            ->join('users', 'users.id', '=', 'daily_time_records.user_id')
            // Filter by date range (inclusive)
            ->whereDate('daily_time_records.date', '>=', $from)
            ->whereDate('daily_time_records.date', '<=', $to)
            // Only include student records (exclude admin/test accounts)
            ->where('users.role', 'student')
            // Conditionally apply search filter: name, code, or email
            ->when($search !== '', function ($query) use ($search): void {
                $term = '%' . $search . '%';

                $query->where(function ($nested) use ($term): void {
                    // Match first name, middle name, last name, email, or student code
                    $nested
                        ->where('users.first_name', 'like', $term)
                        ->orWhere('users.middle_name', 'like', $term)
                        ->orWhere('users.last_name', 'like', $term)
                        ->orWhere('users.email', 'like', $term)
                        ->orWhere('users.student_code', 'like', $term);
                });
            })
            // Order: most recent date first, then by student last name
            ->orderByDesc('daily_time_records.date')
            ->orderBy('users.last_name')
            // Select columns with explicit aliases (required for stdClass → array casting)
            ->get([
                'daily_time_records.date as date',
                'daily_time_records.time_in as time_in',
                'daily_time_records.lunch_out as lunch_out',
                'daily_time_records.lunch_in as lunch_in',
                'daily_time_records.time_out as time_out',
                'users.student_code as student_code',
                'users.first_name as first_name',
                'users.middle_name as middle_name',
                'users.last_name as last_name',
                'users.email as email',
            ]);

        // Generate filename with date range for easy identification
        $fileName = 'attendance-report-' . $from . '-to-' . $to . '.csv';

        // Stream response: direct output to browser as CSV download
        return response()->streamDownload(function () use ($records, $metrics): void {
            // Open output stream for binary writing (required for fputcsv)
            $output = fopen('php://output', 'wb');

            // Guard: stream initialization failure
            if ($output === false) {
                return;
            }

            // Write CSV header row: column names in stable order for predictable imports
            fputcsv($output, [
                'Date',
                'Student Code',
                'Student Name',
                'Email',
                'Time In',
                'Lunch Out',
                'Lunch In',
                'Time Out',
                'Worked Hours (HH:MM)',
            ]);

            // Write data row for each attendance record
            foreach ($records as $record) {
                // Cast stdClass to array for safe key access
                $raw = (array) $record;
                // Combine name parts, filter nulls, and trim whitespace
                $name = trim(collect([
                    $raw['first_name'] ?? null,
                    $raw['middle_name'] ?? null,
                    $raw['last_name'] ?? null,
                ])->filter()->implode(' '));

                // Write CSV row with computed worked hours using AttendanceMetrics
                fputcsv($output, [
                    Carbon::parse((string) ($raw['date'] ?? now()->toDateString()))->format('Y-m-d'),
                    $raw['student_code'] ?? '',
                    $name !== '' ? $name : 'Student Profile Incomplete',
                    $raw['email'] ?? '',
                    $raw['time_in'] ?? '',
                    $raw['lunch_out'] ?? '',
                    $raw['lunch_in'] ?? '',
                    $raw['time_out'] ?? '',
                    $metrics->formatHoursMinutes($metrics->workedSeconds($raw)),
                ]);
            }

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}