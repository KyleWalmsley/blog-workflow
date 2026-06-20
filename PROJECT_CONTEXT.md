# Blog Workflow — Project Context

> **Purpose of this file:** Permanent project memory and onboarding document for Claude Code sessions. Read this before making any changes. Update this file whenever a significant development milestone is completed.
>
> Last updated: 2026-06-20 — Admin panel content area visual redesign complete.

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

## Implemented Features (as of 2026-06-18)

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
- [x] **Client email field** — added to clients table, form, model, and validation
- [x] **Automated review delivery email** — triggered on "Send For Review"; uses runtime SMTP config from DB; logs to `outgoing_emails`
- [x] **Settings UI** — tab-based (`/admin/settings`); SMTP Settings tab + Email Templates tab; gear icon in sidebar above logout; active tab indicator overlaps divider line (classic tabs-on-a-rule pattern)
- [x] **Disence branding** — login screen uses `disence-full.png` (full horizontal logo, full card width, gradient background); sidebar uses `disence-icon.png` (44px collapsed, centred at x=40) cross-fading to `disence-sidebar.png` (icon-only wordmark, 32px height) on expand; all three files at `public/images/`
- [x] **SMTP settings stored in DB** — `settings` key-value table; `Setting::get/set()` helpers; configured at runtime via `Config::set()` (desktop app — no .env editing required)
- [x] **Email Templates** — `email_templates` table; "Review Invitation" template editable via UI; subject + body fields
- [x] **Outgoing email log** — `outgoing_emails` table with type, status, recipient, error capture; shown on Notifications → Outgoing tab
- [x] **Notifications split** — Incoming tab (admin_notifications) + Outgoing tab (outgoing_emails)
- [x] **Job Activity panel** — outgoing emails for a job shown chronologically on the job show page
- [x] **Warning flash** — amber flash type added; shown when email skipped (no client email or SMTP unconfigured)
- [x] **WYSIWYG editor** — Quill.js 2.x via CDN on blog create/edit pages; toolbar: headings, bold, italic, lists, link; pre-populated on edit; paragraph spacing shown in editor (`p { margin-bottom: 0.75em }`); `clipboard: { matchVisual: false }` preserves paragraph breaks on paste; form selection fixed via `hidden.closest('form')` (avoids attaching to sidebar logout form)
- [x] **Database seeder** — `DatabaseSeeder` seeds demo client (Lumina Coffee Roasters), two jobs, four blogs, one notification, and the Review Invitation email template
- [x] ZIP cleanup on re-export — old ZIP deleted before creating new one in `JobExportService`
- [x] Duplicate root route removed from `routes/web.php`
- [x] Dead `admin_access_code` key removed from `config/app.php`; `AccessController` corrected to read from `config('blog-workflow.access_code')`
- [x] Hardcoded fallback access code removed from `config/blog-workflow.php`
- [x] **Review portal — header alignment** — logo/title/client/revision row left-aligned to match main content column (220px left margin), no longer floating centred across full page width
- [x] **Review portal — one article at a time** — `toggleBlog()` closes all other articles before opening the clicked one
- [x] **Review portal — keyword/location labels** — "KEYWORDS" and "LOCATION" all-caps labels added inline before each chip in the collapsed card header so clients understand the context; `min-width: 68px; flex-shrink: 0` prevents labels from clipping into chips

---

## Known Bugs (as of 2026-06-18)

| # | Severity | Location | Description |
|---|----------|----------|-------------|
| 1 | Medium | `JobExportService.php` | `@unlink` and `@rmdir` suppress errors silently; ZIP failures give no user feedback |
| 2 | Low | `ReviewSubmissionService::submitReview()` | Revision limit reached notification fires but does not block a subsequent `/submit` call; only individual blog decline is guarded |
| 3 | Low | `Job::booted()` | No retry logic if `review_token` has a collision (negligible risk at this scale) |
| 4 | Low | `Blog::booted()` | `max(sort_order)` on create has a theoretical race condition under concurrent creates |

---

## Missing Features

### Nice to Have
- "Resend Review Email" button on job show page (retry failed or re-send email)
- Email notification for `ReviewSubmitted` (currently incoming notification only — no outbound email)
- Blog sort-order drag-and-drop reordering (Sortable.js)
- Soft deletes on clients and jobs
- Pagination on jobs list within client show view

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

**Admin access code:** Set `ADMIN_ACCESS_CODE` in `.env`. No fallback — the app will reject all logins if this env var is missing.

