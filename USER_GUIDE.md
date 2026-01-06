# User Guide - Stone Crusher ERP

Welcome to the **Stone Crusher ERP** User Guide. This system helps you manage stone crusher operations, including projects, clients, vehicles, and distance calculations.

---

## 🏗️ Getting Started

### Login
Access the system at your secure URL.
- **Admin Login**: Use your provided admin credentials.
- **User Login**: Standard users have limited access.

### Dashboard (Admin)
Upon logging in as an Admin, you will see the **Dashboard**:
- **Statistics Cards**: Quick view of Total, Active, Completed, and Pending projects.
- **Recent Projects**: A table showing the 5 most recently updated projects with their progress.
- **Sidebar Navigation**: Access all modules from the left menu.

---

## 🛠️ Settings & Configuration

Before starting, configure the system settings:
1. Navigate to **Settings** in the sidebar.
2. **Company Info**: Set your Company Name, Currency, and Financial Year.
3. **Operational Settings**:
   - **Crusher Location**: Enter Latitude and Longitude (used for distance calculations).
   - **Default Diesel Rate**: Current market rate.
   - **Rate per KM**: Standard transport rate.
   - **Google Maps API Key**: (Optional) Add your key for precise distance calculation.
4. Click **Save Settings**.

---

## 📂 Master Data Management

Store reusable information here to speed up daily entries.

### Clients
Manage your customer database.
- **View**: See a list of all clients with contact info.
- **Add**: Click "Add Client" and enter Name, Email, Phone, and Address.
- **Edit/Delete**: Use actions in the list.

### Vehicles
Manage your fleet or contractor trucks.
- **Add**: Enter Vehicle Number, Type (e.g., Tipper, Dumper), and Owner info.

### Metal Types
Define the materials you produce/sell.
- **Add**: Enter Name (e.g., "20mm Aggregate", "M-Sand") and Description.

### Projects
Manage your active work sites.
- **Add Project**:
  - **Name**: Project title (e.g., "Highway 44 Expansion").
  - **Client**: Select from your Client list.
  - **Location**: Enter site address.
  - **Estimated Quantity**: Total tons expected.
  - **Project Dates**: Start and End dates.
  - **Progress**: Use the slider to set initial progress (0-100%).
- **Tracking**: Update progress and status as key milestones are met.

---

## 👥 User Management (Admin Only)

Control who can access the system.
- **Add User**: Create accounts for staff.
- **Roles**: Assign "Admin" (full access) or "User" (restricted access) roles.
- **Manage**: Edit details or delete accounts when staff leave.

---

## 📅 Attendance Module

Track and manage employee attendance efficiently.

### Daily Register
- **View**: Go to **Attendance > Daily Register** to see a full list of staff attendance for the selected date.
- **Filters**: Filter by specific **Date** or **Employee**.
- **Status Indicators**:
  - `Present`: On-time arrival.
  - `Late`: Checked in after the configured *Shift Start Time*.
  - `Half Day`: Early departure or significant lateness.
  - `Leave` / `Absent`: As marked.

### Marking Attendance
- **Add Entry**: Click **Add New** on the Daily Register page.
  - Select **Staff Member**, **Date**, **Check In/Out Time**, and **Status**.
  - **Remarks**: Optional notes (mandatory for edits).
- **Edit Entry**: Click **Edit** on any record to update times or change status.
  - *Note*: Auditors can see who modified a record and the reason provided.

### Monthly Report
- **View**: Go to **Attendance > Monthly Report**.
- **Summary**: view a table showing total Present, Late, Half Day, Leave, and Absent days for each employee for the selected month.
- **Export**: Click **Export CSV** to download the data for payroll processing.
- **Print**: Use the **Print** button for a hard copy.

### Configuration
- **Shift Timings**: Go to **Settings** to define your standard **Shift Start Time** and **Shift End Time**.
  - These settings determine whether an employee is marked as 'Late' or 'Half Day'.

---
---

## 💰 Client Financials & Ledger (New)

Manage client accounts, track advance payments, and monitor outstanding balances.

### 1. Client Management Enhanced
- **Credit Limit & Notes**: When adding a new client, you can now set a **Credit Limit** and add strictly private **Notes**.
- **Net Balance**: The main Client List now shows a **Balance** column:
  - **Green**: Advance/Excess payment received.
  - **Red**: Outstanding amount (Client owes you).
- **Ledger Access**: Click on any client name or the **"Ledger"** button to view their full financial history.

### 2. Client Ledger
The Ledger is your single source of truth for a client's account.
- **Transaction History**: View a chronological list of all sales and payments.
- **Date Filter**: Use the date pickers at the top to filter transactions for a specific period (e.g., "This Month").
- **Print**: Click the printer icon to generate a hard copy of the filtered ledger.

### 3. Recording Transactions
To record a new payment or sale:
1. Go to the **Client Ledger**.
2. Click **"Record Transaction"**.
3. **Transaction Type**:
   - Select **Credit** for payments received (Advance).
   - Select **Debit** for sales/invoices (Balance Due).
