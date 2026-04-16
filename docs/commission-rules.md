# Commission Rules

All commission parameters are stored in the `commission_settings` table and are editable by admins. The values below are the seeded defaults.

## Gross Margin Tiers

Commission rates are determined by the deal's **estimated gross margin percentage (GM %)**:

| GM % Range | Commission Rate |
|-----------|-----------------|
| Below 35% | 0% (no commission) |
| Exactly 35% | MAX($750, 0.5% of Sold Contract Value) |
| 35.1% – 37.9% | 3% |
| 38.0% – 40.9% | 5% |
| 41.0% – 43.9% | 7% |
| 44.0% – 46.9% | 9% |
| Exactly 47.0% | 10% |
| Above 47.0% | 10% base + surplus bonus |

## Calculation Components

### Base Commission
```
Base Commission = Sold Contract Value x Tier Commission Rate
```

### Floor Protection
If a deal is at exactly 35% GM, the rep receives **MAX($750, 0.5% of Sold Contract Value)** — whichever is greater.

### Surplus Bonus
For deals **above** 47.0% GM (not at exactly 47%), reps earn an additional bonus:
```
Surplus Bonus = 0.5 x ((Estimated GM% - 47%) x Sold Contract Value)
```

### Fast Close Bonus
Fast close is **auto-calculated only** from deal dates:
- Deal status must be **Closed Won**
- Days to Close = Contract Signed Date - Appointment Date
- If Days to Close <= 3, the deal qualifies as a fast close
- Fast close bonus = flat **$250**

Users cannot manually toggle fast close. It is determined entirely by the deal dates.

## Total Commission Formula

```
Total Payout = Base Commission + Surplus Bonus + Fast Close Bonus
```

## Settings Reference

| Key | Default Value | Description |
|-----|--------------|-------------|
| `min_gm_percent` | 35 | Floor GM% (below = no commission) |
| `target_gm_percent` | 47 | Target GM% (above = surplus bonus) |
| `floor_min_amount` | 750 | Minimum commission at floor GM% |
| `floor_percent` | 0.5 | Percentage of sold value at floor GM% |
| `tier_35_1_37_9_rate` | 3 | Commission rate for 35.1%–37.9% GM |
| `tier_38_40_9_rate` | 5 | Commission rate for 38%–40.9% GM |
| `tier_41_43_9_rate` | 7 | Commission rate for 41%–43.9% GM |
| `tier_44_46_9_rate` | 9 | Commission rate for 44%–46.9% GM |
| `tier_47_rate` | 10 | Commission rate at 47%+ GM |
| `surplus_multiplier` | 0.5 | Multiplier applied to surplus above target |
| `fast_close_days` | 3 | Max days for fast close qualification |
| `fast_close_spiff` | 250 | Flat bonus for fast close deals |
