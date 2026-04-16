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

Rewards reps who hit close rate targets.

| Target | Min Appointments | Bonus |
|--------|-----------------|-------|
| 20% close rate | (none specified) | **$500** |
| 30%+ close rate | 12 appointments | **$1,000** |

The higher tier replaces the lower (not stacked).

### 3. Fast Close Bonus

Per-deal bonus for deals closed quickly.

| Requirement | Value |
|-------------|-------|
| Qualification | Same as commission fast close (within 3 days) |
| Bonus per deal | **$250** |

### 4. Highest Close Rate Bonus

Monthly award for the rep with the best close rate.

| Requirement | Value |
|-------------|-------|
| Bonus amount | **$500** |
| Tie handling | Configurable (split, both win, or most appointments wins) |

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
| `improvement_min_appointments` | 10 | Minimum appointments for improvement eligibility |
| `target_bonus_20` | 500 | Bonus for hitting 20% close rate |
| `target_bonus_30` | 1000 | Bonus for hitting 30%+ close rate |
| `target_bonus_30_min_appointments` | 12 | Minimum appointments for 30% target bonus |
| `fast_close_bonus_per_deal` | 250 | Per-deal fast close SPIFF amount |
| `highest_close_rate_bonus` | 500 | Monthly highest close rate award |
| `highest_close_rate_tie_handling` | split | How to handle ties (split / both_win / most_appointments) |
