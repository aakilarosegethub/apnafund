<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use App\Models\Campaign;
use App\Http\Controllers\Controller;

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
        
        return view($this->activeTheme . 'page.rewards', compact('pageTitle', 'campaign', 'rewards'));
    }
} 