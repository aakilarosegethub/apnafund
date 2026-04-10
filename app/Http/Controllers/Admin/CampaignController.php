<?php

namespace App\Http\Controllers\Admin;

use App\Models\Comment;
use App\Models\Campaign;
use App\Models\Category;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;
use Intervention\Image\Facades\Image as InterventionImage;

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
        $campaign   = Campaign::with('payoutBank')->findOrFail($id);
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
        if (!admin_can('campaigns.approve')) {
            $toast[] = ['error', 'You do not have permission to approve or reject campaigns.'];
            return back()->withToasts($toast);
        }
        $campaign = Campaign::findOrFail($id);

        $newStatus = null;
        if ($type == 'approve') {
            $newStatus = ManageStatus::CAMPAIGN_APPROVED;
            $template = 'CAMPAIGN_APPROVE';
            $toastMsg = 'Campaign approval success';
        } elseif ($type == 'unapprove') {
            $newStatus = ManageStatus::CAMPAIGN_PENDING;
            $template = null;
            $toastMsg = 'Campaign unapproved successfully. Status changed to pending.';
        } else {
            $newStatus = ManageStatus::CAMPAIGN_REJECTED;
            $template = 'CAMPAIGN_REJECT';
            $toastMsg = 'Campaign rejection success';
        }
        // Update only status column via raw DB - bypass Eloquent entirely so nothing touches start_date/end_date
        \DB::table('campaigns')->where('id', $id)->update(['status' => $newStatus, 'start_date' => $campaign->start_date]);
        if ($template) {
            notify($campaign->user, $template, [
                'campaign_name' => $campaign->name,
            ]);
        }

        $typeNorm = strtolower((string) $type);
        if ($typeNorm === 'approve' && $campaign->user_id) {
            try {
                \App\Models\UserNotification::notifyCampaignApproved((int) $campaign->user_id, $campaign);
            } catch (\Throwable $e) {
                \Log::warning('Campaign approved UserNotification failed', ['error' => $e->getMessage()]);
            }
        }
        if ($typeNorm === 'reject' && $campaign->user_id) {
            try {
                \App\Models\UserNotification::notifyCampaignRejected((int) $campaign->user_id, $campaign);
            } catch (\Throwable $e) {
                \Log::warning('Campaign rejected UserNotification failed', ['error' => $e->getMessage()]);
            }
        }

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

    /**
     * Upload campaign image immediately on selection (admin)
     */
    function uploadCampaignImage() {
        try {
            $validator = \Validator::make(request()->all(), [
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:5120', // 5MB max
            ], [
                'image.max' => 'Campaign image must be under 5 MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $imageFile = request('image');
            // Store original image (no resize) for detail page
            $originalFilename = fileUploader($imageFile, getFilePath('campaignOriginal'));

            // Enforce WebP output
            $extension = 'webp';
            $quality = 90;

            // Generate new filename
            $filename = uniqid() . time() . '.' . $extension;
            $path = public_path(getFilePath('campaign'));

            // Create directory if doesn't exist
            if (!file_exists($path)) {
                mkdir($path, 0775, true);
            }

            // Resize and convert to WebP
            $imageSize = getFileSize('campaign');
            $sizeParts = $imageSize ? explode('x', strtolower($imageSize)) : [855, 475];
            $targetWidth = (int) ($sizeParts[0] ?? 855);
            $targetHeight = (int) ($sizeParts[1] ?? 475);
            saveUploadedImageAsWebp($imageFile, $path . '/' . $filename, $quality, $targetWidth, $targetHeight);

            // Create thumbnail
            $thumbSize = getThumbSize('campaign');
            if ($thumbSize) {
                $thumbDimensions = explode('x', $thumbSize);
                saveUploadedImageAsWebp($imageFile, $path . '/thumb_' . $filename, $quality, $thumbDimensions[0], $thumbDimensions[1]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'image' => $filename,
                'image_url' => asset(getFilePath('campaign') . '/' . $filename),
                'image_original' => $originalFilename,
                'image_original_url' => asset(getFilePath('campaignOriginal') . '/' . $originalFilename)
            ]);
        } catch (\Exception $e) {
            \Log::error('Admin campaign image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
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
            'image'         => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])->max(5120)],
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

            // Update basic fields (explicit Carbon parse for dates to avoid locale/format issues)
            $campaign->category_id = $request->category_id;
            $campaign->name = $request->name;
            $campaign->slug = Str::slug($request->name);
            $campaign->description = $request->description;
            $campaign->goal_amount = $request->goal_amount;
            $campaign->start_date = \Carbon\Carbon::parse($request->start_date)->format('Y-m-d');
            $campaign->end_date = \Carbon\Carbon::parse($request->end_date)->format('Y-m-d');
            $campaign->location = $request->location;
            $campaign->youtube_url = $request->youtube_url;

            // Handle image upload with resize and WebP (required)
            if ($request->filled('uploaded_image')) {
                $uploadedImageName = $request->input('uploaded_image');
                $uploadedOriginalName = $request->input('uploaded_image_original');
                $imagePath = public_path(getFilePath('campaign') . '/' . $uploadedImageName);

                if (file_exists($imagePath)) {
                    if ($campaign->image && $campaign->image !== $uploadedImageName) {
                        $oldImagePath = public_path(getFilePath('campaign') . '/' . $campaign->image);
                        if (file_exists($oldImagePath)) {
                            @unlink($oldImagePath);
                        }
                        $oldThumbPath = public_path(getFilePath('campaign') . '/thumb_' . $campaign->image);
                        if (file_exists($oldThumbPath)) {
                            @unlink($oldThumbPath);
                        }
                    }
                    $campaign->image = $uploadedImageName;
                    if ($uploadedOriginalName) {
                        if ($campaign->image_original && $campaign->image_original !== $uploadedOriginalName) {
                            $oldOriginalPath = public_path(getFilePath('campaignOriginal') . '/' . $campaign->image_original);
                            if (file_exists($oldOriginalPath)) {
                                @unlink($oldOriginalPath);
                            }
                        }
                        $campaign->image_original = $uploadedOriginalName;
                    }
                }
            } elseif ($request->hasFile('image')) {
                $imageFile = $request->file('image');
                $oldImage = $campaign->image;

                // Remove old image if exists
                if ($oldImage) {
                    $oldImagePath = public_path(getFilePath('campaign') . '/' . $oldImage);
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                    $oldThumbPath = public_path(getFilePath('campaign') . '/thumb_' . $oldImage);
                    if (file_exists($oldThumbPath)) {
                        @unlink($oldThumbPath);
                    }
                }

                $extension = 'webp';
                $quality = 90;

                $filename = uniqid() . time() . '.' . $extension;
                $path = public_path(getFilePath('campaign'));
                if (!file_exists($path)) {
                    mkdir($path, 0775, true);
                }

                // Store original image (no resize) for detail page
                $campaign->image_original = fileUploader($imageFile, getFilePath('campaignOriginal'), null, $campaign->image_original);

                $imageSize = getFileSize('campaign');
                $sizeParts = $imageSize ? explode('x', strtolower($imageSize)) : [855, 475];
                $targetWidth = (int) ($sizeParts[0] ?? 855);
                $targetHeight = (int) ($sizeParts[1] ?? 475);
                saveUploadedImageAsWebp($imageFile, $path . '/' . $filename, $quality, $targetWidth, $targetHeight);

                $thumbSize = getThumbSize('campaign');
                if ($thumbSize) {
                    $thumbDimensions = explode('x', $thumbSize);
                    saveUploadedImageAsWebp($imageFile, $path . '/thumb_' . $filename, $quality, $thumbDimensions[0], $thumbDimensions[1]);
                }

                $campaign->image = $filename;
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
            $toast[] = ['error', 'Failed to update campaign: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    function fixAllImages() {
        try {
            $campaigns = Campaign::whereNotNull('image')->get();
            $fixed = 0;
            $failed = 0;
            
            foreach ($campaigns as $campaign) {
                try {
                    $imageName = basename($campaign->image);
                    $imagePath = public_path(getFilePath('campaign') . '/' . $imageName);
                    
                    // Check if image file exists
                    if (!file_exists($imagePath)) {
                        $failed++;
                        continue;
                    }
                    
                        $image = InterventionImage::make($imagePath);
                        if ($image->width() == 1024 && $image->height() == 576) {
                        continue; // Already correct size, skip
                    }
                    
                    $extension = 'webp';
                    $quality = 90;
                    
                    // Generate new filename
                    $newFilename = uniqid() . time() . '.' . $extension;
                    $path = public_path(getFilePath('campaign'));
                    
                    // Force resize to 1024x576 while keeping aspect ratio (crop if needed)
                    $image->fit(1024, 576);
                    
                    // Save as WebP (required)
                    saveImageAsWebp($image, $path . '/' . $newFilename, $quality);
                    
                    // Create thumbnail
                    $thumbSize = getThumbSize('campaign');
                    if ($thumbSize) {
                        $thumbDimensions = explode('x', $thumbSize);
                        $thumbImage = InterventionImage::make($imagePath);
                        $thumbImage->fit((int) $thumbDimensions[0], (int) $thumbDimensions[1]);
                        
                        saveImageAsWebp($thumbImage, $path . '/thumb_' . $newFilename, $quality);
                    }
                    
                    // Delete old image
                    if (file_exists($imagePath)) {
                        @unlink($imagePath);
                    }
                    
                    // Delete old thumbnail if exists
                    $oldThumbPath = public_path(getFilePath('campaign') . '/thumb_' . $imageName);
                    if (file_exists($oldThumbPath)) {
                        @unlink($oldThumbPath);
                    }
                    
                    // Update campaign with new image filename
                    $campaign->image = $newFilename;
                    $campaign->save();
                    
                    $fixed++;
                } catch (\Exception $e) {
                    \Log::error('Failed to fix image for campaign ' . $campaign->id . ': ' . $e->getMessage());
                    $failed++;
                }
            }
            
            return response()->json([
                'success' => true,
                'fixed' => $fixed,
                'failed' => $failed,
                'message' => "Fixed {$fixed} campaign images. {$failed} failed."
            ]);
        } catch (\Exception $e) {
            \Log::error('Fix all images error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fixing images: ' . $e->getMessage()
            ], 500);
        }
    }
}
