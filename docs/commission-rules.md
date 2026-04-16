# Commission Rules

All commission parameters are stored in the `commission_settings` table and are editable by admins. The values below are the seeded defaults.

## Gross Margin Tiers

Commission rates are determined by the deal's **estimated gross margin percentage (GM %)**:

| GM % Range | Tier | Commission Rate |
|-----------|------|-----------------|
| Below 35% | Below Floor | 0% (no commission) |
| 35% | Floor | 3% ($750 minimum protection) |
| 36-37% | Tier 1 | 4% |
| 38-39% | Tier 2 | 5% |
| 40-41% | Tier 3 | 6% |
| 42-43% | Tier 4 | 7% |
| 44-45% | Tier 5 | 8% |
| 46% | Tier 6 | 9% |
| 47%+ | Target+ | 10% |

## Calculation Components

### Base Commission
```
Base Commission = Sold Contract Value x Tier Commission Rate
```

### Floor Protection
If a deal is at exactly 35% GM, the rep receives a **minimum $750 commission** regardless of the calculated amount.

### Surplus Bonus
For deals above the **target GM (47%)**, reps earn an additional bonus on the surplus:
```
Surplus = GM% - Target GM%
Surplus Bonus = Sold Contract Value x Surplus% x Surplus Multiplier (0.5)
```

### Fast Close Bonus
Deals closed within **3 days or fewer** from appointment to contract signing earn a flat **$250 bonus**.

## Total Commission Formula

```
Total Payout = Base Commission + Surplus Bonus + Fast Close Bonus
```

## Settings Reference

| Key | Default Value | Description |
|-----|--------------|-------------|
| `min_gm_percent` | 35 | Floor GM% (below = no commission) |
| `target_gm_percent` | 47 | Target GM% (above = surplus bonus) |
| `floor_commission` | 750 | Minimum commission at floor GM% |
| `tier_1_rate` | 0.04 | Commission rate for 36-37% GM |
| `tier_2_rate` | 0.05 | Commission rate for 38-39% GM |
| `tier_3_rate` | 0.06 | Commission rate for 40-41% GM |
| `tier_4_rate` | 0.07 | Commission rate for 42-43% GM |
| `tier_5_rate` | 0.08 | Commission rate for 44-45% GM |
| `tier_6_rate` | 0.09 | Commission rate for 46% GM |
| `target_rate` | 0.10 | Commission rate at 47%+ GM |
| `surplus_multiplier` | 0.5 | Multiplier applied to surplus above target |
| `fast_close_days` | 3 | Max days for fast close qualification |
| `fast_close_spiff` | 250 | Flat bonus for fast close deals |
