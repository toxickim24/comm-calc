# User Roles & Permissions

## Role Definitions

| Role | Enum Value | Description |
|------|------------|-------------|
| **Admin** | `admin` | Full system access. Manages users, settings, and views all data. |
| **Manager** | `manager` | Views all reps' data. Accesses SPIFF management and deal oversight. |
| **Sales Rep** | `sales_rep` | Views own data only. Uses calculator, logs deals, views own scores. |

## Access Control Matrix

| Feature / Page | Admin | Manager | Sales Rep |
|----------------|:-----:|:-------:|:---------:|
| Dashboard (with YTD summary) | x | x | x |
| Commission Calculator (with comparison) | x | x | x |
| Deal Log (own deals) | x | x | x |
| Deal Log (all reps + batch updates) | x | x | - |
| Weekly Scoreboard (view) | x | x | x |
| Weekly Scoreboard (edit scores) | x | x | - |
| Monthly SPIFF | x | x | - |
| Notification Bell | x | x | x |
| Per-Rep Commission PDF | x | x | own only |
| Admin: User Management | x | - | - |
| Admin: Commission Settings | x | - | - |
| Admin: SPIFF Settings | x | - | - |
| Admin: Branding Settings | x | - | - |
| Admin: Month Locking | x | - | - |
| Admin: Audit Logs | x | - | - |

## Route Protection

Routes are protected by three custom middleware layers:

1. **`CheckRole`** -- Validates the user's role against allowed roles for the route. Returns 403 if unauthorized.
2. **`EnsureUserIsActive`** -- Checks `is_active` flag on every request. Auto-logs out deactivated users.
3. **`ForcePasswordChange`** -- Redirects users with `force_password_change = true` to the password change page. They cannot access any other page until they change their password.

## Admin Safeguards

- Cannot delete the last remaining admin account
- Cannot downgrade the last admin to a non-admin role
- Cannot delete your own account
- All user changes are recorded in audit logs
