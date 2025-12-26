# Changelog

All notable changes to the **Stone Crusher ERP** project will be documented in this file.

## [v1.0.0] - 2025-12-24

### Added
- **Authentication System**
    - Secure login/logout functionality using Laravel Breeze.
    - Role-based access control (Admin vs. User) using Spatie Permission.
    - Admin-only routes and dashboard protection.

- **Admin Dashboard**
    - Comprehensive dashboard with statistics cards (Total, Active, Completed, Pending Projects).
    - Recent Projects widget showing latest activity.
    - User management quick actions.

- **Master Data Management**
    - **Clients Module**: Create, read, update, delete (CRUD) clients with contact details.
    - **Vehicles Module**: Manage fleet details (Trucks, etc.).
    - **Metal Types Module**: Configure types of materials/stones.
    - **Projects Module**: Full project tracking with location, client linkage, and status.

- **Project Tracking Enhancements**
    - **Location Tracking**: Added location field to projects for site identification.
    - **Quantity Estimation**: Added estimated quantity (tons) field.
    - **Progress Tracking**: New progress slider (0-100%) with visual progress bars in lists.

- **Distance Calculation Service**
    - **Dual API Support**: Flexible distance calculation using OpenStreetMap (Nominatim) or Google Maps.
    - **Settings**: Configurable Crusher Location (Lat/Long) and optional Google Maps API Key.
    - **Caching**: 30-day caching of geocoded results for performance.

- **Settings Management**
    - Global application settings configuration.
    - Company details, currency, financial year, and date format settings.
    - Operational settings: Default Diesel Rate, Rate per KM.

- **User Management**
    - Full CRUD for users.
    - Role assignment (Admin/User).
    - Visual status indicators.

- **UI/UX**
    - **Tabler Theme**: Integration of the modern, responsive Tabler admin template (v1.4.0).
    - **Dark/Light Mode**: Sidebar with dark theme, clean light content area.
    - **Responsive Design**: Mobile-friendly layout for all modules.
    - **Components**: Reusable Blade components for Cards, Tables, Buttons, and Alerts.

### Fixed
- **Sidebar Visibility**: Resolved issue where sidebar was hidden on desktop by adding custom CSS override.
- **Admin Permissions**: Implemented super-admin bypass in `AppServiceProvider` to grant admins full access automatically.
- **Asset Loading**: Fixed Tailwind CSS import issue in `app.css` to ensure proper compilation.

### Technical
- **Tech Stack**: Laravel 11, SQLite, Tailwind CSS, Tabler UI.
- **Testing**: Comprehensive feature tests for Role Access, Tabler Layout, Distance Service, and Project Enhancements.
- **Architecture**: Modular Service-Repository pattern (e.g., `DistanceService`).
