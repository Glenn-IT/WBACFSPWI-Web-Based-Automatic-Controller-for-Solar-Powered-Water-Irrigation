# WBACFSPWI — Web-Based Automatic Controller for Solar-Powered Water Irrigation

A PHP + MySQL admin panel for monitoring and controlling an ESP8266/ESP32-based
solar-powered irrigation system: live sensor dashboard, schedule management,
manual override, alerts, reports, user/role management, and audit logging.

See [`docs/project-structure-and-plan.md`](docs/project-structure-and-plan.md) for
full architecture/schema and [`docs/checklist.md`](docs/checklist.md) for build
progress and how to resume work.

## Features

- **Dashboard** — live soil moisture, battery voltage, solar output, and pump
  state (polls every 15s), today's schedule, and recent alerts.
- **Schedule** — CRUD for irrigation schedules (time, duration, days of week).
- **Reports** — date-range sensor trend chart (Chart.js) and irrigation event
  history, with CSV export.
- **Alerts/Notifications** — threshold-based alerts (low moisture/battery),
  filterable, mark read/unread.
- **Manual Override** — force the pump on/off with an auto-revert timer.
- **User Management** — role-based accounts (`super_admin`, `admin`, `viewer`),
  restricted to `super_admin`.
- **Logs** — filterable, paginated audit log of logins, schedule/profile/user
  changes, and overrides.
- **Profile** — edit own name/email, change password, set/update a security
  question for account recovery.
- **Forgot Password** — recover access by correctly answering your security
  question before setting a new password.

## Stack

- PHP (native, no framework) + MySQL via PDO
- Session-based auth (`password_hash`/`password_verify`), CSRF tokens on all forms
- Bootstrap 5 + vanilla JS/fetch for live widgets, Chart.js for reports
- ESP8266/ESP32 devices talk to the app over HTTP, authenticated with a shared
  `X-Api-Key` header

## Requirements

- XAMPP (Apache + MySQL) or any PHP 8+ / MySQL 5.7+ environment
- Web root must point at the `public/` folder — nothing outside `public/` is
  web-reachable

## Setup

1. Create a database and load the schema + seed data:
   ```sh
   mysql -u root -p -e "CREATE DATABASE wbacfspwi"
   mysql -u root -p wbacfspwi < database/schema.sql
   mysql -u root -p wbacfspwi < database/seeds.sql
   ```
   If you're updating an existing database created before the Forgot Password
   feature, also run `database/migrations/001_add_security_question.sql`.
2. Point your web server's document root at `public/` — nothing outside
   `public/` is web-reachable, so `http://localhost/<project-folder>/...`
   won't work directly. Set up an Apache virtual host instead:
   - In `C:/xampp/apache/conf/extra/httpd-vhosts.conf`, add:
     ```apache
     <VirtualHost *:80>
         DocumentRoot "C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public"
         ServerName wbacfspwi.local
         <Directory "C:/xampp/htdocs/WBACFSPWI-Web-Based-Automatic-Controller-for-Solar-Powered-Water-Irrigation/public">
             AllowOverride All
             Require all granted
         </Directory>
     </VirtualHost>
     ```
   - Add `127.0.0.1 wbacfspwi.local` to `C:/Windows/System32/drivers/etc/hosts`
     (needs an elevated editor).
   - Restart Apache from the XAMPP Control Panel.
   - Visit `http://wbacfspwi.local/login.php` in Chrome.

   For a quick one-off test without touching Apache config, you can instead
   run `php -S 127.0.0.1:8899` from inside `public/` and browse to
   `http://127.0.0.1:8899/login.php` — fine for development, but not a
   substitute for the vhost setup above.
3. Log in at `/login.php` with the seeded super admin:
   - Email: `admin@wbacfspwi.local`
   - Password: `Admin@123`
   - **Change this password immediately via Profile after first login.**
4. Override the device API key before connecting real hardware — set the
   `WBACFSPWI_DEVICE_API_KEY` environment variable (defaults to
   `dev-local-device-key`, which is for local dev only). See `config/device.php`.

## Device integration

The ESP8266/ESP32 firmware talks to two endpoints under `public/api/device/`,
authenticated with an `X-Api-Key` header matching `DEVICE_API_KEY`:

- `GET /api/device/pull-schedule.php` — fetch active schedules and any pending
  manual override.
- `POST /api/device/report.php` — report sensor readings and pump state as
  JSON: `{ "soil_moisture": 45.2, "battery_voltage": 12.1, "solar_output": 30.5, "pump_state": "on"|"off", "schedule_id": 3 }`.

Both endpoints are rate-limited to 60 requests/minute per IP
(`src/helpers/RateLimiter.php`).

## Project layout

```
config/       Database, device, and bootstrap config
public/       Web root — pages, assets, and API endpoints
src/          Models, services, and helpers (autoloaded)
database/     Schema and seed SQL
storage/      App logs and rate-limit state (not web-reachable)
docs/         Architecture plan and build checklist
```

## Status

All six build phases (Foundation, Core Admin Features, Arduino Integration,
Added Features, Reports & Polish, Auth Enhancements) are functionally complete
and verified via simulated device/browser traffic. The one open item is
testing against real ESP8266/ESP32 hardware — see
[`docs/checklist.md`](docs/checklist.md) for details.
