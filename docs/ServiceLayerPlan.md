# TimeKeep Service Layer Plan (Phase 2)

Owner: Backend Team  
Version: v1.0  
Last Updated: 2026-03-03

## Goal

Define a scalable and maintainable backend service layer before coding controllers.

## Architecture Rule

- Controllers handle HTTP concerns only.
- Form Requests handle request validation only.
- Services handle business rules, orchestration, and transactions.
- Models handle persistence mapping, relationships, and casts.

## Service Boundaries

### AttendanceService

Responsibility:
- Enforce attendance state machine and one-record-per-day rule.

Primary methods:
- timeIn(userId, payload)
- lunchOut(userId, payload)
- lunchIn(userId, payload)
- timeOut(userId, payload)
- getTodayRecord(userId, date)

Business guarantees:
- Sequence enforcement: time_in -> lunch_out -> lunch_in -> time_out
- Idempotency protection for repeat submissions
- Transaction for write + audit log

### UserApprovalService

Responsibility:
- Handle approval lifecycle for student registration.

Primary methods:
- approveUser(adminId, userId, notes)
- rejectUser(adminId, userId, notes)

Business guarantees:
- Only admin role can approve/reject
- approved_by and approved_at always set on decision
- Action always audited

### SiteService

Responsibility:
- Manage site lifecycle and geofence settings.

Primary methods:
- createSite(payload)
- updateSite(siteId, payload)
- deactivateSite(siteId)
- activateSite(siteId)
- listActiveSites()

Business guarantees:
- company_name required
- allowed_radius_m must be positive and bounded by policy
- Action always audited for create/update/state changes

### AuditLogService

Responsibility:
- Provide consistent append-only audit writes.

Primary methods:
- recordCreate(actorUserId, modelType, modelId, newValues, metadata)
- recordUpdate(actorUserId, modelType, modelId, oldValues, newValues, metadata)
- recordDelete(actorUserId, modelType, modelId, oldValues, metadata)
- recordAction(actorUserId, action, modelType, modelId, oldValues, newValues, metadata)

Business guarantees:
- Standard payload structure
- No in-place edits in normal flow

## Transaction Policy

Use a DB transaction when an operation:
1. Writes domain data, and
2. Must also write an audit record.

Examples:
- attendance event update + audit write
- user approval update + audit write
- site update + audit write

## Domain Error Contract

All services should throw domain-level errors with stable codes:
- ATTENDANCE_OUT_OF_ORDER
- ATTENDANCE_ALREADY_SET
- DAILY_RECORD_EXISTS
- USER_NOT_APPROVED
- FORBIDDEN_ROLE
- SITE_INACTIVE
- RECORD_NOT_FOUND

HTTP mapping is controller responsibility.

## Thin Controller Checklist

For every controller action:
1. Validate request using Form Request.
2. Resolve authenticated user and role context.
3. Call one service method.
4. Map service result/error to HTTP response.
5. Do not place business rules in controller.

## Idempotency Rules (Attendance)

- Duplicate submit of the same action should not produce inconsistent state.
- Repeated call with no state change should return a safe response and no duplicate transition.
- If duplicate call creates ambiguity, return ATTENDANCE_ALREADY_SET.

## Performance Baseline

- Paginate all list endpoints by default.
- Add and preserve indexes used in filters:
  - daily_time_records (user_id, date)
  - audit_logs (user_id, action), (user_id, model_type), (user_id, created_at)
- Keep service methods focused and deterministic to simplify caching later if needed.

## Testing Targets Before Feature Expansion

1. Attendance flow happy path (all four actions in order)
2. Attendance out-of-order rejection
3. Duplicate action rejection
4. Approval authorization check
5. Audit log write verification for each critical mutation

## Implementation Entry Order (When Coding Starts)

1. Form Requests for attendance and approval actions
2. AttendanceService + AuditLogService
3. UserApprovalService
4. SiteService
5. Controllers that call services only
6. Route protection with middleware/policies
7. Feature tests for critical path

## Done Criteria for Phase 2

- Every critical mutation endpoint calls a service method.
- No business branching exists in controllers.
- Service errors are stable and documented.
- All critical writes have matching audit entries.
- Core feature tests pass for sequence and authorization rules.
