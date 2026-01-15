<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DonateController extends BaseApiController
{
    /**
     * Donate Now
     */
    public function donateNow(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['fund_id']) || empty($data['amt']) || empty($data['tip']) || empty($data['payment_method_id'])) {
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

        $fund_id = strip_tags($this->h->real_string($data['fund_id']));
        $uid = strip_tags($this->h->real_string($uid));
        $amt = strip_tags($this->h->real_string($data['amt']));
        $wall_amt = strip_tags($this->h->real_string($data['wall_amt'] ?? 0));
        $tip = strip_tags($this->h->real_string($data['tip']));
        $payment_method_id = $data['payment_method_id'];
        $transaction_id = $data['transaction_id'] ?? '';
        $platform_fees = $data['platform_fees'] ?? 0;
        $is_anonymous = $data['is_anonymous'] ?? 0;

        $set = $this->h->fetchData("SELECT * FROM `tbl_setting`");
        $deposite_date = date("Y-m-d H:i:s");

        // Get total deposits for campaign (using deposits table with campaign_id)
        $checkdepositeQuery = $this->h->queryfire("SELECT COALESCE(SUM(`amount`), 0) AS total_fund from deposits where campaign_id=" . $fund_id . "");
        if (!$checkdepositeQuery) {
            return response()->json([
                "ResponseCode" => "500",
                "Result" => "false",
                "ResponseMsg" => "Error fetching deposit data!"
            ], 500);
        }
        $checkdeposite = $checkdepositeQuery->fetch_assoc();
        if (!$checkdeposite) {
            $checkdeposite = ['total_fund' => 0];
        }

        // Get campaign details (using campaigns table)
        $fundrequireQuery = $this->h->queryfire("SELECT * from campaigns where id=" . $fund_id . "");
        if (!$fundrequireQuery) {
            return response()->json([
                "ResponseCode" => "404",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found!"
            ], 404);
        }
        $fundrequire = $fundrequireQuery->fetch_assoc();
        if (!$fundrequire) {
            return response()->json([
                "ResponseCode" => "404",
                "Result" => "false",
                "ResponseMsg" => "Campaign not found!"
            ], 404);
        }

        // Use goal_amount from campaigns table
        $goal_amount = $fundrequire['goal_amount'] ?? $fundrequire['target_amount'] ?? 0;
        $remain_amt = $goal_amount - $checkdeposite['total_fund'];

        if ($amt > $remain_amt) {
            if ($remain_amt <= 0.01) {
                // Update campaign status to completed (using campaigns table)
                $table = "campaigns";
                $field = array('status' => 1); // 1 = approved/completed
                $where = "where id=" . $fund_id . "";
                $check = $this->h->updateData_Api($field, $table, $where);

                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "fund already completed"
                ], 401);
            } else {
                // Refund to wallet (using users table)
                $vpQuery = $this->h->queryfire("select balance as wallet from users where id=" . $uid . "");
                if (!$vpQuery) {
                    return response()->json([
                        "ResponseCode" => "500",
                        "Result" => "false",
                        "ResponseMsg" => "Error fetching user data!"
                    ], 500);
                }
                $vp = $vpQuery->fetch_assoc();
                if (!$vp) {
                    return response()->json([
                        "ResponseCode" => "404",
                        "Result" => "false",
                        "ResponseMsg" => "User not found!"
                    ], 404);
                }

                $table = "users";
                $field = array('balance' => ($vp['wallet'] ?? 0) + $amt);
                $where = "where id=" . $uid . "";
                $check = $this->h->updateData_Api($field, $table, $where);

                $timestamp = date("Y-m-d H:i:s");
                $timestamps = date("Y-m-d");
                $table = "wallet_report";
                $field_values = array("uid", "message", "status", "amt", "tdate");
                $data_values = array($uid, 'Deposite Refund Of Fund Id#' . $fund_id, 'Credit', $amt, $timestamps);

                $checks = $this->h->insertData_Api($field_values, $data_values, $table);

                return response()->json([
                    "ResponseCode" => "401",
                    "Result" => "false",
                    "ResponseMsg" => "Fund Exist Limit To Deposite Required Only now " . $remain_amt . $set['currency'] . ". So We Refund To Your Deposite To Wallet."
                ], 401);
            }
        } else {
            // Get user wallet balance (using users table)
            $vpQuery = $this->h->queryfire("select balance as wallet from users where id=" . $uid . "");
            if (!$vpQuery) {
                return response()->json([
                    "ResponseCode" => "500",
                    "Result" => "false",
                    "ResponseMsg" => "Error fetching user data!"
                ], 500);
            }
            $vp = $vpQuery->fetch_assoc();
            if (!$vp) {
                return response()->json([
                    "ResponseCode" => "404",
                    "Result" => "false",
                    "ResponseMsg" => "User not found!"
                ], 404);
            }

            if (($vp['wallet'] ?? 0) >= $wall_amt) {
                if ($wall_amt != 0) {
                    $mt = intval($vp['wallet'] ?? 0) - intval($wall_amt);
                    $table = "users";
                    $field = array('balance' => "$mt");
                    $where = "where id=" . $uid . "";

                    $check = $this->h->updateData_Api($field, $table, $where);
                    $timestamps = date("Y-m-d");
                    $table = "wallet_report";
                    $field_values = array("uid", "message", "status", "amt", "tdate");
                    $data_values = array($uid, 'Wallet Used in Order Id#' . $fund_id, 'Debit', $wall_amt, $timestamps);

                    $checks = $this->h->insertData_Api($field_values, $data_values, $table);
                }

                // Insert deposit (using deposits table with campaign_id and user_id)
                $table = "deposits";
                $field_values = ["campaign_id", "user_id", "amount", "status", "method_code", "trx"];
                $data_values = [$fund_id, $uid, $amt, 1, $payment_method_id, $transaction_id];
                $check = $this->h->insertData_Api($field_values, $data_values, $table);

                // Get campaign owner details (using campaigns table)
                $fundataQuery = $this->h->queryfire("SELECT user_id as uid from campaigns where id=" . $fund_id . "");
                if (!$fundataQuery) {
                    return response()->json([
                        "ResponseCode" => "500",
                        "Result" => "false",
                        "ResponseMsg" => "Error fetching campaign data!"
                    ], 500);
                }
                $fundata = $fundataQuery->fetch_assoc();
                if (!$fundata) {
                    return response()->json([
                        "ResponseCode" => "404",
                        "Result" => "false",
                        "ResponseMsg" => "Campaign not found!"
                    ], 404);
                }

                // Get campaign owner user data (using users table)
                $udataQuery = $this->h->queryfire("select firstname, lastname from users where id=" . $fundata["uid"] . "");
                if (!$udataQuery) {
                    $name = "User";
                } else {
                    $udata = $udataQuery->fetch_assoc();
                    $name = trim(($udata['firstname'] ?? '') . ' ' . ($udata['lastname'] ?? ''));
                }

                // Send OneSignal notification
                $currency = $set['currency'] ?? $set['site_currency'] ?? '';
                $content = array(
                    "en" => 'Some One Donate On your Fund#' . $fund_id . '. amount is ' . number_format($amt, 2) . $currency
                );
                $heading = array(
                    "en" => "Donation Done!!"
                );

                $fields = array(
                    'app_id' => $set['one_key'] ?? '',
                    'included_segments' => array("Active Users"),
                    'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $fundata["uid"])),
                    'contents' => $content,
                    'headings' => $heading
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic ' . ($set['one_hash'] ?? '')
                ));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);

                $timestamp = date("Y-m-d H:i:s");

                $title_main = "Donation Done!!";
                $description = 'Some One Donate On your Fund#' . $fund_id . '. amount is ' . number_format($amt, 2) . $currency;

                $table = "tbl_notification";
                $field_values = ["uid", "datetime", "title", "description"];
                $data_values = [$fundata["uid"], $timestamp, $title_main, $description];
                $this->h->insertData_Api($field_values, $data_values, $table);

                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "true",
                    "ResponseMsg" => "Deposite Done Successfully!"
                ]);
            } else {
                // Get wallet balance (using users table)
                $tbwalletQuery = $this->h->queryfire("select balance as wallet from users where id=" . $uid . "");
                if (!$tbwalletQuery) {
                    return response()->json([
                        "ResponseCode" => "500",
                        "Result" => "false",
                        "ResponseMsg" => "Error fetching wallet data!"
                    ], 500);
                }
                $tbwallet = $tbwalletQuery->fetch_assoc();
                return response()->json([
                    "ResponseCode" => "200",
                    "Result" => "false",
                    "ResponseMsg" => "Wallet Balance Not There As Per Fund Deposite Refresh One Time Screen!!!",
                    "wallet" => $tbwallet['wallet'] ?? 0
                ]);
            }
        }
    }

    /**
     * My Donate Fund List
     */
    public function myDonateFundList(Request $request): JsonResponse
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
        // Get user's deposits (using deposits table with user_id and campaign_id)
        $fund = $this->h->queryfire("SELECT * FROM `deposits` WHERE user_id=" . $uid . " AND status = 1");
        if (!$fund) {
            return response()->json([
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "fund list Successfully!!!",
                "fundlist" => []
            ]);
        }
        
        $c = [];

        while ($row = $fund->fetch_assoc()) {
            if (!$row) break;
            
            $campaign_id = $row['campaign_id'] ?? $row['fund_id'] ?? 0;
            if (!$campaign_id) continue;
            
            // Get campaign details (using campaigns table)
            $selQuery = $this->h->queryfire("select * from campaigns where id=" . $campaign_id . "");
            if (!$selQuery) continue;
            $sel = $selQuery->fetch_assoc();
            if (!$sel) continue;
            
            // Get total donation amount for this campaign by this user
            $donaQuery = $this->h->queryfire("select COALESCE(SUM(amount), 0) as total_amount from deposits WHERE campaign_id=" . $campaign_id . " and user_id=" . $uid . " AND status = 1");
            $dona = $donaQuery ? $donaQuery->fetch_assoc() : null;
            $total_donate = $dona ? ($dona['total_amount'] ?? 0) : 0;

            // Use helper function to format campaign data
            $pol = $this->formatCampaignData($sel, [
                'patient_photo' => ['images/default.png'],
                'fund_for' => '',
                'total_donate' => $total_donate
            ]);

            // Override specific fields for myDonateFundList
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

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "fund list Successfully!!!",
            "fundlist" => $c
        ]);
    }

}

