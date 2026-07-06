# WBACFSPWI — Admin Panel: Project Structure & Plan

Web-Based Automatic Controller for Solar-Powered Water Irrigation.
This document defines the folder structure and implementation plan for the admin web panel that controls and monitors the Arduino-based irrigation system.

## 1. Stack Decisions

- **Backend**: PHP (native, no framework) + MySQL, running under XAMPP (`htdocs`).
- **Frontend**: Server-rendered PHP views + Bootstrap (or similar) for styling, vanilla JS/AJAX (fetch) for dynamic bits (dashboard live data, schedule form, override toggle).
- **Arduino link**: ESP8266/ESP32 module on the irrigation controller connects over WiFi/LAN. It exposes a small HTTP server (or polls our API) so the web app can:
  - Push schedule updates / manual override commands to the device.
  - Receive sensor readings (soil moisture, solar/battery status, pump state) via periodic HTTP POST to our API.
- **Auth**: PHP sessions, password hashing (`password_hash`/`password_verify`).

## 2. Folder Structure

```
WBACFSPWI/
├── docs/
│   └── project-structure-and-plan.md      (this file)
├── config/
│   └── database.php                        DB connection (PDO)
├── public/                                  Web root (point vhost/XAMPP alias here)
│   ├── index.php                            Router / entry point
│   ├── login.php
│   ├── logout.php
│   ├── assets/
│   │   ├── css/
│   │   ├── js/
│   │   └── img/
│   ├── admin/
│   │   ├── partials/                        head/sidebar/footer/guard includes
│   │   ├── dashboard.php
│   │   ├── schedule.php
│   │   ├── reports.php
│   │   ├── profile.php
│   │   ├── logs.php
│   │   ├── alerts.php                       Notifications feature
│   │   ├── override.php                     Manual override controls
│   │   └── users.php                        User/role management
│   └── api/                                 Must live under public/ to be web-reachable
│       ├── device/
│       │   ├── report.php                    Device POSTs sensor data + pump/solar status here
│       │   └── pull-schedule.php             Device GETs current schedule/override state here
│       └── admin/
│           └── dashboard-data.php            AJAX polling for live dashboard widgets
├── src/
│   ├── controllers/
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── ScheduleController.php
│   │   ├── ReportController.php
│   │   ├── ProfileController.php
│   │   ├── LogController.php
│   │   ├── AlertController.php
│   │   ├── OverrideController.php
│   │   └── UserController.php
│   ├── models/
│   │   ├── User.php
│   │   ├── Schedule.php
│   │   ├── SensorReading.php
│   │   ├── IrrigationEvent.php
│   │   ├── Alert.php
│   │   └── AuditLog.php
│   ├── services/
│   │   ├── DeviceApiClient.php              Talks to ESP8266/ESP32 (send schedule/override, poll status)
│   │   ├── NotificationService.php          Email/SMS dispatch for alerts
│   │   └── AuthService.php
│   └── helpers/
│       ├── Auth.php                         Session/role guard middleware
│       └── Response.php                     JSON helper for API endpoints
├── database/
│   ├── schema.sql
│   └── seeds.sql
├── storage/
│   └── logs/                                 App-level error logs (separate from DB audit logs)
└── vendor/                                   (if composer is introduced later, e.g. PHPMailer)
```

## 3. Database Schema (draft)

- `users` — id, name, email, password_hash, role (super_admin/admin/viewer), created_at, last_login_at
- `schedules` — id, label, start_time, end_time, days_of_week, duration_minutes, is_active, created_by, created_at
- `sensor_readings` — id, soil_moisture, battery_voltage, solar_output, recorded_at
- `irrigation_events` — id, schedule_id (nullable, null = manual), trigger_type (scheduled/manual), started_at, ended_at, status
- `overrides` — id, user_id, action (on/off), reason, created_at
- `alerts` — id, type (low_moisture/low_battery/pump_fail/schedule_conflict), message, is_read, created_at
- `audit_logs` — id, user_id, action, details, ip_address, created_at

## 4. Admin Panel Features

1. **Dashboard** — live soil moisture, solar/battery status, pump state, today's schedule summary, recent alerts widget.
2. **Schedule** — CRUD for irrigation schedules; pushes updates to device via `DeviceApiClient`.
3. **Reports** — historical charts/tables: water usage over time, irrigation event history, exportable (CSV).
4. **Profile Management** — edit own name/email/password, view last login.
5. **Logs** — audit log viewer (who changed what schedule/override, when) with filters.
6. **Alerts/Notifications** *(new)* — configurable thresholds (e.g. moisture < X%), email notification via `NotificationService`, in-panel alert center.
7. **Manual Override** *(new)* — force pump on/off outside schedule, with a safety auto-revert timer; logged to `overrides` and `audit_logs`.
8. **User/Role Management** *(new)* — super_admin can create/deactivate accounts and assign roles (super_admin, admin, viewer); role-gated access to Schedule/Override/Users pages.

## 5. Roles & Permissions (draft)

| Feature            | super_admin | admin | viewer |
|---------------------|:---:|:---:|:---:|
| Dashboard            | ✓ | ✓ | ✓ |
| Schedule (edit)      | ✓ | ✓ | – |
| Manual override      | ✓ | ✓ | – |
| Reports              | ✓ | ✓ | ✓ |
| Logs                 | ✓ | ✓ | – |
| Alerts config        | ✓ | ✓ | – |
| User management      | ✓ | – | – |
| Own profile          | ✓ | ✓ | ✓ |

## 6. Arduino Integration Flow

1. ESP module connects to WiFi, on boot calls `GET /api/device/pull-schedule.php` to sync current schedule + any pending override.
2. Every N minutes, ESP calls `POST /api/device/report.php` with sensor readings + pump status → stored in `sensor_readings` / `irrigation_events`, evaluated against alert thresholds.
3. When admin edits a schedule or triggers override in the panel, the change is stored in DB immediately; device picks it up on its next poll (or, if the device exposes its own local server, `DeviceApiClient` pushes it directly).
4. Device auth: shared API key/token per device stored in `config/` and checked in `api/device/*` endpoints.

## 7. Build Plan (phased)

**Phase 1 — Foundation**
- `database/schema.sql`, `config/database.php`
- Auth: login/logout, session guard, password hashing
- Base layout (sidebar nav: Dashboard, Schedule, Reports, Profile, Logs, Alerts, Override, Users)

**Phase 2 — Core Admin Features**
- Dashboard (static first, then live via AJAX)
- Schedule CRUD
- Profile management
- Logs viewer

**Phase 3 — Arduino Integration**
- `api/device/report.php` + `api/device/pull-schedule.php`
- `DeviceApiClient` service
- Dashboard goes live with real sensor data

**Phase 4 — Added Features** ✅ DONE
- Alerts/Notifications (thresholds + email dispatch) — page done; email dispatch still deferred
- Manual override with auto-revert
- User/role management + permission gating

**Phase 5 — Reports & Polish** ✅ DONE (except real firmware testing)
- Reports with charts (e.g. Chart.js) + CSV export
- UI polish, mobile responsiveness
- Basic security pass (input validation, CSRF tokens on forms, rate-limit device endpoints)

## 8. Open Items / TBD
- Notification channel: email only for now (PHPMailer) — SMS can be added later if needed.
- Exact ESP32 firmware endpoints/payload format to be finalized when firmware code is available.
