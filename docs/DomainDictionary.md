# TimeKeep Domain Dictionary

Owner: Backend Team  
Version: v1.0  
Last Updated: 2026-03-03

## Purpose

This document is the single source of truth for core backend terms and business meaning.

## How To Use

- Every field that drives backend behavior must be defined here.
- Any schema or behavior change must update this document first.
- If code and this document conflict, pause implementation and resolve the conflict.

## Core Entities

| Term | Definition | Allowed Values | Source of Truth |
|---|---|---|---|
| User | Identity account for admins and students using the system. | role and status constrained by schema | users table |
| Site | Company or OJT location used for attendance and geofence rules. | active/inactive via is_active | sites table |
| DailyTimeRecord | One-day attendance timeline per user. | one record per user per date | daily_time_records table |
| AuditLog | Immutable event log for important data mutations. | action and model metadata required | audit_logs table |

## User Terms

| Term | Definition | Allowed Values | Rules Impacted | What It Is Not |
|---|---|---|---|---|
| User.first_name | Person given name. | non-empty string up to 30 | profile, registration | not full name |
| User.middle_name | Optional middle name. | nullable string up to 30 | profile | not required |
| User.last_name | Family name. | non-empty string up to 30 | profile, registration | not full name |
| User.role | Authorization class for account. | admin, student | access control | not approval status |
| User.status | Registration approval lifecycle state. | pending, approved, rejected | onboarding, login policy | not online presence |
| User.approved_by | Admin user id who approved/rejected account. | nullable valid user id | approval auditability | not a boolean flag |
| User.approved_at | Date-time when account decision happened. | nullable datetime | approval traceability | not account creation time |
| User.is_active | Runtime activity/presence status. | true, false | monitoring/presence | not account approval |
| User.last_seen_at | Last known activity timestamp. | nullable datetime | monitoring | not login timestamp guarantee |

## Site Terms

| Term | Definition | Allowed Values | Rules Impacted | What It Is Not |
|---|---|---|---|---|
| Site.company_name | Display/business name of OJT company/site. | non-empty string | site management, display | not user name |
| Site.address | Human-readable address of site. | nullable string | display, admin setup | not geofence coordinate |
| Site.allowed_radius_m | Max allowed distance in meters for geofence validation. | unsigned integer, default 100 | attendance validation | not kilometers |
| Site.location | Geographic point for geofence center. | POINT geometry | attendance validation | not plain text address |
| Site.is_active | Whether site can be selected/used currently. | true, false | site selection | not soft delete |

## Daily Time Record Terms

| Term | Definition | Allowed Values | Rules Impacted | What It Is Not |
|---|---|---|---|---|
| DailyTimeRecord.user_id | Owner of the daily attendance record. | valid user id | attendance ownership | not actor of every action |
| DailyTimeRecord.date | Business date of attendance record. | valid date | uniqueness, reporting | not datetime event stream |
| DailyTimeRecord.time_in | Start-of-day attendance time. | nullable HH:MM:SS | sequence rules | not full datetime |
| DailyTimeRecord.lunch_out | Lunch start time. | nullable HH:MM:SS | sequence rules | not break duration |
| DailyTimeRecord.lunch_in | Lunch end time. | nullable HH:MM:SS | sequence rules | not break duration |
| DailyTimeRecord.time_out | End-of-day attendance time. | nullable HH:MM:SS | sequence rules, completion | not overtime calculation |

## Audit Log Terms

| Term | Definition | Allowed Values | Rules Impacted | What It Is Not |
|---|---|---|---|---|
| AuditLog.user_id | Account that triggered the action. | nullable valid user id | accountability | not target record owner |
| AuditLog.action | Mutation verb. | create, update, delete, approve, reject, attendance_action | audit analytics | not authorization grant |
| AuditLog.model_type | Target entity type name. | User, Site, DailyTimeRecord, etc. | polymorphic audit tracking | not PHP class requirement |
| AuditLog.model_id | Target entity id affected by action. | nullable unsigned bigint | audit lookup | not enough without model_type |
| AuditLog.old_values | Before-state snapshot for changed fields. | nullable JSON object | diff/audit reviews | not complete backup |
| AuditLog.new_values | After-state snapshot for changed fields. | nullable JSON object | diff/audit reviews | not complete backup |
| AuditLog.ip_address | Request source IP. | nullable string | security diagnostics | not trusted identity |
| AuditLog.user_agent | Request client signature. | nullable text | diagnostics | not authentication proof |

## Cross-Entity Business Rules (Invariants)

1. Exactly one DailyTimeRecord per user per date.
2. Attendance sequence is strictly: time_in -> lunch_out -> lunch_in -> time_out.
3. A student account cannot perform protected student actions unless status is approved.
4. All critical create/update/delete and approval actions must emit an AuditLog.
5. AuditLog records are append-only in normal application flow.

## Attendance State Transitions

| Current State | Allowed Action | Next State | Reject If |
|---|---|---|---|
| No record for today | time_in | time_in set | duplicate daily record exists |
| time_in set | lunch_out | lunch_out set | lunch_out already set |
| lunch_out set | lunch_in | lunch_in set | lunch_in already set or lunch_out missing |
| lunch_in set | time_out | complete | time_out already set |
| complete | none | complete | any attendance action attempted |

## Error Code Catalog (Draft)

| Code | Meaning |
|---|---|
| ATTENDANCE_OUT_OF_ORDER | Attendance action does not match allowed sequence |
| ATTENDANCE_ALREADY_SET | Attempted to set an attendance field twice |
| DAILY_RECORD_EXISTS | User already has a daily record for this date |
| USER_NOT_APPROVED | User status is not approved for action |
| SITE_INACTIVE | Selected site is not currently active |
| FORBIDDEN_ROLE | Role is not allowed to perform this action |

## Change Log

- v1.0 (2026-03-03): Initial domain dictionary aligned with current migrations and model design.
