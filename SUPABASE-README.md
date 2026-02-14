# Supabase Configuration Guide

To integrate Supabase into this Laravel project, you will need the following credentials from your Supabase project settings.

## Environment Variables

Add these variables to your `.env` file:

```env
SUPABASE_URL=your-project-url.supabase.co
SUPABASE_ANON_KEY=your-anon-public-key
SUPABASE_SERVICE_ROLE_KEY=your-service-role-secret-key
```

## Connection Details (Confirmed)
- **Host:** aws-1-ap-northeast-1.pooler.supabase.com
- **Port:** 6543
- **User:** postgres.nxegcxnihumeojtmbrht
- **Password:** sojjat-huVjat-2mikxa
- **Region:** Tokyo (ap-northeast-1)
- **SSL Mode:** require

## Credential Details

- **SUPABASE_URL**: The API URL found under `Settings > API` in your Supabase dashboard.
- **SUPABASE_ANON_KEY**: The `anon` `public` key used for client-side interactions and RLS (Row Level Security).
- **SUPABASE_SERVICE_ROLE_KEY**: The `service_role` `secret` key. **Warning: This key bypasses RLS.** Use it only on the server side (Laravel backend) and never expose it to the frontend.

## Integration Notes

- You can find these in the Supabase Dashboard under **Project Settings > API**.
- For Laravel integration, you might consider using a package like `supabase-php` or using Laravel's built-in HTTP client to interact with the Supabase REST API.
connectionstring => postgresql://postgres:sojjat-huVjat-2mikxa@db.nxegcxnihumeojtmbrht.supabase.co:5432/postgres

url => https://nxegcxnihumeojtmbrht.supabase.co

publickey => sb_publishable_TP_PlTOoqK4FOLDXYdf_ZA_VU2Lky9O

annonkey => eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Im54ZWdjeG5paHVtZW9qdG1icmh0Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NzA5Njk2ODYsImV4cCI6MjA4NjU0NTY4Nn0.x-hHhJylwDRGiY_q0g7YRQ_bLS-S19xbOxJN5J87qnA

databasepassword => sojjat-huVjat-2mikxa
