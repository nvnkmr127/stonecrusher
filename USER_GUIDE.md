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

## 🚛 Distance Calculation Service

The system automatically calculates distances between your **Crusher Location** (set in Settings) and project sites if coordinates are available.
- **Dual Mode**:
  - Uses **Google Maps** if an API key is provided (Recommended for accuracy).
  - Fallback to **OpenStreetMap** (Free) if no key is present.

---

## 🔒 Security Best Practices

- **Passwords**: Use strong passwords for all accounts.
- **Role Access**: Only give "Admin" access to trusted management staff.
- **Logout**: Always log out when finished, especially on shared computers.

---

## Support

For technical support or feature requests, contact your system administrator.
