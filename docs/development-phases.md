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
  - [x] Fast close bonus ($250 for deals closed in < 3 days)
  - [x] Floor protection ($750 minimum at 35% GM)
- [x] Real-time commission calculator page (input contract value + GM %, see breakdown)
- [x] Commission payout record creation (linked to deals)
- [x] Settings snapshot capture on payout

---

## Phase 3: Deal Management
> Full deal pipeline CRUD with status tracking.

- [ ] Deal Log page with CRUD operations
  - [ ] Create deal (client name, contract value, GM %, dates, status)
  - [ ] Edit deal
  - [ ] Delete deal (soft delete)
  - [ ] Status transitions (Lead -> Appointment Set -> Quote Sent -> Closed Won/Lost)
- [ ] Deal filtering (by month, status, sales rep)
- [ ] Deal search
- [ ] Days-to-close auto-calculation
- [ ] Fast close flag auto-detection (< 3 days)
- [ ] Commission auto-calculation on Closed Won
- [ ] Deal audit trail

---

## Phase 4: Weekly Scoreboard
> Weekly performance metrics and rep comparisons.

- [ ] Weekly score data entry / auto-calculation
  - [ ] Appointments count
  - [ ] Quotes sent
  - [ ] Deals closed
  - [ ] Close rate calculation
  - [ ] Average days to close
  - [ ] Fast close count
- [ ] Scoreboard display (table view, sortable)
- [ ] Week selector (date range picker)
- [ ] Rep comparison view (manager/admin)
- [ ] Personal stats view (sales rep)

---

## Phase 5: Monthly SPIFF Bonuses
> Monthly incentive bonus calculations with admin overrides.

- [ ] Admin: SPIFF Settings UI (edit thresholds and bonus amounts)
- [ ] SPIFF calculation engine
  - [ ] Close rate improvement bonus ($500 for 5+ point improvement, 10+ appointments)
  - [ ] Target close rate bonus ($500 at 20%, $1000 at 30%+ with 12+ appointments)
  - [ ] Fast close bonus ($250 per qualifying deal)
  - [ ] Highest close rate bonus ($500, with tie-handling rules)
- [ ] Monthly SPIFF payout page
- [ ] Admin override capability (with notes)
- [ ] SPIFF payout history
- [ ] Settings snapshot on payout

---

## Phase 6: Dashboard Enhancements & Reporting
> Charts, leaderboards, activity feeds, and export capabilities.

- [ ] Dashboard charts (commission trends, deal pipeline, close rates)
- [ ] Leaderboard (top reps by commission, close rate, deals)
- [ ] Activity feed (recent deals, payouts, changes)
- [ ] Monthly summary reports
- [ ] PDF export (commission statements, SPIFF reports)
- [ ] Excel export (deal logs, payout history)
- [ ] Month-end locking workflow (freeze snapshots)

---

## Phase 7: Polish & Production Readiness
> Final hardening, testing, and deployment prep.

- [ ] Comprehensive test suite (unit + feature tests)
- [ ] Input validation hardening
- [ ] Error handling and user-friendly error pages
- [ ] Performance optimization (eager loading, caching)
- [ ] Production environment configuration
- [ ] Deployment documentation
- [ ] User guide / help documentation
