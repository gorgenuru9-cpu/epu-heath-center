# Ethiopia Police University Health Center – Clinic Management System

## Overview
A full-featured web-based clinic management system built with **PHP 8+ / MySQL / Bootstrap 5**.

## Features
| Module | Description |
|---|---|
| **Patients** | Registration (auto Patient ID), profiles, search, allergies & chronic conditions |
| **Appointments** | Booking, scheduling, status tracking |
| **Medical Records** | Visit notes, SOAP-style documentation, vital signs with auto-BMI |
| **Prescriptions** | Multi-medicine prescription, printable Rx, dispense tracking |
| **Laboratory** | Lab order management, result entry, printable lab report |
| **Pharmacy** | Medicine inventory, low-stock alerts, restock |
| **Billing** | Bill creation, payment recording, balance tracking |
| **Admin** | Staff account management, role-based access, reports & analytics |
| **Audit Log** | All actions logged with user, timestamp, and IP |

## User Roles
| Role | Access |
|---|---|
| **Admin** | Full access + staff management + reports |
| **Doctor** | Medical records, prescriptions, lab orders |
| **Nurse** | Vital signs, appointments, records |
| **Receptionist** | Patient registration, appointments, billing |
| **Pharmacist** | Prescriptions, medicine stock, dispensing |
| **Lab Technician** | Lab orders, enter results |
| **Department Head / Medical Director** | Staff Performance Monitoring, Leave Management, Issue Notice / Directive, Notice Board Management, Patient Flow Report, Revenue and Financial, Report, Audit Trail & System Monitoring |

## Installation

### Prerequisites
- XAMPP (Apache + MySQL + PHP 8+)

### Steps

1. **Place files** in `C:\xampp\htdocs\EPU health\`

2. **Create database**
   - Open phpMyAdmin: http://localhost/phpmyadmin
   - Click **Import** → Select `database/epu_health_center.sql` → Go

3. **Configure database** (if needed)
   - Edit `config/database.php` — default settings work with XAMPP out of the box

4. **Access the system**
   ```
   http://localhost/EPU health/
   ```

5. **Default login**
   ```
   Username: admin
   Password: Admin@1234
   ```
   > Change this password immediately after first login via Admin → Staff Accounts → Edit

## Directory Structure
```
EPU health/
├── config/          Database configuration
├── includes/        Header, footer, auth helpers
├── assets/          CSS and JS
├── database/        SQL schema file
├── patients/        Patient management
├── appointments/    Appointment booking
├── medical_records/ Visit records & vital signs
├── prescriptions/   Prescription management
├── lab/             Laboratory orders & results
├── pharmacy/        Medicine inventory
├── billing/         Billing & payments
└── admin/           User management & reports
```

## Security Notes
- All user input is sanitized with `htmlspecialchars()` and prepared statements
- Passwords are hashed with `password_hash()` (bcrypt)
- Role-based access control on every sensitive page
- Audit log tracks all significant actions
