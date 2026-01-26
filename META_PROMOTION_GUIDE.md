# Meta (Facebook) Marketing API Integration Guide

## 📋 Overview
This feature allows users to promote/boost their campaigns directly from the platform using Meta (Facebook) Marketing API. The implementation creates a complete Facebook advertising campaign including Campaign, Ad Set, Ad Creative, and Ad.

---

## 🚀 Quick Setup

### 1. Database Setup

Run the SQL migration to create the `campaign_promotions` table:

```bash
# For Laravel
php artisan migrate

# OR run the plain SQL file
mysql -u your_user -p your_database < create_campaign_promotions_table.sql
```

### 2. Environment Variables

Add these to your `.env` file:

```env
META_ACCESS_TOKEN=your_meta_access_token_here
META_AD_ACCOUNT_ID=123456789012345
META_PAGE_ID=your_facebook_page_id_here
```

#### How to Get Meta Credentials:

**META_ACCESS_TOKEN:**
1. Go to [Facebook Graph API Explorer](https://developers.facebook.com/tools/explorer/)
2. Select your app
3. Click "Generate Access Token"
4. Grant permissions: `ads_management`, `pages_read_engagement`, `ads_read`
5. For production, generate a long-lived token (60 days) or use System User token

**META_AD_ACCOUNT_ID:**
1. Go to [Facebook Ads Manager](https://business.facebook.com/adsmanager/)
2. Click Settings → Account Settings
3. Copy the Ad Account ID (numbers only, without "act_" prefix)

**META_PAGE_ID:**
1. Go to your Facebook Page → Settings → About
2. Copy the Page ID
3. Or use Graph API: `GET /me/accounts`

---

## 📁 File Structure

```
apnafund/
├── app/
│   ├── Helpers/
│   │   └── MetaApiHelper.php                    # cURL helper for Meta API
│   ├── Http/Controllers/User/
│   │   └── CampaignPromotionController.php      # Main promotion logic
│   └── Models/
│       └── CampaignPromotion.php                # Model for promotions
├── database/migrations/
│   └── 2026_01_24_000001_create_campaign_promotions_table.php
├── routes/
│   └── user.php                                 # Routes for promotion endpoints
├── resources/views/themes/green/user/campaign/
│   └── edit.blade.php                           # Frontend UI with AJAX
└── create_campaign_promotions_table.sql         # Plain SQL migration
```

---

## 🔧 Implementation Details

### Backend Flow

1. **User clicks "Promote Campaign"** → AJAX request sent to backend
2. **Backend validates** campaign ownership and approval status
3. **Meta API Integration:**
   - Create Meta Campaign (objective: `OUTCOME_TRAFFIC`)
   - Create Ad Set (with targeting, budget, page_id)
   - Create Ad Creative (link to campaign page)
   - Create Ad (set to `ACTIVE` status)
4. **Save to database** all Meta IDs for future reference
5. **Return success** with Meta campaign/ad IDs

### API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/user/campaign/promotion/{campaignId}/promote` | Start promoting campaign |
| POST | `/user/campaign/promotion/{campaignId}/pause` | Pause promotion |
| GET | `/user/campaign/promotion/{campaignId}/status` | Get promotion status |

---

## 🎯 Meta Campaign Structure

```
Meta Campaign (OUTCOME_TRAFFIC)
└── Ad Set
    ├── Daily Budget: $10-$10,000
    ├── Targeting: Countries, Age 18-65
    ├── Optimization: LINK_CLICKS
    └── Promoted Object: page_id
        └── Ad Creative
            ├── Message/Copy
            ├── Link URL: campaign detail page
            └── Call-to-Action: LEARN_MORE
                └── Ad (ACTIVE)
```

---

## 💡 Features

✅ **Full Meta API Integration** using cURL (Graph API v19.0)  
✅ **Reusable Helper Functions** for all Meta API operations  
✅ **Database Tracking** of all promotion activities  
✅ **Error Handling** with detailed logging  
✅ **Permission Checks** (owner/collaborator validation)  
✅ **Status Management** (promote, pause, resume)  
✅ **Budget Control** with validation  
✅ **Multi-Country Targeting**  
✅ **Real-time Status Updates** via AJAX  

---

## 📊 Database Schema

```sql
campaign_promotions:
- id (primary key)
- campaign_id (foreign key → campaigns)
- meta_campaign_id (varchar)
- meta_adset_id (varchar)
- meta_ad_id (varchar)
- meta_creative_id (varchar)
- status (pending|active|paused|error)
- daily_budget (decimal)
- error_message (text)
- promoted_at (timestamp)
- created_at, updated_at
```

---

## 🧪 Testing

### 1. Check Environment Setup
```bash
php artisan tinker
>>> env('META_ACCESS_TOKEN')
>>> env('META_AD_ACCOUNT_ID')
>>> env('META_PAGE_ID')
```

### 2. Test Meta API Connection
Create a test route:
```php
Route::get('/test-meta-api', function() {
    try {
        $result = \App\Helpers\MetaApiHelper::makeRequest(
            '/act_' . env('META_AD_ACCOUNT_ID'),
            [],
            'GET'
        );
        return response()->json(['success' => true, 'data' => $result]);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
```

### 3. Test Promotion Flow
1. Login as campaign owner
2. Go to campaign edit page → Boost tab
3. Set budget and target countries
4. Click "Promote Campaign"
5. Check browser console for AJAX response
6. Verify in Facebook Ads Manager

---

## 🐛 Troubleshooting

### Error: "META_ACCESS_TOKEN is not configured"
- Check `.env` file has `META_ACCESS_TOKEN` set
- Run `php artisan config:cache` to refresh config

### Error: "Meta API Error [190]: Invalid OAuth 2.0 Access Token"
- Access token expired (Facebook tokens expire)
- Generate new token from Graph API Explorer
- For production, use long-lived token or System User token

### Error: "Meta API Error [100]: Invalid parameter"
- Check `META_AD_ACCOUNT_ID` format (numbers only, no "act_" prefix)
- Verify `META_PAGE_ID` is correct

### Error: "Campaign not found" or 403 Forbidden
- User must be campaign owner or collaborator
- Campaign must be approved (approval = 1)

### Error: "Campaign already being promoted"
- Check `campaign_promotions` table for existing active promotion
- Pause existing promotion first or update status

---

## 🔐 Security Best Practices

1. **Never commit** `.env` file to version control
2. **Use long-lived tokens** for production (System User recommended)
3. **Implement rate limiting** on promotion endpoints
4. **Log all API calls** for audit trail
5. **Validate budget limits** to prevent abuse
6. **Restrict permissions** to campaign owners/collaborators only

---

## 📈 Production Considerations

### Meta Business Manager Setup
For production, you should:
1. Create a **Facebook Business Manager** account
2. Create a **System User** with permanent access token
3. Grant System User permissions: `ads_management`, `pages_read_engagement`
4. Use System User token instead of personal access token

### Budget & Billing
- Facebook charges are separate from platform
- Consider implementing budget limits per user/campaign
- Monitor spending via Meta Ads Manager
- Set up billing alerts in Facebook Business Manager

### Compliance
- Ensure ads comply with [Facebook Advertising Policies](https://www.facebook.com/policies/ads/)
- Some categories require special ad category declaration
- Certain regions have additional restrictions

---

## 🛠️ Helper Functions Reference

### `MetaApiHelper::makeRequest($endpoint, $params, $method)`
Generic cURL wrapper for Meta API calls.

### `MetaApiHelper::createCampaign($name, $objective, $status)`
Creates a Meta campaign.

### `MetaApiHelper::createAdSet($campaignId, $name, $dailyBudget, ...)`
Creates an ad set within a campaign.

### `MetaApiHelper::createAdCreative($name, $message, $linkUrl, ...)`
Creates ad creative with link and message.

### `MetaApiHelper::createAd($name, $adsetId, $creativeId, $status)`
Creates an ad and sets status (ACTIVE/PAUSED).

### `MetaApiHelper::updateStatus($objectId, $status)`
Updates status of any Meta object (campaign/adset/ad).

---

## 📝 Notes

- This implementation uses **Meta Graph API v19.0**
- All API calls use **cURL** (no external PHP libraries required)
- Compatible with **PHP 7.4+** and **Laravel 8+**
- Can be adapted for plain PHP projects (remove Laravel-specific code)

---

## 📞 Support

For Meta API issues:
- [Facebook Developer Documentation](https://developers.facebook.com/docs/marketing-api)
- [Marketing API Reference](https://developers.facebook.com/docs/marketing-api/reference)
- [Graph API Explorer](https://developers.facebook.com/tools/explorer/)

---

## 🎉 Credits

Developed for **Apna Fund** crowdfunding platform.

**Version:** 1.0.0  
**Date:** January 24, 2026  
**API Version:** Meta Graph API v19.0
