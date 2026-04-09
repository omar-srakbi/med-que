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
- [Troubleshooting](#-troubleshooting)
- [Upcoming Features](#-upcoming-features)
- [Support & Training](SUPPORT.md)

---

## ✨ Features

### 🏥 Patient Management
- ✅ Quick patient registration with minimal information
- ✅ Complete patient profiles (name, parents, birth date, national ID, phone)
- ✅ **Enhanced patient search** with visual details (larger font, more info)
- ✅ Patient history tracking (visits, medical records, payments)
- ✅ Incomplete profile management (receptionist completes later)
- ✅ Auto-completion of patient profiles when info is complete

### 🎫 Ticket & Queue System
- ✅ Same-day ticket creation
- ✅ Advance booking (next day) - Head Cashier only
- ✅ **Shared yearly ticket sequence** (99M capacity per year)
- ✅ **Per-department queue numbers** (daily reset)
- ✅ **Ticket Format:** `TK00000001` (2-char prefix + 8 digits)
- ✅ **Queue Format:** `Q10001` (unique prefix + 4 digits)
- ✅ Real-time queue display screen
- ✅ Custom service shortcuts (e.g., CBC, XRAY) - see [Configuration](#-configuration)
- ✅ **Auto-suggest** for available queue prefixes
- ✅ **Real-time validation** for duplicate prefixes

### 🏢 Departments & Services
- ✅ 6 pre-configured departments:
  - Clinics (العيادات)
  - Kidney Center (مركز الكلى)
  - Blood Laboratory (مختبر الدم)
  - Radiology Center (مركز الأشعة)
  - MRI (الرنين المغناطيسي)
  - Physiological Treatment (العلاج الطبيعي)
- ✅ Custom service pricing per department
- ✅ Service activation/deactivation

### 📋 Medical Records
- ✅ Diagnosis tracking
- ✅ Prescriptions management
- ✅ Test results storage
- ✅ Follow-up appointment scheduling
- ✅ Doctor and nurse access
- ✅ Linked to patient tickets
- ✅ **Audit logs for all medical record changes** (who edited what, when)

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
- ✅ Customizable currency (see [Configuration](#-configuration))
- ✅ Cash-only payments (expandable)

### 📊 Reports
- ✅ **9 built-in reports:**
  - Daily patients report
  - Daily revenue report
  - Patient history report
  - Department performance
  - Revenue by department
  - Payment tracking
  - Ticket summary
  - Service utilization
  - Staff activity
- ✅ **Custom report builder** - create your own reports with simple or advanced modes
- ✅ **Report permissions management** - control who can view, edit, delete, or export each custom report
  - Grant permissions to specific users or entire roles
  - User-specific permissions take priority over role permissions
  - Fine-grained control: View, Edit, Delete, Export
- ✅ **Multiple export formats:**
  - CSV export
  - PDF export
  - Excel export (XLSX)
- ✅ **Advanced filtering:**
  - Date range
  - Department
  - Patient
  - Service type
  - Custom columns
- ✅ **Column customization** - choose which columns to display

> ⚠️ **Note:** The Custom Report Builder is still experimental. Most features work well, but some edge cases haven't been fully tested yet.

### ⚙️ Settings & Customization
- ✅ Currency customization (code, symbol, decimals)
- ✅ Ticket format customization per department
- ✅ QR code options for tickets
- ✅ Department ticket settings (prefix, format, sequence)
- ✅ **Receipt designer** - customize receipt layout
- ✅ **Print settings** with accordion-based layout
- ✅ **Live preview** for tickets and receipts
- ✅ System-wide preferences

### 🔍 Search & Navigation
- ✅ Global search (patients, tickets, records)
- ✅ Advanced filters (date, department, status)
- ✅ Keyboard shortcuts for quick access

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
- **Database:** SQLite with WAL mode (default) or MySQL/PostgreSQL
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

### Option 1: Automated Setup (Recommended)

**Windows (PowerShell):**
```powershell
.\setup-first-time.ps1
```

**Linux/Mac (Bash):**
```bash
chmod +x setup-first-time.sh
./setup-first-time.sh
```

Automates everything: installs dependencies (PHP, Composer, Node.js), creates `.env`, generates key, runs migrations & seeders.

### Option 2: Manual Setup

### 1. Clone or Download
```bash
git clone https://github.com/omar-srakbi/med-que.git
cd med-que
```

### 2. Install Dependencies
```bash
composer install
npm install  # Optional:for in-future frontend assets
```

### 3. Setup Environment
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Create Database
```bash
mkdir -p database
touch database/database.sqlite

# On Windows (PowerShell):
# New-Item -Path "database/database.sqlite" -ItemType File
```

### 5. Configure Database
The `.env.example` is already set up for SQLite. If you want MySQL or PostgreSQL, edit `.env` with your database credentials.

**Note:** SQLite uses WAL (Write-Ahead Logging) mode by default for better multi-user concurrency.

### 6. Run Migrations & Seeders
```bash
php artisan migrate:fresh --seed
```

This sets up all the tables, roles, departments, services, and the default admin account.

### 7. Start Server
```bash
php artisan serve
```

Visit: **http://localhost:8000**

---

## ⚙️ Configuration

### Currency Settings

Go to: **Settings → Currency Settings**

Configure your local currency:

| Setting | Example Values | Description |
|---------|---------------|-------------|
| **Currency Code** | JOD, USD, EUR, SAR | ISO currency code |
| **Currency Symbol** | JD, $, €, £, ر.س | Display symbol |
| **Decimal Places** | 0, 1, 2, 3 | Number of decimals |

**Examples:**
- Syria: `SYP` / `ل.س` / `2`
- Jordan: `JOD` / `JD` / `2`
- USA: `USD` / `$` / `2`
- Kuwait: `KWD` / `د.ك` / `3`
- Japan: `JPY` / `¥` / `0`

### Service Shortcuts

Go to: **Departments → Select Department → Add/Edit Service**

Add shortcuts for quick ticket creation. Here are some examples:

| Shortcut | Service | Department |
|----------|---------|------------|
| `CBC` | Complete Blood Count | Blood Laboratory |
| `XRAY` | X-Ray | Radiology Center |
| `MRI` | MRI Scan | MRI |
| `KIDNEY` | Kidney Function Test | Kidney Center |
| `CONSULT` | General Consultation | Clinics |

> 💡 These shortcuts are **not pre-configured**. Add them yourself via:
> **Departments → Select Department → Edit → Add Service → Shortcut field**

**Usage:**
1. Go to **Tickets → Create Ticket**
2. In **Quick Select** field, type shortcut (e.g., `CBC`)
3. Press **Enter** or **Tab**
4. System auto-selects department and service!

### Ticket Numbering

Go to: **Departments → Select Department → Edit → Ticket Settings**

Configure per department:

| Setting | Example | Description |
|---------|---------|-------------|
| **Ticket Prefix** | `TK`, `OP`, `ER` | Shared among departments (2 chars) |
| **Queue Prefix** | `Q1`, `CL`, `XR` | Unique per department (2 chars) |

**How It Works:**

**Ticket Numbers (Yearly, Shared):**
```
Format: {prefix}{sequence}
Example: TK00000001

All departments with same prefix share ONE counter:

| Ticket Number | Department Used | Queue Number |
|--------------|-----------------|--------------|
| `TK00000001` | Clinics | `Q10001` |
| `TK00000002` | Radiology | `Q40001` |
| `TK00000003` | MRI | `Q50001` |
| `TK00000004` | Blood Lab | `Q30001` |
| `TK00000005` | Clinics (next) | `Q10002` |

All 6 departments share the same ticket counter, but each has its **own daily queue number** with a unique prefix (`Q1`–`Q6`).
```

**Queue Numbers (Daily, Per-Department):**
```
Format: {queue_prefix}{sequence}
Example: Q10001

Each department has its own queue counter:
- Clinics (Q1): Q10001, Q10002, Q10003... (resets daily)
- Radiology (Q4): Q40001, Q40002, Q40003... (resets daily)
```

**Capacity:**
- **Ticket Numbers:** 99,999,999 per year (shared)
- **Queue Numbers:** 9,999 per day (per department)

**Auto-Suggest Feature:**
When creating a new department:
1. Click on **Queue Prefix** field
2. System auto-fills next available prefix (e.g., `Q7`)
3. Or type any 2-character alphanumeric code
4. Real-time validation warns if prefix is taken
5. Click suggested button to auto-fill available prefix

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
| Quick Registration | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Complete Patient Profiles | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ |
| Manage Settings | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Manage Report Permissions | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |
| **Full System Access** | ✅ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ |

### Role Descriptions

#### **Admin**
- Full system access
- Can manage all roles and permissions
- Can delete any record
- Access to audit logs
- Can manage report permissions (control who can access custom reports)

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
- Complete incomplete patient profiles
- Answer patient inquiries
- **Limited:** No quick registration (full patient registration only)

#### **Cashier**
- Create tickets for same-day visits
- Process payments & print receipts
- **Add new patients** (full registration with complete information)
- Edit patient information
- Manage settings
- **Limited:** No quick registration (must fill complete patient form)

#### **Head Cashier**
- All Cashier permissions
- Create advance bookings (next day)
- Delete incomplete tickets
- Override ticket cancellations

---

## 🔐 Default Credentials

```
Email: admin@example.com
Password: password
```

See [SUPPORT.md](SUPPORT.md) for other accounts and instuctions.

> ⚠️ **Change default password after first login!**

---

## 📚 Usage Guide

### Quick Ticket Creation (30 seconds)

**Method 1: Using Shortcuts**
1. Go to **Tickets → Create Ticket**
2. Type a shortcut (like `CBC` or `XRAY`) in the Quick Select field
3. Press Enter or Tab — the system fills in the department and service automatically
4. Search for or select the patient
5. Submit the form

> 💡 Want to set up shortcuts? See [Service Shortcuts](#-service-shortcuts) in Configuration.

**Method 2: Using Numeric Codes**
1. Type `1.2` in Quick Select (`1` = dept, `2` = service)
2. Press Enter

### Quick Patient Registration

**For Rush Hours:**
1. Type patient name in Patient field
2. System offers to create new patient
3. Ticket issued immediately
4. Receptionist completes profile later

**For Full Registration:**
1. Go to **Patients → Add Patient**
2. Fill all fields
3. Save

### Queue Management

- **Cashiers:** Create ticket → patient waits → call when ready
- **Doctors/Nurses:** View queue → "Call Next" → mark complete
- **Patients:** Watch display screen → go to department when called

### Medical Records

- **Doctors:** View ticket → "Add Medical Record" → enter diagnosis/prescription → save
- **Receptionists:** **Patients → Incomplete Profiles** → complete missing info

### Reports

Go to **Reports** and pick the report you need. You can filter by date, department, or patient. All reports support export to CSV, PDF, and Excel.

### Report Permissions (Custom Reports Only)
**EXPERIMENTAL**

Control who can access your custom reports:

1. Go to **Reports → Builder**
2. Click the 🛡️ **Permissions** icon on any custom report
3. Add permissions by selecting:
   - A specific **user**, OR
   - An entire **role**
4. Set access levels:
   - 👁️ **Can View** - Access to view the report
   - ✏️ **Can Edit** - Modify report configuration
   - 🗑️ **Can Delete** - Remove the report
   - 📥 **Can Export** - Export report data

**Permission Priority:**
- User-specific permissions take priority over role permissions
- Example: If a user is explicitly denied but their role is allowed, the user **cannot** access the report
- If no user permission exists, the system falls back to role permissions

**Notes:**
- Report owner always has full access
- If a report is marked as public, everyone can view it
- Permissions are enforced on view, edit, delete, and export actions

---

## 🎯 System Goals

The system was built to:

- **Reduce wait times** — quick registration, shortcut-based ticket creation, organized queues
- **Eliminate paperwork** — digital tickets, receipts, medical records, and reports
- **Improve patient flow** — clear numbering, department-specific queues, visual display
- **Keep data accurate** — structured forms, validation, audit trails, role-based access
- **Give real-time insights** — daily reports, revenue tracking, department performance
- **Stay secure** — roles, passwords, session management, activity logs
- **Scale when needed** — multi-department, multi-database, customizable workflows

---

## 🚧 Upcoming Features

### Doctor Selection in Ticket Creation
**Status:** 📋 Planned

**Problem:** Currently, cashiers cannot assign a specific doctor when creating a ticket. The doctor is only determined later when a medical record is created.

**What's Coming:**
- Doctor dropdown in ticket creation form
- Auto-filter doctors by selected department
- Optional doctor assignment (can leave unassigned)
- Doctor information displayed on tickets and receipts
- Doctor-based filtering in reports

**Implementation:**
1. Add `doctor_id` to tickets table
2. Link doctors to departments (pivot table)
3. Add API endpoint to fetch doctors by department
4. Update ticket creation form with dynamic doctor dropdown
5. Display assigned doctor in ticket views and reports

**Estimated Release:** Next major update

---

### Other Planned Features
- **Multi-branch support** - manage multiple clinic locations
- **SMS notifications** - notify patients when their number is called
- **Appointment scheduling** - book specific time slots (not just next day)
- **Insurance integration** - track insurance claims and coverage
- **Patient portal** - allow patients to view their history online
- **Inventory management** - track medications and supplies
- **Billing system** - generate invoices and track outstanding balances

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

## 📞 Support & Training

See [SUPPORT.md](SUPPORT.md) for technical support, customization requests, and staff training resources.

---

## 📄 License

**Proprietary Software** - All rights reserved.

This software is provided "as is", without warranty of any kind, express or implied. In no event shall the authors be liable for any claim or damages arising from the use of this software.

Unauthorized reproduction, distribution, or commercial use is strictly prohibited without express written consent from the author.

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

**Last Updated:** April 2026
**Version:** 1.3.01

---

<p align="center">
  <strong>🏥 Medical Center Management System</strong><br>
  <em>Efficient. Secure. Patient-Focused.</em>
</p>
