# Support & Training

## 🛠️ Technical Support

For technical issues or bugs:

1. Check the **Troubleshooting** section in the [README](README.md#-troubleshooting)
2. Review error logs in `storage/logs/laravel.log`
3. Contact your system administrator

## 💬 Customization Requests

For new features or modifications:

1. Document your requirements
2. Submit to the development team
3. Feature will be reviewed and prioritized

## 🎓 Training

For staff training:

1. Refer to the [Usage Guide](README.md#-usage-guide) in the README
2. Use demo accounts for practice:
   - **Cashier:** cashier@example.com / cashier123
   - **Doctor:** doctor@example.com / doctor123
   - **Receptionist:** receptionist@example.com / receptionist123
3. Schedule training sessions with your system administrator

## 🔄 Backup & Restore

### Backing Up

**SQLite Database:**
```bash
# Copy the database file
cp database/database.sqlite backup/database_$(date +%Y%m%d).sqlite

# Also copy WAL and SHM files (if they exist)
cp database/database.sqlite-wal* backup/ 2>/dev/null
cp database/database.sqlite-shm* backup/ 2>/dev/null
```

**Full Backup (Database + Uploads + Config):**
```bash
tar -czf med-que-backup-$(date +%Y%m%d).tar.gz \
  database/database.sqlite \
  storage/app \
  .env \
  database/database.sqlite-wal database/database.sqlite-shm 2>/dev/null
```

**What to Backup:**
| File/Folder | What It Contains |
|-------------|-----------------|
| `database/database.sqlite` | All data (patients, tickets, records, payments) |
| `database/database.sqlite-wal` | Pending writes (may not exist) |
| `database/database.sqlite-shm` | Shared memory (may not exist) |
| `storage/app/` | Uploaded files, receipts, logs |
| `.env` | Configuration, encryption key (⚠️ critical!) |

### Restoring

```bash
# Stop the server first
# Replace current database with backup
cp backup/database_20260405.sqlite database/database.sqlite

# Restore uploads if needed
tar -xzf med-que-backup-20260405.tar.gz

# Clear caches
php artisan cache:clear
php artisan config:clear
```

> ⚠️ **Never restore just the database without the `.env` file.** If the `APP_KEY` changes, encrypted data becomes unreadable.

## ⬆️ Update / Upgrade Guide

### When a New Version Releases

```bash
# 1. Backup first (see above)
cp database/database.sqlite backup/database_before_update.sqlite

# 2. Pull latest code
git pull origin main

# 3. Update dependencies
composer install --no-dev

# 4. Run new migrations
php artisan migrate --force

# 5. Clear caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# 6. Test the system
php artisan serve
```

### Update Checklist

- [ ] Database backup created
- [ ] Code pulled from repository
- [ ] Dependencies updated (`composer install`)
- [ ] Migrations run (`php artisan migrate`)
- [ ] Caches cleared
- [ ] System tested (login, create ticket, view report)
- [ ] Old backup kept (in case rollback needed)

### Rolling Back a Failed Update

```bash
# Restore database from pre-update backup
cp backup/database_before_update.sqlite database/database.sqlite

# Revert code to previous commit
git checkout <previous-commit-hash>

# Clear caches
php artisan cache:clear
```

## 🔒 Security Checklist

### After First Installation

- [ ] **Change all default passwords** (admin, cashier, doctor, etc.)
- [ ] **Set `APP_DEBUG=false`** in `.env` for production
- [ ] **Set `APP_ENV=production`** in `.env`
- [ ] **Use HTTPS** — set up SSL certificate (Let's Encrypt recommended)
- [ ] **Protect `.env` file** — set file permissions to `600` (owner read/write only)
- [ ] **Protect `database/` folder** — restrict access to web server only
- [ ] **Use strong passwords** — minimum 12 characters, mix of upper/lower/numbers/symbols
- [ ] **Regular backups** — daily for production, tested monthly

### Ongoing Security

- [ ] **Review audit logs** weekly — check for unusual activity
- [ ] **Update dependencies** monthly — run `composer outdated` to check
- [ ] **Review user roles** — remove accounts for departed staff
- [ ] **Rotate passwords** — every 90 days for admin accounts
- [ ] **Monitor disk space** — SQLite will fail if disk is full

### File Permissions (Linux/Unix)

```bash
# Web server can only read these directories
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chmod 600 .env
chmod 600 database/database.sqlite
```

## 📦 Production System Requirements

### Hardware Recommendations

| Users | CPU | RAM | Storage | Database |
|-------|-----|-----|---------|----------|
| 1-5 | 2 cores | 2 GB | 10 GB | SQLite (default) |
| 5-20 | 4 cores | 4 GB | 20 GB | SQLite or MySQL |
| 20+ | 4+ cores | 8+ GB | 50+ GB | MySQL / PostgreSQL |

### Software Requirements (Production)

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| **PHP** | 8.4 | 8.4+ |
| **Web Server** | PHP built-in | Nginx / Apache |
| **Database** | SQLite 3.35+ | MySQL 8.0+ / PostgreSQL 14+ |
| **OS** | Linux (Ubuntu 22.04+) | Ubuntu 24.04 LTS |

### Concurrent Users

- **SQLite (WAL mode):** Handles ~5-10 concurrent writes gracefully
- **MySQL/PostgreSQL:** Handles 50+ concurrent writes
- **Tip:** If you experience "database locked" errors frequently, switch to MySQL

## ❓ FAQ

### How do I change the clinic name?
Go to **Settings → Clinic Information** — you can change the name in both Arabic and English.

### How do I add a new department?
Go to **Departments → Add Department** — set the ticket prefix (shared) and queue prefix (unique).

### Can I delete a ticket by mistake?
Only Admin can delete completed tickets. Head Cashier can delete incomplete tickets. Deleted tickets cannot be recovered — use with caution.

### The system is slow. What can I do?
1. Check disk space (`df -h` on Linux)
2. Clear caches: `php artisan cache:clear`
3. Check logs: `tail -f storage/logs/laravel.log`
4. If using SQLite with many users, consider migrating to MySQL

### How do I change the currency?
Go to **Settings → Currency Settings** — update code, symbol, and decimal places. Existing amounts are not converted — only new displays change.

### Can I use this on multiple branches?
Not yet — multi-branch support is [planned for a future release](README.md#-upcoming-features).

### I forgot my admin password. How do I reset it?
```bash
php artisan tinker
```
Then run:
```php
\App\Models\User::where('email', 'admin@example.com')->update(['password' => bcrypt('newpassword123')]);
```

### How often should I backup?
- **Production:** Daily automated backup recommended
- **Testing:** Before any major change or update

## 📧 Contact & Issue Reporting

### Reporting Bugs

1. **Check existing issues** — search [GitHub Issues](https://github.com/omar-srakbi/med-que/issues) first
2. **Gather information:**
   - What you were doing
   - Error message (screenshot or text)
   - Steps to reproduce
   - System info (PHP version, OS, database type)
   - Relevant log entries from `storage/logs/laravel.log`
3. **Create a new issue** on GitHub with the above details

### Getting Help

| Channel | For |
|---------|-----|
| **GitHub Issues** | Bug reports, feature requests |
| **System Administrator** | Internal setup, user management |
| **Development Team** | Custom features, licensing |

---

## 📋 Quick Reference

### Default Admin Account
```
Email: admin@example.com
Password: password
```

> ⚠️ **Change all default passwords immediately after first login!**

### Common Commands

```bash
# Start development server
php artisan serve

# Clear caches (if issues occur)
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# View logs
tail -f storage/logs/laravel.log
```

### Useful Links

- [Installation Guide](README.md#-installation)
- [Configuration](README.md#-configuration)
- [User Roles](README.md#-user-roles)
- [Troubleshooting](README.md#-troubleshooting)
- [Upcoming Features](README.md#-upcoming-features)

---
