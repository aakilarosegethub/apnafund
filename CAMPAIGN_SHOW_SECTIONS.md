# Campaign Show Page - Sections Structure

## File: `resources/views/themes/ApnaCrowdfunding/page/campaignShow.blade.php`

### Page Sections Overview

Yeh page multiple sections mein divide hai. Har section ka apna structure aur functionality hai:

---

## 1. **Hero Banner Section** (Line 2199-2257)
```blade
<div class="fundraiser-hero">
    - Campaign Title
    - Banner Image/YouTube Video
    - Play Button (if YouTube video)
```
**Features:**
- YouTube video support with play button overlay
- Fallback to campaign image
- Responsive design

---

## 2. **Organizer Section (Top)** (Line 2260-2278)
```blade
<div class="organizer-section">
    - Organizer Avatar
    - Organizer Name
    - "Donation protected" badge
```
**Features:**
- Shows organizer info
- Avatar with initials fallback
- Security badge

---

## 3. **Description Section** (Line 2281-2286)
```blade
<div class="fundraiser-description">
    - Campaign Title
    - Campaign Description (HTML content)
```
**Features:**
- Full campaign description
- AOS animations

---

## 4. **Actions Section** (Line 2290-2293)
```blade
<div class="fundraiser-actions">
    - Contribute Button
    - Share Button
```
**Features:**
- Quick action buttons
- Share modal trigger

---

## 5. **Organizer Details Section** (Line 2296-2313)
```blade
<div class="organizer-section">
    - Organizer Avatar
    - Organizer Name
    - Location
    - Contact Button
```
**Features:**
- Detailed organizer info
- Location display
- Contact functionality

---

## 6. **Reviews & Comments Section** (Line 2316-2445)
```blade
<div class="reviews-section">
    - Section Title
    - Review Form (Guest/Logged in)
    - Reviews Display
    - Filter by Rating
    - Load More functionality
```
**Features:**
- Comment submission form
- Guest commenting support
- Rating display
- Filter reviews by stars
- AJAX form submission
- Toast notifications

**Sub-sections:**
- **Review Form** (Line 2321-2356)
  - Title input
  - Comment textarea
  - Name & Email fields
  - Submit button
  
- **Reviews Display** (Line 2359-2444)
  - Review list
  - Rating filter dropdown
  - Individual review cards
  - Like & Reply buttons

---

## 7. **Fundraiser Details Section** (Line 2448-2457)
```blade
<div class="fundraiser-details">
    - Created date
    - Category
    - Report fundraiser link
```
**Features:**
- Campaign metadata
- Report functionality

---

## 8. **Rewards Section** (Line 2459-2563) ⭐
```blade
<div class="rewards-section">
    - Section Header with Actions
    - Rewards Grid
    - Individual Reward Cards
    - Add/Edit/Delete functionality
```
**Features:**
- Grid layout for rewards
- Add reward button (for campaign owner)
- Manage rewards link
- Reward cards with:
  - Title & Amount
  - Image
  - Description
  - Type (Physical/Digital)
  - Quantity remaining
  - Terms & Conditions
  - Get Reward button
  - Edit/Delete (for owner)

**Reward Card Structure:**
- Header (Title + Amount)
- Image (optional)
- Content (Description + Details)
- Actions (Get Reward + Admin actions)

**Empty State:**
- "No Rewards Available" message
- "Add Your First Reward" button

---

## 9. **Sidebar Section** (Line 2567-2660)

### 9.1 Progress Card (Line 2569-2650)
```blade
<div class="sidebar-card">
    - Progress Circle (SVG)
    - Raised Amount
    - Percentage of Goal
    - Action Buttons (Share, Contribute)
    - Donation Stats
    - Recent Donations List
    - See All / See Top buttons
```
**Features:**
- Circular progress indicator
- Real-time donation stats
- Recent donations display
- Modal triggers for all/top donations

### 9.2 Latest News Link (Line 2653-2659)
```blade
<div class="news-link-container">
    - News & Updates link
```
**Features:**
- News modal trigger

---

## 10. **Modals**

### 10.1 Donations Modal (Line 2664-2676)
```blade
<div class="donations-modal">
    - Modal Header
    - Donations List
    - Close Button
```
**Features:**
- Shows all donations
- Shows top donations
- Responsive design

### 10.2 Reward Modal (Line 2679-2761)
```blade
<div class="reward-modal">
    - Form for Add/Edit Reward
    - Image upload
    - All reward fields
```
**Features:**
- Add new reward
- Edit existing reward
- Image preview
- Form validation

### 10.3 Share Modal (Line 2764-2829)
```blade
<div class="share-modal">
    - Campaign URL (copyable)
    - Social media share buttons
    - Multiple platforms support
```
**Features:**
- Copy link functionality
- Facebook, Twitter, WhatsApp, Telegram
- Email, LinkedIn, Instagram, Reddit, Pinterest
- Swipe-up animation (mobile)

---

## CSS Sections Structure

### Main Styles (Line 17-2177)
- Body & Header styles
- Fundraiser container layout
- Hero banner styles
- Organizer section styles
- Description styles
- Action buttons
- Sidebar card styles
- Progress circle (SVG)
- Rewards section styles (Line 552-783)
- Reviews section styles (Line 1130-1403)
- News modal styles (Line 1404-1594)
- Share modal styles (Line 1673-1987)
- Donations modal styles (Line 2054-2176)
- Responsive breakpoints

---

## JavaScript Functions

### Global Functions (Line 3046-3186)
- `openShareModal()`
- `closeShareModal()`
- `copyShareUrl()`
- `shareOnFacebook()`
- `shareOnTwitter()`
- `shareOnWhatsApp()`
- `shareOnTelegram()`
- `shareViaEmail()`
- `shareOnLinkedIn()`
- `shareOnInstagram()`
- `shareOnReddit()`
- `shareOnPinterest()`

### jQuery Functions (Line 3188-3622)
- Review form submission (AJAX)
- Review filtering
- Donations modal handling
- Toast notifications
- Load more reviews

### Reward Functions (Line 3729-3873)
- `openRewardModal()`
- `closeRewardModal()`
- `editReward(rewardId)`
- `deleteReward(rewardId)`
- Image preview
- Form submission (AJAX)

### Video Functions (Line 3699-3717)
- `showVideo(videoId)` - YouTube video player

---

## Section Pattern

Har section ka pattern similar hai:

```blade
<!-- Section Name -->
<div class="section-class">
    <div class="section-header">
        <h3>Section Title</h3>
        <!-- Actions (if owner) -->
    </div>
    
    <div class="section-content">
        <!-- Content -->
    </div>
</div>
```

---

## Key Features by Section

| Section | Owner Actions | Guest Actions | AJAX | Modal |
|---------|--------------|---------------|------|-------|
| Hero Banner | - | View | No | No |
| Organizer | - | View | No | No |
| Description | - | View | No | No |
| Actions | - | Contribute/Share | No | Share Modal |
| Reviews | - | Comment | Yes | No |
| Rewards | Add/Edit/Delete | Get Reward | Yes | Reward Modal |
| Sidebar | - | View/Donate | Yes | Donations Modal |

---

## Notes

1. **Rewards Section** is the most complex section with:
   - CRUD operations
   - Modal for add/edit
   - Image upload
   - Grid layout
   - Admin actions

2. **Reviews Section** supports:
   - Guest commenting
   - AJAX submission
   - Rating system
   - Filter functionality

3. **Share Modal** has:
   - Multiple platform support
   - Copy link feature
   - Mobile swipe gestures

4. All sections are responsive with mobile breakpoints at 768px

