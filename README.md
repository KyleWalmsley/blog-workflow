# Blog Workflow Tool

Internal Laravel MVP for managing client blog batches, token-based client review, revision limits, in-app notifications, and ZIP/PDF export.

## Requirements

- PHP 8.2+
- Composer
- Node.js 18+ and npm
- SQLite (included; no separate server needed)

## Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file (if needed) and generate app key
cp .env.example .env
php artisan key:generate

# Create SQLite database and run migrations + demo seed data
touch database/database.sqlite
php artisan migrate --seed

# Link public storage for client logos
php artisan storage:link

# Install frontend dependencies and build assets
npm install
npm run build

# Start the development server
php artisan serve
```

Visit `http://127.0.0.1:8000` and enter the access code from `.env` (`ADMIN_ACCESS_CODE`, default: `Hgbhad8v`).

## Workflow

1. **Clients** — Create client profiles with SEO context and optional logo upload.
2. **Jobs** — Create a job for a client; a unique review token is generated automatically.
3. **Blogs** — Add HTML blog articles to the job from the job detail page.
4. **Send for Review** — Moves the job from draft to in-review and enables the client portal.
5. **Client Review** — Share `/review/{token}` with the client. They approve/decline each article and submit.
6. **Revision cycles** — Max 2 revisions (`config/blog-workflow.php`). Declined articles reset to pending via **Prepare Re-Review** after admin edits.
7. **Complete** — Client finalises when all approved, or admin completes manually.
8. **Export** — Download a ZIP of per-blog PDFs for completed jobs.

## Key Routes

| Route | Purpose |
|-------|---------|
| `/access` | Admin access gate |
| `/admin` | Dashboard |
| `/admin/clients` | Client CRUD |
| `/admin/jobs` | Job CRUD + workflow |
| `/admin/notifications` | Notification log |
| `/review/{token}` | Public client review portal |

## Configuration

| Variable | Description |
|----------|-------------|
| `ADMIN_ACCESS_CODE` | Shared access code for admin area (session-based, not user accounts) |
| `blog-workflow.max_revisions` | Maximum client revision cycles (default: 2) |

## Design

- **Admin area** — Dark theme aligned with Digital Growth Dashboard tokens (DM Serif Display, DM Mono, brand green `#34d399`).
- **Client portal** — Light reading-focused theme for article review.

## Notes

- No email, queues, or user authentication — internal MVP only.
- Copy deterrence on article content is best-effort (`user-select: none`, context menu disabled).
- For production or multi-user use, migrate to MySQL and add proper authentication.
