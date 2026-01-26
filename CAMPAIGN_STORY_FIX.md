# Campaign Story "Save and Next" Fix

## Issue
The "Save and Next" button on the campaign story edit page (`/user/campaign/edit/{slug}/story`) was not working correctly:
1. It was redirecting back to the same story page instead of moving to the next section
2. It was showing success messages even when validation errors occurred (e.g., story too short)
3. No proper validation feedback was shown to users before submission

## Changes Made

### 1. Fixed Next Tab Redirect
**File:** `resources/views/themes/green/user/campaign/edit.blade.php`

**Before:**
```html
<input type="hidden" name="next_tab" value="story">
```

**After:**
```html
<input type="hidden" name="next_tab" value="people">
```

**Result:** Now after saving the story, users are redirected to the "People" section (next logical step in campaign creation).

### 2. Added Client-Side Validation
**File:** `resources/views/themes/green/user/campaign/edit.blade.php`

Added the following features:

#### a. Character Counter
- Shows real-time character count as user types
- Displays how many more characters are needed if below minimum (30 characters)
- Changes color from red to green when minimum is met

**HTML Addition:**
```html
<label>Project Story * <span id="charCount" style="color: #666; font-weight: normal; font-size: 14px;">(Minimum 30 characters)</span></label>
```

#### b. Validation Function
Added `validateStoryContent()` JavaScript function that:
- Strips HTML tags from rich text editor content
- Counts actual plain text characters
- Shows error message if less than 30 characters
- Displays alert with specific character count and how many more needed
- Prevents form submission if validation fails

#### c. Form Submission Handler
Enhanced form submission to:
- Validate content before submitting
- Show error alerts if validation fails
- Display loading state on button during submission
- Prevent multiple submissions

### 3. Enhanced Error Display
**Added:**
```html
<p id="storyError" class="note" style="color: red; display: none;"></p>
```

This provides inline error feedback in addition to the alert.

### 4. Server-Side Validation (Already Existed)
**File:** `app/Http/Controllers/User/CampaignController.php` (Line 602-609)

The server-side validation was already correctly implemented:
```php
if ($section == 'story') {
    $this->validate(request(), [
        'description' => 'required|min:30',
    ], [
        'description.required' => 'The story description field is required.',
        'description.min' => 'The story description must be at least 30 characters.',
    ]);
}
```

## How It Works Now

### User Experience Flow:

1. **User enters story text** → Real-time character counter updates
2. **User clicks "Save and Next"** → Client-side validation runs
3. **If validation fails:**
   - Alert shows: "Error: Story must be at least 30 characters. You have X characters. You need Y more characters."
   - Inline error message appears below the editor
   - Form submission is prevented
   - Button remains enabled for retry
4. **If validation passes:**
   - Form submits to server
   - Button shows "Saving..." with spinner
   - Server validates again (security/backup)
   - If server validation fails: User redirected back with error messages
   - If server validation passes: User redirected to "People" section with success message

### Error Messages:

**Client-Side (Immediate Feedback):**
- Character counter: "(15/30 characters - 15 more needed)" in red
- Alert popup: "Error: Story must be at least 30 characters..."
- Inline error: "Story must be at least 30 characters. You have 15 characters (15 more needed)."

**Server-Side (Backup Validation):**
- Laravel validation error: "The story description must be at least 30 characters."
- Toast notification: Error alert with validation message

## Testing

To test the fix:

1. Navigate to: `http://192.168.1.34:8000/user/campaign/edit/{your-campaign-slug}/story`
2. Try to save with less than 30 characters:
   - Should see character counter in red
   - Should see alert preventing submission
   - Should see inline error message
3. Add more than 30 characters:
   - Character counter turns green
   - Can submit successfully
   - Redirected to "People" section

## Files Modified

1. `resources/views/themes/green/user/campaign/edit.blade.php` - Main fix applied
2. Other theme files (`apnafund`, `primary`) - Already correct or not using same structure

## Benefits

✅ **Better UX:** Users get immediate feedback on their input
✅ **Clear Errors:** Specific error messages show exactly what's wrong
✅ **Progress:** Users move forward in the campaign creation flow
✅ **No Confusion:** No more "success" messages when there are errors
✅ **Multiple Validations:** Both client and server-side validation ensure data integrity

## Technical Details

- **Validation Method:** Plain text character count (HTML tags stripped)
- **Minimum Required:** 30 characters (plain text)
- **Editor Used:** Summernote WYSIWYG editor
- **Character Counting:** Uses regex to strip HTML: `content.replace(/<[^>]*>/g, '').replace(/&nbsp;/g, ' ').trim()`

## Date Fixed
January 24, 2026
