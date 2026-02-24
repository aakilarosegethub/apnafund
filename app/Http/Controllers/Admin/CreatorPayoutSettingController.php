<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CreatorCampaignFeeSetting;
use App\Services\CreatorCampaignPayoutService;
use Illuminate\Http\Request;

class CreatorPayoutSettingController extends Controller
{
    public function edit(CreatorCampaignPayoutService $service)
    {
        $pageTitle = 'Creator Campaign Fee Settings';
        $settings = $service->getSettings();

        return view('admin.creator_payout.settings', compact('pageTitle', 'settings'));
    }

    public function update(Request $request, CreatorCampaignPayoutService $service)
    {
        $request->validate([
            'platform_fee_type' => 'required|in:percentage,fixed',
            'platform_fee_value' => 'required|numeric|min:0',
            'marketing_fee_percent' => 'required|numeric|min:0|max:100',
            'chargeback_withholding_percent' => 'required|numeric|min:0|max:100',
            'fulfillment_withholding_percent' => 'required|numeric|min:30|max:50',
        ]);

        $settings = $service->getSettings();
        $settings->platform_fee_type = $request->platform_fee_type;
        $settings->platform_fee_value = $request->platform_fee_value;
        $settings->marketing_fee_percent = $request->marketing_fee_percent;
        $settings->chargeback_withholding_percent = $request->chargeback_withholding_percent;
        $settings->fulfillment_withholding_percent = $request->fulfillment_withholding_percent;
        $settings->updated_by = auth()->guard('admin')->id();
        $settings->save();

        $toast[] = ['success', 'Creator payout settings updated successfully'];
        return back()->withToasts($toast);
    }
}
