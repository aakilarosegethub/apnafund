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
                'type' => 'nullable|in:digital,physical',
                'color_theme' => 'nullable|string',
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
        $reward->type = $request->type ?? 'physical'; // Default to physical
        $reward->color_theme = $request->color_theme ?? 'primary'; // Default to primary
        $reward->terms_conditions = $request->terms_conditions;

        if ($request->hasFile('image')) {
            try {
                // Validate image file
                $imageFile = $request->image;
                $maxSize = 5120; // 5MB in KB
                
                if ($imageFile->getSize() > $maxSize * 1024) {
                    throw new \Exception('Image size must be less than 5MB');
                }
                
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($imageFile->getMimeType(), $allowedTypes)) {
                    throw new \Exception('Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.');
                }
                
                // Get file path and ensure directory exists
                $filePath = getFilePath('reward');
                $fullPath = public_path($filePath);
                
                // Create directory if it doesn't exist
                if (!file_exists($fullPath)) {
                    if (!mkdir($fullPath, 0775, true)) {
                        throw new \Exception('Failed to create upload directory. Please check permissions.');
                    }
                }
                
                // Check if directory is writable, if not try to fix permissions automatically
                if (!is_writable($fullPath)) {
                    // Try to make it writable
                    if (!chmod($fullPath, 0775)) {
                        throw new \Exception('Upload directory is not writable and could not be fixed. Please check permissions manually.');
                    }
                }
                
                $uploadedImage = fileUploader($request->image, getFilePath('reward'), getFileSize('reward'), null, getThumbSize('reward'));
                if ($uploadedImage) {
                    $reward->image = $uploadedImage;
                    \Log::info('Reward image uploaded successfully', [
                        'filename' => $uploadedImage,
                        'campaign_id' => $campaign->id
                    ]);
                } else {
                    throw new \Exception('Image upload returned empty filename');
                }
            } catch (\Exception $e) {
                \Log::error('Reward image upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file_size' => $request->hasFile('image') ? $request->image->getSize() : null,
                    'file_type' => $request->hasFile('image') ? $request->image->getMimeType() : null,
                    'reward_path' => getFilePath('reward'),
                    'full_path' => public_path(getFilePath('reward')),
                    'path_exists' => file_exists(public_path(getFilePath('reward'))),
                    'path_writable' => is_writable(public_path(getFilePath('reward'))),
                    'campaign_id' => $campaign->id
                ]);
                
                $errorMessage = 'Image uploading process has failed';
                if (config('app.debug')) {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                
                $toast[] = ['error', $errorMessage];
                return back()->withToasts($toast);
            }
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

        // Return JSON for AJAX requests
        if (request()->ajax() || request()->wantsJson()) {
            // Get image URL properly
            $imageUrl = null;
            if ($reward->image) {
                $imageUrl = getImage(getFilePath('reward') . '/' . $reward->image, getThumbSize('reward'));
            }
            
            return response()->json([
                'success' => true,
                'reward' => [
                    'id' => $reward->id,
                    'title' => $reward->title,
                    'description' => $reward->description,
                    'minimum_amount' => $reward->minimum_amount,
                    'quantity' => $reward->quantity,
                    'type' => $reward->type,
                    'color_theme' => $reward->color_theme,
                    'terms_conditions' => $reward->terms_conditions,
                    'image' => $reward->image,
                    'image_url' => $imageUrl
                ]
            ]);
        }

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

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string|min:10',
                'minimum_amount' => 'required|numeric|min:1',
                'quantity' => 'nullable|integer|min:1',
                'type' => 'nullable|in:digital,physical',
                'color_theme' => 'nullable|string',
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

        $reward->title = $request->title;
        $reward->description = $request->description;
        $reward->minimum_amount = $request->minimum_amount;
        $reward->quantity = $request->quantity;
        $reward->type = $request->type ?? $reward->type ?? 'physical'; // Keep existing or default
        $reward->color_theme = $request->color_theme ?? $reward->color_theme ?? 'primary'; // Keep existing or default
        $reward->terms_conditions = $request->terms_conditions;

        if ($request->hasFile('image')) {
            try {
                // Validate image file
                $imageFile = $request->image;
                $maxSize = 5120; // 5MB in KB
                
                if ($imageFile->getSize() > $maxSize * 1024) {
                    throw new \Exception('Image size must be less than 5MB');
                }
                
                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($imageFile->getMimeType(), $allowedTypes)) {
                    throw new \Exception('Invalid image type. Only JPG, PNG, GIF, and WEBP are allowed.');
                }
                
                // Get file path and ensure directory exists
                $filePath = getFilePath('reward');
                $fullPath = public_path($filePath);
                
                // Create directory if it doesn't exist
                if (!file_exists($fullPath)) {
                    if (!mkdir($fullPath, 0775, true)) {
                        throw new \Exception('Failed to create upload directory. Please check permissions.');
                    }
                }
                
                // Check if directory is writable, if not try to fix permissions automatically
                if (!is_writable($fullPath)) {
                    // Try to make it writable
                    if (!chmod($fullPath, 0775)) {
                        throw new \Exception('Upload directory is not writable and could not be fixed. Please check permissions manually.');
                    }
                }
                
                $uploadedImage = fileUploader($request->image, getFilePath('reward'), getFileSize('reward'), $reward->image, getThumbSize('reward'));
                if ($uploadedImage) {
                    $reward->image = $uploadedImage;
                    \Log::info('Reward image updated successfully', [
                        'filename' => $uploadedImage,
                        'reward_id' => $reward->id
                    ]);
                } else {
                    throw new \Exception('Image upload returned empty filename');
                }
            } catch (\Exception $e) {
                \Log::error('Reward image upload failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file_size' => $request->hasFile('image') ? $request->image->getSize() : null,
                    'file_type' => $request->hasFile('image') ? $request->image->getMimeType() : null,
                    'reward_path' => getFilePath('reward'),
                    'full_path' => public_path(getFilePath('reward')),
                    'path_exists' => file_exists(public_path(getFilePath('reward'))),
                    'path_writable' => is_writable(public_path(getFilePath('reward'))),
                    'reward_id' => $reward->id
                ]);
                
                $errorMessage = 'Image uploading process has failed';
                if (config('app.debug')) {
                    $errorMessage .= ': ' . $e->getMessage();
                }
                
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $errorMessage
                    ], 400);
                }
                
                $toast[] = ['error', $errorMessage];
                return back()->withToasts($toast);
            }
        }

        $reward->save();

        // Check if request is AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reward updated successfully',
                'reward' => $reward
            ]);
        }

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

        // Check if request is AJAX
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Reward deleted successfully'
            ]);
        }

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