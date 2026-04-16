# Project Overview

## What Is This?

The **Bayside Pavers Commission Calculator** is an internal web application used by the Bayside Pavers sales team and management to:

- Calculate sales rep commissions based on gross margin tiers
- Log and track deals through the sales pipeline
- Display weekly performance scoreboards
- Manage monthly SPIFF (bonus incentive) payouts
- Provide admin tools for user management, settings, and audit trails

## Who Uses It?

| Role | Description |
|------|-------------|
| **Admin** | Full system access. Manages users, commission/SPIFF settings, branding, and audit logs. |
| **Manager** | Views all reps' data. Accesses scoreboard, SPIFF management, and deal logs. |
| **Sales Rep** | Views own deals, commissions, and scoreboard. Uses the commission calculator. |

## Tech Stack

| Layer | Technology |
|-------|-----------|
| **Backend** | Laravel 13.0 (PHP 8.4+) |
| **Frontend** | Livewire 4.2 + Tailwind CSS 4.0 |
| **Database** | MySQL (`comm-calc` database on local WAMP) |
| **Build Tool** | Vite 8.0 |
| **PDF Export** | DomPDF 3.1 |
| **Excel Export** | Maatwebsite Excel 3.1 |
| **Notifications** | SweetAlert2 + Notyf toast notifications |
| **Tooltips** | Tippy.js |

## Architecture

- **Livewire-first** -- No traditional controllers. All page logic lives in Livewire components.
- **Database-driven settings** -- Commission tiers, SPIFF thresholds, and branding are stored in the database and editable by admins at runtime.
- **Monthly snapshot locking** -- At month-end, settings are frozen into snapshots so historical calculations remain accurate.
- **Polymorphic audit logging** -- All changes to users, settings, and deals are tracked with old/new values and IP addresses.
- **Soft deletes** -- Users and deals are soft-deleted to preserve historical records.
- **Auto-calculated fast close** -- Fast close is determined solely from deal dates (Contract Signed - Appointment <= 3 days). It cannot be manually toggled.
- **Weekly scoreboard with manual editing** -- Scores are auto-derived from deal data with recalculate support. Admins/managers can also click-to-edit individual fields, with all changes audit-logged.
- **Closed Won safeguards** -- Changing a deal to Closed Won requires a confirmation modal and both appointment and contract signed dates. Prevents accidental commission payouts.
- **Pre-lock month checklist** -- Before locking a month, admins see a checklist of open deals, missing dates, and uncalculated SPIFFs. Must acknowledge before locking.
- **Month lock enforcement** -- Locked months block deal creation, editing, deletion, status changes, and SPIFF recalculation.
- **In-app notifications** -- Notification bell in the top bar showing recent audit activity.
- **Deal comparison calculator** -- Side-by-side "what if" scenario comparison showing the commission delta.
- **Per-rep commission PDF** -- Individual rep commission statements with SPIFF breakdown for payroll distribution.
- **Year-to-date summary** -- Dashboard table showing cumulative commission + SPIFF totals per rep for the current year.
