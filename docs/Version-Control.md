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
| v2.00 | New rollout cycle: v1.00 auth pages + Dashboard + Profile | login, forgot-password, logout, index, dashboard, profile | schedule, reports, logs, alerts, override, users |
| v3.00 | + Schedule + User Management | login, forgot-password, logout, index, dashboard, profile, schedule, users | reports, logs, alerts, override |

> **Note:** v2.00 starts a new presentation cycle and does not follow the strict
> "one more page than before" rule. Schedule, Reports, Logs, Alerts, Override, and
> Users were re-gated back to Under Construction; Dashboard and Profile stayed
> unlocked alongside the v1.00 auth pages. v3.00 continues this cycle by unlocking
> Schedule and User Management as well, leaving Reports, Logs, Alerts, and Override
> gated.

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
| v1.00 | `v1.00` | `56dec778d21ae9c1b8869a58a71306c5a458e8bf` |
| v1.01 | `v1.01` | `53ce1746cd81fbb7127f47c8034861d0d38fa784` |
| v1.02 | `v1.02` | `37b6546413a3ca41e3ecd1079ef95f8bd9220ddc` |
| v1.03 | `v1.03` | `e42e09d0859c095a6f8e834d06eecae4afbc99e9` |
| v1.04 | `v1.04` | `4637c4e309b52c9723532fe4ae48968ce8769af3` |
| v1.05 | `v1.05` | `a31c26b349a1673ab3582a45024a4ba3e96cce8f` |
| v1.06 | `v1.06` | `612fd13ffcd5d22f769532a15e07298655e39874` |
| v1.07 | `v1.07` | `52e83c1415054c5e4c3af6469b31008786b2e62f` |
| v1.08 | `v1.08` | `9f64df6f749d92f886fcdf1be496fe02a8e2d3da` |
| v2.00 | `v2.00` | `b3b444e33914be047c77523c8edc2d20bfb0a6e4` |
| v3.00 | `v3.00` | *(fill in after commit)* |

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
