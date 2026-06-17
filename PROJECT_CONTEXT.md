# Blog Workflow — Project Context

> **Purpose of this file:** Permanent project memory and onboarding document for Claude Code sessions. Read this before making any changes. Update this file whenever a significant development milestone is completed.
>
> Last updated: 2026-06-17 — UI polish session complete. Sidebar, topbar, headings, and dashboard CTAs all finalised.

---

## Project Overview

An internal tool for managing the end-to-end lifecycle of client blog content batches. The admin team creates clients, assembles blog articles into jobs, sends clients a private review link, collects approvals or decline feedback, manages revision cycles, and exports completed jobs as PDF/ZIP packages.

There are no external user accounts. Access is controlled by a single shared access code for the admin panel, and tokenised URLs for the client review portal.

---

## Business Purpose

- Replace manual back-and-forth (email/Google Docs) for blog content review
- Give clients a clean, branded portal to approve or decline articles with written feedback
- Enforce a maximum revision cycle limit to prevent scope creep
- Produce exportable PDF deliverables for each completed job

---

## Current MVP Scope

- Admin panel: client management, job management, blog article management
- Client review portal: tokenised, public, no login required
- Revision cycle enforcement (configurable maximum, default 2)
- Admin notification log for key workflow events
- ZIP export of per-blog PDFs for completed jobs

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11 |
| PHP | 8.2+ |
| Database | SQLite (file: `database/database.sqlite`) |
| Frontend CSS | Tailwind CSS 3.x with custom component styles (light neutral + blue brand palette) |
| Frontend JS | Alpine.js 3.x (review portal only) |
| Build tool | Vite 5 with `laravel-vite-plugin` |
| PDF generation | `barryvdh/laravel-dompdf` ^3.0 |
| ZIP export | PHP `ZipArchive` (built-in) |
| Auth | None — session flag + access code for admin; token in URL for review portal |

---

## Database Structure

Four tables. All use cascade deletes except `admin_notifications.job_id` which nullifies on delete.

### `clients`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| logo_path | string nullable | stored in `storage/app/public/logos/` |
| website | string nullable | |
| business_description | text nullable | |
| primary_keywords | text nullable | |
| secondary_keywords | text nullable | |
| target_locations | text nullable | |
| target_audience | text nullable | |
| tone_of_voice | text nullable | |
| status | string | enum: `active`, `inactive`; indexed |
| notes | text nullable | |
| timestamps | | |

### `jobs`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| client_id | FK → clients | cascade delete; indexed |
| title | string | |
| status | string | enum: `draft`, `in_review`, `completed`; indexed |
| revision_count | unsigned tinyint | default 0 |
| review_token | string(64) | unique; auto-generated on create |
| review_submitted_at | timestamp nullable | set when client submits review |
| completed_at | timestamp nullable | set when job is finalised |
| timestamps | | |

### `blogs`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| job_id | FK → jobs | cascade delete; indexed |
| sort_order | unsigned smallint | default 0; auto-incremented on create |
| title | string | |
| content | longtext | intended to store HTML from a WYSIWYG editor |
| meta_title | string nullable | |
| meta_description | text nullable | |
| focus_keyword | string nullable | |
| focus_location | string nullable | |
| status | string | enum: `pending`, `approved`, `declined`; indexed |
| client_notes | text nullable | populated by client when declining |
| timestamps | | |

### `admin_notifications`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| type | string | enum: `review_submitted`, `job_completed`, `revision_limit_reached` |
| message | string | |
| job_id | FK → jobs nullable | set null on delete |
| read_at | timestamp nullable | null = unread; indexed |
| timestamps | | |

---

## Status Enums

All status fields use PHP 8.1 backed enums with a `label()` helper method.

- `App\Enums\BlogStatus` — `Pending`, `Approved`, `Declined`
- `App\Enums\JobStatus` — `Draft`, `InReview`, `Completed`
- `App\Enums\ClientStatus` — `Active`, `Inactive`
- `App\Enums\NotificationType` — `ReviewSubmitted`, `JobCompleted`, `RevisionLimitReached`

---

## Architecture

### Pattern
Clean MVC with a dedicated service layer. Controllers are thin — all domain logic lives in services.

### Key Files

