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
        $this->validate(request(), [
            'category_id'         => 'required|integer|gt:0',
            'image'               => ['required', File::types(['png', 'jpg', 'jpeg'])],
            'name'                => 'required|string|max:190|unique:campaigns,name',
            'description'         => 'required|min:30',
            'document'            => ['nullable', File::types('pdf')],
            'goal_amount'         => 'required|numeric|gt:0',
            'preferred_amounts'   => 'required|array|min:1',
            'preferred_amounts.*' => 'required|numeric|gt:0',
            'start_date'          => 'required|date_format:d-m-Y|after:today',
            'end_date'            => 'required|date_format:d-m-Y|after:start_date',
        ], [
            'category_id.required' => 'The category field is required.',
            'category_id.integer'  => 'The category must be an integer.',
        ]);

        $category = Category::where('id', request('category_id'))->active()->first();

        if (!$category) {
            $toast[] = ['error', 'Selected category not found or inactive'];

            return back()->withToasts($toast);
        }

        $images = Gallery::where('user_id', auth()->id())->get();

        if (!count($images)) {
            $toast[] = ['error', 'Minimum one gallery image is required'];

            return back()->withToasts($toast);
        }

        // Gallery images
        $gallery = [];

        foreach ($images as $image) array_push($gallery, $image->image);

        // Store campaign data
        $campaign              = new Campaign();
        $campaign->user_id     = auth()->id();
        $campaign->category_id = request('category_id');

        // Upload main image
        try {
            $campaign->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'), null, getThumbSize('campaign'));
        } catch (Exception) {
            $toast[] = ['error', 'Image uploading process has failed'];

            return back()->withToasts($toast);
        }

        $campaign->gallery     = $gallery;
        $campaign->name        = request('name');
        $campaign->slug        = slug(request('name'));
        $purifier              = new HTMLPurifier();
        $campaign->description = $purifier->purify(request('description'));

        // Upload document
        if (request()->has('document')) {
            try {
                $campaign->document = fileUploader(request('document'), getFilePath('document'));
            } catch (Exception) {
                $toast[] = ['error', 'Document uploading process has failed'];

                return back()->withToasts($toast);
            }
        }

        $campaign->goal_amount       = request('goal_amount');
        $campaign->preferred_amounts = request('preferred_amounts');
        $campaign->start_date        = Carbon::parse(request('start_date'));
        $campaign->end_date          = Carbon::parse(request('end_date'));
        $campaign->save();

        // Delete gallery images
        foreach ($images as $image) $image->delete();

        // Create admin notification
        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = auth()->id();
        $adminNotification->title     = 'New campaign created by ' . auth()->user()->fullname;
        $adminNotification->click_url = urlPath('admin.campaigns.index');
        $adminNotification->save();

        $toast[] = ['success', 'Campaign successfully created'];

        return to_route('user.campaign.index')->withToasts($toast);
    }

    function edit($slug) {
        // Delete previously unused gallery images if exist
        $this->removePreviousGallery();

        $pageTitle  = 'Edit Campaign';
        $categories = Category::get();
        $campaign   = Campaign::where('slug', $slug)
                                ->where('user_id', auth()->id())
                                ->approve()
                                ->select('id', 'image', 'gallery', 'end_date')
                                ->first();

        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found'];
            return back()->withToasts($toast);
        }

        if ($campaign->isExpired()) {
            $toast[] = ['error', 'This campaign has expired'];
            return back()->withToasts($toast);
        }

        return view($this->activeTheme . 'user.campaign.edit', compact('pageTitle', 'categories', 'campaign'));
    }

    /**
     * Remove image while editing a campaign
     */
    function removeImage($id) {
        $campaign = Campaign::where('id', $id)->where('user_id', auth()->id())->approve()->first();

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
        $this->validate(request(), [
            'image'    => ['nullable', File::types(['png', 'jpg', 'jpeg'])],
            'document' => ['nullable', File::types('pdf')],
        ]);

        $campaign = Campaign::where('id', $id)->where('user_id', auth()->id())->approve()->first();
        

        if (!$campaign) {
            $toast[] = ['error', 'Campaign not found'];
            return back()->withToasts($toast);
        }

        if ($campaign->isExpired()) {
            $toast[] = ['error', 'This campaign has expired'];
            return back()->withToasts($toast);
        }

        // Check whether campaign gallery exists or not
        if (!count($campaign->gallery)) {
            $toast[] = ['error', 'Minimum one gallery image is required'];
            return back()->withToasts($toast);
        }

        // Upload new main image
        if (request()->hasFile('image')) {
            try {
                $campaign->image = fileUploader(request('image'), getFilePath('campaign'), getFileSize('campaign'), @$campaign->image, getThumbSize('campaign'));
            } catch (Exception) {
                $toast[] = ['error', 'Image uploading process has failed'];

                return back()->withToasts($toast);
            }
        }

        // Upload document
        if (request()->has('document')) {
            try {
                $campaign->document = fileUploader(request('document'), getFilePath('document'), null, @$campaign->document);
            } catch (Exception) {
                $toast[] = ['error', 'Document uploading process has failed'];

                return back()->withToasts($toast);
            }
        }

        // Update gallery images
        $images = Gallery::where('user_id', auth()->id())->get();

        if (count($images)) {
            $gallery = [];

            foreach ($images as $image) array_push($gallery, $image->image);

            $campaign->gallery = array_merge($campaign->gallery, $gallery);
        }

        $campaign->save();

        // Delete gallery images
        if ($images) {
            foreach ($images as $image) $image->delete();
        }

        $toast[] = ['success', 'Campaign successfully updated'];
        return back()->withToasts($toast);
    }

    function show($slug) {
        $pageTitle    = 'Campaign Details';
        $campaignData = Campaign::where('slug', $slug)->where('user_id', auth()->id())->firstOrFail();
        $comments     = Comment::with('user')
                        ->where('campaign_id', $campaignData->id)
                        ->approve()
                        ->latest()
                        ->limit(6)
                        ->get();

        $commentCount = Comment::where('campaign_id', $campaignData->id)->approve()->count();

        $seoContents['keywords']           = $campaignData->meta_keywords ?? [];
        $seoContents['social_title']       = $campaignData->name;
        $seoContents['description']        = strLimit($campaignData->description, 150);
        $seoContents['social_description'] = strLimit($campaignData->description, 150);
        $imageSize                         = getFileSize('campaign');
        $seoContents['image']              = getImage(getFilePath('campaign') . '/' . $campaignData->image, $imageSize);
        $seoContents['image_size']         = $imageSize;

        return view($this->activeTheme . 'user.campaign.show', compact('pageTitle', 'campaignData', 'comments', 'commentCount', 'seoContents'));
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
}
