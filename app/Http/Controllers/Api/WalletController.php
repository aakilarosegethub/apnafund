<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WalletController extends BaseApiController
{
    /**
     * Get Wallet Report
     */
    public function walletReport(Request $request): JsonResponse
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

        $uid = strip_tags($this->h->real_string($uid));
        $checkimei = $this->h->queryfire("select * from tbl_user where `id`=" . $uid . "")->num_rows;

        if ($checkimei != 0) {
            $wallet = $this->h->queryfire("select * from tbl_user where id=" . $uid . "")->fetch_assoc();

            $sel = $this->h->queryfire("select message,status,amt from wallet_report where uid=" . $uid . " order by id desc");
            $myarray = array();
            $l = 0;
            $k = 0;
            $p = array();
            while ($row = $sel->fetch_assoc()) {
                if ($row['status'] == 'Credit') {
                    $l = $l + $row['amt'];
                } else {
                    $k = $k + $row['amt'];
                }
                $p['message'] = $row['message'];
                $p['status'] = $row['status'];
                $p['amt'] = $row['amt'];
                $myarray[] = $p;
            }

            return response()->json([
                "Walletitem" => $myarray,
                "wallet" => $wallet['wallet'],
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Wallet Report Get Successfully!"
            ]);
        } else {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "false",
                "ResponseMsg" => "Request To Update Own Device!!!!"
            ], 401);
        }
    }
}

