<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserController extends BaseApiController
{
    /**
     * Edit Profile
     */
    public function editProfile(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        $uid = (int) $uid;

        $userQuery = $this->h->queryfire(
            "select id, firstname, lastname, email, mobile, country_code from users where id=" . $uid . " limit 1"
        );
        if (!$userQuery || $userQuery->num_rows === 0) {
            return response()->json([
                "ResponseCode" => "404",
                "Result" => "false",
                "ResponseMsg" => "User not found!"
            ], 404);
        }
        $current = $userQuery->fetch_assoc();

        $field = [];

        if (array_key_exists('name', $data)) {
            $fname = trim(strip_tags($this->h->real_string((string) $data['name'])));
            if ($fname !== '') {
                $field['firstname'] = $fname;
            }
        }
        if (array_key_exists('email', $data)) {
            $email = trim(strip_tags($this->h->real_string((string) $data['email'])));
            if ($email !== '') {
                $field['email'] = $email;
            }
        }
        if (array_key_exists('mobile', $data)) {
            $mobile = trim(strip_tags($this->h->real_string((string) $data['mobile'])));
            if ($mobile !== '') {
                $field['mobile'] = $mobile;
            }
        }
        if (array_key_exists('ccode', $data)) {
            $ccode = trim(strip_tags($this->h->real_string((string) $data['ccode'])));
            if ($ccode !== '') {
                $field['country_code'] = $ccode;
            }
        }
        if (array_key_exists('password', $data)) {
            $password = strip_tags($this->h->real_string((string) $data['password']));
            if ($password !== '') {
                $field['password'] = \Hash::make($password);
            }
        }

        if ($field === []) {
            return response()->json([
                "ResponseCode" => "400",
                "Result" => "false",
                "ResponseMsg" => "No fields to update. Send at least one of: name, email, mobile, ccode, password (non-empty values).",
            ], 400);
        }

        $newEmail = $field['email'] ?? $current['email'];
        $newMobile = $field['mobile'] ?? $current['mobile'];

        if (isset($field['email']) && $newEmail !== $current['email']) {
            $checkemail = $this->h->queryfire(
                "select * from users where email='" . $this->h->real_string($newEmail) . "' and id!=" . $uid . ""
            );
            if ($checkemail && $checkemail->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Email Address Already Used!"
                ], 401);
            }
        }

        if (isset($field['mobile']) && $newMobile !== $current['mobile']) {
            $checkmob = $this->h->queryfire(
                "select * from users where mobile='" . $this->h->real_string($newMobile) . "' and id!=" . $uid . ""
            );
            if ($checkmob && $checkmob->num_rows != 0) {
                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Mobile Number Already Used!"
                ], 401);
            }
        }

        $table = "users";
        $where = "where id=" . $uid . "";

        $check = $this->h->updateData_Api($field, $table, $where);

        // Fetch updated user data with proper error handling (format same as login API)
        $userQuery = $this->h->queryfire("select id, firstname, lastname, email, mobile, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where id=" . $uid . "");
        if (!$userQuery) {
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "Error fetching user data!"
            ], 500);
        }
        
        $c = $userQuery->fetch_assoc();
        if (!$c) {
            return response()->json([
                "ResponseCode" => "404",
                "Result" => "false",
                "ResponseMsg" => "User not found!"
            ], 404);
        }
        
        // Combine firstname and lastname as name for API response
        $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
        // Format rdate if needed
        if (isset($c['rdate'])) {
            $c['rdate'] = date("Y-m-d H:i:s", strtotime($c['rdate']));
        }
        // Remove password from response
        unset($c['password']);
        
        // Try to get settings, but don't fail if table doesn't exist
        $set = $this->h->fetchData("SELECT * FROM `settings` LIMIT 1");
        if (!$set) {
            // Try alternative table name
            $set = $this->h->fetchData("SELECT * FROM `tbl_setting` LIMIT 1");
        }
        $currency = '';
        if ($set) {
            $currency = $set['currency'] ?? $set['site_currency'] ?? '';
        }

        return response()->json([
            "UserLogin" => $c,
            "currency" => $currency,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Profile Update Successfully!"
        ]);
    }

    /**
     * Upload Profile Image
     */
    public function uploadProfileImage(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['img'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }
        
        $uid = $this->h->real_string($uid);
        $img = $data['img'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $imageData = base64_decode($img);
        $path = 'images/profile/' . uniqid() . '.png';
        $fname = base_path('public/' . $path);

        // Create directory if not exists
        $dir = dirname($fname);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($fname, $imageData);

        // Update profile image in users table (image field)
        $table = "users";
        $field = array('image' => $path);
        $where = "where id=" . $uid . "";
        $check = $this->h->updateData_Api($field, $table, $where);
        
        // Fetch updated user data with proper error handling (format same as login API)
        $userQuery = $this->h->queryfire("select id, firstname, lastname, email, mobile, country_code as ccode, status, created_at as rdate, balance as wallet, image as profile_pic from users where id=" . $uid . "");
        if (!$userQuery) {
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "Error fetching user data!"
            ], 500);
        }
        
        $c = $userQuery->fetch_assoc();
        if (!$c) {
            return response()->json([
                "ResponseCode" => "404",
                "Result" => "false",
                "ResponseMsg" => "User not found!"
            ], 404);
        }
        
        // Combine firstname and lastname as name for API response
        $c['name'] = trim(($c['firstname'] ?? '') . ' ' . ($c['lastname'] ?? ''));
        // Format rdate if needed
        if (isset($c['rdate'])) {
            $c['rdate'] = date("Y-m-d H:i:s", strtotime($c['rdate']));
        }
        // Remove password from response
        unset($c['password']);

        return response()->json([
            "UserLogin" => $c,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Profile Image Upload Successfully!!"
        ]);
    }

    /**
     * Update Wallet
     */
    public function updateWallet(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['wallet'])) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Something Went Wrong!"
            ], 401);
        }

        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }

        $wallet = strip_tags($this->h->real_string($data['wallet']));
        $uid = strip_tags($this->h->real_string($uid));
        $checkimei = $this->h->queryfire("select * from tbl_user where `id`=" . $uid . "")->num_rows;

        if ($checkimei != 0) {
            $vp = $this->h->queryfire("select * from tbl_user where id=" . $uid . "")->fetch_assoc();

            $table = "tbl_user";
            $field = array('wallet' => $vp['wallet'] + $wallet);
            $where = "where id=" . $uid . "";
            $check = $this->h->updateData_Api($field, $table, $where);

            $timestamp = date("Y-m-d H:i:s");
            $timestamps = date("Y-m-d");
            $table = "wallet_report";
            $field_values = array("uid", "message", "status", "amt", "tdate");
            $data_values = array($uid, 'Wallet Balance Added!!', 'Credit', $wallet, $timestamps);

            $checks = $this->h->insertData_Api($field_values, $data_values, $table);

            $wallet = $this->h->queryfire("select * from tbl_user where id=" . $uid . "")->fetch_assoc();
            return response()->json([
                "wallet" => $wallet['wallet'],
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Wallet Update successfully!"
            ]);
        } else {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "User Deactivate By Admin!!!!"
            ], 401);
        }
    }

    /**
     * Get Balance
     */
    public function getBalance(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user
        $uid = $this->getUserId($request);
        
        if (empty($uid)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Unauthorized! Please login first."
            ], 401);
        }
        $fundlist = $this->h->queryfire("SELECT COALESCE(GROUP_CONCAT(id), '0') AS fund_list 
            FROM `tbl_fund` 
            WHERE uid = " . $uid . " AND fund_status='Completed'")->fetch_assoc();
        $sales = $this->h->queryfire("SELECT sum(amt) as full_total  
            FROM `tbl_deposit` 
            WHERE fund_id IN(" . $fundlist['fund_list'] . ")")->fetch_assoc();

        $balance = empty($sales['full_total']) ? 0 : $sales['full_total'];

        $bs = 0;
        $payout = $this->h->queryfire("select COALESCE(sum(amt),0) as full_payout from payout_setting where uid=" . $uid . "")->fetch_assoc();
        $finalpayout = empty($payout['full_payout']) ? 0 : $payout['full_payout'];

        if ($sales['full_total'] != '') {
            $bs = number_format((float)($balance) - $finalpayout, 2, '.', '');
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Balance Get Successfully!!",
            "Total_Fund" => floatval($bs),
            "Total_Withdraw" => floatval($payout['full_payout'])
        ]);
    }
}

