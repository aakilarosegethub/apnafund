<?php

namespace App\Http\Controllers;

use App\Models\Campaign;

class RewardController extends Controller
{
    /**
     * Display rewards for a specific campaign (public view).
     */
    public function show($campaignSlug)
    {
        $campaign = Campaign::where('slug', $campaignSlug)
            ->approve()
            ->firstOrFail();

        $rewards = $campaign->rewards()->active()->orderBy('minimum_amount')->get();

        $pageTitle = 'Campaign Rewards';

        return view($this->activeTheme.'page.rewards', compact('pageTitle', 'campaign', 'rewards'));
    }
}
