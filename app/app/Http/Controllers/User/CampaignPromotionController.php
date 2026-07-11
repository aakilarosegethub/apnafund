<?php

namespace App\Http\Controllers\User;

use App\Helpers\MetaApiHelper;
use App\Http\Controllers\Controller;
use App\Models\Campaign;
use App\Models\CampaignPromotion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignPromotionController extends Controller
{
    /**
     * Promote/Boost a campaign using Meta (Facebook) Marketing API
     *
     * This method creates a complete Meta advertising campaign including:
     * - Meta Campaign (with OUTCOME_TRAFFIC objective)
     * - Ad Set (with targeting and budget)
     * - Ad Creative (with campaign link)
     * - Ad (set to ACTIVE status)
     *
     * @param  int  $campaignId
     * @return \Illuminate\Http\JsonResponse
     */
    public function promoteCampaign(Request $request, $campaignId)
    {
        try {
            // Validate request
            $request->validate([
                'daily_budget' => 'nullable|numeric|min:1|max:10000',
                'target_countries' => 'nullable|array',
                'target_countries.*' => 'string|size:2', // ISO country codes
            ]);

            // Find campaign and check ownership
            $campaign = Campaign::findOrFail($campaignId);

            // Check if user owns this campaign or is a collaborator
            $userId = auth()->id();
            $isOwner = $campaign->user_id == $userId;
            $isCollaborator = \App\Models\CampaignCollaborator::where('campaign_id', $campaignId)
                ->where('user_id', $userId)
                ->exists();

            if (! $isOwner && ! $isCollaborator) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to promote this campaign.',
                ], 403);
            }

            // Check if campaign is approved
            if ($campaign->approval != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only approved campaigns can be promoted.',
                ], 400);
            }

            // Check if already promoted
            $existingPromotion = CampaignPromotion::where('campaign_id', $campaignId)
                ->where('status', 'active')
                ->first();

            if ($existingPromotion) {
                return response()->json([
                    'success' => false,
                    'message' => 'This campaign is already being promoted.',
                    'data' => [
                        'meta_campaign_id' => $existingPromotion->meta_campaign_id,
                        'meta_ad_id' => $existingPromotion->meta_ad_id,
                    ],
                ], 400);
            }

            // Get parameters
            $dailyBudget = $request->input('daily_budget', 10.00); // Default $10/day
            $targetCountries = $request->input('target_countries', ['US']); // Default US

            // Build campaign URL
            $campaignUrl = route('campaigns.details', ['slug' => $campaign->slug, 'id' => $campaign->id]);

            // Start database transaction
            DB::beginTransaction();

            try {
                // Create promotion record
                $promotion = new CampaignPromotion;
                $promotion->campaign_id = $campaignId;
                $promotion->daily_budget = $dailyBudget;
                $promotion->status = 'pending';
                $promotion->save();

                // STEP 1: Create Meta Campaign
                $metaCampaignName = 'Boost: '.substr($campaign->name, 0, 100);
                $metaCampaignResponse = MetaApiHelper::createCampaign(
                    $metaCampaignName,
                    'OUTCOME_TRAFFIC', // Objective: Drive traffic to website
                    'PAUSED' // Start paused, will activate with ad
                );

                $metaCampaignId = $metaCampaignResponse['id'] ?? null;
                if (! $metaCampaignId) {
                    throw new \Exception('Failed to create Meta campaign');
                }

                $promotion->meta_campaign_id = $metaCampaignId;
                $promotion->save();

                // STEP 2: Create Ad Set
                $adSetName = 'AdSet: '.substr($campaign->name, 0, 100);

                // Build targeting
                $targeting = [
                    'geo_locations' => [
                        'countries' => $targetCountries,
                    ],
                    'age_min' => 18,
                    'age_max' => 65,
                ];

                // Build promoted object with page_id
                $pageId = env('META_PAGE_ID');
                $promotedObject = [
                    'page_id' => $pageId,
                ];

                $adSetResponse = MetaApiHelper::createAdSet(
                    $metaCampaignId,
                    $adSetName,
                    $dailyBudget,
                    'LINK_CLICKS', // Optimize for link clicks
                    'IMPRESSIONS', // Charge per impression
                    $targeting,
                    $promotedObject
                );

                $metaAdSetId = $adSetResponse['id'] ?? null;
                if (! $metaAdSetId) {
                    throw new \Exception('Failed to create Meta ad set');
                }

                $promotion->meta_adset_id = $metaAdSetId;
                $promotion->save();

                // STEP 3: Create Ad Creative
                $creativeName = 'Creative: '.substr($campaign->name, 0, 100);
                $adMessage = $this->generateAdMessage($campaign);

                $creativeResponse = MetaApiHelper::createAdCreative(
                    $creativeName,
                    $adMessage,
                    $campaignUrl,
                    null, // Image hash - can be added later
                    'LEARN_MORE' // Call to action
                );

                $metaCreativeId = $creativeResponse['id'] ?? null;
                if (! $metaCreativeId) {
                    throw new \Exception('Failed to create Meta ad creative');
                }

                $promotion->meta_creative_id = $metaCreativeId;
                $promotion->save();

                // STEP 4: Create Ad and set to ACTIVE
                $adName = 'Ad: '.substr($campaign->name, 0, 100);
                $adResponse = MetaApiHelper::createAd(
                    $adName,
                    $metaAdSetId,
                    $metaCreativeId,
                    'ACTIVE' // Activate immediately
                );

                $metaAdId = $adResponse['id'] ?? null;
                if (! $metaAdId) {
                    throw new \Exception('Failed to create Meta ad');
                }

                // Update promotion record with success
                $promotion->meta_ad_id = $metaAdId;
                $promotion->status = 'active';
                $promotion->promoted_at = now();
                $promotion->save();

                // Commit transaction
                DB::commit();

                // Log success
                Log::info('Campaign promoted successfully', [
                    'campaign_id' => $campaignId,
                    'meta_campaign_id' => $metaCampaignId,
                    'meta_ad_id' => $metaAdId,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Campaign promoted successfully! Your ad is now active on Facebook.',
                    'data' => [
                        'promotion_id' => $promotion->id,
                        'meta_campaign_id' => $metaCampaignId,
                        'meta_adset_id' => $metaAdSetId,
                        'meta_creative_id' => $metaCreativeId,
                        'meta_ad_id' => $metaAdId,
                        'daily_budget' => $dailyBudget,
                        'status' => 'active',
                    ],
                ]);

            } catch (\Exception $e) {
                // Rollback transaction
                DB::rollBack();

                // Update promotion record with error
                if (isset($promotion) && $promotion->exists) {
                    $promotion->status = 'error';
                    $promotion->error_message = $e->getMessage();
                    $promotion->save();
                }

                // Log error
                Log::error('Campaign promotion failed', [
                    'campaign_id' => $campaignId,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                throw $e; // Re-throw to outer catch
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Campaign not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to promote campaign: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Pause/Stop campaign promotion
     *
     * @param  int  $campaignId
     * @return \Illuminate\Http\JsonResponse
     */
    public function pausePromotion($campaignId)
    {
        try {
            $promotion = CampaignPromotion::where('campaign_id', $campaignId)
                ->where('status', 'active')
                ->firstOrFail();

            // Pause the Meta ad
            if ($promotion->meta_ad_id) {
                MetaApiHelper::updateStatus($promotion->meta_ad_id, 'PAUSED');
            }

            // Update local record
            $promotion->status = 'paused';
            $promotion->save();

            return response()->json([
                'success' => true,
                'message' => 'Campaign promotion paused successfully.',
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to pause promotion', [
                'campaign_id' => $campaignId,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to pause promotion: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get promotion status for a campaign
     *
     * @param  int  $campaignId
     * @return \Illuminate\Http\JsonResponse
     */
    public function getPromotionStatus($campaignId)
    {
        try {
            $promotion = CampaignPromotion::where('campaign_id', $campaignId)
                ->latest()
                ->first();

            if (! $promotion) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'is_promoted' => false,
                        'status' => null,
                    ],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'is_promoted' => $promotion->status === 'active',
                    'status' => $promotion->status,
                    'daily_budget' => $promotion->daily_budget,
                    'promoted_at' => $promotion->promoted_at,
                    'meta_campaign_id' => $promotion->meta_campaign_id,
                    'meta_ad_id' => $promotion->meta_ad_id,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get promotion status: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate ad message/copy for the campaign
     *
     * @param  Campaign  $campaign
     * @return string
     */
    private function generateAdMessage($campaign)
    {
        $message = '🎯 Support this amazing project: '.$campaign->name."\n\n";

        if (! empty($campaign->description)) {
            $description = strip_tags($campaign->description);
            $description = substr($description, 0, 200);
            $message .= $description."...\n\n";
        }

        $message .= '💚 Click to learn more and support this campaign!';

        return $message;
    }
}
