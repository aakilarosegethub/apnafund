# Campaign Show Blade File - Revert Guide

## File: `resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php`

---

## Complete Git History

### All Commits (Latest First):

1. **1124e48** - "Update campaign controllers, views, and server scripts"
   - **Date:** 2025-12-19 16:41 (24 hours ago)
   - **Changes:** 155 insertions, 127 deletions
   - **Status:** Latest commit

2. **343ef2c** - "Update campaign and reward functionality with new features and improvements" ⭐
   - **Date:** 2025-09-10 16:46 (3 months ago)
   - **Changes:** 1,428 insertions, 94 deletions
   - **Note:** Major update - Rewards section add hua

3. **558b64b** - "Fix JavaScript errors in campaign edit page"
   - **Date:** 2025-09-05 22:11 (4 months ago)
   - **Changes:** JavaScript fixes

4. **927ba69** - "Added Custom Code Management and Home Page Management features"
   - **Date:** 2025-08-25 18:41 (4 months ago)
   - **Changes:** Custom code management

5. **9e85f5c** - "Update ApnaCrowdfunding project with new features and improvements"
   - **Date:** 2025-08-21 12:26 (4 months ago)
   - **Changes:** General updates

6. **8344d7e** - "Fix StripeV3 payment gateway - hard code amount to 5 USD and fix API structure"
   - **Date:** 2025-08-21 11:15 (4 months ago)
   - **Changes:** Payment gateway fixes

---

## How to Revert Code

### Option 1: Revert to Previous Commit (Undo Latest Changes)

Agar aap latest commit (1124e48) ko revert karna chahte hain:

```bash
# Revert latest commit changes for this file only
git checkout 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
git commit -m "Revert campaignShow.blade.php to previous version"
```

Ya phir:

```bash
# Restore file to previous commit state
git restore --source=343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
```

---

### Option 2: Revert to Specific Commit

Kisi specific commit par jaane ke liye:

```bash
# Option A: View file at specific commit (temporary, doesn't change working directory)
git show 343ef2c:resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php > temp_file.blade.php

# Option B: Checkout file from specific commit
git checkout 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# Option C: Restore from specific commit
git restore --source=343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
```

---

### Option 3: Create New Branch from Specific Commit

Agar aap kisi purani version par naya branch banana chahte hain:

```bash
# Create new branch from specific commit
git checkout -b revert-campaign-show 343ef2c

# Or checkout specific file only
git checkout -b feature-branch
git checkout 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
git add resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
git commit -m "Revert to previous version"
```

---

### Option 4: View Differences Before Reverting

Pehle dekh lo kya changes hain:

```bash
# See what changed in latest commit
git diff 343ef2c..1124e48 -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# See file at specific commit
git show 343ef2c:resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php | less

# Compare with current file
git diff HEAD -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
```

---

## Common Revert Scenarios

### Scenario 1: Latest Commit ko Undo karna

```bash
# Step 1: See what will be reverted
git diff HEAD~1..HEAD -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# Step 2: Revert to previous version
git checkout HEAD~1 -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# Step 3: Review changes
git diff --staged

# Step 4: Commit the revert
git commit -m "Revert campaignShow.blade.php to previous commit"
```

---

### Scenario 2: Rewards Section wale commit se pehle wala version

Agar aap rewards section se pehle wala version chahte hain:

```bash
# Go back to commit before rewards section (558b64b)
git checkout 558b64b -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
git commit -m "Revert to version before rewards section"
```

---

### Scenario 3: Partial Revert (Specific Changes Only)

Agar aap sirf kuch specific changes ko revert karna chahte hain:

```bash
# Interactive revert
git checkout -p 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
```

---

## Important Notes

⚠️ **WARNING:**
- Revert karne se pehle current changes backup kar lo
- Agar uncommitted changes hain to pehle commit kar lo ya stash kar lo

### Safety Check:

```bash
# Check if you have uncommitted changes
git status

# If you have uncommitted changes, save them first:
git stash save "Backup before revert"

# After revert, if needed, restore your changes:
git stash pop
```

---

## Quick Reference Commands

```bash
# View full history
git log --oneline -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# View specific commit
git show 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# Revert to previous commit (latest undo)
git checkout HEAD~1 -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# Revert to specific commit
git checkout 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# See what changed between commits
git diff 343ef2c..1124e48 -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php

# View file at specific commit
git show 343ef2c:resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
```

---

## Commit Timeline

```
8344d7e (Aug 21) ──┐
                   │
9e85f5c (Aug 21) ──┤
                   │
927ba69 (Aug 25) ──┤
                   │
558b64b (Sep 5)  ──┤
                   │
343ef2c (Sep 10) ──┼── Rewards Section Add (1,428 lines)
                   │
1124e48 (Dec 19) ──┘── Latest (155 insertions, 127 deletions)
```

---

## Recommended Approach

1. **Pehle dekh lo kya changes hain:**
   ```bash
   git diff 343ef2c..HEAD -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
   ```

2. **Agar latest commit ko revert karna hai:**
   ```bash
   git checkout 343ef2c -- resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php
   git commit -m "Revert to previous version"
   ```

3. **Ya agar specific lines/changes ko revert karna hai:**
   - Manually edit karo ya
   - Interactive revert use karo

