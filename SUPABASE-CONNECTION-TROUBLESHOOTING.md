# Supabase Connection Troubleshooting Guide

## Current Error: "FATAL: Tenant or user not found"

This error occurs when connecting to the Supabase Connection Pooler. It indicates the pooler cannot find the specified tenant/user combination.

### Current Configuration
```
DB_HOST=aws-0-us-east-1.pooler.supabase.com
DB_PORT=5432
DB_USERNAME=postgres.nxegcxnihumeojtmbrht
DB_PASSWORD=sojjat-huVjat-2mikxa
DB_SSLMODE=require
```

## Root Cause Analysis

The error "Tenant or user not found" typically means:

1. **Invalid Pooler Configuration** - The Supabase pooler settings need to be verified
2. **Project State** - The Supabase project may be paused or in a restricted state
3. **Credentials Mismatch** - The username/password combination doesn't match the project

## Solution Strategies

### Option 1: Use Direct Database Connection (Recommended First Try)

Instead of the Connection Pooler, use the direct PostgreSQL connection:

```env
DB_HOST=db.nxegcxnihumeojtmbrht.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=sojjat-huVjat-2mikxa
DB_SSLMODE=require
```

**Note**: This requires stable internet connection and may have different rate limits.

### Option 2: Verify Pooler Credentials in Supabase Dashboard

1. Log in to Supabase Dashboard
2. Go to **Project Settings > Database > Connection Pooling**
3. Verify:
   - Connection string matches your `.env` settings
   - Username format (should include project ID)
   - Password is correct
   - SSL Mode is enabled

### Option 3: Try Different Pooler Endpoint

Supabase provides different pooler options. Try:
```
aws-0-us-east-1.pooler.supabase.com (current)
```

Or check your Supabase dashboard for the exact pooler hostname for your region.

### Option 4: Bypass Pooler with Full URL

Update `.env` with:
```env
DB_URL=postgresql://postgres:sojjat-huVjat-2mikxa@db.nxegcxnihumeojtmbrht.supabase.co:5432/postgres?sslmode=require
```

And configure `config/database.php` to use DB_URL.

## Configuration Files Updated

### .env
✅ Configured with Supabase credentials
✅ Set SSL mode to require
✅ Added Supabase API credentials

### config/database.php
✅ PostgreSQL driver set to 'require' SSL mode
✅ Search path set to 'public'

### SUPABASE-README.md
✅ Updated with database configuration details
✅ Added troubleshooting section

## Debugging Steps

### 1. Check Network Connectivity
```bash
ping db.nxegcxnihumeojtmbrht.supabase.co
curl -v https://nxegcxnihumeojtmbrht.supabase.co
```

### 2. Verify .env Variables
```bash
php artisan tinker
>>> env('DB_HOST')
>>> env('DB_USERNAME')
>>> env('DB_PASSWORD')
```

### 3. Test With psql (if installed)
```bash
psql -h aws-0-us-east-1.pooler.supabase.com -U "postgres.nxegcxnihumeojtmbrht" -d postgres -c "SELECT version();"
```

### 4. Enable Laravel Debug Logging
Update `.env`:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

Check `storage/logs/laravel.log` for detailed connection errors.

## Next Steps

1. **Try Option 1** (Direct Connection) first - simplest to diagnose
2. **Verify Supabase Dashboard** - ensure project is active
3. **Check Network** - ensure firewall allows port 5432
4. **Contact Supabase Support** - if credentials are confirmed correct

## Migration Commands

Once connection is established:

```bash
# Test connection
php artisan db:show

# Show migration status
php artisan migrate:status

# Run pending migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database with sample data
php artisan db:seed
```

## Important Notes

- ⚠️ Never commit `.env` to version control (contains sensitive credentials)
- 🔒 Service Role Key should only be used server-side
- 🌐 Anon Key can be safely exposed in frontend code
- 🔐 Always use SSL mode: `sslmode=require` for Supabase
