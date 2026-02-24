<?php

namespace App\Http\Controllers\User;

use Exception;
use HTMLPurifier;
use Carbon\Carbon;
use App\Models\Comment;
use App\Models\Deposit;
use App\Models\Gallery;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\CampaignFaq;
use App\Models\CampaignUpdate;
use App\Models\CampaignCollaborator;
use App\Models\GatewayCurrency;
use App\Models\User;
use App\Models\AdminNotification;
use App\Models\Admin;
use App\Http\Controllers\Controller;
use App\Services\YouTubeUploadService;
use App\Constants\ManageStatus;
use Illuminate\Validation\Rules\File;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image as InterventionImage;

class CampaignController extends Controller
{
    function index() {
        $pageTitle = 'All Campaigns';
        $campaigns = $this->campaignData();

        return view($this->activeTheme . 'user.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function approved() {
        $pageTitle = 'Approved Campaigns';
        $campaigns = $this->campaignData('approve');

        return view($this->activeTheme . 'user.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function pending() {
        $pageTitle = 'Pending Campaigns';
        $campaigns = $this->campaignData('pending');

        return view($this->activeTheme . 'user.campaign.index', compact('pageTitle', 'campaigns'));
    }

    function rejected() {
        $pageTitle = 'Rejected Campaigns';
        $campaigns = $this->campaignData('reject');

        return view($this->activeTheme . 'user.campaign.index', compact('pageTitle', 'campaigns'));
    }

    protected function campaignData($scope = null) {
        if ($scope) {
            $campaigns = Campaign::$scope();
        } else {
            $campaigns = Campaign::query();
        }

        // Get campaigns where user is owner or collaborator
        $userId = auth()->id();
        $collaboratorCampaignIds = CampaignCollaborator::where('user_id', $userId)->pluck('campaign_id')->toArray();

        return $campaigns->with('category')
            ->where(function($query) use ($userId, $collaboratorCampaignIds) {
                $query->where('user_id', $userId)
                      ->orWhereIn('id', $collaboratorCampaignIds);
            })
            ->searchable(['name', 'category:name'])
            ->latest()
            ->paginate(getPaginate());
    }

    function new() {
        // Delete previously unused gallery images if exist
        $this->removePreviousGallery();

        $pageTitle  = 'Create New Campaign';
        $categories = Category::active()->get();

        return view($this->activeTheme . 'user.campaign.new', compact('pageTitle', 'categories'));
    }

    /**
     * Upload image while using dropzone
     */
    function galleryUpload() {
        $validator = Validator::make(request()->all(), [
            'file' => ['required', File::types(['png', 'jpg', 'jpeg', 'webp'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => $validator->errors(),
            ], 400);
        }

        $gallery          = new Gallery();
        $gallery->user_id = auth()->id();
        $gallery->image   = fileUploader(request('file'), getFilePath('campaign'), getFileSize('campaign'));
        $gallery->save();

        return response()->json([
            'message' => 'File successfully uploaded',
            'image'   => $gallery->image,
        ]);
    }

    /**
     * Upload campaign image immediately on selection
     */
    function uploadCampaignImage() {
        try {
            $validator = Validator::make(request()->all(), [
                'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:51200', // 50MB max
            ], [
                'image.max' => 'Campaign image must be under 50 MB.',
            ]);

            if ($validator->fails()) {
                $file = request()->file('image');
                $actualFileSizeKb = null;
                if ($file && $file->isValid()) {
                    $actualFileSizeKb = round($file->getSize() / 1024, 2);
                }

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                    'max_size_mb' => 50,
                    'actual_size_kb' => $actualFileSizeKb
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
            
            // Resize and convert to WebP (with fallback if GD can't read WebP input)
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
            \Log::error('Campaign image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Image upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Upload campaign video immediately on selection
     */
    function uploadCampaignVideo() {
        try {
            $validator = Validator::make(request()->all(), [
                'video' => ['required', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'], // 500MB max
            ], [
                'video.max' => 'Video file size must be less than 500MB.',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 400);
            }

            $videoFile = request('video');
            $filename = fileUploader($videoFile, getFilePath('campaign'));

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'video' => $filename,
                'video_url' => asset(getFilePath('campaign') . '/' . $filename),
            ]);
        } catch (\Exception $e) {
            \Log::error('Campaign video upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Video upload failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove image while using dropzone
     */
    function galleryRemove() {
        $image = request('file');

        fileManager()->removeFile(getFilePath('campaign') . '/' . $image);
        Gallery::where('image', $image)->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'File successfully removed',
        ]);
    }

    /**
     * Delete all gallery images for a campaign
     */
    function deleteAllGallery($id) {
        $campaign = Campaign::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$campaign) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Campaign not found',
            ]);
        }

        if ($campaign->isExpired()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This campaign has expired',
            ]);
        }

        $gallery = $campaign->gallery;

        if (empty($gallery)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'No gallery images found',
            ]);
        }

        // Remove all images from storage
        foreach ($gallery as $image) {
            fileManager()->removeFile(getFilePath('campaign') . '/' . $image);
        }

        // Clear gallery from database
        $campaign->gallery = [];
        $campaign->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'All gallery images deleted successfully',
        ]);
    }

    function store() {
        // Initialize toast array
        $toast = [];
        
        // Debug logging
        \Log::info('Campaign store method called', [
            'request_data' => request()->all(),
            'files' => request()->hasFile('image') ? 'Image file present' : 'No image file',
            'user_id' => auth()->id()
        ]);
        
        try {
            // Set default values if not provided (for quick campaign creation)
            $goalAmount = request('goal_amount', 1000);
            $startDate = request('start_date', date('Y-m-d'));
            $endDate = request('end_date', date('Y-m-d', strtotime('+30 days')));
            
            $this->validate(request(), [
                'category_id'         => 'required|integer|gt:0',
                'image'               => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])], // Made optional for draft creation
                'video'               => ['nullable', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'], // 500MB max
                'youtube_url'         => 'nullable|url',
                'location'            => 'nullable|string|max:255',
                'name'                => 'required|string|max:190|unique:campaigns,name',
                'short_description'   => 'required|string|max:255',
                'description'         => 'required|min:30',
                'goal_amount'         => 'nullable|numeric|gt:0',
                'start_date'          => 'nullable|date_format:Y-m-d|after_or_equal:today',
                'end_date'            => 'nullable|date_format:Y-m-d|after:start_date',
            ], [
                'category_id.required' => 'The category field is required.',
                'category_id.integer'  => 'The category must be an integer.',
                'short_description.required' => 'The short description field is required.',
                'short_description.max' => 'The short description may not be greater than 255 characters.',
                'image.max'            => 'Campaign image must be under 50 MB.',
                'video.max'           => 'Video file size must be less than 500MB.',
                'youtube_url.url'     => 'YouTube URL must be a valid URL.',
                'youtube_url.regex'   => 'Please enter a valid YouTube URL.',
            ]);
            
            // Custom YouTube URL validation
            if (request('youtube_url')) {
                $youtubeUrl = request('youtube_url');
                if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i', $youtubeUrl)) {
                    $toast[] = ['error', 'Please enter a valid YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID)'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please enter a valid YouTube URL'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            }
            
            $category = Category::where('id', request('category_id'))->active()->first();

            if (!$category) {
                $toast[] = ['error', 'Selected category not found or inactive'];
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Selected category not found or inactive'
                    ], 400);
                }
                return back()->withToasts($toast);
            }

            // Handle gallery images - check both approaches
            $gallery = [];
            
            // Approach 1: Check if images were uploaded via Dropzone (stored in Gallery table)
            $dropzoneImages = Gallery::where('user_id', auth()->id())->get();
            if (count($dropzoneImages) > 0) {
                foreach ($dropzoneImages as $image) {
                    array_push($gallery, $image->image);
                }
            }
            
            // Approach 2: Check if images were uploaded directly via file input
            if (request()->hasFile('gallery_images')) {
                $uploadedImages = request()->file('gallery_images');
                foreach ($uploadedImages as $image) {
                    try {
                        $uploadedImage = fileUploader($image, getFilePath('campaign'), getFileSize('campaign'));
                        array_push($gallery, $uploadedImage);
                    } catch (Exception $e) {
                        $toast[] = ['error', 'Gallery image uploading process has failed'];
                        if (request()->ajax()) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Gallery image uploading process has failed'
                            ], 400);
                        }
                        return back()->withToasts($toast);
                    }
                }
            }

            // Check if we have at least one gallery image (optional for now)
            if (empty($gallery)) {
                // Set a default gallery or make it optional
                $gallery = []; // Allow campaigns without gallery images for now
            }

            // Store campaign data
            $campaign              = new Campaign();
            $campaign->user_id     = auth()->id();
            $campaign->category_id = request('category_id');

            // Upload main image (optional - can be added later in edit)
            if (request()->hasFile('image')) {
                try {
                    $imageFile = request('image');
                    // Store original image (no resize) for detail page
                    $campaign->image_original = fileUploader($imageFile, getFilePath('campaignOriginal'));
                    // Store cropped image + thumb for cards
                    $campaign->image = fileUploader($imageFile, getFilePath('campaign'), getFileSize('campaign'), null, getThumbSize('campaign'));
                } catch (Exception) {
                    $toast[] = ['error', 'Image uploading process has failed'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Image uploading process has failed'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            } else {
                // Set a placeholder or null - can be added in edit page
                $campaign->image = null;
                $campaign->image_original = null;
            }

            // Handle video upload or YouTube URL
            if (request()->hasFile('video')) {
                try {
                    // Check if YouTube auto-upload is enabled
                    if (request('auto_upload_youtube') === '1') {
                        $youtubeService = new YouTubeUploadService();
                        
                        if ($youtubeService->isConfigured()) {
                            // Upload to YouTube
                            $videoFile = request('video');
                            $tempPath = $videoFile->getRealPath();
                            
                            $title = request('name') . ' - Campaign Video';
                            $description = 'Campaign video for: ' . request('name') . "\n\n" . request('description');
                            $tags = ['campaign', 'donation', 'fundraising', 'apnacrowdfunding'];
                            
                            $youtubeUrl = $youtubeService->uploadVideo(
                                $tempPath,
                                $title,
                                $description,
                                $tags,
                                'unlisted' // Videos are unlisted by default
                            );
                            
                            $campaign->youtube_url = $youtubeUrl;
                            $campaign->video = null; // Don't store local file if uploaded to YouTube
                            
                            $toast[] = ['success', 'Video uploaded to YouTube successfully!'];
                        } else {
                            // Fallback to local upload if YouTube not configured
                            $campaign->video = fileUploader(request('video'), getFilePath('campaign'), getFileSize('campaign'));
                            $campaign->youtube_url = null;
                            $toast[] = ['warning', 'YouTube not configured. Video saved locally.'];
                        }
                    } else {
                        // Regular local upload
                        $campaign->video = fileUploader(request('video'), getFilePath('campaign'), getFileSize('campaign'));
                        $campaign->youtube_url = null;
                    }
                } catch (Exception $e) {
                    $toast[] = ['error', 'Video uploading process has failed: ' . $e->getMessage()];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Video uploading process has failed: ' . $e->getMessage()
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            } elseif (request('youtube_url')) {
                $campaign->youtube_url = request('youtube_url');
                $campaign->video = null; // Clear video file if YouTube URL is provided
            }

            $campaign->gallery     = !empty($gallery) ? $gallery : [];
            $campaign->name        = request('name');
            $campaign->slug        = slug(request('name'));
            $campaign->location    = request('location');
            $purifier              = new HTMLPurifier();
            $campaign->description = request('description');
            $campaign->short_description = request('short_description');

            $campaign->goal_amount     = $goalAmount;
            $campaign->start_date        = Carbon::parse($startDate);
            $campaign->end_date          = Carbon::parse($endDate);
            $campaign->status           = ManageStatus::CAMPAIGN_PENDING;
            $campaign->save();
            
            // Debug logging
            \Log::info('Campaign saved successfully', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'name' => $campaign->name,
                'goal_amount' => $campaign->goal_amount
            ]);

            // Delete gallery images from Gallery table (if they were uploaded via Dropzone)
            if (count($dropzoneImages) > 0) {
                foreach ($dropzoneImages as $image) {
                    $image->delete();
                }
            }

            // Create admin notification
            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = auth()->id();
            $adminNotification->title     = 'New campaign created by ' . auth()->user()->fullname;
            $adminNotification->click_url = urlPath('admin.campaigns.index');
            $adminNotification->save();

            // Send email notification to all admins
            try {
                $admins = Admin::all();
                $user = auth()->user();
                $campaignName = $campaign->name;
                $campaignLink = route('admin.campaigns.details', $campaign->id);
                $userName = $user->fullname ?? $user->username;
                $userEmail = $user->email ?? 'N/A';
                
                foreach ($admins as $admin) {
                    $emailMessage = "Dear Admin,\n\n";
                    $emailMessage .= "A new campaign has been created and requires your review.\n\n";
                    $emailMessage .= "Campaign Details:\n";
                    $emailMessage .= "- Campaign Name: {$campaignName}\n";
                    $emailMessage .= "- Campaign ID: {$campaign->id}\n";
                    $emailMessage .= "- Created By: {$userName}\n";
                    $emailMessage .= "- Creator Email: {$userEmail}\n";
                    $emailMessage .= "- Goal Amount: " . showAmount($campaign->goal_amount) . "\n";
                    $emailMessage .= "- Start Date: " . showDateTime($campaign->start_date) . "\n";
                    $emailMessage .= "- End Date: " . showDateTime($campaign->end_date) . "\n\n";
                    $emailMessage .= "Please review and approve/reject the campaign.\n\n";
                    $emailMessage .= "View Campaign: {$campaignLink}\n\n";
                    $emailMessage .= "Thank you.";
                    
                    notify($admin, 'DEFAULT', [
                        'message' => $emailMessage,
                        'subject' => 'New Campaign Created - ' . $campaignName,
                    ], ['email']);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send admin email notification: ' . $e->getMessage());
            }

            $toast[] = ['success', 'Campaign successfully created'];

            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign successfully created',
                    'redirect' => route('user.campaign.edit', $campaign->slug)
                ]);
            }

            return to_route('user.campaign.edit', $campaign->slug)->withToasts($toast);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => request()->all()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (Exception $e) {
            \Log::error('Campaign creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => request()->all()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the campaign: ' . $e->getMessage()
                ], 500);
            }
            
            $toast = [['error', 'An error occurred while creating the campaign']];
            return back()->withToasts($toast);
        }
    }

    function edit($slug) {
        // Redirect to basics section by default
        return redirect()->route('user.campaign.edit.basics', $slug);
    }

    function editSection($slug) {
        try {
            // Delete previously unused gallery images if exist
            $this->removePreviousGallery();

            $pageTitle  = 'Edit Campaign';
            $categories = Category::active()->get();
            $campaign = Campaign::where(function($query) use ($slug) {
                                    $query->where('slug', $slug)
                                          ->orWhere('id', $slug);
                                })
                                ->first();
            
            if (!$campaign) {
                $toast[] = ['error', 'Campaign not found - Line 376'];
                return back()->withToasts($toast);
            }

            // Check if user is owner or collaborator
            if (!$campaign->canBeEditedBy(auth()->id())) {
                $toast[] = ['error', 'You do not have permission to edit this campaign'];
                return back()->withToasts($toast);
            }

            if ($campaign->isExpired()) {
                $toast[] = ['error', 'This campaign has expired'];
                return back()->withToasts($toast);
            }

            // Redirect to pay registration fee if enabled and not yet paid
            $setting = bs();
            if (!empty($setting->registration_fee_enabled) && ($setting->registration_fee_min ?? 0) > 0) {
                $hasPaid = Deposit::where('campaign_id', $campaign->id)
                    ->where('deposit_type', 'registration_fee')
                    ->where('status', ManageStatus::PAYMENT_SUCCESS)
                    ->exists();
                if (!$hasPaid && request()->route()->getName() !== 'user.campaign.pay.registration.fee') {
                    return redirect()->route('user.campaign.pay.registration.fee', $campaign->slug);
                }
            }

            // Get section from route name
            $routeName = request()->route()->getName();
            if (strpos($routeName, 'basics') !== false) {
                $section = 'basics';
            } elseif (strpos($routeName, 'reward') !== false) {
                $section = 'reward';
            } elseif (strpos($routeName, 'story') !== false) {
                $section = 'story';
            } elseif (strpos($routeName, 'people') !== false) {
                $section = 'people';
            } elseif (strpos($routeName, 'payment') !== false) {
                $section = 'payment';
            } elseif (strpos($routeName, 'boost') !== false) {
                $section = 'boost';
            } elseif (strpos($routeName, 'faq') !== false) {
                $section = 'faq';
            } elseif (strpos($routeName, 'updates') !== false) {
                $section = 'updates';
            } else {
                $section = 'basics';
            }

            // Load rewards if reward section
            $rewards = null;
            if ($section == 'reward') {
                $rewards = $campaign->rewards()->orderBy('minimum_amount')->get();
            }

            // Load FAQs if FAQ section
            $faqs = null;
            if ($section == 'faq') {
                $faqs = CampaignFaq::where('campaign_id', $campaign->id)->orderBy('order')->orderBy('id')->get();
            }

            // Load updates if updates section
            $updates = null;
            if ($section == 'updates') {
                $updates = $campaign->allUpdates()->get();
            }

            // Load payout banks if payment section
            $payoutBanks = null;
            if ($section == 'payment') {
                $payoutBanks = \App\Models\PayoutBank::where('status', true)
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get();
            }

            // Load collaborators if people section
            $collaborators = null;
            if ($section == 'people') {
                $collaborators = $campaign->collaborators()->with('user')->get();
            }

            return view($this->activeTheme . 'user.campaign.edit', compact('pageTitle', 'categories', 'campaign', 'section', 'rewards', 'faqs', 'updates', 'payoutBanks', 'collaborators'));
        } catch (\Exception $e) {
            $toast[] = ['error', 'Error loading campaign: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    /**
     * Pay registration fee page (campaign creation charge)
     */
    function payRegistrationFee($slug) {
        $campaign = Campaign::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();
        $setting = bs();
        if (empty($setting->registration_fee_enabled) || ($setting->registration_fee_min ?? 0) <= 0) {
            return redirect()->route('user.campaign.edit.basics', $campaign->slug);
        }
        $hasPaid = Deposit::where('campaign_id', $campaign->id)
            ->where('deposit_type', 'registration_fee')
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->exists();
        if ($hasPaid) {
            return redirect()->route('user.campaign.edit.basics', $campaign->slug);
        }
        $feeAmount = (float) ($setting->registration_fee_min ?? 0);
        $userCountry = auth()->user()->country_name ?? '';
        $gatewayCurrencies = GatewayCurrency::whereHas('method', function ($q) use ($userCountry) {
                $q->where('status', true);
                if ($userCountry) {
                    $q->forCountry($userCountry);
                }
            })
            ->where('status', true)
            ->where('min_amount', '<=', $feeAmount)
            ->where('max_amount', '>=', $feeAmount)
            ->orderBy('method_code')
            ->get();
        if ($gatewayCurrencies->isEmpty()) {
            $gatewayCurrencies = GatewayCurrency::whereHas('method', function ($q) use ($userCountry) {
                    $q->where('status', true);
                    if ($userCountry) {
                        $q->forCountry($userCountry);
                    }
                })
                ->where('status', true)
                ->orderBy('method_code')
                ->get();
        }
        $feeAmountUsd = $feeAmount; // Platform currency - treat as base for display
        return view($this->activeTheme . 'user.campaign.pay-registration-fee', compact('campaign', 'feeAmountUsd', 'gatewayCurrencies'));
    }

    /**
     * Submit registration fee - create deposit and redirect to payment
     */
    function submitRegistrationFee($slug) {
        $campaign = Campaign::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();
        $setting = bs();
        if (empty($setting->registration_fee_enabled) || ($setting->registration_fee_min ?? 0) <= 0) {
            return redirect()->route('user.campaign.edit.basics', $campaign->slug);
        }
        $hasPaid = Deposit::where('campaign_id', $campaign->id)
            ->where('deposit_type', 'registration_fee')
            ->where('status', ManageStatus::PAYMENT_SUCCESS)
            ->exists();
        if ($hasPaid) {
            return redirect()->route('user.campaign.edit.basics', $campaign->slug);
        }
        request()->validate([
            'gateway' => 'required|exists:gateways,code',
            'currency' => 'required|string|max:10',
        ]);
        $feeAmount = (float) ($setting->registration_fee_min ?? 0);
        $userCountry = auth()->user()->country_name ?? '';
        $gatewayData = GatewayCurrency::whereHas('method', function ($q) use ($userCountry) {
                $q->where('status', true);
                if ($userCountry) {
                    $q->forCountry($userCountry);
                }
            })
            ->where('method_code', request('gateway'))
            ->where('currency', request('currency'))
            ->where('status', true)
            ->firstOrFail();
        $charge = $gatewayData->fixed_charge + (($feeAmount * $gatewayData->percent_charge) / 100);
        $payable = $feeAmount + $charge;
        $finalAmount = $payable * $gatewayData->rate;
        $deposit = new Deposit();
        $deposit->campaign_id = $campaign->id;
        $deposit->user_id = auth()->id();
        $deposit->deposit_type = 'registration_fee';
        $deposit->donor_type = ManageStatus::KNOWN_DONOR;
        $deposit->full_name = auth()->user()->fullname;
        $deposit->email = auth()->user()->email;
        $deposit->phone = auth()->user()->mobile ?? '';
        $deposit->country = auth()->user()->country_name ?? '';
        $deposit->receiver_id = 0;
        $deposit->method_code = $gatewayData->method_code;
        $deposit->amount = $feeAmount;
        $deposit->method_currency = strtoupper($gatewayData->currency);
        $deposit->charge = $charge;
        $deposit->rate = $gatewayData->rate;
        $deposit->final_amount = $finalAmount; // column name in DB
        $deposit->btc_amount = 0;
        $deposit->btc_wallet = '';
        $deposit->trx = getTrx();
        $deposit->status = ManageStatus::PAYMENT_INITIATE;
        $deposit->save();
        session()->put('Track', $deposit->trx);
        session()->put('registration_fee_campaign_slug', $campaign->slug);
        return to_route('user.deposit.confirm');
    }

    /**
     * Remove image while editing a campaign
     */
    function removeImage($id) {
        $campaign = Campaign::where('id', $id)->first();

        if (!$campaign) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Campaign not found - Line 396',
            ]);
        }

        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You do not have permission to edit this campaign',
            ], 403);
        }

        if ($campaign->isExpired()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'This campaign has expired',
            ]);
        }

        $image   = json_decode(request('image'));
        $gallery = $campaign->gallery;

        if (count($gallery) == 1) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Minimum one gallery image is required',
            ]);
        }

        $index = array_search($image, $gallery);

        if ($index === false) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Image not found',
            ]);
        }

        // Remove image from storage
        fileManager()->removeFile(getFilePath('campaign') . '/' . $image);

        // Delete image from database
        unset($gallery[$index]);
        $updatedGallery = array_values($gallery);

        $campaign->gallery = $updatedGallery;
        $campaign->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Image successfully removed',
        ]);
    }

    function update($id) {
        // Debug logging
        \Log::info('Campaign update method called', [
            'campaign_id' => $id,
            'request_data' => request()->all(),
            'files' => request()->hasFile('image') ? 'Image file present' : 'No image file',
            'user_id' => auth()->id()
        ]);
        
        try {
            // Detect which section is being updated based on request
            $section = request('section', 'basics');
            
            // Different validation rules based on section
            if ($section == 'story') {
                // Story section - only description required
                $this->validate(request(), [
                    'description' => 'required|min:30',
                ], [
                    'description.required' => 'The story description field is required.',
                    'description.min' => 'The story description must be at least 30 characters.',
                ]);
            } else {
                // Basics section - all fields required
                $this->validate(request(), [
                    'category_id'         => 'required|integer|gt:0',
                    'image'               => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])],
                    'video'               => ['nullable', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'], // 500MB max
                    'youtube_url'         => 'nullable|url',
                    'location'            => 'nullable|string|max:255',
                    'name'                => 'required|string|max:190|unique:campaigns,name,' . $id,
                    'short_description'   => 'required|string|max:255',
                    'goal_amount'         => 'required|numeric|gt:0',
                    'start_date'          => 'required|date_format:Y-m-d',
                    'end_date'            => 'required|date_format:Y-m-d|after:start_date|before_or_equal:' . Carbon::parse(request('start_date'))->addDays(30)->format('Y-m-d'),
                    'document'            => ['nullable', File::types('pdf')],
                ], [
                    'category_id.required' => 'The category field is required.',
                    'category_id.integer'  => 'The category must be an integer.',
                    'name.required' => 'The name field is required.',
                    'short_description.required' => 'The short description field is required.',
                    'short_description.max' => 'The short description may not be greater than 255 characters.',
                    'goal_amount.required' => 'The goal amount field is required.',
                    'start_date.required' => 'The start date field is required.',
                    'end_date.required' => 'The end date field is required.',
                    'end_date.before_or_equal' => 'The campaign can last maximum 30 days from start date.',
                    'image.max'            => 'Campaign image must be under 50 MB.',
                    'video.max'           => 'Video file size must be less than 500MB.',
                    'youtube_url.url'     => 'YouTube URL must be a valid URL.',
                    'youtube_url.regex'   => 'Please enter a valid YouTube URL.',
                ]);
            }

            // Custom YouTube URL validation
            if (request('youtube_url')) {
                $youtubeUrl = request('youtube_url');
                if (!preg_match('/^(https?\:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/|v\/)|youtu\.be\/)[\w\-]+/i', $youtubeUrl)) {
                    $toast[] = ['error', 'Please enter a valid YouTube URL (e.g., https://www.youtube.com/watch?v=VIDEO_ID or https://youtu.be/VIDEO_ID)'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Please enter a valid YouTube URL'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            }

            $campaign = Campaign::where('id', $id)->first();

            if (!$campaign) {
                $toast[] = ['error', 'Campaign not found - Line 495'];
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Campaign not found - Line 495'
                    ], 404);
                }
                return back()->withToasts($toast);
            }

            // Check if user is owner or collaborator
            if (!$campaign->canBeEditedBy(auth()->id())) {
                $toast[] = ['error', 'You do not have permission to edit this campaign'];
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You do not have permission to edit this campaign'
                    ], 403);
                }
                return back()->withToasts($toast);
            }

            if ($campaign->isExpired()) {
                $toast[] = ['error', 'This campaign has expired'];
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This campaign has expired'
                    ], 400);
                }
                return back()->withToasts($toast);
            }

            // Update campaign data based on section
            if ($section == 'story') {
                // Story section - only update description
                $purifier = new HTMLPurifier();
                $campaign->description = request('description');
            } else {
                // Basics section - update all fields
                $category = Category::where('id', request('category_id'))->active()->first();

                if (!$category) {
                    $toast[] = ['error', 'Selected category not found or inactive'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Selected category not found or inactive'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }

                $campaign->category_id = request('category_id');
                $campaign->name        = request('name');
                $campaign->slug        = slug(request('name'));
                $campaign->location    = request('location');
                $purifier              = new HTMLPurifier();
                $campaign->short_description = request('short_description');
                $campaign->goal_amount = request('goal_amount');
                $campaign->start_date  = Carbon::parse(request('start_date'));
                $campaign->end_date    = Carbon::parse(request('end_date'));
            }

            // Upload new main image (only for basics section)
            if ($section == 'basics' && request()->hasFile('image')) {
                try {
                    // Validate image file
                    $imageFile = request('image');
                    $maxSize = 51200; // 50MB in KB
                    
                    if ($imageFile->getSize() > $maxSize * 1024) {
                        throw new Exception('Image size must be less than 50MB');
                    }
                    
                    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($imageFile->getMimeType(), $allowedTypes)) {
                        throw new Exception('Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.');
                    }
                    
                    // Get file path and ensure directory exists
                    $filePath = getFilePath('campaign');
                    $fullPath = public_path($filePath);
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($fullPath)) {
                        if (!mkdir($fullPath, 0775, true)) {
                            throw new Exception('Failed to create upload directory. Please check permissions.');
                        }
                    }
                    
                    // Check if directory is writable, if not try to fix permissions automatically
                    if (!is_writable($fullPath)) {
                        // Try to make it writable - recursively fix all parent directories
                        $currentPath = $fullPath;
                        $pathsToFix = [];
                        
                        // Collect all parent paths that need fixing
                        while ($currentPath != public_path() && $currentPath != '/' && $currentPath != '') {
                            $pathsToFix[] = $currentPath;
                            $currentPath = dirname($currentPath);
                        }
                        
                        // Fix permissions for all paths (from parent to child)
                        $pathsToFix = array_reverse($pathsToFix);
                        foreach ($pathsToFix as $path) {
                            if (file_exists($path)) {
                                @chmod($path, 0775);
                                // Also try to change ownership if possible (for web server)
                                if (function_exists('posix_getpwuid') && function_exists('posix_geteuid')) {
                                    $webServerUser = posix_getpwuid(posix_geteuid());
                                    if ($webServerUser && isset($webServerUser['name'])) {
                                        @chown($path, $webServerUser['name']);
                                    }
                                }
                            }
                        }
                        
                        // Check again after trying to fix
                        if (!is_writable($fullPath)) {
                            // Last attempt - try 0777 if 0775 doesn't work
                            @chmod($fullPath, 0777);
                            if (!is_writable($fullPath)) {
                                throw new Exception('Upload directory is not writable. Please check permissions. Path: ' . $fullPath);
                            }
                        }
                    }
                    
                    // Handle image upload - check if already uploaded via AJAX
                    if (request('uploaded_image')) {
                        // Use already uploaded image
                        $uploadedImageName = request('uploaded_image');
                        $uploadedOriginalName = request('uploaded_image_original');
                        $imagePath = public_path(getFilePath('campaign') . '/' . $uploadedImageName);
                        
                        // Verify the uploaded image exists
                        if (file_exists($imagePath)) {
                            // Remove old image if exists
                            if ($campaign->image && $campaign->image != $uploadedImageName) {
                                $oldImagePath = public_path(getFilePath('campaign') . '/' . $campaign->image);
                                if (file_exists($oldImagePath)) {
                                    @unlink($oldImagePath);
                                }
                                // Remove old thumbnail if exists
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
                    } elseif (request()->hasFile('image')) {
                        $imageFile = request('image');
                        $oldImage = @$campaign->image;
                        $oldOriginal = @$campaign->image_original;
                        
                        // Remove old image if exists
                        if ($oldImage) {
                            $oldImagePath = public_path(getFilePath('campaign') . '/' . $oldImage);
                            if (file_exists($oldImagePath)) {
                                @unlink($oldImagePath);
                            }
                            // Remove thumbnail if exists
                            $oldThumbPath = public_path(getFilePath('campaign') . '/thumb_' . $oldImage);
                            if (file_exists($oldThumbPath)) {
                                @unlink($oldThumbPath);
                            }
                        }
                        if ($oldOriginal) {
                            $oldOriginalPath = public_path(getFilePath('campaignOriginal') . '/' . $oldOriginal);
                            if (file_exists($oldOriginalPath)) {
                                @unlink($oldOriginalPath);
                            }
                        }
                        
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
                        
                        // Store original image (no resize) for detail page
                        $campaign->image_original = fileUploader($imageFile, getFilePath('campaignOriginal'));

                        // Resize and convert to WebP (with fallback if GD can't read WebP input)
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
                        
                        $campaign->image = $filename;
                    }
                } catch (Exception $e) {
                    \Log::error('Image upload failed', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'file_size' => request()->hasFile('image') ? request('image')->getSize() : null,
                        'file_type' => request()->hasFile('image') ? request('image')->getMimeType() : null,
                        'campaign_path' => getFilePath('campaign'),
                        'full_path' => public_path(getFilePath('campaign')),
                        'path_exists' => file_exists(public_path(getFilePath('campaign'))),
                        'path_writable' => is_writable(public_path(getFilePath('campaign'))),
                        'campaign_id' => $campaign->id
                    ]);
                    
                    $errorMessage = 'Image uploading process has failed';
                    if (config('app.debug')) {
                        $errorMessage .= ': ' . $e->getMessage();
                    }
                    
                    $toast[] = ['error', $errorMessage];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => $errorMessage
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            }

            // Handle video upload or YouTube URL update
            if (request('uploaded_video')) {
                $uploadedVideoName = request('uploaded_video');
                $videoPath = public_path(getFilePath('campaign') . '/' . $uploadedVideoName);

                if (file_exists($videoPath)) {
                    if ($campaign->video && $campaign->video !== $uploadedVideoName) {
                        $oldVideoPath = public_path(getFilePath('campaign') . '/' . $campaign->video);
                        if (file_exists($oldVideoPath)) {
                            @unlink($oldVideoPath);
                        }
                    }
                    $campaign->video = $uploadedVideoName;
                    $campaign->youtube_url = null; // Clear YouTube URL if file is uploaded
                }
            } elseif (request()->hasFile('video')) {
                try {
                    $campaign->video = fileUploader(request('video'), getFilePath('campaign'), getFileSize('campaign'), @$campaign->video);
                    $campaign->youtube_url = null; // Clear YouTube URL if file is uploaded
                } catch (Exception) {
                    $toast[] = ['error', 'Video uploading process has failed'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Video uploading process has failed'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            } elseif (request()->has('youtube_url')) {
                // Check if YouTube URL field exists in request (even if empty)
                $youtubeUrl = trim(request('youtube_url', ''));
                if (!empty($youtubeUrl)) {
                    try {
                        $campaign->youtube_url = $youtubeUrl;
                        $campaign->video = null; // Clear video file when YouTube URL is provided
                    } catch (Exception $e) {
                        \Log::error('Error setting YouTube URL', ['error' => $e->getMessage()]);
                        $toast[] = ['error', 'Error setting YouTube URL: ' . $e->getMessage()];
                    }
                } else {
                    // Field is empty, clear the YouTube URL from database
                    $campaign->youtube_url = null;
                }
            } elseif (request('video_type') === 'youtube' && !request('youtube_url')) {
                // If YouTube option is selected but no URL provided, clear YouTube URL
                $campaign->youtube_url = null;
            }

            // Upload new document
            if (request()->hasFile('document')) {
                try {
                    $campaign->document = fileUploader(request('document'), getFilePath('document'), getFileSize('document'), @$campaign->document);
                } catch (Exception) {
                    $toast[] = ['error', 'Document uploading process has failed'];
                    if (request()->ajax()) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Document uploading process has failed'
                        ], 400);
                    }
                    return back()->withToasts($toast);
                }
            }

            $campaign->save();
            
            // Redirect based on section and next_tab parameter
            $nextTab = request('next_tab');
            
            if ($nextTab && in_array($nextTab, ['basics', 'story', 'reward', 'people', 'payment', 'boost', 'faq', 'updates'])) {
                // Redirect to next tab if specified
                $redirectRoute = 'user.campaign.edit.' . $nextTab;
            } elseif ($section == 'story') {
                $redirectRoute = 'user.campaign.edit.story';
            } else {
                $redirectRoute = 'user.campaign.edit.basics';
            }
            
            // Debug logging
            \Log::info('Campaign updated successfully', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'section' => $section,
                'next_tab' => $nextTab,
                'name' => $campaign->name,
                'goal_amount' => $campaign->goal_amount
            ]);

            $toast[] = ['success', 'Campaign successfully updated'];
            
            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign successfully updated',
                    'redirect' => route($redirectRoute, $campaign->slug)
                ]);
            }
            
            return redirect()->route($redirectRoute, $campaign->slug)->withToasts($toast);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed in update', [
                'errors' => $e->errors(),
                'request_data' => request()->all()
            ]);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }
            throw $e;
        } catch (QueryException $e) {
            \Log::error('SQL error while updating campaign', [
                'campaign_id' => $id,
                'user_id' => auth()->id(),
                'message' => $e->getMessage(),
                'sql' => $e->getSql(),
                'bindings' => $e->getBindings(),
            ]);

            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'SQL error while updating the campaign: ' . $e->getMessage(),
                    'sql' => $e->getSql(),
                    'bindings' => $e->getBindings(),
                ], 500);
            }

            $toast[] = ['error', 'SQL error while updating the campaign: ' . $e->getMessage()];
            return back()->withToasts($toast);
        } catch (Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while updating the campaign: ' . $e->getMessage()
                ], 500);
            }
            $toast[] = ['error', 'An error occurred while updating the campaign'];
            return back()->withToasts($toast);
        }
    }
    function show($slug) {
        $pageTitle = 'Campaign Details';
        $campaign  = Campaign::with('user', 'category', 'comments.user', 'deposits')
                        ->where('slug', $slug)
                        ->where('user_id', auth()->id())
                        ->firstOrFail();
        $comments  = Comment::with('user')
                        ->where('campaign_id', $campaign->id)
                        ->approve()
                        ->latest()
                        ->limit(6)
                        ->get();

        $commentCount = Comment::where('campaign_id', $campaign->id)->approve()->count();
        
        // Get donations for this campaign
        $donations = Deposit::with('user')
                        ->where('campaign_id', $campaign->id)
                        ->where('status', \App\Constants\ManageStatus::PAYMENT_SUCCESS)
                        ->latest()
                        ->limit(5)
                        ->get();
        
        // Get setting for currency symbol
        $setting = bs();
        
        // Use the same campaign data but with proper relationships
        $campaignData = $campaign;

        $seoContents['keywords']           = $campaign->meta_keywords ?? [];
        $seoContents['social_title']       = $campaign->name;
        $seoContents['description']        = strLimit($campaign->description, 150);
        $seoContents['social_description'] = strLimit($campaign->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaign->image, $imageSize);
        $seoContents['image_size']         = $imageSize;
        $donations         = Deposit::with('user')
                                    ->where('campaign_id', $campaignData->id)
                                    ->done()
                                    ->latest()
                                    ->limit(5)
                                    ->get();

        return view($this->activeTheme . 'user.campaign.show', compact('pageTitle', 'campaign', 'comments', 'commentCount', 'seoContents', 'campaignData', 'donations', 'setting'));
    }

    function destroy($id) {
        $campaign = Campaign::where('id', $id)->first();

        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found - Line 677'];
            return back()->withToasts($toast);
        }

        // Only campaign owner can delete
        if ($campaign->user_id != auth()->id()) {
            $toast[] = ['error', 'Only the campaign owner can delete this campaign'];
            return back()->withToasts($toast);
        }

        // Check if campaign has any donations
        $hasDonations = $campaign->deposits()->where('status', 1)->exists();
        
        if ($hasDonations) {
            $toast[] = ['error', 'Cannot delete campaign that has received donations'];
            return back()->withToasts($toast);
        }

        // Delete campaign image
        if ($campaign->image) {
            fileManager()->removeFile(getFilePath('campaign') . '/' . $campaign->image);
        }

        // Delete campaign document
        if ($campaign->document) {
            fileManager()->removeFile(getFilePath('document') . '/' . $campaign->document);
        }

        // Delete gallery images
        if ($campaign->gallery && is_array($campaign->gallery)) {
            foreach ($campaign->gallery as $image) {
                fileManager()->removeFile(getFilePath('campaign') . '/' . $image);
            }
        }

        // Delete campaign
        $campaign->delete();

        $toast[] = ['success', 'Campaign successfully deleted'];
        return back()->withToasts($toast);
    }

    protected function removePreviousGallery() {
        $images = Gallery::where('user_id', auth()->id())->get();

        if (count($images)) {
            foreach ($images as $image) {
                fileManager()->removeFile(getFilePath('campaign') . '/' . $image->image);
                $image->delete();
            }
        }

        return;
    }

    // Handle image uploads from editor
    public function uploadImage() {
        try {
            $request = request();
            
            // Check for both 'file' (TinyMCE) and 'files' (other editors)
            $file = null;
            if ($request->hasFile('file')) {
                $file = $request->file('file');
            } elseif ($request->hasFile('files')) {
                $file = $request->file('files');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No image file provided'
                ], 400);
            }
            
            // Validate file
            $validator = Validator::make(['file' => $file], [
                'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:51200'] // 50MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ], 400);
            }

            // Upload file using fileUploader helper
            $uploadedFile = fileUploader($file, getFilePath('campaign'), getFileSize('campaign'));
            
            if (!$uploadedFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image'
                ], 500);
            }

            // Return success response with image URL for TinyMCE
            $imageUrl = asset(getFilePath('campaign') . '/' . $uploadedFile);
            
            return response()->json([
                'location' => $imageUrl
            ]);

        } catch (Exception $e) {
            \Log::error('Image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the image: ' . $e->getMessage()
            ], 500);
        }
    }

    // Handle story editor media uploads (images/videos)
    public function uploadStoryMedia($slug) {
        try {
            $campaign = Campaign::where('slug', $slug)
                ->orWhere('id', $slug)
                ->firstOrFail();

            if (!$campaign->canBeEditedBy(auth()->id())) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to upload media for this campaign'
                ], 403);
            }

            $file = request()->file('file');
            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'No file provided'
                ], 400);
            }

            $mimeType = $file->getMimeType() ?? '';
            $isVideo = Str::startsWith($mimeType, 'video/');

            $rules = $isVideo
                ? ['file' => ['required', 'mimes:mp4,webm,ogg,mov', 'max:51200']]
                : ['file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:51200']];

            $validator = Validator::make(['file' => $file], $rules);
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ], 400);
            }

            if ($isVideo) {
                $uploadedFile = fileUploader($file, getFilePath('storyVideo'), getFileSize('storyVideo'));
                $url = asset(getFilePath('storyVideo') . '/' . $uploadedFile);
                return response()->json([
                    'success' => true,
                    'type' => 'video',
                    'mime' => $mimeType,
                    'location' => $url
                ]);
            }

            $uploadedFile = fileUploader($file, getFilePath('storyImage'));
            $url = asset(getFilePath('storyImage') . '/' . $uploadedFile);

            return response()->json([
                'success' => true,
                'type' => 'image',
                'location' => $url
            ]);
        } catch (Exception $e) {
            \Log::error('Story media upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading media: ' . $e->getMessage()
            ], 500);
        }
    }

    // Handle external image URLs - download and upload to server
    public function uploadExternalImage() {
        try {
            $request = request();
            $externalUrl = $request->input('external_url');
            
            if (!$externalUrl) {
                return response()->json([
                    'success' => false,
                    'message' => 'No external URL provided'
                ], 400);
            }

            // Validate URL
            if (!filter_var($externalUrl, FILTER_VALIDATE_URL)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid URL provided'
                ], 400);
            }

            // Download image from external URL
            $imageData = @file_get_contents($externalUrl);
            
            if ($imageData === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image from external URL'
                ], 400);
            }

            // Get image info
            $imageInfo = @getimagesizefromstring($imageData);
            if ($imageInfo === false) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file'
                ], 400);
            }

            // Determine file extension from MIME type
            $mimeType = $imageInfo['mime'];
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp'
            ];
            
            $extension = $extensions[$mimeType] ?? 'jpg';
            
            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $extension;
            
            // Save to temporary file
            $tempFile = sys_get_temp_dir() . '/' . $filename;
            file_put_contents($tempFile, $imageData);
            
            // Create UploadedFile instance
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempFile,
                $filename,
                $mimeType,
                null,
                true
            );
            
            // Upload using fileUploader helper
            $savedFile = fileUploader($uploadedFile, getFilePath('campaign'), getFileSize('campaign'));
            
            // Clean up temp file
            @unlink($tempFile);
            
            if (!$savedFile) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image to server'
                ], 500);
            }

            // Return success response with image URL
            $imageUrl = asset(getFilePath('campaign') . '/' . $savedFile);
            
            return response()->json([
                'location' => $imageUrl
            ]);

        } catch (Exception $e) {
            \Log::error('External image upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'url' => $request->input('external_url')
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading external image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store FAQ for campaign
     */
    function storeFaq($slug) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        request()->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'order' => 'nullable|integer|min:0'
        ]);

        $faq = new CampaignFaq();
        $faq->campaign_id = $campaign->id;
        $faq->question = request('question');
        $faq->answer = request('answer');
        $faq->order = request('order', 0);
        $faq->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ added successfully',
                'faq' => $faq
            ]);
        }

        $toast[] = ['success', 'FAQ added successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Update FAQ
     */
    function updateFaq($slug, $faqId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        $faq = CampaignFaq::where('id', $faqId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        request()->validate([
            'question' => 'required|string|max:500',
            'answer' => 'required|string|max:2000',
            'order' => 'nullable|integer|min:0'
        ]);

        $faq->question = request('question');
        $faq->answer = request('answer');
        $faq->order = request('order', 0);
        $faq->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ updated successfully',
                'faq' => $faq
            ]);
        }

        $toast[] = ['success', 'FAQ updated successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Delete FAQ
     */
    function deleteFaq($slug, $faqId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        $faq = CampaignFaq::where('id', $faqId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        $faq->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'FAQ deleted successfully'
            ]);
        }

        $toast[] = ['success', 'FAQ deleted successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Get FAQ for editing (AJAX)
     */
    function getFaq($slug, $faqId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this campaign'
            ], 403);
        }

        $faq = CampaignFaq::where('id', $faqId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'faq' => $faq
        ]);
    }

    /**
     * Store Update for campaign
     */
    function storeUpdate($slug) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        request()->validate([
            'title' => 'required|string|max:500',
            'content' => 'required|string|min:30',
            'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])],
            'is_published' => 'nullable|boolean'
        ]);

        $update = new CampaignUpdate();
        $update->campaign_id = $campaign->id;
        $update->user_id = auth()->id();
        $update->title = request('title');
        $update->content = request('content');
        $update->slug = slug(request('title')) . '-' . time();
        $update->is_published = request('is_published', true);

        // Upload image if provided
        if (request()->hasFile('image')) {
            try {
                $update->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'));
            } catch (Exception $e) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Image upload failed: ' . $e->getMessage()
                    ], 400);
                }
                $toast[] = ['error', 'Image uploading process has failed'];
                return back()->withToasts($toast);
            }
        }

        $update->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Update added successfully',
                'update' => $update
            ]);
        }

        $toast[] = ['success', 'Update added successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Update Update
     */
    function updateUpdate($slug, $updateId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        $update = CampaignUpdate::where('id', $updateId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        request()->validate([
            'title' => 'required|string|max:500',
            'content' => 'required|string|min:30',
            'image' => ['nullable', File::types(['png', 'jpg', 'jpeg', 'webp'])],
            'is_published' => 'nullable|boolean'
        ]);

        $update->title = request('title');
        $update->content = request('content');
        $update->is_published = request('is_published', $update->is_published);

        // Update slug if title changed
        if ($update->title != request('title')) {
            $update->slug = slug(request('title')) . '-' . time();
        }

        // Upload new image if provided
        if (request()->hasFile('image')) {
            try {
                $update->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'), $update->image);
            } catch (Exception $e) {
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Image upload failed: ' . $e->getMessage()
                    ], 400);
                }
                $toast[] = ['error', 'Image uploading process has failed'];
                return back()->withToasts($toast);
            }
        }

        $update->save();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Update updated successfully',
                'update' => $update
            ]);
        }

        $toast[] = ['success', 'Update updated successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Delete Update
     */
    function deleteUpdate($slug, $updateId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit this campaign'
                ], 403);
            }
            abort(403, 'You do not have permission to edit this campaign');
        }

        $update = CampaignUpdate::where('id', $updateId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        // Delete image if exists
        if ($update->image) {
            fileManager()->removeFile(getFilePath('campaign') . '/' . $update->image);
        }

        $update->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Update deleted successfully'
            ]);
        }

        $toast[] = ['success', 'Update deleted successfully'];
        return back()->withToasts($toast);
    }

    /**
     * Get Update for editing (AJAX)
     */
    function getUpdate($slug, $updateId) {
        $campaign = Campaign::where('slug', $slug)->firstOrFail();
        
        // Check if user is owner or collaborator
        if (!$campaign->canBeEditedBy(auth()->id())) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit this campaign'
            ], 403);
        }

        $update = CampaignUpdate::where('id', $updateId)
            ->where('campaign_id', $campaign->id)
            ->firstOrFail();

        $update->image_url = $update->image ? getImage(getFilePath('campaign') . '/' . $update->image, getFileSize('campaign')) : null;

        return response()->json([
            'success' => true,
            'update' => $update
        ]);
    }

    /**
     * Update payment details for campaign
     */
    function updatePayment($slug) {
        $campaign = Campaign::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        request()->validate([
            'payout_bank_id' => 'required|exists:payout_banks,id',
            'bank_account_number' => 'required|string|max:255',
        ]);

        $payoutBank = \App\Models\PayoutBank::where('id', request('payout_bank_id'))
            ->where('status', true)
            ->first();

        if (!$payoutBank) {
            $toast[] = ['error', 'Selected bank is not available'];
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected bank is not available'
                ], 400);
            }
            return back()->withToasts($toast);
        }

        $campaign->payout_bank_id = request('payout_bank_id');
        $campaign->bank_account_number = request('bank_account_number');
        $campaign->bank_account_email = filter_var(request('bank_account_number'), FILTER_VALIDATE_EMAIL) ? request('bank_account_number') : null;
        $campaign->save();

        // Create admin notification
        $adminNotification = new AdminNotification();
        $adminNotification->user_id = auth()->id();
        $adminNotification->title = auth()->user()->fullname . ' has updated payment details for campaign: ' . $campaign->name;
        $adminNotification->click_url = urlPath('admin.campaigns.index');
        $adminNotification->save();

        $toast[] = ['success', 'Payment details updated successfully'];

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment details updated successfully'
            ]);
        }

        return back()->withToasts($toast);
    }

    /**
     * Add a collaborator to a campaign
     */
    function addCollaborator($slug) {
        try {
            $campaign = Campaign::where(function($query) use ($slug) {
                                $query->where('slug', $slug)
                                      ->orWhere('id', $slug);
                            })
                            ->first();

            if (!$campaign) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaign not found'
                ], 404);
            }

            // Only campaign owner can add collaborators
            if ($campaign->user_id != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the campaign owner can add collaborators'
                ], 403);
            }

            $validator = Validator::make(request()->all(), [
                'user_id' => 'required|exists:users,id'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $userId = request()->input('user_id');

            // Cannot add yourself as collaborator
            if ($userId == auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot add yourself as a collaborator'
                ], 422);
            }

            // Check if user is already a collaborator
            if ($campaign->collaborators()->where('user_id', $userId)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User is already a collaborator'
                ], 422);
            }

            // Add collaborator
            CampaignCollaborator::create([
                'campaign_id' => $campaign->id,
                'user_id' => $userId
            ]);

            $user = User::find($userId);

            return response()->json([
                'success' => true,
                'message' => 'Collaborator added successfully',
                'collaborator' => [
                    'id' => $user->id,
                    'name' => $user->fullname ?? $user->username,
                    'email' => $user->email,
                    'image' => getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile'))
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding collaborator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove a collaborator from a campaign
     */
    function removeCollaborator($slug, $userId) {
        try {
            $campaign = Campaign::where(function($query) use ($slug) {
                                $query->where('slug', $slug)
                                      ->orWhere('id', $slug);
                            })
                            ->first();

            if (!$campaign) {
                return response()->json([
                    'success' => false,
                    'message' => 'Campaign not found'
                ], 404);
            }

            // Only campaign owner can remove collaborators
            if ($campaign->user_id != auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the campaign owner can remove collaborators'
                ], 403);
            }

            $collaborator = $campaign->collaborators()->where('user_id', $userId)->first();

            if (!$collaborator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Collaborator not found'
                ], 404);
            }

            $collaborator->delete();

            return response()->json([
                'success' => true,
                'message' => 'Collaborator removed successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing collaborator: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search users for adding as collaborators
     */
    function searchUsers() {
        try {
            $query = request()->input('q', '');

            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'users' => []
                ]);
            }

            $users = User::where('status', 1)
                        ->where(function($q) use ($query) {
                            $q->where('username', 'like', '%' . $query . '%')
                              ->orWhere('email', 'like', '%' . $query . '%')
                              ->orWhere('firstname', 'like', '%' . $query . '%')
                              ->orWhere('lastname', 'like', '%' . $query . '%');
                        })
                        ->where('id', '!=', auth()->id())
                        ->limit(10)
                        ->get()
                        ->map(function($user) {
                            return [
                                'id' => $user->id,
                                'name' => $user->fullname ?? $user->username,
                                'email' => $user->email,
                                'username' => $user->username,
                                'image' => getImage(getFilePath('userProfile') . '/' . $user->image, getFileSize('userProfile'))
                            ];
                        });

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching users: ' . $e->getMessage()
            ], 500);
        }
    }
}
