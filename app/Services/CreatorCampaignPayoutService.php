<?php

namespace App\Services;

use App\Constants\ManageStatus;
use App\Models\Campaign;
use App\Models\CreatorCampaignFeeSetting;
use App\Models\CreatorCampaignPayout;
use App\Models\Deposit;

/**
 * When a campaign succeeds, creates a {@see CreatorCampaignPayout} row with platform fees and withholdings.
 */
class CreatorCampaignPayoutService
{
    public function getSettings(): CreatorCampaignFeeSetting
    {
        $settings = CreatorCampaignFeeSetting::first();

        if (!$settings) {
            $settings = CreatorCampaignFeeSetting::create([
                'platform_fee_type' => 'percentage',
                'platform_fee_value' => 0,
                'marketing_fee_percent' => 0,
                'chargeback_withholding_percent' => 5,
                'fulfillment_withholding_percent' => 30,
            ]);
        }

        return $settings;
    }

    public function ensurePayoutRecord(Campaign $campaign, ?int $adminId = null): ?CreatorCampaignPayout
    {
        if ($campaign->creatorPayout) {
            return $campaign->creatorPayout;
        }

        $totalRaised = $this->getTotalRaised($campaign);

        if (!$this->isCampaignSuccessful($campaign, $totalRaised)) {
            return null;
        }

        $settings = $this->getSettings();
        $amounts = $this->calculateAmounts($settings, $totalRaised);

        return CreatorCampaignPayout::create([
            'campaign_id' => $campaign->id,
            'success_marked_at' => now(),
            'total_raised' => $totalRaised,
            'platform_fee_type' => $settings->platform_fee_type,
            'platform_fee_value' => $settings->platform_fee_value,
            'platform_fee_amount' => $amounts['platform_fee_amount'],
            'marketing_fee_percent' => $settings->marketing_fee_percent,
            'marketing_fee_amount' => $amounts['marketing_fee_amount'],
            'chargeback_withholding_percent' => $settings->chargeback_withholding_percent,
            'chargeback_withholding_amount' => $amounts['chargeback_withholding_amount'],
            'fulfillment_withholding_percent' => $settings->fulfillment_withholding_percent,
            'fulfillment_withholding_amount' => $amounts['fulfillment_withholding_amount'],
            'net_payable_amount' => $amounts['net_payable_amount'],
            'withheld_total_amount' => $amounts['withheld_total_amount'],
            'created_by' => $adminId,
        ]);
    }

    public function getTotalRaised(Campaign $campaign): float
    {
        $total = Deposit::where('campaign_id', $campaign->id)
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->sum('amount');

        return round((float) $total, 2);
    }

    public function isCampaignSuccessful(Campaign $campaign, ?float $totalRaised = null): bool
    {
        if ($campaign->status != ManageStatus::CAMPAIGN_APPROVED) {
            return false;
        }

        $totalRaised = $totalRaised ?? $this->getTotalRaised($campaign);
        $goalAmount = (float) ($campaign->goal_amount ?? 0);

        if ($campaign->end_date && $campaign->end_date->isPast()) {
            return true;
        }

        return $goalAmount > 0 && $totalRaised >= $goalAmount;
    }

    public function calculateAmounts(CreatorCampaignFeeSetting $settings, float $totalRaised): array
    {
        $platformFeeAmount = 0.0;
        if ($settings->platform_fee_type === 'fixed') {
            $platformFeeAmount = min($totalRaised, (float) $settings->platform_fee_value);
        } else {
            $platformFeeAmount = $totalRaised * ((float) $settings->platform_fee_value / 100);
        }

        $marketingFeeAmount = $totalRaised * ((float) $settings->marketing_fee_percent / 100);
        $chargebackWithholdingAmount = $totalRaised * ((float) $settings->chargeback_withholding_percent / 100);
        $fulfillmentWithholdingAmount = $totalRaised * ((float) $settings->fulfillment_withholding_percent / 100);

        $platformFeeAmount = round($platformFeeAmount, 2);
        $marketingFeeAmount = round($marketingFeeAmount, 2);
        $chargebackWithholdingAmount = round($chargebackWithholdingAmount, 2);
        $fulfillmentWithholdingAmount = round($fulfillmentWithholdingAmount, 2);

        $withheldTotalAmount = round($chargebackWithholdingAmount + $fulfillmentWithholdingAmount, 2);
        $netPayableAmount = $totalRaised - $platformFeeAmount - $marketingFeeAmount - $withheldTotalAmount;
        $netPayableAmount = round(max(0, $netPayableAmount), 2);

        return [
            'platform_fee_amount' => $platformFeeAmount,
            'marketing_fee_amount' => $marketingFeeAmount,
            'chargeback_withholding_amount' => $chargebackWithholdingAmount,
            'fulfillment_withholding_amount' => $fulfillmentWithholdingAmount,
            'withheld_total_amount' => $withheldTotalAmount,
            'net_payable_amount' => $netPayableAmount,
        ];
    }
}
