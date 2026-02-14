# Supabase Database Migration Guide

## Configuration Summary

Your Laravel application has been successfully configured to connect to Supabase PostgreSQL. This guide outlines the setup and migration process.

## Current Configuration

### Environment Variables (`.env`)
```
DB_CONNECTION=pgsql
DB_HOST=db.nxegcxnihumeojtmbrht.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=sojjat-huVjat-2mikxa
DB_SSLMODE=require

SUPABASE_URL=https://nxegcxnihumeojtmbrht.supabase.co
SUPABASE_ANON_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im54ZWdjeG5paHVtZW9qdG1icmh0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzA5Njk2ODYsImV4cCI6MjA4NjU0NTY4Nn0.x-hHhJylwDRGiY_q0g7YRQ_bLS-S19xbOxJN5J87qnA
SUPABASE_SERVICE_ROLE_KEY=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im54ZWdjeG5waHVraWF1ZW9ydG1icmh0Iiwicm9sZSI6InNlcnZpY2Vfcm9sZSIsImlhdCI6MTc3MDk2OTY4NiwiZXhwIjoyMDg2NTQ1Njg2fQ.4XisxLpMQazajAbuUpTOrjEecI1_p4tWJzAtbMLSigI
```

## Database Configuration Updates

### 1. `.env` file
- ✅ Updated `DB_CONNECTION` from URL format to `pgsql`
- ✅ Added individual database credentials:
  - `DB_HOST`: Supabase PostgreSQL host
  - `DB_PORT`: PostgreSQL port (5432)
  - `DB_DATABASE`: Database name (postgres)
  - `DB_USERNAME`: Database user (postgres)
  - `DB_PASSWORD`: Database password
- ✅ Enabled SSL mode: `DB_SSLMODE=require` (required for Supabase)
- ✅ Added Supabase API credentials for REST API access

### 2. `config/database.php`
- ✅ Updated PostgreSQL driver configuration
- ✅ Changed `sslmode` from `prefer` to `require` for secure connections

## Migration Steps

### Step 1: Test Database Connection
```bash
php artisan db:show
```

If you see database information, the connection is successful.

### Step 2: Run Pending Migrations
```bash
php artisan migrate
```

This will create all tables defined in your migrations:
- `users` table
- `cache` table
- `jobs` table
- `brands` table
- `categories` table
- `products` table
- `orders` table
- `order_items` table
- `personal_access_tokens` table
- `wishlists` table

### Step 3: Seed Database (Optional)
If you want to populate test data:
```bash
php artisan db:seed
```

### Step 4: Verify Tables
```bash
php artisan tinker
>>> DB::connection()->getPdo()
>>> DB::table('users')->count()
```

## Troubleshooting

### Connection Errors

#### "could not translate host name to address"
- Check your internet connection
- Verify the Supabase project is active
- Ensure firewall allows outbound connections to Supabase on port 5432

#### "SQLSTATE[08006]"
- Verify `DB_HOST`, `DB_USERNAME`, and `DB_PASSWORD` are correct
- Check that SSL mode is set to `require` (Supabase requires SSL)
- Ensure your Supabase project hasn't been paused

#### "SQLSTATE[HY000]"
- Confirm the database password is correct
- Check for special characters in password that may need escaping

### Existing Data
If you already have data in Supabase and need to preserve it:
1. Comment out migrations that create tables that already exist
2. Run only the new migrations with: `php artisan migrate --step`
3. Or use: `php artisan migrate:status` to see which migrations have run

## Security Notes

1. **Never commit `.env` file** - It contains sensitive credentials
2. **Service Role Key** - Only use on the server side, never expose to frontend
3. **Anon Key** - Can be exposed to frontend; use Row Level Security (RLS) for access control
4. **SSL Enforcement** - Always use `sslmode=require` for Supabase connections

## Database URL Format

If you prefer using a single connection URL instead of separate credentials, Supabase provides:
```
postgresql://postgres:[PASSWORD]@db.[PROJECT-ID].supabase.co:5432/postgres?sslmode=require
```

To use this format:
1. Set `DB_URL` environment variable with the complete URL
2. Comment out individual `DB_*` credentials
3. The database config will use `DB_URL` if present

## Next Steps

1. Run migrations: `php artisan migrate`
2. Test the connection with Tinker: `php artisan tinker`
3. Verify table creation in Supabase Dashboard
4. Test API endpoints with sample data
5. Configure Row Level Security (RLS) policies in Supabase for security

## Support Resources

- [Supabase Documentation](https://supabase.com/docs)
- [Laravel Database Documentation](https://laravel.com/docs/database)
- [Supabase PostgreSQL Connection](https://supabase.com/docs/guides/database/postgres)
