<?php

namespace App\Http\Controllers\User;

use Exception;
use HTMLPurifier;
use Carbon\Carbon;
use App\Models\Comment;
use App\Models\Gallery;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\AdminNotification;
use App\Http\Controllers\Controller;
use App\Services\YouTubeUploadService;
use Illuminate\Validation\Rules\File;
use Illuminate\Support\Facades\Validator;

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

        return $campaigns->with('category')
            ->where('user_id', auth()->id())
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
            'file' => ['required', File::types(['png', 'jpg', 'jpeg'])],
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

    function store() {
        // Debug logging
        \Log::info('Campaign store method called', [
            'request_data' => request()->all(),
            'files' => request()->hasFile('image') ? 'Image file present' : 'No image file',
            'user_id' => auth()->id()
        ]);
        
        try {
            $this->validate(request(), [
                'category_id'         => 'required|integer|gt:0',
                'image'               => ['required', File::types(['png', 'jpg', 'jpeg'])],
                'video'               => ['nullable', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'], // 500MB max
                'youtube_url'         => 'nullable|url',
                'location'            => 'nullable|string|max:255',
                'name'                => 'required|string|max:190|unique:campaigns,name',
                'description'         => 'required|min:30',
                'goal_amount'         => 'required|numeric|gt:0',
                'start_date'          => 'required|date_format:Y-m-d|after_or_equal:today',
                'end_date'            => 'required|date_format:Y-m-d|after:start_date',
            ], [
                'category_id.required' => 'The category field is required.',
                'category_id.integer'  => 'The category must be an integer.',
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

            // Upload main image
            try {
                $campaign->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'), null, getThumbSize('campaign'));
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
                            $tags = ['campaign', 'donation', 'fundraising', 'apnafund'];
                            
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
            $campaign->description = $purifier->purify(request('description'));

            $campaign->goal_amount     = request('goal_amount');
            $campaign->start_date        = Carbon::parse(request('start_date'));
            $campaign->end_date          = Carbon::parse(request('end_date'));
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

            $toast[] = ['success', 'Campaign successfully created'];

            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign successfully created',
                    'redirect' => route('user.campaign.index')
                ]);
            }

            return to_route('user.campaign.index')->withToasts($toast);
            
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
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred while creating the campaign: ' . $e->getMessage()
                ], 500);
            }
            $toast[] = ['error', 'An error occurred while creating the campaign'];
            return back()->withToasts($toast);
        }
    }

    function edit($slug) {

        try {
            
            // Delete previously unused gallery images if exist
            $this->removePreviousGallery();

            $pageTitle  = 'Edit Campaign';
            $categories = Category::active()->get();
            $campaign   = Campaign::where('slug', $slug)
                                    ->where('user_id', auth()->id())
                                    // ->approve()
                                    ->first();

            if (!$campaign) {
                $toast[] = ['error', 'Campaign not found'];
                return back()->withToasts($toast);
            }

            if ($campaign->isExpired()) {
                $toast[] = ['error', 'This campaign has expired'];
                return back()->withToasts($toast);
            }

            return view('themes.apnafund.user.campaign.edit', compact('pageTitle', 'categories', 'campaign'));
        } catch (\Exception $e) {
            $toast[] = ['error', 'Error loading campaign: ' . $e->getMessage()];
            return back()->withToasts($toast);
        }
    }

    /**
     * Remove image while editing a campaign
     */
    function removeImage($id) {
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
            $this->validate(request(), [
                'category_id'         => 'required|integer|gt:0',
                'image'               => ['nullable', File::types(['png', 'jpg', 'jpeg'])],
                'video'               => ['nullable', File::types(['mp4', 'avi', 'mov', 'wmv', 'flv', '3gp']), 'max:512000'], // 500MB max
                'youtube_url'         => 'nullable|url',
                'location'            => 'nullable|string|max:255',
                'name'                => 'required|string|max:190|unique:campaigns,name,' . $id,
                'description'         => 'required|min:30',
                'goal_amount'         => 'required|numeric|gt:0',
                'start_date'          => 'required|date_format:Y-m-d',
                'end_date'            => 'required|date_format:Y-m-d|after:start_date',
                'document'            => ['nullable', File::types('pdf')],
            ], [
                'category_id.required' => 'The category field is required.',
                'category_id.integer'  => 'The category must be an integer.',
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

            $campaign = Campaign::where('id', $id)->where('user_id', auth()->id())->first();

            if (!$campaign) {
                $toast[] = ['error', 'Campaign not found'];
                if (request()->ajax()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Campaign not found'
                    ], 404);
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

            // Update campaign data
            $campaign->category_id = request('category_id');
            $campaign->name        = request('name');
            $campaign->slug        = slug(request('name'));
            $campaign->location    = request('location');
            $purifier              = new HTMLPurifier();
            $campaign->description = $purifier->purify(request('description'));
            $campaign->goal_amount = request('goal_amount');
            $campaign->start_date  = Carbon::parse(request('start_date'));
            $campaign->end_date    = Carbon::parse(request('end_date'));

            // Upload new main image
            if (request()->hasFile('image')) {
                try {
                    $campaign->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'), @$campaign->image, getThumbSize('campaign'));
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
            }

            // Handle video upload or YouTube URL update
            if (request()->hasFile('video')) {
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
            } elseif (request('youtube_url')) {
                try {
                    $campaign->youtube_url = request('youtube_url');
                    $campaign->video = null; // Clear video file when YouTube URL is provided
                } catch (Exception $e) {
                    \Log::error('Error setting YouTube URL', ['error' => $e->getMessage()]);
                    $toast[] = ['error', 'Error setting YouTube URL: ' . $e->getMessage()];
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
            
            // Debug logging
            \Log::info('Campaign updated successfully', [
                'campaign_id' => $campaign->id,
                'user_id' => $campaign->user_id,
                'name' => $campaign->name,
                'goal_amount' => $campaign->goal_amount
            ]);

            $toast[] = ['success', 'Campaign successfully updated'];
            
            // Check if request is AJAX
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Campaign successfully updated',
                    'redirect' => route('user.campaign.index')
                ]);
            }
            
            return back()->withToasts($toast);
            
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
        $campaign  = Campaign::with('user', 'category', 'comments.user')
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

        $seoContents['keywords']           = $campaign->meta_keywords ?? [];
        $seoContents['social_title']       = $campaign->name;
        $seoContents['description']        = strLimit($campaign->description, 150);
        $seoContents['social_description'] = strLimit($campaign->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaign->image, $imageSize);
        $seoContents['image_size']         = $imageSize;

        return view($this->activeTheme . 'user.campaign.show', compact('pageTitle', 'campaign', 'comments', 'commentCount', 'seoContents'));
    }

    function destroy($id) {
        $campaign = Campaign::where('id', $id)->where('user_id', auth()->id())->first();

        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found'];
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
            
            if (!$request->hasFile('files')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No image file provided'
                ], 400);
            }

            $file = $request->file('files');
            
            // Validate file
            $validator = Validator::make(['file' => $file], [
                'file' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'] // 5MB max
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first('file')
                ], 400);
            }

            // Generate unique filename
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // Upload file
            $path = fileManager()->uploadFile($file, getFilePath('campaign'), null, $filename);
            
            if (!$path) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image'
                ], 500);
            }

            // Return success response with image URL
            $imageUrl = asset(getFilePath('campaign') . '/' . $filename);
            
            return response()->json([
                'success' => true,
                'message' => 'Image uploaded successfully',
                'files' => [$imageUrl],
                'baseurl' => asset(getFilePath('campaign') . '/'),
                'path' => $filename
            ]);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading the image: ' . $e->getMessage()
            ], 500);
        }
    }
}
