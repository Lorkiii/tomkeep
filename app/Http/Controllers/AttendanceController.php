<?php

namespace App\Http\Controllers;

use App\Exceptions\AttendanceException;
use App\Services\AttendanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    public function __construct(private readonly AttendanceService $attendanceService)
    {
    }

    public function mark(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'action' => ['required', Rule::in(AttendanceService::allowedActions())],
            'occurred_at' => ['nullable', 'date_format:H:i:s'],
            'actor_user_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);

        try {
            $record = $this->attendanceService->mark(
                userId: $validated['user_id'],
                action: $validated['action'],
                occurredAt: $validated['occurred_at'] ?? null,
                actorUserId: $validated['actor_user_id'] ?? null,
                ipAddress: $request->ip(),
                userAgent: $request->userAgent(),
            );
        } catch (AttendanceException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'error_code' => $exception->errorCode(),
            ], $exception->status());
        }

        return response()->json([
            'message' => 'Attendance action recorded successfully.',
            'data' => $record,
        ]);
    }
}
