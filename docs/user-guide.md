# User Guide

## Getting Started

### Logging In

1. Go to the application URL
2. Enter your email and password
3. If prompted, change your password on first login

### Default Accounts (Development Only)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@baysidepavers.com | password |
| Manager | manager@baysidepavers.com | password |
| Sales Rep | john@baysidepavers.com | password |
| Sales Rep | jane@baysidepavers.com | password |

### Notification Bell

The bell icon in the top-right header shows recent system activity. A red badge indicates new events in the last 24 hours. Click the bell to see a dropdown of recent actions. Admins can click "View all audit logs" to open the full log.

---

## For Sales Reps

### Dashboard
Your dashboard shows:
- Deals this month and how many are closed
- Your commission earnings for the month
- Current pipeline value
- Commission trend over the last 6 months
- **Year-to-date summary** showing your total commission and SPIFF earnings for the year

### Commission Calculator
1. Go to **Calculator** in the sidebar
2. Enter the **Sold Contract Value** (e.g., $25,000)
3. Enter the **Estimated Gross Margin %** (e.g., 42%)
4. See the full commission breakdown instantly on the right

**Compare Scenarios:** Click "Compare Scenarios" to open a second set of inputs (Scenario B). Enter a different GM% to see how it changes the payout. A green/red delta shows the difference between the two scenarios.

**Note:** Fast close bonus ($250) is not shown in the calculator. It is automatically applied when a deal is closed within 3 days (determined from deal dates, not manually toggled).

### Deal Log
1. Go to **Deal Log** in the sidebar
2. Click **New Deal** to create one
3. Fill in: client name, contract value, GM%, dates, and status
4. As the deal progresses, change its status using the dropdown in the table
5. When changing to **Closed Won**, a confirmation modal appears (since this triggers a commission payout)
6. **Closed Won requires** both Appointment Date and Contract Signed Date
7. Fast close is auto-detected from the dates (Contract Signed - Appointment <= 3 days)
8. GM% values are color-coded: red (below 35%), amber (35-40%), green (41-46%), bold green (47%+)

**Mobile view:** On phones and tablets, deals display as stacked cards instead of a table for easier reading.

**Excel export:** Click the **Excel** button to download the current month's deals as a spreadsheet.

### Weekly Scoreboard
Scores are **auto-calculated from deal data**. Admins and managers can click **Recalculate** to refresh scores for any week.

View your weekly performance metrics:
- Appointments, quotes sent, deals closed
- Close rate, average days to close
- Fast close count

---

## For Managers

Managers can do everything sales reps can, plus:

- **See all reps' deals** in the Deal Log (with rep filter)
- **Batch status updates** — select multiple deals using checkboxes, then change status in bulk (Closed Won is blocked from batch updates for safety)
- **View team scoreboard** with all reps' weekly stats and team totals
- **Edit scoreboard values** — click any score value to edit inline (Enter to save, Escape to cancel). All edits are audit-logged
- **Recalculate** scoreboard and SPIFF data from deal records
- **Access Monthly SPIFF** page to view and manage bonus calculations
- **Download per-rep commission PDFs** from the YTD summary table on the dashboard

---

## For Admins

Admins have full system access including everything managers can do, plus:

### User Management
- **Admin > Users**: Create, edit, deactivate, and delete user accounts
- Reset passwords and force password changes
- Cannot delete the last admin or your own account

### Commission Settings
- **Admin > Commission Settings**: Edit GM thresholds, tier rates, surplus multiplier, fast close parameters
- Current schedule summary shown at the top for quick reference
- Changes take effect immediately for new calculations
- Use **Month Locking** to freeze settings for completed months

### SPIFF Settings
- **Admin > SPIFF Settings**: Edit improvement bonuses, target bonuses, fast close per-deal amounts, highest close rate bonus, and tie handling rules
- Current SPIFF schedule summary shown at the top
- Both 20% and 30% target close rate bonuses require 12+ appointments

### Branding
- **Admin > Branding**: Change the company name and upload a logo

### Month Locking
- **Admin > Month Locking**: Lock completed months to freeze commission and SPIFF settings
- **Pre-lock checklist**: Before locking, a checklist modal shows open deals, missing dates, and uncalculated SPIFFs. You can still lock with warnings, but must acknowledge them
- Locked months **block** all deal modifications and SPIFF recalculations
- Locked months capture a snapshot of settings at that point in time
- Unlock if corrections are needed (with audit trail)

### Audit Logs
- **Admin > Audit Logs**: View all system changes with who, what, when, and before/after values
- Search by user name and filter by action type
- Scoreboard manual edits are also logged here

### Exports
- **Commission PDF** (all reps): Download from the dashboard header
- **Per-rep Commission PDF**: Download from the YTD summary table on the dashboard. Includes deal-by-deal breakdown and SPIFF details
- **Payouts Excel**: Download payout history from the dashboard
- **Deal Log Excel**: Download from the Deal Log page
- **SPIFF PDF**: Download from the Monthly SPIFF page

---

## Tips

- The **lightning bolt** icon next to a deal means it qualified as a fast close (auto-detected, not manually set)
- Commission is only calculated on **Closed Won** deals
- Closed Won requires both Appointment Date and Contract Signed Date
- Fast close = Contract Signed Date - Appointment Date <= 3 days
- GM% is color-coded in the deal log: red (below floor), amber (lower tiers), green (mid-high), bold (47%+)
- Use the **month picker** on Deal Log, Scoreboard, and SPIFF pages to view historical data
- The **leaderboard** on the dashboard ranks reps by total commission for the current month
- The **YTD table** at the bottom of the dashboard shows cumulative earnings per rep
- Use **Compare Scenarios** in the calculator to see how different GM% affects your commission
- All changes are logged in the audit trail for accountability
- Locked months cannot be modified — talk to an admin if corrections are needed