```
app/
  Enums/                          — Status enums
  Http/
    Controllers/
      AccessController.php        — Admin access code gate
      DashboardController.php     — Admin dashboard KPIs
      Admin/
        ClientController.php      — Client CRUD + logo upload
        JobController.php         — Job CRUD + workflow transitions
        BlogController.php        — Blog CRUD (nested under jobs)
        ExportController.php      — ZIP download trigger
        NotificationController.php — Notification centre
      Review/
        ReviewController.php      — Client review portal display
        ReviewSubmissionController.php — Review AJAX endpoints
    Middleware/
      EnsureAdminAccess.php       — Session flag check for /admin/*
    Requests/                     — Form Request validation classes
  Models/
    Client.php  Job.php  Blog.php  AdminNotification.php
  Services/
    JobWorkflowService.php        — sendForReview, prepareReReview, complete, finalize
    ReviewSubmissionService.php   — updateBlogReview, submitReview, finalize
    NotificationService.php       — notify(), unreadCount()
    JobExportService.php          — exportJobAsZip() using DomPDF + ZipArchive
  Providers/
    AppServiceProvider.php        — View composers for unread notification count

resources/
  css/
    admin.css   — Dark-themed admin panel (CSS variables + Tailwind)
    review.css  — Light-themed review portal (CSS variables + Tailwind)
  js/
    app.js      — Alpine.js reviewPortal() component (review portal only)
  views/
    layouts/    — admin.blade.php, guest.blade.php, review.blade.php
    access/     — Access code entry page
    admin/      — All admin panel views (clients, jobs, blogs, notifications, dashboard)
    review/     — Client review portal views + partials + modals
    exports/    — blog-pdf.blade.php (DomPDF template)

config/
  blog-workflow.php               — max_revisions (default 2), access_code (from env)

routes/
  web.php                         — All routes (admin group behind 'admin' middleware)
```

### Admin Access
- Middleware `EnsureAdminAccess` checks `session('admin_unlocked')`
- `AccessController::store()` validates against `config('blog-workflow.access_code')` (env: `ADMIN_ACCESS_CODE`)
- No user model — one shared access code for all admin users

### Review Portal
- Public routes — no session or auth required
- `review_token` (64-char random string) in URL acts as the identifier
- CSRF tokens passed via `x-csrf-token` header in Alpine.js `fetch` calls (read from a data attribute on the root element)

### View Composers
`AppServiceProvider` registers two view composers that inject `unreadNotifications` count into `admin.partials.sidebar` and `admin.partials.topbar` on every admin page load.

---

## Implemented Features (as of 2026-06-17)

- [x] Admin access code gate (session-based)
- [x] Dashboard with KPI cards and recent jobs
- [x] Client CRUD with logo upload and full profile fields
- [x] Job CRUD with auto-generated review token
- [x] Blog CRUD nested under jobs with auto sort_order
- [x] Workflow transitions: Draft → InReview → Completed
- [x] Prepare Re-Review: reset declined blogs to pending
- [x] Client review portal (tokenised, public)
- [x] Per-blog approve/decline with notes (Alpine.js AJAX)
- [x] Review progress bar
- [x] Review submit + finalize flow
- [x] Revision cycle enforcement (configurable max, default 2)
- [x] Admin notification log (ReviewSubmitted, JobCompleted, RevisionLimitReached)
- [x] Mark notification read / mark all read
- [x] ZIP export of per-blog PDFs (DomPDF) — confirmed working, returns `application/zip`
- [x] Status badges and status enums throughout
- [x] Form Request validation on all inputs
- [x] Copy protection on review portal (CSS + JS)
- [x] Responsive admin (light neutral theme) and review (light) layouts
- [x] Admin sidebar: hover-expand (80px collapsed → 256px expanded) with SVG Heroicons; smooth symmetric open/close via padding-left + gap transitions
- [x] Admin topbar: 80px, neutral-50/80 frosted glass, tab-pill nav (Dashboard / Notifications), bell icon with red dot for unread
- [x] Sidebar: all icons pixel-perfect centred at x=40 in collapsed state (padding-left trick, no justify-content switching)
- [x] Sidebar: logout button at bottom with separator line; neutral grey active state (not blue)
- [x] Page headings: Inter 30px/500 h1 on all index pages (Dashboard, Jobs, Clients, Notifications)
- [x] Dashboard quick actions: "Add Client" (secondary) + "Add Job" (primary) buttons in heading row
- [x] Button variants: `.btn-primary` (dark solid) and `.btn-secondary` (white outlined) matching reference design
- [x] Font consistency: `.card-title` uses Inter 600 throughout (no more DM Serif in body content)
- [x] **Review link Copy button** on job show page (tokenised URL in a textbox + Copy button)
- [x] **Admin "Complete" button** on job show page when job is InReview (alongside Prepare Re-Review)

---

## Known Bugs (as of 2026-06-17)

| # | Severity | Location | Description |
|---|----------|----------|-------------|
| 1 | Low | `routes/web.php:14,16` | Root `GET /` redirect defined twice — second silently overrides first |
| 2 | Medium | `config/app.php` | Custom key `admin_access_code` is dead code — `AccessController` uses `config('blog-workflow.access_code')` instead |
| 3 | Medium | `JobExportService.php` | `@unlink` and `@rmdir` suppress errors silently; ZIP failures give no user feedback |
| 4 | Low | `ReviewSubmissionService::submitReview()` | Revision limit reached notification fires but does not block a subsequent `/submit` call; only individual blog decline is guarded |
| 5 | Low | `Job::booted()` | No retry logic if `review_token` has a collision (negligible risk at this scale) |
| 6 | Low | `NotificationController::markAllRead()` | Bulk `update(['read_at' => now()])` overwrites already-set timestamps; should scope to `whereNull('read_at')` |
| 7 | Low | `Blog::booted()` | `max(sort_order)` on create has a theoretical race condition under concurrent creates |

