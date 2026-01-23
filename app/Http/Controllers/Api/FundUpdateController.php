<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class FundUpdateController extends BaseApiController
{
    /**
     * Add Fund Update
     */
    public function fundUpdate(Request $request): JsonResponse
    {
        if (empty($request->input('fund_id')) || empty($request->input('description'))) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $fund_id = strip_tags($this->h->real_string($request->input('fund_id')));
        $description = strip_tags($this->h->real_string($request->input('description')));
        
        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }
        $size = (int) $request->input('size', 0);
        $timestamp = date("Y-m-d H:i:s");

        if ($size > 0) {
            $uploadedFiles = $this->processFileUploads('fundupdate', $size, '/images/fund_update/');
            $multifile = implode('$;', $uploadedFiles);

            $table = "fund_update";
            $field_values = ["fund_id", "uid", "photo", "update_desc", "update_date"];
            $data_values = [$fund_id, $uid, $multifile, $description, $timestamp];
        } else {
            $table = "fund_update";
            $field_values = ["fund_id", "uid", "update_desc", "update_date"];
            $data_values = [$fund_id, $uid, $description, $timestamp];
        }

        $check = $this->h->insertData_Api($field_values, $data_values, $table);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Update Send Successfully!!"
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

        $fund_id = $data['fund_id'];
        
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

        $fund_id = $data['fund_id'];
        
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

