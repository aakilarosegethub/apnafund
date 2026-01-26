# Meta (Facebook) Marketing API Configuration
# Add these environment variables to your .env file

# Meta Access Token
# Get this from: https://developers.facebook.com/tools/explorer/
# Select your app → Generate Access Token → Grant permissions: ads_management, pages_read_engagement
# For production, use a long-lived User Access Token or System User Access Token
META_ACCESS_TOKEN=your_meta_access_token_here

# Meta Ad Account ID
# Find this in Facebook Ads Manager → Account Settings
# Format: The numeric ID only (without "act_" prefix)
# Example: If your account is "act_123456789012345", use "123456789012345"
META_AD_ACCOUNT_ID=123456789012345

# Meta Page ID
# Find this in Facebook Page Settings → About
# Or use Graph API Explorer: GET /me/accounts
# This is the Facebook Page that will be used for ads
META_PAGE_ID=your_facebook_page_id_here

# Example .env configuration:
# META_ACCESS_TOKEN=EAABsbCS1iHgBO7ZCqL9ZCxZBZBmPvKZBZCr...
# META_AD_ACCOUNT_ID=123456789012345
# META_PAGE_ID=987654321012345
