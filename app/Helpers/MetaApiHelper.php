<?php

namespace App\Helpers;

/**
 * Meta Marketing API Helper
 * Provides reusable cURL functions for Facebook/Meta Marketing API integration
 */
class MetaApiHelper
{
    /**
     * Base URL for Meta Graph API v19.0
     */
    private static $baseUrl = 'https://graph.facebook.com/v19.0';

    /**
     * Generic cURL helper for Meta API requests
     * 
     * @param string $endpoint - API endpoint (e.g., '/act_123456789/campaigns')
     * @param array $params - POST parameters
     * @param string $method - HTTP method (GET, POST, DELETE)
     * @return array - Decoded JSON response
     * @throws \Exception on cURL errors
     */
    public static function makeRequest($endpoint, $params = [], $method = 'POST')
    {
        $accessToken = env('META_ACCESS_TOKEN');
        
        if (empty($accessToken)) {
            throw new \Exception('META_ACCESS_TOKEN is not configured in .env file');
        }

        // Add access token to params
        $params['access_token'] = $accessToken;

        // Build URL
        $url = self::$baseUrl . $endpoint;

        // Initialize cURL
        $ch = curl_init();

        if ($method === 'GET') {
            $url .= '?' . http_build_query($params);
            curl_setopt($ch, CURLOPT_HTTPGET, true);
        } elseif ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        } elseif ($method === 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            $url .= '?' . http_build_query($params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        // Set User-Agent
        curl_setopt($ch, CURLOPT_USERAGENT, 'ApnaFund-Platform/1.0');

        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Handle cURL errors
        if ($curlError) {
            throw new \Exception('cURL Error: ' . $curlError);
        }

        // Decode response
        $result = json_decode($response, true);

        // Handle API errors
        if ($httpCode >= 400 || isset($result['error'])) {
            $errorMsg = $result['error']['message'] ?? 'Unknown Meta API error';
            $errorCode = $result['error']['code'] ?? $httpCode;
            throw new \Exception("Meta API Error [{$errorCode}]: {$errorMsg}");
        }

        return $result;
    }

    /**
     * Create a Meta Campaign
     * 
     * @param string $name - Campaign name
     * @param string $objective - Campaign objective (e.g., 'OUTCOME_TRAFFIC')
     * @param string $status - Campaign status ('PAUSED' or 'ACTIVE')
     * @return array - Campaign data with 'id'
     */
    public static function createCampaign($name, $objective = 'OUTCOME_TRAFFIC', $status = 'PAUSED')
    {
        $adAccountId = env('META_AD_ACCOUNT_ID');
        
        if (empty($adAccountId)) {
            throw new \Exception('META_AD_ACCOUNT_ID is not configured');
        }

        $endpoint = "/act_{$adAccountId}/campaigns";
        
        $params = [
            'name' => $name,
            'objective' => $objective,
            'status' => $status,
            'special_ad_categories' => '[]', // Required by Meta, empty array means no special categories
        ];

        return self::makeRequest($endpoint, $params);
    }

    /**
     * Create an Ad Set
     * 
     * @param string $campaignId - Parent campaign ID
     * @param string $name - Ad Set name
     * @param float $dailyBudget - Daily budget in cents (e.g., 1000 = $10.00)
     * @param string $optimizationGoal - Optimization goal (e.g., 'LINK_CLICKS')
     * @param string $billingEvent - Billing event (e.g., 'IMPRESSIONS')
     * @param array $targeting - Targeting parameters
     * @param array $promotedObject - Promoted object (page_id, etc.)
     * @return array - Ad Set data with 'id'
     */
    public static function createAdSet($campaignId, $name, $dailyBudget, $optimizationGoal = 'LINK_CLICKS', $billingEvent = 'IMPRESSIONS', $targeting = [], $promotedObject = [])
    {
        $adAccountId = env('META_AD_ACCOUNT_ID');
        $pageId = env('META_PAGE_ID');

        if (empty($adAccountId) || empty($pageId)) {
            throw new \Exception('META_AD_ACCOUNT_ID or META_PAGE_ID is not configured');
        }

        $endpoint = "/act_{$adAccountId}/adsets";

        // Convert daily budget to cents (Meta requires integer in cents)
        $dailyBudgetCents = intval($dailyBudget * 100);

        // Default targeting if not provided
        if (empty($targeting)) {
            $targeting = [
                'geo_locations' => [
                    'countries' => ['US'], // Default to US, change as needed
                ],
                'age_min' => 18,
                'age_max' => 65,
            ];
        }

        // Default promoted object
        if (empty($promotedObject)) {
            $promotedObject = [
                'page_id' => $pageId,
            ];
        }

        $params = [
            'name' => $name,
            'campaign_id' => $campaignId,
            'daily_budget' => $dailyBudgetCents,
            'optimization_goal' => $optimizationGoal,
            'billing_event' => $billingEvent,
            'bid_strategy' => 'LOWEST_COST_WITHOUT_CAP',
            'status' => 'PAUSED',
            'targeting' => json_encode($targeting),
            'promoted_object' => json_encode($promotedObject),
        ];

        return self::makeRequest($endpoint, $params);
    }

    /**
     * Create an Ad Creative
     * 
     * @param string $name - Creative name
     * @param string $message - Ad copy/message
     * @param string $linkUrl - Destination URL
     * @param string $imageHash - Image hash (optional, can be uploaded separately)
     * @param string $callToAction - CTA type (e.g., 'LEARN_MORE', 'DONATE_NOW')
     * @return array - Creative data with 'id'
     */
    public static function createAdCreative($name, $message, $linkUrl, $imageHash = null, $callToAction = 'LEARN_MORE')
    {
        $adAccountId = env('META_AD_ACCOUNT_ID');
        $pageId = env('META_PAGE_ID');

        if (empty($adAccountId) || empty($pageId)) {
            throw new \Exception('META_AD_ACCOUNT_ID or META_PAGE_ID is not configured');
        }

        $endpoint = "/act_{$adAccountId}/adcreatives";

        // Build object story spec
        $objectStorySpec = [
            'page_id' => $pageId,
            'link_data' => [
                'message' => $message,
                'link' => $linkUrl,
                'call_to_action' => [
                    'type' => $callToAction,
                    'value' => [
                        'link' => $linkUrl,
                    ],
                ],
            ],
        ];

        // Add image if provided
        if ($imageHash) {
            $objectStorySpec['link_data']['image_hash'] = $imageHash;
        }

        $params = [
            'name' => $name,
            'object_story_spec' => json_encode($objectStorySpec),
        ];

        return self::makeRequest($endpoint, $params);
    }

    /**
     * Create an Ad
     * 
     * @param string $name - Ad name
     * @param string $adsetId - Parent ad set ID
     * @param string $creativeId - Creative ID
     * @param string $status - Ad status ('PAUSED' or 'ACTIVE')
     * @return array - Ad data with 'id'
     */
    public static function createAd($name, $adsetId, $creativeId, $status = 'ACTIVE')
    {
        $adAccountId = env('META_AD_ACCOUNT_ID');

        if (empty($adAccountId)) {
            throw new \Exception('META_AD_ACCOUNT_ID is not configured');
        }

        $endpoint = "/act_{$adAccountId}/ads";

        $params = [
            'name' => $name,
            'adset_id' => $adsetId,
            'creative' => json_encode(['creative_id' => $creativeId]),
            'status' => $status,
        ];

        return self::makeRequest($endpoint, $params);
    }

    /**
     * Update Ad/AdSet/Campaign status
     * 
     * @param string $objectId - Object ID (campaign, adset, or ad ID)
     * @param string $status - New status ('ACTIVE', 'PAUSED', 'DELETED')
     * @return array - Updated object data
     */
    public static function updateStatus($objectId, $status)
    {
        $endpoint = "/{$objectId}";
        
        $params = [
            'status' => $status,
        ];

        return self::makeRequest($endpoint, $params);
    }
}
