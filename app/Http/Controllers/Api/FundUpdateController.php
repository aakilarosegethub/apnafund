<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

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

        $table = "tbl_fund";
        $field = [
            "fund_status" => 'Cancelled',
            "reject_comment" => $reject_comment
        ];

        $where = "where id=" . $fund_id . " and uid=" . $uid . "";

        $check = $this->h->updateData_Api($field, $table, $where);

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

        $table = "tbl_fund";
        $field = [
            "fund_status" => 'Completed'
        ];

        $where = "where id=" . $fund_id . " and uid=" . $uid . "";

        $check = $this->h->updateData_Api($field, $table, $where);

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

        $table = "tbl_fund";
        if (empty($exp_date)) {
            $field = [
                "cat_id" => $cat_id,
                "title" => $title,
                "fund_for" => $fund_for,
                "fund_amt" => $fund_amt,
                "full_address" => $full_address,
                "lats" => $lats,
                "longs" => $longs,
                "fund_story" => $fund_story,
                "patient_title" => $patient_title,
                "patient_diagnosis" => $patient_diagnosis,
                "fund_plan" => $fund_plan,
                "status" => $status,
                "fund_photos" => $imageList,
                "patient_photo" => $imageLists,
                "medical_certificate" => $imageListss,
                'is_approve' => 0
            ];
        } else {
            $field = [
                "cat_id" => $cat_id,
                "title" => $title,
                "fund_for" => $fund_for,
                "fund_amt" => $fund_amt,
                "full_address" => $full_address,
                "lats" => $lats,
                "longs" => $longs,
                "fund_story" => $fund_story,
                "exp_date" => $exp_date,
                "patient_title" => $patient_title,
                "patient_diagnosis" => $patient_diagnosis,
                "fund_plan" => $fund_plan,
                "status" => $status,
                "fund_photos" => $imageList,
                "patient_photo" => $imageLists,
                "medical_certificate" => $imageListss,
                'is_approve' => 0
            ];
        }

        $where = "where uid=" . $uid . " and id=" . $record_id . "";

        $check = $this->h->updateData_Api($field, $table, $where);

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

