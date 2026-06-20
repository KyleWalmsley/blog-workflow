# Blog Workflow — Project Context

> **Purpose of this file:** Permanent project memory and onboarding document for Claude Code sessions. Read this before making any changes. Update this file whenever a significant development milestone is completed.
>
> Last updated: 2026-06-20 — Website Copywriting job type complete; UI polish session complete.

---

## Project Overview

An internal tool for managing the end-to-end lifecycle of client content jobs. The admin team creates clients, assembles content into jobs (blog articles or website copy sections), sends clients a private review link, collects approvals or decline feedback, manages revision cycles, and exports completed jobs as PDF/ZIP packages.

There are no external user accounts. Access is controlled by a single shared access code for the admin panel, and tokenised URLs for the client review portal.

**Planned deployment:** `content.navigro.co.uk` subdomain (once `navigro.co.uk` is purchased). Currently runs locally via PHP built-in server.

---

## Business Purpose

- Replace manual back-and-forth (email/Google Docs) for content review
- Give clients a clean, branded portal to approve or decline content with written feedback
- Enforce a maximum revision cycle limit to prevent scope creep
- Produce exportable PDF deliverables for each completed job
- Part of the Navigo business — internal tooling, not distributed

---

## Current MVP Scope

- Admin panel: client management, job management, blog article management, copy section management
- Two job types: **Blog Creation** (blog articles) and **Website Copywriting** (structured sections)
- Client review portal: tokenised, public, no login required
- Revision cycle enforcement (configurable maximum, default 2)
- Admin notification log for key workflow events
- ZIP export of per-article or per-section PDFs for completed jobs

---

## Technology Stack

| Layer | Technology |
|-------|-----------|
| Framework | Laravel 11 |
| PHP | 8.2+ |
| Database | SQLite (file: `database/database.sqlite`) |
| Frontend CSS | Tailwind CSS 3.x with custom component styles |
| Frontend JS | Alpine.js 3.x (review portal + copy section forms) |
| Build tool | Vite 5 with `laravel-vite-plugin` |
| PDF generation | `barryvdh/laravel-dompdf` ^3.0 |
| ZIP export | PHP `ZipArchive` (built-in) |
| Auth | None — session flag + access code for admin; token in URL for review portal |

---

## Database Structure

### `clients`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| name | string | |
| email | string nullable | used for review invitation delivery |
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
| job_type | string | enum: `blog_creation`, `website_copywriting`; default `blog_creation` |
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
| content | longtext | HTML from Quill editor |
| meta_title | string nullable | |
| meta_description | text nullable | |
| focus_keyword | string nullable | |
| focus_location | string nullable | |
| status | string | enum: `pending`, `approved`, `declined`; indexed |
| client_notes | text nullable | populated by client when declining |
| timestamps | | |

### `copy_sections`
| Column | Type | Notes |
|--------|------|-------|
| id | PK | |
| job_id | FK → jobs | cascade delete; indexed |
| sort_order | unsigned smallint | set automatically from `CopySectionType::sortOrder()` |
| section_type | string | enum: see `CopySectionType`; determines which fields are used |
| title | string nullable | service/product name OR page name (for Meta type) |
| headline | string nullable | banner headline OR about_us heading |
| sub_headline | string nullable | banner sub-headline |
| content | longtext nullable | rich text; used by about_us, about_page, service |
| meta_title | string nullable | |
| meta_description | text nullable | |
| status | string | enum: `pending`, `approved`, `declined` |
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

### `settings`
Key-value store. Read via `Setting::get('key')`, write via `Setting::set('key', 'value')`. Used for SMTP config stored at runtime (no .env editing required).

### `outgoing_emails`
Log of all sent/failed emails. Type enum: `review_invitation | reminder | completion`.

### `email_templates`
Editable email templates. Name slug used as lookup key (e.g. `review_invitation`).

---

## Enums

All status fields use PHP 8.1 backed enums with a `label()` helper.

- `App\Enums\BlogStatus` — `Pending`, `Approved`, `Declined`
- `App\Enums\JobStatus` — `Draft`, `InReview`, `Completed`
- `App\Enums\JobType` — `BlogCreation = 'blog_creation'`, `WebsiteCopywriting = 'website_copywriting'`
- `App\Enums\CopySectionType` — `Banner`, `AboutUs`, `AboutPage`, `Service`, `Meta`
- `App\Enums\CopySectionStatus` — `Pending`, `Approved`, `Declined`
- `App\Enums\ClientStatus` — `Active`, `Inactive`
- `App\Enums\NotificationType` — `ReviewSubmitted`, `JobCompleted`, `RevisionLimitReached`

### CopySectionType field matrix

