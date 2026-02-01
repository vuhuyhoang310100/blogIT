# Scheduled Publishing Feature Guide

This document provides a comprehensive overview of the Scheduled Publishing feature, including technical implementation, performance optimizations, and server configuration.

## 1. Feature Overview

The Scheduled Publishing feature allows authors to set a specific future date and time for a post to go live. The system automatically handles the transition from `Schedule` to `Published` state without manual intervention.

## 2. Technical Implementation

### Core Components

- **Model:** `app/Models/Post.php` (contains `scopeScheduledToPublish`)
- **Validation:** `app/Rules/ValidPublishedAt.php` (ensures dates are valid and in the future)
- **Service:** `app/Services/PostService.php` (centralizes publishing logic)
- **Command:** `app/Console/Commands/PublishScheduledPosts.php` (discovers due posts)
- **Job:** `app/Jobs/PublishPostJob.php` (executes the state change via the queue)

### Database Optimization

A composite index was added to the `posts` table to ensure the discovery query remains fast as the database grows:

```sql
CREATE INDEX posts_status_publish_at_index ON posts (status, publish_at);
```

### State Management

1. When status is set to `Schedule`, the user-provided date is stored in `publish_at`. `published_at` remains `null`.
2. When the job executes, `status` becomes `Published`, `published_at` is set to `now()`, and `publish_at` is cleared (`null`).

## 3. How to Use (Admin Dashboard)

### Scheduling a Post

1. Open the **Post Editor** (Create or Edit).
2. Go to the **Settings** tab.
3. In the **Publication Status** section, change the status to **Schedule**.
4. A **Scheduled For** field will appear. Select your desired future date and time.
5. Click **Save Post**.

### UI Feedback

- Scheduled posts appear with a **blue "Scheduled" badge** in the post list.
- A warning will appear in the editor if you select a date that has already passed.

## 4. Server Configuration & Deployment

To ensure scheduled posts go live automatically, you must configure the following on your server:

### A. Task Scheduler (Cron)

Laravel's scheduler must run every minute to check for posts that have reached their scheduled time.

Add this entry to your server's crontab (`crontab -e`):

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### B. Queue Worker (Supervisor)

The actual publishing process is handled in the background via the queue. You must have a queue worker running.

**Example Supervisor Configuration:**

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path-to-your-project/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=your-user
numprocs=2
redirect_stderr=true
stdout_logfile=/path-to-your-project/storage/logs/worker.log
```

## 5. Maintenance & CLI Commands

### Manual Trigger

To manually check and publish due posts without waiting for the cron job:

```bash
php artisan app:publish-scheduled-posts
```

### Testing locally

If you don't have a queue worker running locally, you can process the publishing job manually after running the command:

```bash
php artisan queue:work --once
```

## 6. Performance & Safety Notes

- **Overlapping:** The command is configured with `withoutOverlapping()`, ensuring that if a large batch of posts takes more than a minute to process, a second command won't start and cause conflicts.
- **Atomicity:** The publishing transition happens inside a database transaction with a `lockForUpdate()` to prevent race conditions.
- **Validation:** The system ignores date fields when status is `Draft` or `Pending`, preventing accidental validation blocks.
