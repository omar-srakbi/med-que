# 🏥 Medical Center Management System

A comprehensive clinic management system built with Laravel 12 for managing patients, tickets, medical records, and financial operations.

![Laravel](https://img.shields.io/badge/Laravel-12-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.4+-777BB4?style=flat&logo=php)
![SQLite](https://img.shields.io/badge/Database-SQLite-003B57?style=flat&logo=sqlite)
![License](https://img.shields.io/badge/License-Proprietary-red?style=flat)

---

## 📖 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [User Roles](#-user-roles)
- [Default Credentials](#-default-credentials)
- [Usage Guide](#-usage-guide)
- [System Goals](#-system-goals)
- [Support](#-support)

---

## ✨ Features

### 🏥 Patient Management
- ✅ Quick patient registration with minimal information
- ✅ Complete patient profiles (name, parents, birth date, national ID, phone)
- ✅ Advanced search (name, national ID, phone)
- ✅ Patient history tracking (visits, medical records, payments)
- ✅ Incomplete profile management (receptionist completes later)

### 🎫 Ticket & Queue System
- ✅ Same-day ticket creation
- ✅ Advance booking (next day) - Head Cashier only
- ✅ Automatic queue number generation
- ✅ Real-time queue display screen
- ✅ Custom service shortcuts (e.g., CBC, XRAY, MRI)
- ✅ Quick select by typing shortcut codes
- ✅ Department-specific ticket numbering formats

### 🏢 Departments & Services
- ✅ 6 pre-configured departments:
  - Clinics (العيادات)
  - Kidney Center (مركز الكلى)
  - Blood Laboratory (مختبر الدم)
  - Radiology Center (مركز الأشعة)
  - MRI (الرنين المغناطيسي)
  - Physiological Treatment (العلاج الطبيعي)
- ✅ Custom service pricing per department
- ✅ Custom service shortcuts for quick booking
- ✅ Department-specific ticket numbering
- ✅ Service activation/deactivation

### 📋 Medical Records
- ✅ Diagnosis tracking
- ✅ Prescriptions management
- ✅ Test results storage
- ✅ Follow-up appointment scheduling
- ✅ Doctor and nurse access
- ✅ Linked to patient tickets

### 👥 Staff Management
- ✅ Role-based access control (RBAC)
- ✅ Custom role creation with specific permissions
- ✅ Staff assignment to roles
- ✅ Activity audit logs (who did what, when)
- ✅ Staff can be reassigned between roles
- ✅ Role enable/disable functionality

### 💰 Financial Management
- ✅ Payment tracking per ticket
- ✅ Receipt generation and printing
- ✅ Daily revenue reports
- ✅ Customizable currency:
  - Currency Code (JOD, USD, EUR, SAR, etc.)
  - Currency Symbol (JD, $, €, £, ر.س, etc.)
  - Decimal Places (0-3)
- ✅ Cash-only payments (expandable)

### 📊 Reports
- ✅ Daily patients report (new patients per day)
- ✅ Daily revenue report (income by department)
- ✅ Patient history report (complete visit history)
- ✅ Export to CSV
- ✅ Filter by date range, department, patient

### ⚙️ Settings & Customization
- ✅ Currency customization (code, symbol, decimals)
- ✅ Ticket format customization per department
- ✅ QR code options for tickets
- ✅ Department ticket settings (prefix, format, sequence)
- ✅ System-wide preferences

### 🔍 Search & Navigation
- ✅ Global search (patients, tickets, records)
- ✅ Advanced filters (date, department, status)
- ✅ Keyboard shortcuts for quick access
- ✅ Bilingual interface (Arabic RTL + English)

### 📱 User Interface
- ✅ Responsive design (desktop, tablet, mobile)
- ✅ Arabic (RTL) and English (LTR) support
- ✅ Modern Bootstrap 5 UI
- ✅ Interactive charts (Chart.js)
- ✅ Real-time updates (AJAX)

---

## 🛠️ Requirements

### Server Requirements:
- **PHP:** 8.4 or higher
- **Database:** SQLite (default) or MySQL/PostgreSQL
- **Web Server:** Apache/Nginx or PHP built-in server
- **Extensions:**
  - BCMath PHP Extension
  - Ctype PHP Extension
  - Fileinfo PHP Extension
  - JSON PHP Extension
  - Mbstring PHP Extension
  - OpenSSL PHP Extension
  - PDO PHP Extension
  - Tokenizer PHP Extension
  - XML PHP Extension

### Development Tools (Optional):
- **Composer:** For PHP dependencies
- **Node.js & NPM:** For frontend assets
- **Git:** For version control

---

## 📦 Installation

### 1. Clone or Download
```bash
git clone https://github.com/omar-srakbi/med-que.git
```

### 2. Install Dependencies
```bash
composer install
npm install  # Optional, for frontend assets
```

### 3. Setup Environment
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=medical_center
# DB_USERNAME=root
# DB_PASSWORD=
```

### 5. Run Migrations & Seeders
```bash
# Create database and run migrations
php artisan migrate --seed

# This creates:
# - All database tables
# - Default roles (Admin, Doctor, Nurse, etc.)
# - Default departments and services
# - Admin user account
```

### 6. Start Development Server
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## ⚙️ Configuration

### Currency Settings

Navigate to: **Settings → Currency Settings**

Configure your local currency:

| Setting | Example Values | Description |
|---------|---------------|-------------|
| **Currency Code** | JOD, USD, EUR, SAR | ISO currency code |
| **Currency Symbol** | JD, $, €, £, ر.س | Display symbol |
| **Decimal Places** | 0, 1, 2, 3 | Number of decimals |

**Examples:**
- Syria: `SYP` / `SP` / `0`
- Jordan: `JOD` / `JD` / `2`
- USA: `USD` / `$` / `2`
- Kuwait: `KWD` / `د.ك` / `3`
- Japan: `JPY` / `¥` / `0`

### Service Shortcuts

Navigate to: **Departments → Select Department → Add/Edit Service**

Add shortcuts for quick ticket creation:

| Shortcut | Service | Department |
|----------|---------|------------|
| `CBC` | Complete Blood Count | Blood Laboratory |
| `XRAY` | X-Ray | Radiology Center |
| `MRI` | MRI Scan | MRI |
| `KIDNEY1` | Kidney Function Test | Kidney Center |
| `CONSULT` | General Consultation | Clinics |

**Usage:**
1. Go to **Tickets → Create Ticket**
2. In **Quick Select** field, type shortcut (e.g., `CBC`)
3. Press **Enter** or **Tab**
4. System auto-selects department and service!

### Ticket Numbering

Navigate to: **Departments → Select Department → Edit → Ticket Settings**

Configure per department:

| Setting | Example | Description |
|---------|---------|-------------|
| **Ticket Prefix** | `TKT`, `OPD`, `ER` | Appears at start of ticket number |
| **Number Format** | `{prefix}-{date}-{seq}` | Pattern for ticket numbers |
| **Sequence Padding** | `4` | Number of digits (4 = 0001) |

**Variables:**
- `{prefix}` - Department prefix
- `{date}` - Date (YYYYMMDD)
- `{seq}` - Sequence number
- `{dept}` - Department abbreviation

**Example Format:**
```
Format: {prefix}-{date}-{seq}
Result: TKT-20260324-0001
```

---

## 👥 User Roles

### Role Permissions Matrix

| Permission | Admin | Doctor | Nurse | Lab Tech | Rad Tech | Receptionist | Cashier | Head Cashier |
|------------|-------|--------|-------|----------|----------|--------------|---------|--------------|
| View Patients | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Manage Patients | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| Delete Patients | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Create Tickets | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Delete Tickets (incomplete) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Delete Tickets (completed) | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| Advance Booking | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Create Payments | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| View Medical Records | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Manage Medical Records | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Quick Registration | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ | ✅ |
| Complete Patient Profiles | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Manage Settings | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| **Full System Access** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### Role Descriptions

#### **Admin**
- Full system access
- Can manage all roles and permissions
- Can delete any record
- Access to audit logs

#### **Doctor**
- View patient information and history
- Create and edit medical records
- View tickets and queue status
- Add diagnoses and prescriptions

#### **Nurse**
- View patient information
- Create medical records
- Assist doctors with patient care
- Update vital signs

#### **Lab Technician**
- View patient information
- Create and manage lab test records
- Upload test results
- Mark tests as complete

#### **Radiology Tech**
- View patient information
- Create and manage radiology records
- Upload X-ray/MRI images
- Mark procedures as complete

#### **Receptionist**
- Add and edit patient information
- Quick patient registration
- Complete incomplete patient profiles
- Answer patient inquiries

#### **Cashier**
- Create tickets for same-day visits
- Process payments
- Print receipts
- Quick patient registration

#### **Head Cashier**
- All Cashier permissions
- Create advance bookings (next day)
- Delete incomplete tickets
- Override ticket cancellations

---

## 🔐 Default Credentials

### Administrator Account
```
Email: admin@example.com
Password: password
```

### Cashier Account
```
Email: cashier@example.com
Password: cashier123
```

### Head Cashier Account
```
Email: headcashier@example.com
Password: head123
```

### Doctor Account
```
Email: doctor@example.com
Password: doctor123
```

### ⚠️ Security Notice
**Change these passwords immediately after first login!**

---

## 📚 Usage Guide

### Quick Ticket Creation (30 seconds)

**Method 1: Using Shortcuts**
1. Go to **Tickets → Create Ticket**
2. In **Quick Select** field, type shortcut (e.g., `CBC`)
3. Press **Enter** or **Tab**
4. System auto-selects department and service
5. Search/select patient (or type new name)
6. Submit → Ticket created!

**Method 2: Using Numeric Codes**
1. Go to **Tickets → Create Ticket**
2. In **Quick Select** field, type: `1.2`
   - `1` = First department
   - `2` = Second service in that department
3. Press **Enter**
4. System auto-selects!

### Quick Patient Registration

**For Rush Hours:**
1. Go to **Tickets → Create Ticket**
2. In **Patient** field, type patient name (e.g., "Ahmed Hassan")
3. If patient doesn't exist, system offers to create
4. Patient created with minimal info
5. Ticket issued immediately
6. **Later:** Receptionist completes patient profile

**For Normal Registration:**
1. Go to **Patients → Add Patient**
2. Fill all required fields:
   - First Name, Last Name
   - Father Name, Mother Name
   - Birth Date, Birth Place
   - National ID, Phone
3. Save → Patient fully registered

### Queue Management

**For Cashiers:**
1. Create ticket → Patient gets queue number
2. Patient waits in waiting area
3. Call patient when doctor ready

**For Doctors/Nurses:**
1. View queue status for your department
2. Click "Call Next" to call next patient
3. Mark ticket as complete when done

**For Patients:**
1. Receive ticket with queue number
2. Watch queue display screen
3. Go to department when number called

### Medical Records

**For Doctors:**
1. View patient ticket
2. Click "Add Medical Record"
3. Enter:
   - Diagnosis
   - Prescriptions
   - Test results
   - Follow-up date
4. Save → Record linked to patient

**For Receptionists:**
1. Go to **Patients → Incomplete Profiles**
2. See list of patients with minimal data
3. Click "Complete Profile"
4. Add missing information
5. Save → Profile complete

### Reports

**Daily Revenue Report:**
1. Go to **Reports → Daily Revenue**
2. Select date
3. View:
   - Total revenue
   - Revenue by department
   - Payment list
4. Export to CSV if needed

**Patient History:**
1. Go to **Reports → Patient History**
2. Select patient from dropdown
3. View:
   - All visits
   - All medical records
   - All payments
4. Print or export

---

## 🎯 System Goals

### 1. Reduce Wait Times ⏱️
- Quick registration (30 seconds)
- Fast ticket creation with shortcuts
- Organized queue management
- Real-time queue display

### 2. Minimize Paperwork 📄
- Digital tickets and receipts
- Electronic medical records
- Digital payment tracking
- CSV export for reports

### 3. Improve Patient Flow 🚶
- Organized queue system
- Department-specific queues
- Clear numbering system
- Visual queue display

### 4. Enhance Data Accuracy ✅
- Structured data entry forms
- Validation on all inputs
- Audit trail for all changes
- Role-based data access

### 5. Provide Insights 📊
- Daily patient statistics
- Revenue tracking
- Department performance
- Patient visit history

### 6. Ensure Security 🔒
- Role-based access control
- Activity audit logs
- Password protection
- Session management

### 7. Support Growth 📈
- Multi-department support
- Scalable architecture
- Customizable workflows
- Ready for multi-branch expansion

---

## 🆘 Troubleshooting

### Common Issues

**Issue: "Route not found"**
```bash
php artisan route:clear
php artisan view:clear
```

**Issue: "Permission denied"**
- Check user role permissions
- Ensure user is logged in
- Contact admin for access

**Issue: "Database locked"**
```bash
# Close all connections
# Delete database/database.sqlite-journal
# Try again
```

**Issue: "Patient not found in search"**
- Check spelling
- Try national ID search
- Patient may not be registered yet

---

## 📞 Support

### Technical Support
For technical issues or bugs:
1. Check the **Troubleshooting** section
2. Review error logs in `storage/logs/laravel.log`
3. Contact your system administrator

### Customization Requests
For new features or modifications:
1. Document your requirements
2. Submit to development team
3. Feature will be reviewed and prioritized

### Training
For staff training:
1. Refer to this README
2. Use demo accounts for practice
3. Schedule training sessions with admin

---

## 📄 License

**Proprietary Software** - All rights reserved.

This software is confidential and proprietary. Unauthorized copying, distribution, or use is strictly prohibited.

---

## 🙏 Credits

Built with:
- **Laravel 12** - PHP Framework
- **Bootstrap 5** - UI Framework
- **Chart.js** - Charts and Graphs
- **SortableJS** - Drag and Drop
- **Bootstrap Icons** - Icon Library

---

## 📱 System Information

| Component | Version |
|-----------|---------|
| **Framework** | Laravel 12.x |
| **PHP** | 8.4+ |
| **Database** | SQLite |
| **UI** | Bootstrap 5.3 |
| **Charts** | Chart.js 4.4 |
| **Icons** | Bootstrap Icons 1.11 |

---

**Last Updated:** March 2026  
**Version:** 1.0.0

---

<p align="center">
  <strong>🏥 Medical Center Management System</strong><br>
  <em>Efficient. Secure. Patient-Focused.</em>
</p>
