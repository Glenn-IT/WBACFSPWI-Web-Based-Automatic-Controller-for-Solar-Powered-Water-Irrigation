# WBACFSPWI Admin Panel — Build Checklist

Tracks progress against the plan in [project-structure-and-plan.md](./project-structure-and-plan.md).
Update the checkboxes as work continues. See "How to resume" at the bottom.

## Phase 1 — Foundation ✅ DONE

- [x] `database/schema.sql` — users, schedules, sensor_readings, irrigation_events, overrides, alerts, audit_logs
- [x] `database/seeds.sql` — default super_admin (`admin@wbacfspwi.local` / `Admin@123` — **change this password**)
- [x] `config/database.php` — PDO connection
- [x] `config/bootstrap.php` — autoloader + shared includes
- [x] `src/helpers/Auth.php` — session guard, `requireLogin()`, `requireRole()`
- [x] `src/helpers/Csrf.php` — CSRF token helper for forms
- [x] `src/services/AuthService.php`, `src/models/User.php`
- [x] `public/login.php`, `public/logout.php`, `public/index.php`
- [x] `public/admin/partials/{head,sidebar,footer,guard}.php` — shared layout, role-aware nav
- [x] `public/admin/dashboard.php` — placeholder page

## Phase 2 — Core Admin Features ✅ DONE

- [x] `src/models/AuditLog.php` — records login, schedule changes, profile changes
- [x] `src/models/Schedule.php` + `public/admin/schedule.php` — full CRUD, enable/disable, days-of-week picker
- [x] `public/admin/profile.php` — edit name/email, change password
- [x] `public/admin/logs.php` — filterable, paginated audit log viewer

## Phase 3 — Arduino Integration ✅ DONE

- [x] `config/device.php` — `DEVICE_API_KEY` shared secret + alert thresholds
- [x] `src/helpers/DeviceAuth.php` — validates `X-Api-Key` header on device endpoints
- [x] `src/models/SensorReading.php`, `IrrigationEvent.php`, `Alert.php`
- [x] `public/api/device/report.php` — device POSTs sensor readings + pump state; auto-creates alerts on threshold breach; tracks irrigation event start/stop
- [x] `public/api/device/pull-schedule.php` — device GETs active schedules + any pending override
- [x] `public/api/admin/dashboard-data.php` + live dashboard — dashboard now polls real sensor/alert/schedule data every 15s
- [x] Verified via curl simulation of ESP32 calls (report + pull-schedule) and confirmed dashboard reflects live data

**Note:** `api/` was relocated to `public/api/` since only `public/` is web-reachable — the plan doc has been updated to reflect this.

## Phase 4 — Added Features ✅ DONE

- [x] **Alerts/Notifications page** (`public/admin/alerts.php`) — filter by type/read status, mark read, mark all read, delete; `Alert::list/markRead/markAllRead/delete` added to the model. Email dispatch is still deferred (see Known TODOs — needs PHPMailer).
- [x] **Manual override** (`public/admin/override.php` + new `src/models/Override.php`) — admin-triggered pump on/off with reason + auto-revert window, writes to `overrides` table, shows current active override + history. Device already reads pending overrides via `pull-schedule.php` (Phase 3) — verified the two are wired together correctly.
- [x] **User/role management** (`public/admin/users.php`) — create users, change role (inline select), activate/deactivate, delete; restricted to `super_admin`; guards against self-deactivation/self-deletion/self-role-change. `User` model extended with `all/create/updateRole/setActive/delete`.
- [x] Verified via curl: alerts created by simulated device report → visible/filterable/markable in UI; override applied via UI → confirmed picked up by `GET /api/device/pull-schedule.php`; created a viewer user, confirmed role-gating blocks them from alerts/override/users (302 redirects) but allows dashboard, then deleted the test user via the UI.

## Phase 5 — Reports & Polish ✅ DONE (except real firmware — see note)

- [x] `public/admin/reports.php` — date-range filter, Chart.js trend line (soil moisture/battery/solar), irrigation events table, CSV export. `SensorReading::rangeBetween()` and `IrrigationEvent::between()` added to support it.
- [x] Mobile responsiveness pass — sidebar is now an off-canvas drawer below 768px (hamburger toggle + backdrop in `head.php`/`footer.php`, styles in `app.css`), tables already used `.table-responsive`.
- [x] Security pass — added `src/helpers/RateLimiter.php` (file-based sliding window, 60 req/min per IP) enforced in `DeviceAuth::requireValidKey()` ahead of key validation; verified with a 65-request burst (60×200, 5×429). Reviewed CSRF coverage across all 6 POST-handling pages (login, profile, schedule, alerts, override, users) — all verify tokens and all forms emit them.
- [ ] **Real ESP8266/ESP32 firmware integration** — still only tested via curl simulation (no physical device available in this environment). Endpoints (`report.php`, `pull-schedule.php`) and payload formats are documented and stable; this item needs real hardware to close out.

## Phase 6 — Auth Enhancements ✅ DONE

- [x] **Security question / Forgot Password** — `database/migrations/001_add_security_question.sql` adds `security_question`/`security_answer_hash` to `users` (also folded into `schema.sql` for fresh installs); shared question list in `config/security_questions.php`. `User::updateSecurityQuestion()`/`verifySecurityAnswer()` hash the answer (normalized to lowercase/trimmed, so case doesn't matter).
- [x] `public/forgot-password.php` — 3-step session-driven flow: enter email → step 2 presents a combobox of all security questions (not pre-filled/revealed) plus an answer field, and the user must pick the account's actual question AND give the right answer before advancing; a wrong question or wrong answer gives the same generic "Incorrect question or answer" so neither part is leaked → set a new password. Linked from `login.php`.
- [x] `public/admin/profile.php` — new "Security Question" card to set/update your own question + answer (requires current password to change, like the password-change form).
- [x] Global show/hide password toggle — `public/assets/js/app.js` auto-wraps every `input[type=password]` on a page with a Show/Hide button; wired into `login.php`, `forgot-password.php`, and the admin `footer.php` (covers profile/users/schedule/alerts/override for free).
- [x] Verified via curl: full reset flow (wrong answer → 302/rejected, correct answer in different case → accepted, password reset, login with new password succeeds); toggle script present on all 4 password-bearing page types; admin password restored to the seeded default afterward.

## Known TODOs / Decisions Deferred

- Notification channel: email only planned (PHPMailer), not yet wired up.
- `DEVICE_API_KEY` default (`dev-local-device-key`) is for local dev only — must be overridden via `WBACFSPWI_DEVICE_API_KEY` env var before any real deployment.
- Default admin password (`Admin@123`) must be changed via the Profile page after first login.
- Any existing (pre-Phase-6) database needs `database/migrations/001_add_security_question.sql` applied — fresh installs get it automatically from `schema.sql`.
- Users created before Phase 6 have no security question set, so Forgot Password won't work for them until they set one via Profile.

## How to Resume

1. Read this checklist to see the last completed phase.
2. Read `docs/project-structure-and-plan.md` for the full architecture/schema reference.
3. Start XAMPP's MySQL, ensure the `wbacfspwi` database exists with schema + seed loaded (`database/schema.sql`, `database/seeds.sql`).
4. Point the web server at `public/` as the document root (XAMPP vhost, or `php -S 127.0.0.1:8899` from inside `public/` for quick local testing).
5. All planned phases are functionally complete. The one open item is real ESP8266/ESP32 firmware integration testing (currently only curl-simulated) — do that when hardware is available, using the payload formats documented in `report.php` / `pull-schedule.php`. Also revisit "Known TODOs" below (email notifications, device key, default password) before any real deployment.
