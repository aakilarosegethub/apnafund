<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ActivityController extends BaseApiController
{
    /**
     * Get Activity List
     */
    public function activityList(Request $request): JsonResponse
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
        // Use campaigns table instead of tbl_fund
        $getfundlist = $this->h->queryfire("select group_concat(`id`) as fundlist from campaigns where user_id=" . $uid . "")->fetch_assoc();
        $fund_id = $getfundlist['fundlist'];

        if (empty($fund_id)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "true",
                "ResponseMsg" => "No Donation Found!!",
                "activitylist" => []
            ], 401);
        } else {
            // Use deposits table with campaign_id (not fund_id)
            $actionlist = $this->h->queryfire("SELECT * FROM `deposits` where campaign_id IN(" . $fund_id . ") order by created_at desc");
            $zol = array();
            while ($row = $actionlist->fetch_assoc()) {
                // Use campaigns table instead of tbl_fund
                $fundlist = $this->h->queryfire("select * from campaigns where id=" . $row['campaign_id'] . "")->fetch_assoc();
                // Use users table instead of tbl_user
                $udata = $this->h->queryfire("select * from users where id=" . $row["user_id"] . "")->fetch_assoc();

                $pol = [
                    'donator_id' => $udata['id'],
                    'fund_title' => $fundlist['name'] ?? '', // campaigns table uses 'name' not 'title'
                    'profile_pic' => $udata['image'] ?? 'images/default.png', // users table uses 'image' not 'profile_pic'
                    'name' => ($row['is_anonymous'] == 1) ? 'Anonymous' : ($udata['name'] ?? ($udata['firstname'] ?? '' . ' ' . $udata['lastname'] ?? '')),
                    'donation_amt' => $row['amount'] ?? 0 // deposits table uses 'amount' not 'amt'
                ];
                $zol[] = $pol;
            }

            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "activity list get Successfully!!!",
                "activitylist" => $zol
            ]);
        }
    }
}

