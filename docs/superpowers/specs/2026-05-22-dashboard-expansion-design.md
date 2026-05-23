# Dashboard Expansion Design (Admin)
Date: 2026-05-22

## 1. Objective
Expand the existing Admin Dashboard to support:
- Crusher Profit
- Quarry Expense
- Net Profit
- Diesel Analytics
- Monthly P&L snapshot

Without rewriting existing working modules. Prefer extension over replacement and keep backward compatibility.

## 2. Current State (Baseline)
- Controller: [AdminDashboardController](file:///Users/naveenadicharla/Documents/stonecrusher%20erp/app/Http/Controllers/AdminDashboardController.php)
- View: [admin/dashboard.blade.php](file:///Users/naveenadicharla/Documents/stonecrusher%20erp/resources/views/admin/dashboard.blade.php)
- Charts: ApexCharts loaded via CDN in the Blade view.
- Queries:
  - Multiple per-stat queries for the same time window.
  - Weekly series is built with per-day loops (4 queries/day).
- Access control:
  - Route middleware `role:admin` (primary).
  - Shared UI partials use `@role('admin')`.

## 3. Problems to Solve
- Dashboard query cost scales poorly (N×day loops and repeated aggregates).
- Business metrics are not formally defined (e.g., revenue filters on status).
- Widget markup is duplicated (stat cards are not componentized).
- Profitability widgets must match existing Operational P&L report numbers.

## 4. Non-Goals
- No visual redesign of the overall dashboard layout in this phase.
- No migration away from Blade to Livewire in this phase.
- No removal of existing cards/charts; only additive + internal refactor.
- No changes to source-of-truth business logic for Sales/Ledger/P&L.

## 5. Metric Definitions (Source of Truth)
All profitability metrics are sourced from Operational Records, so dashboard and reports match.

### 5.1 Operational Units
- “Crusher” unit: OperationalUnit identified by configured code/name (existing report logic currently looks up unit by code/name).
- “Quarry” unit: same.

### 5.2 Profit & Expense
- Revenue: OperationalRecords where `tag.type = revenue`
- Expense: OperationalRecords where `tag.type = expense`

### 5.3 Widgets
- Crusher Profit (MTD): `sum(crusher revenue) - sum(crusher expense)`
- Quarry Expense (MTD): `sum(quarry expense)`
- Net Profit (MTD): `(crusher net) + (quarry net)`
- Diesel Analytics:
  - Liters issued today + MTD liters from DieselEntry (existing)
  - Series (last 7/30 days): liters grouped by date, optionally filtered by operational_unit_id
- Monthly P&L:
  - Current month breakdown by tag (revenues/expenses) for both units
  - Same grouping used by [ReportController::operationalProfitLoss](file:///Users/naveenadicharla/Documents/stonecrusher%20erp/app/Http/Controllers/ReportController.php#L451-L525)

## 6. Data Sources & Query Plan

### 6.1 New Query Layer (Extension)
Introduce a dedicated service responsible for assembling dashboard data:
- `App\Services\Dashboard\AdminDashboardMetricsService`

Responsibilities:
- Provide all counters and series in one call.
- Centralize business filters (e.g., GatePass status = completed).
- Return a normalized array suitable for Blade and charts.

### 6.2 Aggregation Strategy (Reduce Queries)
Replace per-day loops with grouped queries:
- GatePass weekly series:
  - `DATE(date)` grouped for count, sum(net_weight), sum(total_amount)
  - Filter: `status = completed`
- DieselEntry weekly series:
  - `DATE(date)` grouped for sum(liters)
- OperationalRecord MTD:
  - Join `operational_tags` to split revenue vs expense
  - Group by unit and tag as needed

### 6.3 Caching
Cache the computed dashboard payload briefly:
- Key: `dashboard:admin:<YYYY-MM-DD>` for daily slices + `dashboard:admin:mtd:<YYYY-MM>`
- TTL: 30–120 seconds

Constraints:
- Do not cache user-specific privileged data beyond admin scope (admin-only route).

## 7. UI Componentization Plan

### 7.1 New Blade Components (Additive)
Add a reusable metric card component aligned with existing `stat-premium-card` look:
- `resources/views/components/metric-card.blade.php`

Props:
- `label`, `value`, `subtext` (optional)
- `icon` slot
- `accent` (theme class like `bg-blue-lt`, `text-blue`)
- `href` (optional to make the whole card clickable)

### 7.2 Chart Containers
Extract a minimal `chart-card` wrapper component only if it reduces duplication without forcing a redesign.

## 8. Chart Delivery
Phase 1:
- Keep ApexCharts via CDN to avoid bundler changes.
- Move inline JS into a dedicated Blade partial to minimize view noise, but keep data injection stable.

Phase 2 (optional):
- Move ApexCharts to Vite dependency and render charts from a JSON endpoint to allow async refresh.

## 9. Role-Based Rendering
- Continue using route-level protection as primary enforcement.
- Use view-level `@role` only for navigation/actions, not for securing data.

## 10. Acceptance Criteria
- Admin dashboard loads with materially fewer DB queries (weekly series computed via grouped queries, no per-day loop queries).
- New widgets appear:
  - Crusher Profit (MTD), Quarry Expense (MTD), Net Profit (MTD)
  - Diesel series (7/30 days configurable)
  - Monthly P&L snapshot (same totals as Operational P&L report for the same month)
- No breaking changes to existing routes, reports, or ledger logic.
- UI uses reusable metric card component for all primary “stat” tiles.

## 11. Rollout Steps
1. Add `AdminDashboardMetricsService` and update `AdminDashboardController` to use it.
2. Replace weekly loops with grouped queries; add short caching.
3. Add `x-metric-card` component and refactor dashboard cards to use it.
4. Add MTD profitability widgets from OperationalRecords (crusher/quarry/net).
5. Add Diesel analytics series widget(s).
6. Add Monthly P&L snapshot widget fed from the same aggregation logic as Operational P&L report.

## 12. Risks & Mitigations
- Metric mismatch between dashboard and reports: enforce shared aggregation rules and validate against Operational P&L report totals.
- Missing indexes for date-range group-bys: add composite indexes as a follow-up migration if needed.
- Incomplete operational posting (expenses not fully captured): show “data coverage” note on widgets until posting is complete (no forced backfill in this phase).