**Important — server setup notes (resolved 2026-06-17):**
- `php artisan serve` fails on this machine with "Failed to listen" — use the PHP built-in server directly instead
- `public/router.php` is a custom router script that correctly serves static files (CSS/JS) and routes everything else through Laravel. It was created because `artisan serve` is unavailable and specifying `public/index.php` as the router sends all requests (including CSS) through Laravel
- `.claude/launch.json` is configured to use `php -S 127.0.0.1:8000 -t public public/router.php`
- Built Vite assets are committed at `public/build/` — `npm run dev` is not needed to view the app unless actively changing CSS/JS

---

## Current Project Status (2026-06-20)

**Phase:** Active development — admin panel content area visual redesign complete (2026-06-20).

Completed milestones:
- ✅ Full MVP smoke tested end-to-end (access gate, dashboard, review portal, export)
- ✅ Admin panel redesigned: light neutral palette, white cards, blue brand
- ✅ Sidebar polished to match reference design — smooth symmetric expand/collapse, logout, centred icons; Settings above logout
- ✅ Topbar: 80px, tab-pill navigation, bell icon with unread dot
- ✅ Page headings (Inter 30px) + dashboard quick-action buttons
- ✅ Consistent Inter font throughout admin content
- ✅ Automated review delivery emails with DB-stored SMTP config
- ✅ Settings UI with SMTP + Email Templates tabs
- ✅ Outgoing email log + Notifications split into Incoming/Outgoing
- ✅ Job Activity panel (outgoing emails per job)
- ✅ Quill.js WYSIWYG editor on blog create/edit (form selection bug fixed; paragraph spacing; paste line breaks)
- ✅ Database seeder with demo data
- ✅ Review portal: header left-aligned, one-article-at-a-time accordion, keyword/location labels on card headers
- ✅ **Content area visual redesign** — all admin pages ported to Tailwind utility classes; custom semantic CSS classes removed
  - White cards: `bg-white border border-neutral-200 rounded-xl shadow-sm`
  - Tailwind grid utilities replace `.g2/.g3/.g4`; table pattern with `divide-y` and `hover:bg-neutral-50`
  - Status badge rewritten: micro-badge `text-[10px]` with colour-matched backgrounds
  - Right sidebar: 272px fixed, blue gradient fade at top (`linear-gradient` directly on `.right-sidebar`)
  - Topbar: `position: fixed; left: 80px; right: 0` — spans full width so bell sits at far right above right sidebar
  - Content padding: `52px 112px` (top/sides) — doubled horizontal padding matching reference design proportions
  - KPI cards: `p-7`, `text-3xl` numbers, relevant coloured SVG icons above each label
  - Table rows and headers: `py-4` for taller, more spacious feel
  - All card section headings: `text-xs font-semibold text-neutral-500 uppercase tracking-wide` — consistent with KPI labels

**Admin CSS layout (current):**
- `.page`: `padding: 52px 112px 48px; gap: 28px`
- `.content`: `margin-left: 80px; margin-right: 272px; padding-top: 80px`
- `.topbar`: `position: fixed; top: 0; left: 80px; right: 0; z-index: 45; padding: 0 32px 0 52px; height: 80px`
- `.right-sidebar`: `width: 272px; position: fixed; top: 80px; right: 0; height: calc(100vh - 80px); background: linear-gradient(180deg, #dbeafe 0%, #eff6ff 18%, #f8fbff 32%, #ffffff 52%)`

**Admin CSS variables (current):**
`--bg: #f5f5f5` / `--bg1: #ffffff` / `--bg2: #ffffff` / `--bg3: #f5f5f5` / `--border: #e5e5e5`
`--brand: #2563eb` / `--brandglow: #eff6ff` / `--brandborder: #dbeafe`
`--text: #171717` / `--text2: #737373` / `--text3: #a3a3a3`
`--amber: #f59e0b` / `--ambglow: #fffbeb` / `--ambborder: #fde68a` (warning flash)

**Key tables added previously (2026-06-18):**
- `settings` — key-value store; read via `Setting::get('key')`, write via `Setting::set('key', 'value')`
- `outgoing_emails` — log of all sent/failed emails; type enum: `review_invitation | reminder | completion`
- `email_templates` — editable email templates; name slug used as lookup key (e.g. `review_invitation`)
- `clients.email` — added for review invitation delivery

**Next session priorities (in order):**
1. Blog sort-order drag-and-drop — replace the sort order number input on blog create/edit with a drag-to-reorder UI on the job show page (Sortable.js or similar CDN)
2. New job type: **Website Content** — details to be discussed at the start of next session