4. **Payment Mode**: For advances, specify (Cash, UPI, Bank Transfer, etc.).
5. **Amount**: Enter the value.
6. **Save**: The ledger will auto-recalculate the Net Balance.

### 4. Editing Transactions (Admin Only)
Only Admins have the permission to correct past mistakes.
- Locate the transaction in the Ledger.
- Click the **"Edit"** button in the Actions column.
- Modify the details (Date, Amount, etc.).
- **Mandatory Reason**: You **must** provide a reason for the edit. This is logged for audit purposes.

### 5. Outstanding Report
Get a bird's-eye view of your receivables.
- **Navigate**: Go to **Reports > Client Outstanding**.
- **Summary**: See total Sales, Total Advances, and Total Outstanding across the company.
- **Table**: Detailed breakdown per client with "Advance" or "Outstanding" status badges.
- **Export**: Click **"Export CSV"** to download the data for Excel analysis.

---

## 🚛 Gate Pass & Sales Management (New)

A complete system to track vehicle movements, calculate weights, and record sales automatically.

### 1. Creating a Gate Pass (Entry)
- **Navigate**: Go to **Gate Passes** > **New Gate Pass**.
- **Entry Details**:
  - **Vehicle**: Select the truck.
  - **Client**: Choose the customer. (Note the **Current Balance** displayed below).
  - **Weights**: Enter **Tare Weight** (Empty Truck) if known, or leave blank to weigh later.
- **Save**: Click **"Create Gate Pass"**. The status will be `Pending`.

### 2. Completing a Sale (Exit)
When the loaded truck leaves:
1. **Edit**: Open the pending Gate Pass.
2. **Material**: Select the product (e.g., 20mm Aggregate). Rate is auto-fetched.
3. **Weighing**: Enter **Gross Weight** (Loaded Truck). The system auto-calculates **Net Weight**.
   - *Alternative*: For volume sales, enter **Loading Quantity (CFT)**.
4. **Financials**:
   - **Diesel Amount**: Enter if diesel was provided to the driver (added to total).
   - **Driver Advance**: Enter any cash given to the driver (recorded for tracking).
5. **Status**: Change to `Completed`.
6. **Save**: This action **automatically creates a Debit Transaction** in the Client's Ledger.

### 3. Payments & Receipts
- **Record Payment**: On the Gate Pass List, click the **"Pay"** button on any completed entry.
- **Modal**: Enter Amount, Date, and Payment Mode (Cash/UPI/Bank).
- **Status**: The Gate Pass payment status updates to `Paid` naturally.

### 4. Admin Controls
- **Edit**: Admins can edit completed passes but **must** provide a reason in the Remarks (Audit Log).
- **Cancel**: Only Admins can cancel a pass. This **immediately deletes** the corresponding financial transaction, reversing the charge to the client.

### 5. Reports
- **Daily Sales Report**: Click **"Daily Report"** on the Gate Pass list to see:
   - Total Sales Value.
   - Total Diesel & Outstanding.
   - Metal-wise quantity breakdown.

---

## 🌍 Distance & Transport Management (New)
The system now features a robust engine for calculating, tracking, and billing transport costs.

### 1. Delivery Locations
- **Auto-Save**: When you create a Gate Pass for a new location, checking "Save this location" stores its coordinates.
- **Reuse**: Next time, simply select the location from the dropdown to auto-fill the distance.
- **Accuracy**: Distances are precise and calculated using geolocation coordinates.

### 2. Transport Billing in Gate Passes
- **Read-Only Distance**: To ensure accuracy, the Distance (KM) field is **read-only**. You cannot manually type a distance.
    - **How to Set Distance**:
        1. Select a **Saved Location** from the autocomplete list.
        2. Or, use the **"Use Coordinates"** feature: Enter Latitude/Longitude and click **Calculate**.
- **Transport Cost**: Automatically calculated based on `Distance × Rate per KM × Vehicle Multiplier`.
- **Round Trip**: Check the **"Round Trip?"** box to double the billable distance (e.g., for return journey).
- **Bill to Client**: Check **"Bill Transport to Client?"** to include the transport cost in the final invoice amount shown in the Client Ledger.

### 3. Distance Calculator Tool
Need a quick quote without creating a pass?
- **Navigate**: Go to **Distance Calculator**.
- **Input**: Enter Delivery Coordinates or use "Get My Location".
- **Options**: Select Vehicle Type and Round Trip status.
- **Result**: Instantly see the estimated transport cost.

### 4. Distance Report
- **Navigate**: Go to **Gate Passes > Distance Report** (or via the button on the index page).
- **Insights**: View total trips, total distance covered, and total transport revenue.
- **Breakdown**: Analyze which locations are your most frequent destinations.

