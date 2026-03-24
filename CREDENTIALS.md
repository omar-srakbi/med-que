# 🔐 Medical Center System - User Credentials

## 📋 All Login Credentials

### 👨‍💼 Admin Accounts
| # | Email | Password | Permissions |
|---|-------|----------|-------------|
| 1 | `admin@example.com` | `admin123` | All (*) |

**Admin Can:**
- ✅ Everything (full system access)
- ✅ Delete any ticket (completed or pending)
- ✅ Delete Admin role (with password: `1234`)
- ✅ Manage all settings
- ✅ Manage staff, roles, departments
- ✅ View all reports

---

### 💰 Cashier Accounts
| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Cashier** | `cashier@example.com` | `cashier123` | Basic cashier |
| **Head Cashier** | `headcashier@example.com` | `head123` | Advanced cashier |

**Cashier Can:**
- ✅ Create tickets
- ✅ Process payments
- ✅ Manage patients
- ✅ Access settings

**Head Cashier Additional:**
- ✅ Book tickets for tomorrow
- ✅ Delete incomplete tickets
- ✅ Manage all settings

---

### 🩺 Medical Staff Accounts
| Role | Email | Password | Permissions |
|------|-------|----------|-------------|
| **Doctor** | `doctor@example.com` | `doctor123` | Medical records |
| **Nurse** | *(create from Staff page)* | - | Medical records |

**Doctor/Nurse Can:**
- ✅ View patients
- ✅ Create/Edit medical records
- ✅ View medical history

---

### 📝 Other Roles
| Role | Email | Password |
|------|-------|----------|
| **Receptionist** | *(create from Staff)* | - |
| **Lab Technician** | *(create from Staff)* | - |
| **Radiology Tech** | *(create from Staff)* | - |

---

## 🔑 Special Passwords

| Purpose | Password |
|---------|----------|
| **Delete Admin Role** | `1234` |
| **Default for new users** | Set during creation |

---

## ➕ How to Create New Admin

1. Login as Admin
2. Go to **Staff** → **Add Staff**
3. Fill in details:
   - First Name, Last Name
   - Email (e.g., `newadmin@example.com`)
   - Password (choose strong password)
   - **Role: Admin**
4. Click **Save**

✅ New admin can now login!

---

## 📍 Login URL
```
http://localhost:8000/login
```

---

## ⚠️ Security Recommendations

1. **Change default passwords** after first login
2. **Use strong passwords** for admin accounts
3. **Don't share** admin credentials
4. **Regularly review** staff access

---

## 🗑️ To Delete Admin Role

1. Go to **Roles** page
2. Click **Trash** on Admin role
3. Enter password: `1234`
4. Confirm deletion

⚠️ **Warning:** This cannot be undone!

---

**Last Updated:** {{ date('Y-m-d H:i') }}
