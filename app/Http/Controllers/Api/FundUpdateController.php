<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Models\Campaign;
use App\Models\CampaignUpdate;
use Illuminate\Support\Str;

class FundUpdateController extends BaseApiController
{
    /**
     * Add Fund Update
     */
    public function fundUpdate(Request $request): JsonResponse
    {
        // Web parity: creates a row in campaign_updates (CampaignUpdate), same as user campaign "Updates" tab.
        // Accepts: campaign_id|fund_id, content|description, optional title, image|main_img
        $fundIdRaw = $request->input('campaign_id') ?: $request->input('fund_id');
        $content = trim((string) ($request->input('content') ?? $request->input('description') ?? ''));
        if ($fundIdRaw === null || $fundIdRaw === '' || $content === '') {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "campaign_id (or fund_id) and content (or description) required."
            ], 401);
        }

        $fund_id = (int) round((float) trim((string) $fundIdRaw, " \t\n\r\0\x0B\"'"));
        $titleRaw = $request->input('title');
        $title = $titleRaw !== null && $titleRaw !== ''
            ? strip_tags($this->h->real_string((string) $titleRaw))
            : Str::limit(strip_tags($this->h->real_string($content)), 500);
        if ($title === '') {
            $title = 'Update';
        }
        $description = $content;

        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        $campaign = Campaign::where('id', $fund_id)->first();
        if (!$campaign || !$campaign->canBeEditedBy($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found or unauthorized."
            ], 401);
        }

        if ($campaign->isExpired()) {
            return response()->json([
                "ResponseCode" => "400",
                "Result" => "false",
                "ResponseMsg" => "This campaign has expired."
            ], 400);
        }

        if (strlen($description) < 30) {
            return response()->json([
                "ResponseCode" => "422",
                "Result" => "false",
                "ResponseMsg" => "Content must be at least 30 characters."
            ], 422);
        }

        // Same storage as web updates: filename only, under campaign image path
        $imageName = null;
        $imageFile = $request->file('image') ?: $request->file('main_img');
        if ($imageFile && $imageFile->isValid()) {
            try {
                $imageName = fileUploader($imageFile, getFilePath('campaign'), getFileSize('campaign'));
            } catch (\Exception $e) {
                return response()->json([
                    "ResponseCode" => "400",
                    "Result" => "false",
                    "ResponseMsg" => 'Image upload failed: ' . $e->getMessage(),
                ], 400);
            }
        }

        if ($imageName === null) {
            foreach (['image', 'main_img'] as $fileKey) {
                if (!isset($_FILES[$fileKey])) {
                    continue;
                }
                $fu = $_FILES[$fileKey];
                $err = is_array($fu['error'] ?? null) ? ($fu['error'][0] ?? UPLOAD_ERR_NO_FILE) : ($fu['error'] ?? UPLOAD_ERR_NO_FILE);
                if ($err === UPLOAD_ERR_OK) {
                    $imageName = $this->uploadFromPhpFiles($fileKey, '/' . trim(getFilePath('campaign'), '/') . '/');
                    break;
                }
                if ($err !== UPLOAD_ERR_NO_FILE) {
                    return response()->json([
                        "ResponseCode" => "400",
                        "Result" => "false",
                        "ResponseMsg" => $this->uploadErrorMessage($err),
                    ], 400);
                }
            }
        }
        if ($imageName !== null && str_contains($imageName, '/')) {
            $imageName = basename($imageName);
        }

        try {
            $update = new CampaignUpdate();
            $update->campaign_id = $fund_id;
            $update->user_id = $uid;
            $update->title = $title;
            $update->content = $description;
            $update->slug = slug($title) . '-' . time();
            $update->is_published = true;
            if ($imageName) {
                $update->image = $imageName;
            }
            $update->save();
        } catch (\Exception $e) {
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "Database error: " . $e->getMessage()
            ], 500);
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Update Send Successfully!!",
            "update_id" => $update->id,
        ]);
    }

    /**
     * Cancel Fund
     */
    public function cancelFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['fund_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $fund_id = (int) round((float) $data['fund_id']);
        
        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }
        
        $reject_comment = $data['reject_comment'] ?? '';

        // Use campaigns table instead of tbl_fund
        // campaigns: status 0 = rejected/cancelled
        // Check if campaign belongs to user
        $campaign = DB::table('campaigns')->where('id', $fund_id)->where('user_id', $uid)->first();
        
        if (!$campaign) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found or unauthorized!"
            ], 401);
        }

        // Update campaign status to rejected (0 = cancelled/rejected)
        DB::table('campaigns')
            ->where('id', $fund_id)
            ->where('user_id', $uid)
            ->update([
                'status' => 0, // CAMPAIGN_REJECTED = 0 (cancelled)
                'reject_reason' => $reject_comment, // Store reject comment if column exists
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Cancelled Successfully!!"
        ]);
    }

    /**
     * Complete Fund
     */
    public function completeFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['fund_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $fund_id = (int) round((float) $data['fund_id']);
        
        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        // Use campaigns table instead of tbl_fund
        // Check if campaign belongs to user
        $campaign = DB::table('campaigns')->where('id', $fund_id)->where('user_id', $uid)->first();
        
        if (!$campaign) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found or unauthorized!"
            ], 401);
        }

        // For campaigns table, "Completed" is not a status - it's calculated based on end_date
        // But we can mark as expired by updating end_date to past or mark as approved (1)
        // Since campaign is already running (status=1), we just update end_date to mark it complete
        DB::table('campaigns')
            ->where('id', $fund_id)
            ->where('user_id', $uid)
            ->update([
                'end_date' => date('Y-m-d'), // Set end date to today to mark as complete
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Completed Successfully!!"
        ]);
    }

    /**
     * Delete Fund (user can delete own campaign only)
     */
    public function deleteFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['fund_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $fund_id = (int) round((float) $data['fund_id']);
        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        $campaign = DB::table('campaigns')->where('id', $fund_id)->where('user_id', $uid)->first();

        if (!$campaign) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found or unauthorized!"
            ], 401);
        }

        // Delete campaign (updates in campaigns.updates go with it)
        DB::table('campaigns')->where('id', $fund_id)->where('user_id', $uid)->delete();

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Deleted Successfully!!"
        ]);
    }

    /**
     * Edit Fund
     */
    public function editFund(Request $request): JsonResponse
    {
        if (empty($request->input('cat_id')) || empty($request->input('title')) || empty($request->input('fund_for')) || empty($request->input('fund_amt'))) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $cat_id = strip_tags($this->h->real_string($request->input('cat_id')));
        $title = strip_tags($this->h->real_string($request->input('title')));
        $fund_for = strip_tags($this->h->real_string($request->input('fund_for')));
        $fund_amt = strip_tags($this->h->real_string($request->input('fund_amt')));
        $full_address = strip_tags($this->h->real_string($request->input('full_address', '')));
        $lats = strip_tags($this->h->real_string($request->input('lats', '')));
        $longs = strip_tags($this->h->real_string($request->input('longs', '')));
        $fund_story = strip_tags($this->h->real_string($request->input('fund_story', '')));
        $exp_date = strip_tags($this->h->real_string($request->input('exp_date', '')));
        $patient_title = strip_tags($this->h->real_string($request->input('patient_title', '')));
        $patient_diagnosis = strip_tags($this->h->real_string($request->input('patient_diagnosis', '')));
        $fund_plan = strip_tags($this->h->real_string($request->input('fund_plan', '')));
        $status = $request->input('status', 'Pending');
        $record_id = $request->input('record_id');
        $imlist = $request->input('imlist', '0');
        $imlists = $request->input('imlists', '0');
        $imlistss = $request->input('imlistss', '0');
        $uid = $request->input('uid');

        $fundsize = (int) $request->input('fundsize', 0);
        $petientsize = (int) $request->input('petientsize', 0);
        $certicatesize = (int) $request->input('certicatesize', 0);

        $multifile = '';
        $multifiles = '';
        $multifiless = '';

        if ($fundsize > 0) {
            $uploadedFiles = $this->processFileUploads('fundpic', $fundsize, '/images/fund_photo/');
            $multifile = implode('$;', $uploadedFiles);
        }

        if ($petientsize > 0) {
            $uploadedFiless = $this->processFileUploads('petpic', $petientsize, '/images/pet_photo/');
            $multifiles = implode('$;', $uploadedFiless);
        }

        if ($certicatesize > 0) {
            $uploadedFilesss = $this->processFileUploads('certpic', $certicatesize, '/images/fund_certificate/');
            $multifiless = implode('$;', $uploadedFilesss);
        }

        // Handle image lists
        $imageList = '';
        if (empty($_FILES['fundpic0']['name'][0]) && $imlist != "0") {
            $imageList = $imlist;
        } elseif (empty($_FILES['fundpic0']['name'][0]) && $imlist == "0") {
            $imageList = $imlist;
        } elseif ($imlist == "0") {
            $imageList = $multifile;
        } else {
            $imageList = $imlist . '$;' . $multifile;
        }

        $imageLists = '';
        if (empty($_FILES['petpic0']['name'][0]) && $imlists != "0") {
            $imageLists = $imlists;
        } elseif (empty($_FILES['petpic0']['name'][0]) && $imlists == "0") {
            $imageLists = $imlists;
        } elseif ($imlists == "0") {
            $imageLists = $multifiles;
        } else {
            $imageLists = $imlists . '$;' . $multifiles;
        }

        $imageListss = '';
        if (empty($_FILES['certpic0']['name'][0]) && $imlistss != "0") {
            $imageListss = $imlistss;
        } elseif (empty($_FILES['certpic0']['name'][0]) && $imlistss == "0") {
            $imageListss = $imlistss;
        } elseif ($imlistss == "0") {
            $imageListss = $multifiless;
        } else {
            $imageListss = $imlistss . '$;' . $multifiless;
        }

        // Use campaigns table instead of tbl_fund
        // Check if campaign belongs to user
        $campaign = DB::table('campaigns')->where('id', $record_id)->where('user_id', $uid)->first();
        
        if (!$campaign) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found or unauthorized!"
            ], 401);
        }

        // Build gallery from all image types
        $gallery = [];
        if (!empty($imageList) && $imageList != "0") {
            $fundPhotos = explode('$;', $imageList);
            $gallery = array_merge($gallery, $fundPhotos);
        }
        if (!empty($imageLists) && $imageLists != "0") {
            $patientPhotos = explode('$;', $imageLists);
            $gallery = array_merge($gallery, $patientPhotos);
        }
        if (!empty($imageListss) && $imageListss != "0") {
            $certPhotos = explode('$;', $imageListss);
            $gallery = array_merge($gallery, $certPhotos);
        }
        
        // First photo is main image
        $mainImage = !empty($gallery) ? $gallery[0] : $campaign->image;
        
        // Map status: 'Pending' -> 2, 'Approved' -> 1, 'Cancelled' -> 0
        $campaignStatus = 2; // default to pending
        if ($status == 'Approved') {
            $campaignStatus = 1;
        } elseif ($status == 'Cancelled' || $status == 'Rejected') {
            $campaignStatus = 0;
        }
        
        // Generate new slug if title changed
        $slug = $campaign->slug;
        if ($title != $campaign->name) {
            $newSlug = \Illuminate\Support\Str::slug($title);
            // Check if slug already exists (excluding current campaign)
            $slugExists = DB::table('campaigns')->where('slug', $newSlug)->where('id', '!=', $record_id)->exists();
            $slug = $slugExists ? $newSlug . '-' . $record_id : $newSlug;
        }

        // Prepare update data for campaigns table
        $updateData = [
            'category_id' => $cat_id,
            'name' => $title,
            'slug' => $slug,
            'description' => $fund_story,
            'image' => $mainImage,
            'gallery' => !empty($gallery) ? json_encode($gallery) : null,
            'goal_amount' => $fund_amt,
            'location' => $full_address,
            'status' => $campaignStatus,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        // Add end_date if provided
        if (!empty($exp_date)) {
            $updateData['end_date'] = $exp_date;
        }

        // Update campaign
        DB::table('campaigns')
            ->where('id', $record_id)
            ->where('user_id', $uid)
            ->update($updateData);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Update Successfully Wait For Approval!!!!!"
        ]);
    }

    /**
     * Upload from PHP $_FILES (fallback when Laravel Request file bag is not populated).
     * Handles both single file (main_img) and array form (main_img[]).
     */
    private function uploadFromPhpFiles(string $key, string $url): ?string
    {
        if (!isset($_FILES[$key])) {
            return null;
        }
        $fu = $_FILES[$key];
        $err = is_array($fu['error'] ?? null) ? ($fu['error'][0] ?? UPLOAD_ERR_NO_FILE) : ($fu['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($err !== UPLOAD_ERR_OK) {
            return null;
        }
        $name = is_array($fu['name'] ?? null) ? ($fu['name'][0] ?? '') : ($fu['name'] ?? '');
        $tmpName = is_array($fu['tmp_name'] ?? null) ? ($fu['tmp_name'][0] ?? '') : ($fu['tmp_name'] ?? '');
        if (!is_uploaded_file($tmpName)) {
            return null;
        }
        $basePath = base_path('public');
        $targetPath = $basePath . $url;
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $ext = pathinfo($name, PATHINFO_EXTENSION) ?: 'jpg';
        $ext = preg_replace('/[^a-zA-Z0-9]/', '', $ext) ?: 'jpg';
        $newName = uniqid() . date('YmdHis') . mt_rand() . '.' . $ext;
        if (!move_uploaded_file($tmpName, $targetPath . $newName)) {
            return null;
        }
        return ltrim($url, '/') . $newName;
    }

    private function uploadErrorMessage(int $code): string
    {
        $messages = [
            UPLOAD_ERR_INI_SIZE => 'Image is too large (server limit).',
            UPLOAD_ERR_FORM_SIZE => 'Image is too large.',
            UPLOAD_ERR_PARTIAL => 'Image was only partially uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Server upload error (no temp directory).',
            UPLOAD_ERR_CANT_WRITE => 'Server could not save the image.',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by server extension.',
        ];
        return $messages[$code] ?? 'Image upload failed.';
    }

    /**
     * Upload a single image (e.g. main_img) and return stored path relative to public.
     */
    private function uploadSingleImage($file, $url)
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        $basePath = base_path('public');
        $targetPath = $basePath . $url;
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $newName = uniqid() . date('YmdHis') . mt_rand() . '.' . $ext;
        $file->move($targetPath, $newName);
        return ltrim($url, '/') . $newName;
    }

    /**
     * Upload from UploadedFile using getRealPath() (when isValid() is false but tmp file exists, e.g. Postman/curl).
     */
    private function uploadSingleImageFromPath($file, string $url): ?string
    {
        $path = $file ? $file->getRealPath() : null;
        if (!$path || !file_exists($path) || !is_readable($path)) {
            return null;
        }
        $realPath = realpath($path);
        $tempDir = realpath(sys_get_temp_dir()) ?: sys_get_temp_dir();
        if (!is_uploaded_file($path) && (!$realPath || strpos($realPath, $tempDir) !== 0)) {
            return null;
        }
        $basePath = base_path('public');
        $targetPath = $basePath . $url;
        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $newName = uniqid() . date('YmdHis') . mt_rand() . '.' . $ext;
        if (!@rename($file->getRealPath(), $targetPath . $newName)) {
            if (!@copy($file->getRealPath(), $targetPath . $newName)) {
                return null;
            }
            @unlink($file->getRealPath());
        }
        return ltrim($url, '/') . $newName;
    }

    /**
     * Process file uploads
     */
    private function processFileUploads($prefix, $count, $url)
    {
        $basePath = base_path('public');
        $targetPath = $basePath . $url;

        if (!is_dir($targetPath)) {
            mkdir($targetPath, 0755, true);
        }

        $uploadedFiles = [];

        for ($i = 0; $i < $count; $i++) {
            $fileKey = $prefix . $i;
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $newName = uniqid() . date('YmdHis') . mt_rand() . '.jpg';
                $fileUrl = ltrim($url, '/') . $newName;
                $uploadedFiles[] = $fileUrl;

                move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath . $newName);
            }
        }

        return $uploadedFiles;
    }
}