### 5. Admin Settings
Admins can fine-tune the transport engine:
- **Vehicle Multipliers**: Edit a vehicle to set its capacity multiplier (e.g., 1.5x for large dumpers).
- **Global Settings**: Go to **Settings** to update:
    - **Crusher Location**: The starting point for all calculations.
    - **Rate per KM**: The base charging rate.
    - **Default Round Trip**: Choose if new passes should default to round-trip billing.

---

## 🔒 Security Best Practices

- **Passwords**: Use strong passwords for all accounts.
- **Role Access**: Only give "Admin" access to trusted management staff.
- **Logout**: Always log out when finished, especially on shared computers.

---

## 📊 Reports & Data Security (New)

Phase 6 introduces robust reporting tools and strictly enforced data security measures.

### 1. Daily Closing (Data Locking)
To ensure financial integrity, the system enforces a "One Closing Per Date" rule.
- **Closing a Day**:
    - Go to **Daily Closings** via the sidebar.
    - Click **"Close Day"**.
    - Review the day's summary (Total Sales, Cash Collected).
    - Confirm the closure.
- **Effect**: Once a day is **Closed**, NO changes can be made to that date's records (Gate Passes, Payments, Attendance). The date is effectively locked.
- **Reopening**: Only an **Admin** can reopen a closed day.
    - Go to the Daily Closings list.
    - Click **"Reopen"**.
    - **Mandatory Reason**: You must enter a reason for auditing purposes.

### 2. Exporting Reports
All reports in the system now support one-click export options:
- **Export CSV**: Download raw data for Excel/Google Sheets analysis.
- **Export PDF**: Download a formatted, print-ready document.
- **Where to find**: Look for the **"Export CSV"** and **"Export PDF"** buttons at the top right of any report page.

### 3. Advanced Reporting Suite
- **Daily Report**: (`Reports > Daily`)
    - Comprehensive snapshot of a single day's activity.
    - Includes: Sales Summary, Collection Summary (Cash/Online), Diesel usage, and detailed Transaction list.
- **Monthly Summary**: (`Reports > Monthly`)
    - Calendar view of the entire month.
    - Tracks Daily Sales vs. Daily Collections to monitor cash flow gaps.
- **Custom Report**: (`Reports > Custom`)
    - Generate a report for *any* specific date range (e.g., "Last 45 Days").
- **Analytical Summaries**: (`Reports > Summary > [Type]`)
    - **By Metal**: Which material is selling the most?
    - **By Client**: Who are your top buyers?
    - **By Vehicle**: Which trucks are doing the most trips?
- **Distance Report**: (`Gate Passes > Distance Report`)
    - Analyze transport costs. See which routes are most profitable and which are costing too much (Cost per KM).
- **Attendance Report**: (`Attendance > Report`)
    - Monthly view of staff presence. Useful for payroll calculation.
    - Export PDF for a clean attendance sheet.
- **Outstanding Report**: (`Reports > Outstanding`)
    - Critical for debt collection. Shows exactly how much every client owes you vs. how much advance they have paid.

---

---

## 🛡️ Administration & Security (New)

Phase 7 brings enterprise-grade security, auditing, and backup capabilities to protect your business data.

### 1. Audit Logs (Tracing Actions)
Every critical action in the system is now tracked.
- **View Logs**: Go to **Administration > Audit Logs**.
- **What is Logged?**: Login/Logout, Creating/Editing/Deleting Records (Sales, Payments, Attendance), and System Settings changes.
- **Details**: Each log shows:
    - **Who**: The user who performed the action.
    - **What**: The type of action (e.g., "Updated Sale").
    - **When**: Precise timestamp.
    - **IP Address**: The location/network of the user.
    - **Changes**: Technical details of what changed (Old Value vs. New Value).
- **Filtering**: Use the filters at the top to search by User, Module, or Date Range.

### 2. Backups & Disaster Recovery
Your data is automatically protected against server failure or accidental deletion.
- **Automatic Backups**: The system takes a full backup (Database + Files) every day at 01:00 AM.
- **Cloud Storage**: Backups are securely uploaded to **Google Drive** for off-site safety.
- **Manual Backup**:
    - Go to **Administration > Backups**.
    - Click **"Create Backup"**.
    - The process runs in the background. Refresh the page to see the new backup.
- **Download**: You can download any backup ZIP file to your local computer.

### 3. Restore (Disaster Recovery)
**⚠️ DANGER ZONE**: This feature allows you to roll back the system to a previous state.
- **How to Restore**:
    1. Go to the **Backups** list.
    2. Click the **"Restore"** button next to any backup file.
    3. **Confirm**: You will be asked to confirm. **THIS WILL OVERWRITE CURRENT DATA**.
    4. **Result**: The system database will be replaced with the data from that specific backup file.
    - *Use Case*: Use this only if data is corrupted or completely lost.

### 4. Google Drive Connection
To enable cloud backups:
1. Go to **Administration > Backups**.
2. Click **"Connect Google Drive"**.
3. Sign in with your company Google account.
4. Grant permission.
5. Status will change to **"Drive Connected"**.

---

## Support

For technical support or feature requests, contact your system administrator.
