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
        $getfundlist = $this->h->queryfire("select group_concat(`id`) as fundlist from tbl_fund where uid=" . $uid . "")->fetch_assoc();
        $fund_id = $getfundlist['fundlist'];

        if (empty($fund_id)) {
            return response()->json([
                "ResponseCode" => "401",
                "Result" => "true",
                "ResponseMsg" => "No Donation Found!!",
                "activitylist" => []
            ], 401);
        } else {
            $actionlist = $this->h->queryfire("SELECT * FROM `tbl_deposit` where fund_id IN(" . $fund_id . ") order by deposite_date desc");
            $zol = array();
            while ($row = $actionlist->fetch_assoc()) {
                $fundlist = $this->h->queryfire("select * from tbl_fund where id=" . $row['fund_id'] . "")->fetch_assoc();
                $udata = $this->h->queryfire("select * from tbl_user where id=" . $row["uid"] . "")->fetch_assoc();

                $pol = [
                    'donator_id' => $udata['id'],
                    'fund_title' => $fundlist['title'],
                    'profile_pic' => $udata['profile_pic'] ?? 'images/default.png',
                    'name' => ($row['is_anonymous'] == 1) ? 'Anonymous' : $udata['name'],
                    'donation_amt' => $row['amt']
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

