# How to Run & What to Fix - Med-Que Project

## Quick Start Guide

### Prerequisites
- **PHP 8.4+** (required by this project)
- **Composer**
- **Git**

### Step 1: Install PHP 8.4 (if not already installed)

For Debian/Ubuntu:
```bash
# Add Sury PHP repository
sudo apt-get update
sudo apt-get install -y ca-certificates curl gnupg
sudo curl -sSLo /usr/share/keyrings/deb-php.gpg https://packages.sury.org/php/apt.gpg
echo "deb [signed-by=/usr/share/keyrings/deb-php.gpg] https://packages.sury.org/php/ bookworm main" | sudo tee /etc/apt/sources.list.d/php.list

# Install PHP 8.4 with required extensions
sudo apt-get update
sudo apt-get install -y php8.4 php8.4-cli php8.4-common php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip php8.4-sqlite3 php8.4-mysql php8.4-bcmath

# Verify installation
php -v  # Should show PHP 8.4.x
```

### Step 2: Install Dependencies

```bash
# Navigate to project directory
cd /path/to/med-que

# Install Composer dependencies
composer install
```

### Step 3: Environment Setup

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### Step 4: Database Setup

```bash
# Create SQLite database file
mkdir -p database
touch database/database.sqlite

# Run migrations
php artisan migrate

# Run seeders (creates admin user and default data)
php artisan db:seed
```

### Step 5: Start Development Server

```bash
php artisan serve
```

Access the application at: **http://127.0.0.1:8000**

### Default Login Credentials

- **Email:** `admin@example.com`
- **Password:** `password`

---

## Issues Fixed

### 1. PHP Version Mismatch
**Problem:** Project requires PHP 8.4+, system had PHP 8.2
**Solution:** Upgraded to PHP 8.4 using Sury repository

### 2. Missing Vendor Directory
**Problem:** `vendor/autoload.php` not found
**Solution:** Ran `composer install`

### 3. Missing .env File
**Problem:** No `.env` file, application key missing
**Solution:** 
- Copied `.env.example` to `.env`
- Generated key with `php artisan key:generate`

### 4. Missing Database
**Problem:** SQLite database file doesn't exist
**Solution:**
- Created `database/database.sqlite`
- Ran migrations with `php artisan migrate`
- Ran seeders with `php artisan db:seed`

### 5. Race Condition in Ticket Creation
**Problem:** Unique constraint violation on `ticket_number` when creating tickets concurrently
**Root Cause:** 
- Multiple departments using same ticket number format (`TKT-{date}-{seq}`)
- No database locking when incrementing sequence numbers

**Solution:** Modified `app/Http/Controllers/TicketController.php`:
- Added `lockForUpdate()` on Department query
- Added `lockForUpdate()` on Ticket count query
- Added `lockForUpdate()` on Payment count query
- Wrapped all operations in database transaction

### 6. Duplicate Ticket Numbers Across Departments
**Problem:** All departments used same prefix `TKT`, causing unique constraint violations
**Solution:** Assigned unique prefixes to each department:
| Department | Prefix |
|------------|--------|
| Clinics | CLN |
| Kidney Center | KID |
| Blood Laboratory | LAB |
| Radiology Center | RAD |
| MRI | MRI |
| Physiological Treatment | PHY |

**Command to fix:**
```bash
php artisan tinker --execute="
\$depts = [
    1 => ['prefix' => 'CLN'],
    2 => ['prefix' => 'KID'],
    3 => ['prefix' => 'LAB'],
    4 => ['prefix' => 'RAD'],
    5 => ['prefix' => 'MRI'],
    6 => ['prefix' => 'PHY'],
];
foreach(\$depts as \$id => \$data) {
    App\Models\Department::find(\$id)?->update(\$data);
}"
```

### 7. Clear Caches (if issues persist)
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Files Modified

### `app/Http/Controllers/TicketController.php`
- Added database locking to prevent race conditions
- Moved `DB::beginTransaction()` before department query
- Added `lockForUpdate()` to Department, Ticket, and Payment queries

---

## Recommended Improvements for Original Repository

1. **Add `.env` to .gitignore** (should already be there)
2. **Include a setup script** for first-time installation
3. **Add database seeding for department prefixes** with unique values
4. **Consider using UUIDs** for ticket numbers if global uniqueness is needed
5. **Add proper error handling** for database constraint violations
6. **Document PHP version requirements** in README.md
7. **Add a troubleshooting section** to README.md

---

## Troubleshooting

### Error: "No application encryption key has been set"
```bash
php artisan key:generate
```

### Error: "SQLSTATE[23000]: Integrity constraint violation: 19 UNIQUE constraint failed: tickets.ticket_number"
```bash
# Reset department sequences
php artisan tinker --execute="App\Models\Department::all()->each(function(\$d) { \$count = App\Models\Ticket::where('department_id', \$d->id)->count(); \$d->update(['ticket_current_seq' => \$count]); });"

# Ensure unique prefixes (see Issue #6 above)
```

### Error: "Class not found" after composer install
```bash
composer dump-autoload
```

### 500 Error on page load
```bash
# Check logs
tail -f storage/logs/laravel.log

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

---

## Project Structure Notes

- **Database:** SQLite at `database/database.sqlite`
- **Sessions:** Database-backed (`sessions` table)
- **Cache:** File-based (default Laravel)
- **Queue:** Sync driver (default)

---

## Last Updated
2026-03-26
