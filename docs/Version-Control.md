# Version Control — Week-by-Week Presentation Rollout

This project is presented incrementally: each Git tag is a permanent snapshot of the
system with exactly one more page unlocked than the version before it. Pages that are
not yet part of the current version show an **Under Construction** placeholder instead.

---

## Rollout Schedule

| Version | Feature Unlocked | Pages Unlocked (cumulative) | Pages Still Gated |
|---------|------------------|------------------------------|-------------------|
| v1.00 | Login / Forgot Password / Logout | login, forgot-password, logout, index | dashboard, schedule, reports, logs, alerts, override, users, profile |
| v1.01 | Admin: Dashboard (+ live data API) | + dashboard | schedule, reports, logs, alerts, override, users, profile |
| v1.02 | Admin: Schedule | + schedule | reports, logs, alerts, override, users, profile |
| v1.03 | Admin: Reports | + reports | logs, alerts, override, users, profile |
| v1.04 | Admin: Logs | + logs | alerts, override, users, profile |
| v1.05 | Admin: Alerts | + alerts | override, users, profile |
| v1.06 | Admin: Manual Override | + override | users, profile |
| v1.07 | Admin: User Management | + users | profile |
| v1.08 | Admin: Profile (Full System) | + profile | — none — |

The two device API endpoints (`api/device/report.php`, `api/device/pull-schedule.php`)
are never gated — they serve the irrigation hardware, not the presentation.

---

## Under Construction Strategy

- `components/under-construction.php` defines `CURRENT_VERSION` at the top, renders a
  full-page card (hard-hat icon, version badge, title, description, Go Back button),
  and calls `exit` at the bottom.
- Every gated page has this as its **very first statement**:
  ```php
  require_once __DIR__ . '/../../components/under-construction.php';
  ```
  Because the component exits, the real page content never runs.
- Unlocking a page = deleting that one line. Nothing else about the page changes, so
  every unlocked page works fully the moment it is released.
- `public/api/admin/dashboard-data.php` uses a **data gate** instead (returns an empty
  JSON payload) so a direct URL call never leaks live data before v1.01.
- Links that point to still-gated pages naturally land on the Under Construction card.

---

## Git Commands Used Per Version

```bash
git add <unlocked-page.php> components/under-construction.php
git commit -m "feat: implement vX.XX - unlock [Feature]"
git tag vX.XX
git push origin main
git push origin vX.XX
```

---

## How Git Tags Work as Permanent Snapshots

A tag is a permanent, named pointer to one exact commit. `git checkout v1.03` restores
the entire project exactly as it was when v1.03 was presented — gated pages and all —
no matter how much later work happens on `main`. Tags never move on their own, so each
week's presentation state stays reproducible forever. On GitHub, each pushed tag also
appears under **Releases / Tags**, where the snapshot can be browsed or downloaded as
a ZIP.

Return to the latest state with:

```bash
git checkout main
```

---

## GitHub Release Tags

| Version | Tag | Commit Hash |
|---------|-----|-------------|
| v1.00 | `v1.00` | _(filled after push)_ |
| v1.01 | `v1.01` | _(filled after push)_ |
| v1.02 | `v1.02` | _(filled after push)_ |
| v1.03 | `v1.03` | _(filled after push)_ |
| v1.04 | `v1.04` | _(filled after push)_ |
| v1.05 | `v1.05` | _(filled after push)_ |
| v1.06 | `v1.06` | _(filled after push)_ |
| v1.07 | `v1.07` | _(filled after push)_ |
| v1.08 | `v1.08` | _(filled after push)_ |

Regenerate this list any time with:

```bash
git tag | sort | xargs -I{} git log -1 --format="{} %H" {}
```

---

## When the Prof / Client Requests Changes After a Presentation

Fix it on `main` first, then re-point the affected version tag at the new commit:

```bash
# 1. Make the fix on main
git checkout main
git add .
git commit -m "feat: update [page] per feedback"
git push origin main

# 2. Delete the old tag (local + remote) and re-create it on the new commit
git tag -d vX.XX
git push origin :refs/tags/vX.XX
git tag vX.XX
git push origin vX.XX
```

Only re-point the tag of the version the feedback applies to. Versions after it are
unaffected unless the same page appears in them (later tags already include newer
commits once re-created the same way).
