# Database Schema

Database: `comm-calc` (MySQL)

## Entity Relationship Overview

```
users ──< deals ──< commission_payouts
  │
  ├──< weekly_scores
  ├──< spiff_payouts
  ├──< audit_logs
  └──< password_change_logs

commission_settings (standalone key-value)
spiff_settings (standalone key-value)
monthly_snapshots (month-level locking)
branding_settings (singleton)
```

---

## Tables

### users
Core user accounts with role-based access and soft deletes.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | Auto-increment |
| name | string | |
| email | string | Unique |
| password | string | Bcrypt hashed (12 rounds) |
| role | enum | `admin`, `manager`, `sales_rep` |
| force_password_change | boolean | Default: false |
| is_active | boolean | Default: true |
| email_verified_at | timestamp | Nullable |
| remember_token | string | Nullable |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp | Soft delete |

### deals
Sales pipeline records linked to a user.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| user_id | FK -> users | Cascade on delete |
| month | string | Format: YYYY-MM |
| client_name | string | |
| appointment_date | date | Nullable |
| contract_signed_date | date | Nullable |
| deposit_date | date | Nullable |
| original_contract_price | decimal(12,2) | Nullable |
| sold_contract_value | decimal(12,2) | |
| estimated_gm_percent | decimal(5,2) | |
| deal_status | enum | `lead`, `appointment_set`, `quote_sent`, `closed_won`, `closed_lost` |
| days_to_close | integer | Nullable |
| is_fast_close | boolean | Default: false |
| notes | text | Nullable |
| created_at / updated_at | timestamps | |
| deleted_at | timestamp | Soft delete |

**Indexes:** `[user_id, month]`, `deal_status`

### commission_payouts
Calculated commission records per deal.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| deal_id | FK -> deals | Cascade on delete |
| user_id | FK -> users | Cascade on delete |
| month | string | |
| sold_contract_value | decimal(12,2) | |
| gm_percent | decimal(5,2) | |
| tier | string | Tier name at time of calculation |
| base_commission | decimal(10,2) | |
| surplus_bonus | decimal(10,2) | |
| fast_close_bonus | decimal(10,2) | |
| total_payout | decimal(10,2) | |
| settings_snapshot | JSON | Frozen commission settings |
| created_at / updated_at | timestamps | |

### weekly_scores
Weekly performance metrics per rep.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| user_id | FK -> users | Cascade on delete |
| week_start | date | |
| week_end | date | |
| appointments | integer | Default: 0 |
| quotes_sent | integer | Default: 0 |
| deals_closed | integer | Default: 0 |
| close_rate | decimal(5,2) | Default: 0 |
| avg_days_to_close | decimal(5,2) | Default: 0 |
| fast_closes | integer | Default: 0 |
| created_at / updated_at | timestamps | |

**Unique constraint:** `[user_id, week_start]`

### spiff_payouts
Monthly SPIFF incentive records.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| user_id | FK -> users | Cascade on delete |
| month | string | |
| appointments | integer | |
| deals_closed | integer | |
| close_rate | decimal(5,2) | |
| prior_close_rate | decimal(5,2) | Nullable |
| improvement_points | decimal(5,2) | Default: 0 |
| improvement_bonus | decimal(10,2) | Default: 0 |
| target_bonus | decimal(10,2) | Default: 0 |
| fast_close_count | integer | Default: 0 |
| fast_close_bonus | decimal(10,2) | Default: 0 |
| highest_close_rate_bonus | decimal(10,2) | Default: 0 |
| total_spiff | decimal(10,2) | Default: 0 |
| is_override | boolean | Default: false |
| override_notes | text | Nullable |
| settings_snapshot | JSON | Frozen SPIFF settings |
| created_at / updated_at | timestamps | |

**Unique constraint:** `[user_id, month]`

### commission_settings
Key-value store for commission calculation parameters.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| key | string | Unique |
| value | string | |
| label | string | Human-readable label |
| description | text | Nullable |

No timestamps. 12 default entries (see [Commission Rules](commission-rules.md)).

### spiff_settings
Key-value store for SPIFF bonus parameters.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| key | string | Unique |
| value | string | |
| label | string | Human-readable label |
| description | text | Nullable |

No timestamps. 9 default entries (see [SPIFF Rules](spiff-rules.md)).

### monthly_snapshots
Month-end locking mechanism for financial auditability.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| month | string | Unique, format: YYYY-MM |
| is_locked | boolean | Default: false |
| locked_at | timestamp | Nullable |
| locked_by | FK -> users | Nullable |
| unlocked_at | timestamp | Nullable |
| unlocked_by | FK -> users | Nullable |
| commission_settings_snapshot | JSON | Nullable |
| spiff_settings_snapshot | JSON | Nullable |
| created_at / updated_at | timestamps | |

### branding_settings
Singleton table for company branding.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| logo_path | string | Nullable |
| company_name | string | Default: "Bayside Pavers" |
| created_at / updated_at | timestamps | |

### audit_logs
Polymorphic change tracking for compliance.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| user_id | FK -> users | Nullable (null on delete) |
| action | string | e.g., "created", "updated", "deleted" |
| auditable_type | string | Model class name |
| auditable_id | bigint | Model record ID |
| old_values | JSON | Nullable |
| new_values | JSON | Nullable |
| ip_address | string(45) | Nullable |
| created_at | timestamp | |

### password_change_logs
Password change audit trail.

| Column | Type | Notes |
|--------|------|-------|
| id | bigint (PK) | |
| user_id | FK -> users | Cascade on delete |
| changed_by | FK -> users | Nullable (null on delete) |
| change_type | string | `self` or `admin_reset` |
| created_at | timestamp | |