| Type | headline | sub_headline | title (page/name) | content | meta |
|------|----------|-------------|-------------------|---------|------|
| `banner` | ✅ Headline | ✅ Sub-headline | — | — | — |
| `about_us` | ✅ Heading | — | — | ✅ | — |
| `about_page` | — | — | — | ✅ | ✅ |
| `service` | — | — | ✅ Service name | ✅ | ✅ |
| `meta` | — | — | ✅ Page name | — | ✅ |

Sort order (from `CopySectionType::sortOrder()`): banner → about_us → about_page → service → meta. Set automatically on create; no UI sort field.

---

## Architecture

### Pattern
Clean MVC with a dedicated service layer. Controllers are thin — all domain logic lives in services.

### Key Files

```
app/
  Enums/
    BlogStatus.php  JobStatus.php  JobType.php  ClientStatus.php
    CopySectionType.php  CopySectionStatus.php  NotificationType.php
  Http/
    Controllers/
      AccessController.php
      DashboardController.php
      Admin/
        ClientController.php
        JobController.php          — passes jobTypes to create/edit; copySections to show
        BlogController.php
        CopySectionController.php  — NEW: CRUD for copy sections nested under jobs
        ExportController.php
        NotificationController.php
      Review/
        ReviewController.php       — loads copySections for copywriting jobs
        ReviewSubmissionController.php — updateBlogReview + updateSectionReview
    Requests/
      Admin/
        StoreCopySectionRequest.php   — NEW
        UpdateCopySectionRequest.php  — NEW
      Review/
        UpdateSectionReviewRequest.php — NEW
  Models/
    Client.php  Job.php  Blog.php  CopySection.php  AdminNotification.php
  Services/
    JobWorkflowService.php        — branches on isCopywriting() for sections vs blogs
    ReviewSubmissionService.php   — updateBlogReview + updateSectionReview
    NotificationService.php
    JobExportService.php          — branches on isCopywriting() for PDF export

resources/
  css/
    admin.css   — [x-cloak] rule added; modal styles for delete confirmation
    review.css  — section group headings, section subheadings, copy-section banner styles
  js/
    app.js      — reviewPortal() extended: jobType, sections[], toggleSection(),
                  setSectionStatus(), saveSectionNotes()
  views/
    layouts/    — admin.blade.php, guest.blade.php, review.blade.php (all have favicon)
    admin/
      copy-sections/
        create.blade.php   — loads Alpine.js + Quill via CDN
        edit.blade.php     — loads Alpine.js + Quill via CDN
        _form.blade.php    — Alpine x-data sectionType; x-cloak on all conditional blocks;
                             fields shown per type; no sort_order field
      jobs/show.blade.php  — custom delete modal (no browser confirm()); sections sorted by type
      notifications/index.blade.php — fixed-width type badges + action buttons
    review/show.blade.php  — section group headings between type groups; per-type subheadings
                             inside expanded cards; sections sorted by sortOrder()
    exports/
      blog-pdf.blade.php
      copy-section-pdf.blade.php  — NEW: PDF template for individual copy sections

routes/web.php
  — copy section admin routes (create, store, edit, update, destroy)
  — PATCH /review/{token}/sections/{copySection} → ReviewSubmissionController@updateSection
```

### Admin Access
- Middleware `EnsureAdminAccess` checks `session('admin_unlocked')`
- `AccessController::store()` validates against `config('blog-workflow.access_code')` (env: `ADMIN_ACCESS_CODE`)
- No user model — one shared access code for all admin users

### Review Portal
- Public routes — no session or auth required
- `review_token` (64-char random string) in URL acts as the identifier
- Alpine.js `reviewPortal()` component handles both job types
- For copywriting: sections passed as JSON sorted by `sortOrder()`; sidebar shows one entry per section; group headings separate types visually

### Alpine.js on Admin Pages
- Admin layout does **not** load Alpine globally
- Copy section create/edit pages load Alpine via CDN (`defer`) in `@push('scripts')`
- This is required for `x-data`/`x-show`/`x-model` in `_form.blade.php`

### Delete Confirmations
- Browser `confirm()` is **never used** — replaced with a custom styled modal
- `show.blade.php` contains a shared `#delete-modal` div + vanilla JS `openDeleteModal(form)` / `closeDeleteModal()`

---

## Implemented Features (as of 2026-06-20)

- [x] Admin access code gate (session-based)
- [x] Dashboard with KPI cards and recent jobs
- [x] Client CRUD with logo upload and full profile fields
- [x] Job CRUD with auto-generated review token — now includes **Job Type** selector
- [x] **Job Type: Blog Creation** — existing blog article workflow unchanged
- [x] **Job Type: Website Copywriting** — section-based content workflow
  - [x] Add/edit/delete copy sections (one at a time)
  - [x] Section types: Banner, About Us, About Page, Service/Product, Meta Data
  - [x] Fields shown per type (Alpine.js x-show with x-cloak)
  - [x] Sections ordered by type (no manual sort_order)
  - [x] Review portal: section group headings, per-section approve/decline
  - [x] Export: ZIP of per-section PDFs