---

## Missing Features

### Critical
- **Rich-text (WYSIWYG) editor for blog content** — `content` field is a plain `<textarea>` but the review portal and PDF both render it as HTML. Confirmed during smoke test: content renders as one long unformatted text block. Candidate: Quill or TipTap via CDN.

### Important
- **Database seeder** — no demo/test data; fresh installs require fully manual setup
- **ZIP cleanup on re-export** — `storage/app/exports/` accumulates old ZIPs; should delete prior ZIP for the same job before creating a new one
- **Remove hardcoded access code fallback** from `config/blog-workflow.php` — security hygiene

### Nice to Have
- Email notification for `ReviewSubmitted`
- Blog sort-order drag-and-drop reordering (Sortable.js)
- Soft deletes on clients and jobs
- Pagination on jobs list within client show view

> **Audit corrections (found during smoke test 2026-06-17):** "Copy Review Link" button and admin "Complete" button were flagged as missing in the original audit but are both already implemented and working.

---

## Development Principles

1. **Service layer for all domain logic** — controllers call services, never contain business rules directly
2. **Enums for all status fields** — never raw string comparisons in code
3. **Form Requests for all validation** — never `$request->validate()` inline in controllers
4. **No user auth model** — this is a shared-access internal tool; do not add Laravel Breeze/Sanctum/etc.
5. **SQLite only** — do not introduce MySQL or any server-dependent database
6. **No new NPM packages without discussion** — Tailwind + Alpine.js is the approved frontend stack; CDN additions (e.g. Quill) are acceptable for simple integrations
7. **No comments that describe what code does** — only comment the non-obvious why
8. **No backwards-compatibility shims** — this is an internal tool with no external API consumers

---

## Local Development Setup

```bash
# 1. Copy environment file (if .env doesn't exist)
cp .env.example .env

# 2. Generate app key (if APP_KEY is blank)
php artisan key:generate

# 3. Run migrations (SQLite file already exists in repo)
php artisan migrate

# 4. Link public storage (for client logos)
php artisan storage:link

# 5. Install frontend dependencies (if node_modules missing)
npm install

# 6. Start the development server via Claude Code preview
# Uses .claude/launch.json — starts PHP built-in server on port 8000
# with public/router.php as the router script (required for static asset serving)
```

**Admin access code:** Set `ADMIN_ACCESS_CODE` in `.env`. The current fallback in `config/blog-workflow.php` is `Hgbhad8v` (this should be removed — see Known Bugs #2).

**Important — server setup notes (resolved 2026-06-17):**
- `php artisan serve` fails on this machine with "Failed to listen" — use the PHP built-in server directly instead
- `public/router.php` is a custom router script that correctly serves static files (CSS/JS) and routes everything else through Laravel. It was created because `artisan serve` is unavailable and specifying `public/index.php` as the router sends all requests (including CSS) through Laravel
- `.claude/launch.json` is configured to use `php -S 127.0.0.1:8000 -t public public/router.php`
- Built Vite assets are committed at `public/build/` — `npm run dev` is not needed to view the app unless actively changing CSS/JS

---

## Current Project Status (2026-06-17)

**Phase:** Active development — admin UI polish session complete (2026-06-17).

Completed milestones:
- ✅ Full MVP smoke tested end-to-end (access gate, dashboard, review portal, export)
- ✅ Admin panel redesigned: light neutral palette, white cards, blue brand
- ✅ Sidebar polished to match reference design — smooth symmetric expand/collapse, logout, centred icons
- ✅ Topbar: 80px, tab-pill navigation, bell icon with unread dot
- ✅ Page headings (Inter 30px) + dashboard quick-action buttons
- ✅ Consistent Inter font throughout admin content

**Admin CSS variables (current):**
`--bg: #f5f5f5` / `--bg1: #ffffff` / `--bg2: #ffffff` / `--bg3: #f5f5f5` / `--border: #e5e5e5`
`--brand: #2563eb` / `--brandglow: #eff6ff` / `--brandborder: #dbeafe`
`--text: #171717` / `--text2: #737373` / `--text3: #a3a3a3`

**Next session priorities (in order):**
1. **WYSIWYG editor for blog content** — `resources/views/admin/blogs/_form.blade.php` — critical before real usage; content field is longtext but currently plain textarea; review portal and PDF render it as HTML
2. Fix duplicate root route — `routes/web.php` lines 14 & 16
3. Remove dead `admin_access_code` key from `config/app.php`
4. Remove hardcoded fallback access code from `config/blog-workflow.php`
5. Add database seeder for demo data
6. Clean up ZIP accumulation in `JobExportService`
