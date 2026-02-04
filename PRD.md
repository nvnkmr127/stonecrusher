# Stone Crusher ERP - Product Requirements Document (PRD)

## 1. Introduction
**Stone Crusher ERP** is a comprehensive web-based enterprise resource planning system designed specifically for stone crushing and quarry operations. It streamlines the management of daily operations including vehicle gate passes, material sales, client ledgers, transport distance calculations, and employee attendance.

### 1.1 Purpose
The purpose of this system is to replace manual record-keeping with a digital, automated solution that ensures data integrity, accurate financial tracking, and real-time operational visibility.

### 1.2 Target Audience
- **Stone Crusher Owners/Admins**: For complete oversight of business performance and financials.
- **Managers**: For managing daily site operations, projects, and staff.
- **Accountants**: For tracking payments, ledgers, and financial reporting.
- **Operators/Staff**: For data entry (Gate Passes, Attendance).

## 2. Technical Stack
- **Backend Framework**: Laravel (PHP 8.2+)
- **Frontend**: Blade Templates / Tailwind CSS
- **Database**: MySQL
- **Authentication**: Laravel Breeze / Spatie Permission
- **External Services**: Google Drive API (Backups), Google Maps API (Distance Calculation)

## 3. User Roles & Permissions
The system employs Role-Based Access Control (RBAC) with the following roles:

| Role | Access Level | Description |
| :--- | :--- | :--- |
| **Admin** | Full Access | Can access all modules, settings, user management, backups, and audit logs. Can edit locked transactions. |
| **Manager** | High Access | Access to operations, master data, and reports. Restricted from system settings and sensitive admin logs. |
| **Accountant** | Financial Access | Focus on Client Ledgers, Payments, and Financial Reports. |
| **User** | Limited Access | Basic access for viewing dashboard and non-sensitive data (configurable). |

## 4. Functional Modules

### 4.1 Authentication & Security
- **Secure Login**: Email and password-based authentication.
- **Role Management**: Assign and manage roles for users.
- **Audit Logs**: Track all critical actions (Create, Update, Delete) with timestamps, user IDs, and old/new values.
- **Data Locking**: "Daily Closing" feature to lock financial records for a specific date, preventing tampering.

### 4.2 Dashboard
- **Key Metrics**: Real-time cards for Total Sales, Cash Collected, Active Projects, and Pending Gate Passes.
- **Recent Activity**: Quick view of recent projects or gate passes.
- **Visual Charts**: Sales trends and material breakdowns (Future Scope).

### 4.3 Master Data Management
Central repository for operational data:
- **Clients**: Manage customer details, credit limits, and view net balances.
- **Vehicles**: Track fleet details (Number, Type, Owner, Capacity/Multiplier).
- **Metal Types**: Define products (e.g., 20mm Agg, M-Sand) and descriptions.
- **Projects**: Manage client sites/projects with start/end dates and progress tracking.

### 4.4 Operations: Gate Pass & Sales
The core module for recording sales and material movement.
- **Entry (Inbound)**: Create a gate pass for an incoming empty truck. Record Vehicle, Client, and Tare Weight.
- **Exit (Outbound)**: Complete the pass upon exit. Record Gross Weight (auto-calculated Net Weight), Material, and Diesel/Driver Advance.
- **Sales Calculation**: 
  - `Net Weight * Rate` or `CFT * Rate` for volume sales.
  - Auto-debit Client Ledger.
- **Transport Billing**: 
  - Integrated Distance Calculator using Google Maps or saved locations.
  - `Distance * Rate/KM * Vehicle Multiplier`.
  - Option to bill transport to client (Round Trip support).

### 4.5 Financials: Client Ledger
A double-entry style ledger system for tracking client accounts.
- **Transaction History**: Chronological list of Debits (Sales) and Credits (Payments).
- **Balance Tracking**: Real-time "Net Balance" (Green for Advance, Red for Due).
- **Payments**: Record Cash, UPI, or Bank transfers against client accounts.
- **Outstanding Reports**: Detailed breakdown of who owes what, exportable to CSV/PDF.

### 4.6 Human Resources: Attendance
- **Daily Register**: Mark Present, Late, Half-Day, or Absent for staff.
- **Shift Logic**: Auto-tag "Late" based on configured Shift Start Time.
- **Monthly Reports**: Calendar view summarty of attendance for payroll processing.

### 4.7 Reporting Suite
Comprehensive reporting for decision making.
- **Daily Sales Report**: End-of-day summary of sales, collections, and diesel usage.
- **Monthly Summary**: High-level view of monthly performance.
- **Distance Report**: Analysis of transport costs and vehicle usage.
- **Client Outstanding**: Aged debt analysis.
- **Export Formats**: All reports support CSV and PDF export.

### 4.8 System Administration
- **Settings**: Configure Company Info, Financial Year, GST/Tax Rates, and default Operational Logic.
- **Backups**: 
    - Automated daily database & file backups.
    - Cloud sync to Google Drive.
    - Manual "Restore" capability for disaster recovery.
- **User Management**: Create/Edit/Deactivate system users.

## 5. Non-Functional Requirements
- **Performance**: Pages should load within 2 seconds. Reports should handle thousands of records efficiently.
- **Reliability**: 99.9% Uptime. Automated backups ensure data safety.
- **Usability**: responsive design for access on Tablets and Desktops.
- **Scalability**: DB schema designed to handle multi-year operational data.

## 6. Assumptions & Constraints
- Google Maps API Key is required for automatic distance feature.
- Internet connection is required for Google Drive backups.
- "Daily Closing" is a hard constraint; days must be reopened by Admin to edit past data.
