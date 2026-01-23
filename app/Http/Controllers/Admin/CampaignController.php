<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\Campaign;
use App\Models\Category;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class CampaignController extends Controller
{
    function index() {
        $pageTitle = 'All Campaigns';
        $campaigns = $this->campaignData();

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function pending() {
        $pageTitle = 'Pending Campaigns';
        $campaigns = $this->campaignData('pending');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function approved() {
        $pageTitle = 'Approved Campaigns';
        $campaigns = $this->campaignData('approve');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function rejected() {
        $pageTitle = 'Rejected Campaigns';
        $campaigns = $this->campaignData('reject');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function running() {
        $pageTitle = 'Running Campaigns';
        $campaigns = $this->campaignData('running');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function expired() {
        $pageTitle = 'Expired Campaigns';
        $campaigns = $this->campaignData('expired');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function upcoming() {
        $pageTitle = 'Upcoming Campaigns';
        $campaigns = $this->campaignData('upcoming');

        return view('admin.campaign.index', compact('pageTitle', 'campaigns'));
    }

    protected function campaignData($scope = null) {
        if ($scope) $campaigns = Campaign::$scope();
        else $campaigns = Campaign::query();

        return $campaigns->with(['user', 'category'])
            ->searchable(['name', 'category:name', 'user:username'])
            ->latest()
            ->paginate(getPaginate());
    }

    function details($id) {
        $pageTitle  = 'Campaign Details';
        $backRoute  = route('admin.campaigns.index');
        $campaign   = Campaign::findOrFail($id);
        $totalDonor = $campaign->deposits()->done()->count();
        $comments   = $campaign->comments()->with('user')->paginate(getPaginate());
        
        // Get reward claims (deposits with rewards) - only if reward_id column exists
        $rewardClaims = collect([]);
        try {
            // Check if reward_id column exists in deposits table
            $columns = Schema::getColumnListing('deposits');
            if (in_array('reward_id', $columns)) {
                $rewardClaims = $campaign->deposits()
                    ->whereNotNull('reward_id')
                    ->where('status', ManageStatus::PAYMENT_SUCCESS)
                    ->with(['reward', 'user'])
                    ->latest()
                    ->paginate(getPaginate());
            }
        } catch (\Exception $e) {
            // If query fails, return empty collection
            $rewardClaims = collect([]);
        }

        return view('admin.campaign.details', compact('pageTitle', 'backRoute', 'campaign', 'comments', 'totalDonor', 'rewardClaims'));
    }

    function updateStatus($id, $type) {
        $campaign         = Campaign::findOrFail($id);
        $campaign->status = ($type == 'approve') ? ManageStatus::CAMPAIGN_APPROVED : ManageStatus::CAMPAIGN_REJECTED;
        $campaign->save();

        $template = ($type == 'approve') ? 'CAMPAIGN_APPROVE' : 'CAMPAIGN_REJECT';

        notify($campaign->user, $template, [
            'campaign_name' => $campaign->name,
        ]);

        $toastMsg = ($type == 'approve') ? 'Campaign approval success' : 'Campaign rejection success';

        $toast[] = ['success', $toastMsg];

        return back()->withToasts($toast);
    }

    function updateFeatured($id) {
        return Campaign::changeStatus($id, 'featured');
    }

    function edit($id) {
        $pageTitle = 'Edit Campaign';
        $campaign = Campaign::findOrFail($id);
        $categories = Category::active()->get();
        
        return view('admin.campaign.edit', compact('pageTitle', 'campaign', 'categories'));
    }

    function update(Request $request, $id) {
        $campaign = Campaign::findOrFail($id);
        
        $request->validate([
            'category_id'   => 'required|integer|exists:categories,id',
            'name'          => 'required|string|max:190|unique:campaigns,name,' . $id,
            'description'   => 'required|string|min:30',
            'goal_amount'   => 'required|numeric|gt:0',
            'start_date'    => 'required|date_format:Y-m-d',
            'end_date'      => 'required|date_format:Y-m-d|after:start_date',
            'location'      => 'nullable|string|max:255',
            'youtube_url'   => 'nullable|url',
            'image'         => ['nullable', File::types(['png', 'jpg', 'jpeg'])],
            'video'         => ['nullable', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'],
        ], [
            'category_id.required' => 'The category field is required.',
            'category_id.exists'   => 'The selected category is invalid.',
            'name.required'        => 'The campaign name is required.',
            'name.unique'          => 'This campaign name already exists.',
            'description.required' => 'The description is required.',
            'description.min'      => 'The description must be at least 30 characters.',
            'goal_amount.required' => 'The goal amount is required.',
            'goal_amount.gt'       => 'The goal amount must be greater than 0.',
            'start_date.required'  => 'The start date is required.',
            'end_date.required'    => 'The end date is required.',
            'end_date.after'       => 'The end date must be after the start date.',
            'youtube_url.url'      => 'Please enter a valid YouTube URL.',
            'video.max'            => 'Video file size must be less than 500MB.',
        ]);

        // Custom YouTube URL validation
        if ($request->youtube_url) {
            $youtubeUrl = $request->youtube_url;
            if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i', $youtubeUrl)) {
                $toast[] = ['error', 'Please enter a valid YouTube URL'];
                return back()->withToasts($toast);
            }
        }

        try {
            // Update category
            $category = Category::where('id', $request->category_id)->active()->first();
            if (!$category) {
                $toast[] = ['error', 'Selected category is not active'];
                return back()->withToasts($toast);
            }

            // Update basic fields
            $campaign->category_id = $request->category_id;
            $campaign->name = $request->name;
            $campaign->slug = Str::slug($request->name);
            $campaign->description = $request->description;
            $campaign->goal_amount = $request->goal_amount;
            $campaign->start_date = $request->start_date;
            $campaign->end_date = $request->end_date;
            $campaign->location = $request->location;
            $campaign->youtube_url = $request->youtube_url;

            // Handle image upload
            if ($request->hasFile('image')) {
                $old = $campaign->image;
                $campaign->image = fileUploader($request->file('image'), getFilePath('campaign'), getFileSize('campaign'), $old);
            }

            // Handle video upload
            if ($request->hasFile('video')) {
                $old = $campaign->video;
                $campaign->video = fileUploader($request->file('video'), getFilePath('campaignVideo'), getFileSize('campaignVideo'), $old);
            }

            $campaign->save();

            $toast[] = ['success', 'Campaign updated successfully'];
            return back()->withToasts($toast);
        } catch (\Exception $e) {
            \Log::error('Campaign update failed: ' . $e->getMessage());
            $toast[] = ['error', 'Failed to update campaign. Please try again.'];
            return back()->withToasts($toast);
        }
    }
}
