<?php

namespace App\Http\Controllers\User;

use App\Models\Reward;
use App\Models\Campaign;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class RewardController extends Controller
{
    /**
     * Display rewards for a specific campaign.
     */
    public function index($slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $rewards = $campaign->rewards()->active()->orderBy('minimum_amount')->get();

        $pageTitle = 'Campaign Rewards';
        
        return view($this->activeTheme . 'user.reward.index', compact('pageTitle', 'campaign', 'rewards'));
    }

    /**
     * Show the form for creating a new reward.
     */
    public function create($slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $pageTitle = 'Add New Reward';
        
        return view($this->activeTheme . 'user.reward.create', compact('pageTitle', 'campaign'));
    }

    /**
     * Store a newly created reward.
     */
    public function store(Request $request, $slug)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'minimum_amount' => 'required|numeric|min:1',
                'quantity' => 'nullable|integer|min:1',
                'type' => 'required|in:digital,physical',
                'color_theme' => 'required|string',
                'terms_conditions' => 'nullable|string',
                'image' => ['nullable', File::types(['png', 'jpg', 'jpeg'])->max(2048)],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        }

        $reward = new Reward();
        $reward->campaign_id = $campaign->id;
        $reward->title = $request->title;
        $reward->description = $request->description;
        $reward->minimum_amount = $request->minimum_amount;
        $reward->quantity = $request->quantity;
        $reward->type = $request->type;
        $reward->color_theme = $request->color_theme;
        $reward->terms_conditions = $request->terms_conditions;

        if ($request->hasFile('image')) {
            $reward->image = fileUploader($request->image, getFilePath('reward'));
        }

        $reward->save();

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reward created successfully',
                'reward' => $reward
            ]);
        }

        $toast[] = ['success', 'Reward created successfully'];
        return redirect()->route('user.rewards.index', $campaign->slug)->withToasts($toast);
    }

    /**
     * Show the form for editing a reward.
     */
    public function edit($slug, $rewardId)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $reward = $campaign->rewards()->findOrFail($rewardId);

        $pageTitle = 'Edit Reward';
        
        return view($this->activeTheme . 'user.reward.edit', compact('pageTitle', 'campaign', 'reward'));
    }

    /**
     * Update the specified reward.
     */
    public function update(Request $request, $slug, $rewardId)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $reward = $campaign->rewards()->findOrFail($rewardId);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'minimum_amount' => 'required|numeric|min:1',
            'quantity' => 'nullable|integer|min:1',
            'type' => 'required|in:digital,physical',
            'color_theme' => 'required|string',
            'terms_conditions' => 'nullable|string',
            'image' => ['nullable', File::types(['png', 'jpg', 'jpeg'])->max(2048)],
        ]);

        $reward->title = $request->title;
        $reward->description = $request->description;
        $reward->minimum_amount = $request->minimum_amount;
        $reward->quantity = $request->quantity;
        $reward->type = $request->type;
        $reward->color_theme = $request->color_theme;
        $reward->terms_conditions = $request->terms_conditions;

        if ($request->hasFile('image')) {
            $reward->image = fileUploader($request->image, getFilePath('reward'), null, $reward->image);
        }

        $reward->save();

        $toast[] = ['success', 'Reward updated successfully'];
        return redirect()->route('user.rewards.index', $campaign->slug)->withToasts($toast);
    }

    /**
     * Remove the specified reward.
     */
    public function destroy($slug, $rewardId)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $reward = $campaign->rewards()->findOrFail($rewardId);
        $reward->delete();

        $toast[] = ['success', 'Reward deleted successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Toggle reward status (active/inactive).
     */
    public function toggleStatus($slug, $rewardId)
    {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $reward = $campaign->rewards()->findOrFail($rewardId);
        $reward->is_active = !$reward->is_active;
        $reward->save();

        $status = $reward->is_active ? 'activated' : 'deactivated';
        $toast[] = ['success', "Reward {$status} successfully"];
        return back()->withToasts($toast);
    }
} 