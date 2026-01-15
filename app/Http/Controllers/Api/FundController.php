<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class FundController extends BaseApiController
{
    /**
     * Get Fund List
     */
    public function fundList(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user (token required)
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthenticated! Please provide a valid token."
            ], 401);
        }

        $status = $data['status'] ?? 'Pending';

        // Map old status values to campaigns table status
        // campaigns table: status = 2 (pending), status = 1 (approved), status = 0 (rejected/cancelled)
        // For 'Completed', we check if end_date has passed or raised_amount >= goal_amount
        if ($status == 'Pending') {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 2");
        } else if ($status == 'Cancelled') {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 0");
        } else {
            // Completed: either status is approved and (end_date passed or raised_amount >= goal_amount)
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND status = 1 AND (end_date < CURDATE() OR raised_amount >= goal_amount)");
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund list Successfully!!!",
                "fundlist" => []
            ]);
        }

        $c = [];
        while ($row = $sel->fetch_assoc()) {
            if (!$row) break;

            // Get charity info if exists (check if tbl_charity table exists)
            $charity_name = "";
            $charity_tinno = "";
            $charity_img = "";
            
            // Use helper function to format campaign data (gallery handling is done in helper)
            $pol = $this->formatCampaignData($row, [
                'charity_name' => $charity_name,
                'charity_tinno' => $charity_tinno,
                'charity_img' => $charity_img,
                'patient_photo' => [], // Not in campaigns table
                'fund_for' => '' // Not in campaigns table
            ]);
            
            // Override specific fields for fundList
            $pol['lats'] = '';
            $pol['longs'] = '';
            $pol['patient_title'] = '';
            $pol['patient_diagnosis'] = '';
            $pol['fund_plan'] = '';
            $pol['medical_certificate'] = [];
            $pol['reject_comment'] = '';
            
            $c[] = $pol;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "fund list Successfully!!!",
            "fundlist" => $c
        ]);
    }


    /**
     * Get Fund by ID
     */
    public function fundById(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user (token only)
        $uid = null;
        if (auth()->check()) {
            $uid = auth()->user()->id;
        }
        
        if (empty($data['fund_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $fund_id = $data['fund_id'];
        $status = $data['status'] ?? 'Home';

        // If status is not 'Home', require authentication
        if ($status != 'Home' && empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        // Use campaigns table instead of tbl_fund
        if ($status == 'Home') {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE id=" . (int)$fund_id . "");
        } else {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE user_id=" . (int)$uid . " AND id=" . (int)$fund_id . "");
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund Data Get Successfully!!!",
                "funddata" => [],
                "fundupdate" => []
            ]);
        }

        $c = [];
        while ($row = $sel->fetch_assoc()) {
            if (!$row) break;

            // Get charity info if exists (check if tbl_charity table exists)
            $charity_name = "";
            $charity_tinno = "";
            $charity_img = "";

            // Get total donaters count
            $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$row['id'] . " AND status = 1");
            $donatersResult = $funded ? $funded->fetch_assoc() : null;
            $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;

            // Use helper function to format campaign data (gallery handling is done in helper)
            $pol = $this->formatCampaignData($row, [
                'charity_name' => $charity_name,
                'charity_tinno' => $charity_tinno,
                'charity_img' => $charity_img,
                'patient_photo' => ['images/default.png'],
                'fund_for' => '', // Not in campaigns table
                'status' => $row['status'] ?? 0,
                'total_donaters' => $total_donaters,
                'donaterlist' => $this->getDonaterList($row['id'], true)
            ]);

            // Override specific fields for fundById
            $pol['lats'] = '';
            $pol['longs'] = '';
            $pol['patient_title'] = '';
            $pol['patient_diagnosis'] = '';
            $pol['fund_plan'] = '';
            $pol['medical_certificate'] = [];
            $pol['reject_comment'] = '';
            $pol['remain_amt'] = sprintf("%.2f", $pol['remain_amt']);
            
            $c[] = $pol;
        }

        // Get fund updates (check if fund_update table exists with campaign_id)
        $up = $this->h->queryfire("SELECT * FROM fund_update WHERE fund_id=" . (int)$fund_id . " OR campaign_id=" . (int)$fund_id . " ORDER BY update_date DESC");
        $lop = [];
        if ($up) {
            while ($updateRow = $up->fetch_assoc()) {
                if (!$updateRow) break;
            $ko = [
                    "id" => $updateRow["id"],
                    "photo" => empty($updateRow["photo"]) ? [] : (is_string($updateRow["photo"]) ? explode('$;', $updateRow["photo"]) : (is_array($updateRow["photo"]) ? $updateRow["photo"] : [])),
                    "update_desc" => $updateRow["update_desc"] ?? '',
                    "update_date" => $updateRow["update_date"] ?? ''
            ];
            $lop[] = $ko;
            }
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "fund Data Get Successfully!!!",
            "funddata" => $c,
            "fundupdate" => $lop
        ]);
    }

    /**
     * Create Fund Raise
     */
    public function fundRaise(Request $request): JsonResponse
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
        $charity_id = $request->input('charity_id', '');
        
        // Get user ID from authenticated user (token only)
        $uid = null;
        if (auth()->check()) {
            $uid = auth()->user()->id;
        }
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first. Token required."
            ], 401);
        }

        $fundsize = (int) $request->input('fundsize', 0);
        $petientsize = (int) $request->input('petientsize', 0);
        $certicatesize = (int) $request->input('certicatesize', 0);
        $fund_date = date("Y-m-d");

        $multifile = '';
        $multifiles = '';
        $multifiless = '';

        // Process fund photos
        if ($fundsize > 0) {
            $uploadedFiles = $this->processFileUploads('fundpic', $fundsize, '/images/fund_photo/');
            $multifile = implode('$;', $uploadedFiles);
        }

        // Process patient photos
        if ($petientsize > 0) {
            $uploadedFiless = $this->processFileUploads('petpic', $petientsize, '/images/pet_photo/');
            $multifiles = implode('$;', $uploadedFiless);
        }

        // Process certificate photos
        if ($certicatesize > 0) {
            $uploadedFilesss = $this->processFileUploads('certpic', $certicatesize, '/images/fund_certificate/');
            $multifiless = implode('$;', $uploadedFilesss);
        }

        $table = "tbl_fund";
        if (empty($exp_date)) {
            $field_values = ["cat_id", "title", "fund_for", "fund_amt", "fund_story", "fund_photos", "fund_date", "patient_photo", "patient_title", "patient_diagnosis", "fund_plan", "medical_certificate", "uid", "status", "charity_id", "longs", "lats", "full_address"];
            $data_values = [$cat_id, $title, $fund_for, $fund_amt, $fund_story, $multifile, $fund_date, $multifiles, $patient_title, $patient_diagnosis, $fund_plan, $multifiless, $uid, $status, $charity_id, $longs, $lats, $full_address];
        } else {
            $field_values = ["cat_id", "title", "fund_for", "fund_amt", "fund_story", "fund_photos", "exp_date", "fund_date", "patient_photo", "patient_title", "patient_diagnosis", "fund_plan", "medical_certificate", "uid", "status", "charity_id", "longs", "lats", "full_address"];
            $data_values = [$cat_id, $title, $fund_for, $fund_amt, $fund_story, $multifile, $exp_date, $fund_date, $multifiles, $patient_title, $patient_diagnosis, $fund_plan, $multifiless, $uid, $status, $charity_id, $longs, $lats, $full_address];
        }

        $check = $this->h->insertData_Api($field_values, $data_values, $table);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Raise Submited Wait For Approval!!"
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
            if ($_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $newName = uniqid() . date('YmdHis') . mt_rand() . '.jpg';
                $fileUrl = ltrim($url, '/') . $newName;
                $uploadedFiles[] = $fileUrl;

                move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetPath . $newName);
            }
        }

        return $uploadedFiles;
    }

    /**
     * Category Wise Fund
     */
    public function categoryWiseFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['cat_id'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $cat_id = (int)$data['cat_id'];
        $timestamp = date("Y-m-d");

        // Use campaigns table instead of tbl_fund
        // campaigns table: status = 1 (approved), status = 2 (pending), status = 0 (rejected)
        // For public API, show only approved campaigns (status = 1)
        if ($cat_id != 0) {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE category_id=" . $cat_id . " AND status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY id DESC");
        } else {
            $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY id DESC");
        }

        // Check if query was successful
        if (!$sel) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Home Data Get Successfully!!!",
                "catwisefund" => []
            ]);
        }

        $cp = [];
        while ($rows = $sel->fetch_assoc()) {
            if (!$rows) break;
            
            // Use helper function to format campaign data
            $fundData = $this->formatCampaignData($rows);
            
                $cp[] = $fundData;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Home Data Get Successfully!!!",
            "catwisefund" => $cp
        ]);
    }

    /**
     * Search Fund
     */
    public function searchFund(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['keyword'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        $keyword = $this->h->real_string($data['keyword']);
        $timestamp = date("Y-m-d");

        // Search in campaigns table
        // campaigns table: status = 1 (approved), status = 2 (pending), status = 0 (rejected)
        // Search in name (title), description (fund_story), and location fields
        $keywordEscaped = $this->h->real_string($keyword); // Already escaped by real_string
        $selpop = $this->h->queryfire("SELECT * FROM campaigns 
            WHERE status = 1 
            AND (end_date IS NULL OR end_date >= '" . $timestamp . "')
            AND (
                name LIKE '%" . $keywordEscaped . "%' 
                OR description LIKE '%" . $keywordEscaped . "%'
                OR location LIKE '%" . $keywordEscaped . "%'
            )
            ORDER BY id DESC");

        // Check if query was successful
        if (!$selpop) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Fund Data Get Successfully!!!",
                "fundlist" => []
            ]);
        }

        $listnearby = [];
        while ($pop = $selpop->fetch_assoc()) {
            if (!$pop) break;
            
            // Use helper function to format campaign data
            $fundData = $this->formatCampaignData($pop, [
                'patient_photo' => $pop['image'] ?? ''
            ]);
            
            $listnearby[] = $fundData;
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Fund Data Get Successfully!!!",
            "fundlist" => $listnearby
        ]);
    }
}

