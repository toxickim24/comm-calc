# Development Phases

Status key: [x] = complete, [ ] = not started

---

## Phase 1: Foundation & Authentication
> Core infrastructure, auth system, user management, and admin tools.

- [x] Laravel project scaffolding (Laravel 13, Livewire 4, Tailwind 4, Vite 8)
- [x] Database schema design (13 tables with migrations)
- [x] Enum definitions (UserRole, DealStatus)
- [x] User model with role-based access (Admin, Manager, Sales Rep)
- [x] Login page with email/password authentication
- [x] Rate-limited login (5 attempts before lockout)
- [x] Active user enforcement middleware (auto-logout if deactivated)
- [x] Force password change middleware and flow
- [x] Self-service password change page
- [x] Logout functionality
- [x] Role-based route middleware (`CheckRole`)
- [x] Responsive sidebar navigation with role-based visibility
- [x] Mobile-friendly layout with hamburger menu
- [x] Admin: User Management CRUD (create, edit, delete, password reset)
  - [x] Prevent deleting last admin
  - [x] Prevent downgrading last admin
  - [x] Prevent self-deletion
  - [x] Search by name/email, filter by role, pagination
- [x] Admin: Branding Settings (company name + logo upload)
- [x] Admin: Audit Log viewer (search, filter, pagination)
- [x] Audit logging system (polymorphic, tracks old/new values + IP)
- [x] Password change logging
- [x] Database seeders (users, commission settings, SPIFF settings, branding)
- [x] Toast notifications (Notyf) and confirmation modals (SweetAlert2)
- [x] Tooltip system (Tippy.js)
- [x] Dashboard with role-aware statistics
- [x] Custom color theme (Bayside Pavers green palette)

---

## Phase 2: Commission Calculator
> Real-time commission calculation engine with admin-configurable tiers.

- [x] Admin: Commission Settings UI (edit tiers, thresholds, multipliers)
- [x] Commission calculation engine/service
  - [x] Gross margin tier lookup
  - [x] Base commission calculation
  - [x] Surplus bonus calculation (above target GM)
  - [x] Fast close bonus ($250 for deals closed in <= 3 days, auto-calculated from deal dates only)
  - [x] Floor protection (MAX($750, 0.5% of contract value) at exactly 35% GM)
- [x] Real-time commission calculator page (input contract value + GM %, see breakdown — no manual fast close toggle)
- [x] Commission payout record creation (linked to deals)
- [x] Settings snapshot capture on payout

---

## Phase 3: Deal Management
> Full deal pipeline CRUD with status tracking.

- [x] Deal Log page with CRUD operations
  - [x] Create deal (client name, contract value, GM %, dates, status)
  - [x] Edit deal
  - [x] Delete deal (soft delete)
  - [x] Status transitions (Lead -> Appointment Set -> Quote Sent -> Closed Won/Lost)
- [x] Deal filtering (by month, status, sales rep)
- [x] Deal search
- [x] Days-to-close auto-calculation
- [x] Fast close flag auto-detection (< 3 days)
- [x] Commission auto-calculation on Closed Won
- [x] Deal audit trail

---

## Phase 4: Weekly Scoreboard
> Auto-calculated weekly performance metrics with admin/manager recalculate and manual per-field editing support.

- [x] Weekly score auto-calculation from deal data (with Recalculate button for admins/managers)
  - [x] Appointments count
  - [x] Quotes sent
  - [x] Deals closed
  - [x] Close rate calculation
  - [x] Average days to close
  - [x] Fast close count
- [x] Scoreboard display (table view, sortable)
- [x] Week selector (date range picker)
- [x] Rep comparison view (manager/admin)
- [x] Personal stats view (sales rep)

---

## Phase 5: Monthly SPIFF Bonuses
> Monthly incentive bonus calculations with admin overrides.

- [x] Admin: SPIFF Settings UI (edit thresholds and bonus amounts)
- [x] SPIFF calculation engine
  - [x] Close rate improvement bonus ($500 for 5+ point improvement, 10+ appointments)
  - [x] Target close rate bonus ($500 at 20%, $1000 at 30%+ with 12+ appointments)
  - [x] Fast close bonus ($250 per qualifying deal)
  - [x] Highest close rate bonus ($500, with tie-handling rules)
- [x] Monthly SPIFF payout page
- [x] Admin override capability (with notes)
- [x] SPIFF payout history
- [x] Settings snapshot on payout

---

## Phase 6: Dashboard Enhancements & Reporting
> Charts, leaderboards, activity feeds, and export capabilities.

- [x] Dashboard charts (commission trends, deal pipeline, close rates)
- [x] Leaderboard (top reps by commission, close rate, deals)
- [x] Activity feed (recent deals, payouts, changes)
- [x] Monthly summary reports
- [x] PDF export (commission statements, SPIFF reports)
- [x] Excel export (deal logs, payout history)
- [x] Month-end locking workflow (freeze snapshots)

---

## Phase 7: Polish & Production Readiness
> Final hardening, testing, and deployment prep.

- [x] Comprehensive test suite (unit + feature tests)
- [x] Input validation hardening
- [x] Error handling and user-friendly error pages
- [x] Performance optimization (eager loading, caching)
- [x] Production environment configuration
- [x] Deployment documentation
- [x] User guide / help documentation

---

## Phase 8: Safeguards, Reporting & UX Hardening
> High-impact safety improvements and reporting features. Does NOT modify core commission/SPIFF calculation logic.

### Deal Entry Safeguards
- [x] Closed Won confirmation modal (SweetAlert2 confirmation before status change triggers commission)
- [x] Required dates for Closed Won (appointment_date + contract_signed_date mandatory before CW save/status change)
- [x] Contract value > 0 form validation (gt:0 rule + service-layer protection)

### Month-End Locking Safeguards
- [x] Pre-lock month checklist (show open deals, missing dates, uncalculated SPIFFs before locking)
- [x] Admin acknowledgement required before final lock

### Reporting & Insights
- [x] Per-rep commission statement PDF (single rep filtered, for payroll distribution)
- [x] Year-to-date commission + SPIFF summary (per rep, on dashboard)

### UX Improvements
- [x] Mobile card view for deal log (stacked cards below lg breakpoint)
- [x] Deal comparison calculator (side-by-side "what if" scenarios)
- [x] GM% color coding in deal log table (red < 35, amber 35-40, green 41+, bold 47+)
- [x] Batch status updates for managers (checkbox select + bulk action, CW blocked in batch)
- [x] In-app notification bell (recent audit events dropdown in top bar)
- [x] Weekly scoreboard manual per-field editing (click-to-edit with Enter/Escape, audit logged)
