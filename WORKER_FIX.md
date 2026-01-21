# Queue Worker Fix - Complete Report

## Problem Identified
The queue worker container was continuously restarting every few minutes. Investigation revealed the root cause:

### Root Cause
The worker container was using a custom shell entrypoint that **did not run database migrations**. When the worker attempted to access the `jobs` table for the Laravel database queue driver, the table didn't exist, causing the queue:work command to fail silently.

**Error Chain:**
1. Worker starts with custom `sh` entrypoint
2. Config cache is cleared
3. `queue:work` command is executed
4. Queue driver tries to access the `jobs` table (which doesn't exist)
5. Job processing fails, the sh process exits
6. Docker restarts the container due to `restart: unless-stopped`
7. Cycle repeats every few minutes

## Solution Implemented

### Changes Made to Both `compose.dev.yaml` and `compose.prod.yaml`:

#### 1. **Added Database Migration Step**
```bash
php artisan migrate --force
```
This runs BEFORE the queue:work command to ensure the `jobs` table exists.

#### 2. **Fixed Restart Policy**
- **Changed from:** `restart: unless-stopped`
- **Changed to:** `restart: on-failure`

**Why:** 
- `unless-stopped`: Always restarts unless manually stopped (masks the real issue)
- `on-failure`: Only restarts if the process exits with an error (allows graceful shutdown and immediate detection of real issues)

#### 3. **Updated Queue Worker Command**
```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=60 --verbose
```

**Changes:**
- **Removed:** `--max-time=3600` (causes worker to exit after 1 hour regardless of jobs)
- **Added:** `--timeout=60` (individual job timeout - proper way to limit execution time)

### Modified Files
- `/Users/binyet/www/hpvtcrm/compose.dev.yaml` (Development environment)
- `/Users/binyet/www/hpvtcrm/compose.prod.yaml` (Production environment)

## Verification

### Development Testing
✅ Worker successfully:
1. Runs all database migrations on startup
2. Creates the `jobs` table
3. Starts the queue worker
4. Runs continuously without restarting
5. Properly processes jobs from the queue

### What Changed in Worker Startup Sequence
**Before (Broken):**
```
Starting queue worker...
Configuration cache cleared...
[FAIL] - jobs table doesn't exist
[Container restarts]
```

**After (Fixed):**
```
Ensuring database migrations are run...
Configuration cache cleared...
[Migrations create the jobs table]
Starting queue worker...
[Worker runs continuously, processing jobs]
```

## Deployment Instructions

### For Development
```bash
cd /Users/binyet/www/hpvtcrm
docker compose -f compose.dev.yaml down
docker compose -f compose.dev.yaml up -d
```

### For Production
```bash
# On production server
cd /var/www/hpvtcrm  # or your production path
docker compose -f compose.prod.yaml down
docker compose -f compose.prod.yaml up -d
```

## Benefits of This Fix

1. **Stable Worker Process:** Worker stays running continuously without spurious restarts
2. **Automatic Database Setup:** Migrations run automatically on worker startup
3. **Better Failure Handling:** Only restarts on actual failures, not on normal operation
4. **Proper Job Timeout:** Using `--timeout` instead of `--max-time` is the correct approach
5. **Production Ready:** Changes apply to both development and production environments

## Monitoring

To verify the worker is running properly:

```bash
# Check worker status
docker compose ps worker

# View worker logs
docker compose logs -f worker

# Check specific time range
docker logs <worker-container-id> --since 5m
```

The worker should show:
- Migration execution on startup
- "Starting queue worker..." message
- No error messages or container restarts

## Additional Notes

- The worker uses the same database connection as the application
- Jobs are stored in the `jobs` table created by Laravel migrations
- The `--verbose` flag provides detailed logging of job processing
- The `--sleep=3` parameter means the worker checks for new jobs every 3 seconds
- The `--tries=3` parameter means failed jobs are retried up to 3 times
