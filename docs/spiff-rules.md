# SPIFF (Monthly Bonus) Rules

SPIFFs are monthly incentive bonuses awarded to sales reps based on performance metrics. All parameters are stored in the `spiff_settings` table and are editable by admins. The values below are the seeded defaults.

## Bonus Components

### 1. Close Rate Improvement Bonus

Rewards reps who improve their close rate month-over-month.

| Requirement | Value |
|-------------|-------|
| Minimum improvement | 5 percentage points over prior month |
| Minimum appointments | 10 in the current month |
| Bonus amount | **$500** |

### 2. Target Close Rate Bonus

Rewards reps who hit close rate targets. **Both tiers require 12+ appointments.**

| Target | Min Appointments | Bonus |
|--------|-----------------|-------|
| 20% close rate | 12 | **$500** |
| 30%+ close rate | 12 | **$1,000** |

The 30%+ tier **replaces** the 20% tier (not stacked).

### 3. Fast Close Bonus

Per-deal bonus for deals closed quickly. Fast close is **auto-determined from deal dates only** (Contract Signed Date - Appointment Date <= 3 days). It cannot be manually set.

| Requirement | Value |
|-------------|-------|
| Qualification | Days to Close <= 3 (auto-calculated) |
| Bonus per deal | **$250** |

### 4. Highest Close Rate Bonus

Monthly award for the rep with the best close rate.

| Requirement | Value |
|-------------|-------|
| Bonus amount | **$500** |
| Tie handling | Configurable (all win, split, or no bonus on tie) |

## Total Monthly SPIFF Formula

```
Total SPIFF = Improvement Bonus
            + Target Bonus
            + (Fast Close Count x $250)
            + Highest Close Rate Bonus
```

## Admin Overrides

Admins can manually override any rep's SPIFF payout for a given month. Overrides require notes explaining the reason and are tracked in the audit log.

## Settings Reference

| Key | Default Value | Description |
|-----|--------------|-------------|
| `improvement_bonus` | 500 | Bonus for 5+ point close rate improvement |
| `improvement_min_points` | 5 | Minimum improvement points required |
| `improvement_min_appts` | 10 | Minimum appointments for improvement eligibility |
| `target_20_bonus` | 500 | Bonus for hitting 20% close rate (requires 12+ appts) |
| `target_30_bonus` | 1000 | Bonus for hitting 30%+ close rate (requires 12+ appts) |
| `target_min_appts` | 12 | Minimum appointments required for BOTH target bonuses |
| `fast_close_per_deal` | 250 | Per-deal fast close SPIFF amount |
| `highest_close_rate_bonus` | 500 | Monthly highest close rate award |
| `tie_handling` | 1 | 1 = all tied reps win, 2 = split, 3 = no bonus on tie |
