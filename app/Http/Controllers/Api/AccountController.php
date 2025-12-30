<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AccountController extends BaseApiController
{
    /**
     * Delete Account
     */
    public function deleteAccount(Request $request): JsonResponse
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

        $table = "tbl_user";
        $field = ["status" => '0'];
        $where = "where id=" . $uid . "";

        $check = $this->h->updateData_Api($field, $table, $where);

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Account Delete Successfully!!"
        ]);
    }
}