- [x] Workflow transitions: Draft → InReview → Completed (both job types)
- [x] Prepare Re-Review: resets declined content to pending (both job types)
- [x] Client review portal (tokenised, public) — handles both job types
- [x] Review progress bar + submit/finalize flow (both job types)
- [x] Revision cycle enforcement (configurable max, default 2)
- [x] Admin notification log (Incoming + Outgoing tabs)
  - [x] Fixed-width type badges (uniform size regardless of label length)
  - [x] Mark Read / Read buttons same width, Read centred and faded
- [x] ZIP export of per-blog or per-section PDFs (DomPDF + ZipArchive)
- [x] Custom delete confirmation modal (no browser confirm() dialogs)
- [x] Navigo branding throughout (navigo-full.png, navigo-icon.png, favicon.png)
- [x] SMTP settings stored in DB; Settings UI with SMTP + Email Templates tabs
- [x] Automated review delivery email on "Send For Review"
- [x] Outgoing email log + Job Activity panel
- [x] Quill.js WYSIWYG editor on blog + copy section create/edit
- [x] Database seeder with demo data
- [x] Review portal: "Click to expand" hint on collapsed cards
- [x] Copy protection on review portal

---

## Known Bugs

| # | Severity | Location | Description |
|---|----------|----------|-------------|
| 1 | Medium | `JobExportService.php` | `@unlink` and `@rmdir` suppress errors silently; ZIP failures give no user feedback |
| 2 | Low | `ReviewSubmissionService::submitReview()` | Revision limit notification fires but does not block a subsequent `/submit` call |
| 3 | Low | `Job::booted()` | No retry logic if `review_token` has a collision (negligible risk) |

---

## Missing / Nice to Have

- "Resend Review Email" button on job show page
- Email notification for `ReviewSubmitted` (currently incoming notification only)
- Blog sort-order drag-and-drop reordering
- Soft deletes on clients and jobs

---

## Development Principles

1. **Service layer for all domain logic** — controllers call services, never contain business rules
2. **Enums for all status/type fields** — never raw string comparisons in code
3. **Form Requests for all validation** — never `$request->validate()` inline in controllers
4. **No user auth model** — shared-access internal tool; do not add Breeze/Sanctum/etc.
5. **SQLite only** — do not introduce MySQL or any server-dependent database
6. **No new NPM packages without discussion** — Tailwind + Alpine.js approved; CDN additions acceptable
7. **No browser `confirm()` dialogs** — always use the custom `#delete-modal` pattern
8. **No comments that describe what code does** — only comment the non-obvious why
9. **Alpine.js on admin pages** — load via CDN in `@push('scripts')` on pages that need it; do not add to admin layout globally

---

## Local Development Setup

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan storage:link
npm install
# Server: php -S 127.0.0.1:8000 -t public public/router.php
# After any CSS/JS change: npm run build
```

**Admin access code:** Set `ADMIN_ACCESS_CODE` in `.env`.

**Server notes:** `php artisan serve` fails on this machine. Use PHP built-in server directly via `.claude/launch.json` (`php -S 127.0.0.1:8000 -t public public/router.php`). Built Vite assets committed at `public/build/`.

---

## Deployment Plan

- Target: `content.navigro.co.uk` (subdomain of `navigro.co.uk` — to be purchased)
- Hosting: small VPS (~£3-6/month)
- SSL: Let's Encrypt (free)
- Mail: domain email or Mailgun/Postmark via `navigro.co.uk`
- Review links will work correctly once deployed to live domain (currently `127.0.0.1:8000` only accessible locally)

---

## Current Project Status (2026-06-20)

**Phase:** Active development — Website Copywriting job type complete; UI polish complete.

Completed milestones:
- ✅ Full MVP (blog creation workflow) smoke tested end-to-end
- ✅ Admin panel redesigned: light neutral palette, white cards
- ✅ Navigo branding (logos, favicon)
- ✅ SMTP + email templates settings UI
- ✅ Outgoing email log + notifications split
- ✅ Quill.js WYSIWYG editor
- ✅ **Website Copywriting job type** — full workflow from create → sections → review → export
- ✅ Custom delete confirmation modal system
- ✅ Notifications table polished (uniform badge widths, aligned action buttons)
- ✅ Deployment plan: `content.navigro.co.uk` on VPS once domain purchased
