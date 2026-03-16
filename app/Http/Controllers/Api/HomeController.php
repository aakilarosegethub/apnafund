<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class HomeController extends BaseApiController
{

    /**
     * Calculate distance between two coordinates
     */
    protected function calculateDistance($originLat, $originLng, $destLat, $destLng, $apiKey)
    {
        $unit = "K";
        $theta = (float)$originLng - (float)$destLng;
        $dist = sin(deg2rad((float)$originLat)) * sin(deg2rad((float)$destLat)) + 
                cos(deg2rad((float)$originLat)) * cos(deg2rad((float)$destLat)) * cos(deg2rad((float)$theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            $distanceInKilometers = $miles * 1.609344;
            return round($distanceInKilometers, 2);
        } else if ($unit == "N") {
            $distanceInNauticalMiles = $miles * 0.8684;
            return round($distanceInNauticalMiles, 2);
        } else {
            return round($miles, 2);
        }
    }


    /**
     * Get home data
     */
    public function index(Request $request): JsonResponse
    {
        // Handle both JSON input and form data
        $data = $this->getRequestData($request);
        
        // Get user ID from authenticated user or fallback to request (uid can be 0 for guest)
        $uid = $this->getUserId($request) ?? 0;
        $lats = $data['lats'] ?? 0;
        $longs = $data['longs'] ?? 0;

        // Get settings from settings table
        $set = $this->h->fetchData("SELECT * FROM `settings` ORDER BY id DESC LIMIT 1");

        // Check user block status
        if ($uid == 0) {
            $is_block = "0";
            $check_user_verify = null;
        } else {
            $userResult = $this->h->queryfire("select * from users where id=" . (int)$uid . "");
            $check_user_verify = $userResult ? $userResult->fetch_assoc() : null;
            // Users table status: 1 = active, 0 = inactive (banned)
            $is_block = empty($check_user_verify["status"]) || $check_user_verify["status"] == 0 ? "1" : "0";
        }

        // Get categories from categories table using Laravel DB query
        $c = [];
        try {
            // Use raw SQL query to handle cases where status column might not exist
            // Try with status filter first, if it fails, get all categories
            try {
                $sel = $this->h->queryfire("SELECT * FROM categories WHERE (status = 'active' OR status = 1) ORDER BY id ASC");
            } catch (\Exception $e) {
                // If status column doesn't exist, get all categories
                $sel = $this->h->queryfire("SELECT * FROM categories ORDER BY id ASC");
            }
            
            if ($sel) {
                while ($row = $sel->fetch_assoc()) {
                    if (!$row) break;
                    $pol = [];
                    $pol['id'] = $row['id'];
                    // Use 'name' column from categories table, fallback to 'title' if exists
                    $pol['title'] = isset($row['name']) ? $row['name'] : (isset($row['title']) ? $row['title'] : '');
                    $c[] = $pol;
                }
            }
        } catch (\Exception $e) {
            // If there's an error, return empty array
            $c = [];
        }

        // Get banners from banners table using Laravel DB query
        $cps = [];
        try {
            // Use raw SQL query to handle cases where status column might not exist
            // Try with status filter first, if it fails, get all banners
            try {
                $sel = $this->h->queryfire("SELECT * FROM banners WHERE (status = 'active' OR status = 1) ORDER BY `order` ASC, id DESC LIMIT 5");
            } catch (\Exception $e) {
                // If status column doesn't exist, get all banners
                $sel = $this->h->queryfire("SELECT * FROM banners ORDER BY `order` ASC, id DESC LIMIT 5");
            }
            
            if ($sel) {
                while ($row = $sel->fetch_assoc()) {
                    if (!$row) break;
                    $cps[] = [
                        'id' => $row['id'],
                        'title' => $row['title'] ?? '',
                        'fund_photos' => $row['image'] ? url($row['image']) : '',
                        'link' => $row['link'] ?? '',
                        'description' => $row['description'] ?? '',
                    ];
                }
            }
        } catch (\Exception $e) {
            // If there's an error, return empty array
            $cps = [];
        }

        // Get all feature funds from campaigns table (exclude expired - Kickstarter style)
        $cp = [];
        $timestamp = date("Y-m-d");
        $sel = $this->h->queryfire("SELECT * FROM campaigns WHERE status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY id DESC");
        
        if ($sel) {
            while ($rows = $sel->fetch_assoc()) {
                // Get total deposits for this campaign (only successful payments)
                $depositResult = $this->h->queryfire("SELECT COALESCE(SUM(amount), 0) AS total_deposite FROM deposits WHERE campaign_id=" . (int)$rows["id"] . " AND status = 1");
                $getd = $depositResult ? $depositResult->fetch_assoc() : null;
                $total_deposite = $getd ? ($getd['total_deposite'] ?? 0) : ($rows['raised_amount'] ?? 0);
                $goal_amount = $rows['goal_amount'] ?? $rows['target_amount'] ?? 0;
                $remain_amt = $goal_amount - $total_deposite;
                
                if ($remain_amt > 0) {
                    $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$rows['id'] . " AND status = 1");
                    $donatersResult = $funded ? $funded->fetch_assoc() : null;
                    $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;
                    
                    // Use helper function to format campaign data (exclude_story for list - heavy fields only in fundById)
                    $pols = $this->formatCampaignData($rows, [
                        'fund_for' => '',
                        'patient_photo' => [],
                        'total_donaters' => $total_donaters,
                        'exclude_story' => true,
                    ]);
                    
                    // Override specific fields for feature funds
                    $pols['lats'] = '';
                    $pols['longs'] = '';
                    $pols['patient_title'] = '';
                    $pols['patient_diagnosis'] = '';
                    $pols['fund_plan'] = '';
                    $pols['medical_certificate'] = [];
                    $pols['reject_comment'] = '';
                    $pols['fund_status'] = $rows['status'] ?? '';
                    $pols['remain_amt'] = $remain_amt;
                    
                    $cp[] = $pols;
                }
            }
        }

        // Get popular funds (top 5 by donations) from campaigns table (exclude expired)
        $listpopular = [];
        $selpop = $this->h->queryfire("SELECT SUM(d.amount) AS total_deposite, d.campaign_id, c.*
            FROM deposits AS d
            JOIN campaigns AS c ON d.campaign_id = c.id
            WHERE d.status = 1 AND c.status = 1 AND (c.end_date IS NULL OR c.end_date >= '" . $timestamp . "')
            GROUP BY d.campaign_id 
            ORDER BY total_deposite DESC 
            LIMIT 5");
        
        if ($selpop) {
            while ($pop = $selpop->fetch_assoc()) {
                $campaignId = $pop['campaign_id'] ?? $pop['id'] ?? 0;
                $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$campaignId . " AND status = 1");
                $donatersResult = $funded ? $funded->fetch_assoc() : null;
                $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;
                
                // Use helper function to format campaign data (exclude_story for list - heavy fields only in fundById)
                $popular = $this->formatCampaignData($pop, [
                    'fund_for' => '',
                    'patient_photo' => [],
                    'total_donaters' => $total_donaters,
                    'exclude_story' => true,
                ]);
                
                // Override specific fields for popular funds
                $popular['id'] = $campaignId;
                $popular['lats'] = '';
                $popular['longs'] = '';
                $popular['patient_title'] = '';
                $popular['patient_diagnosis'] = '';
                $popular['fund_plan'] = '';
                $popular['medical_certificate'] = [];
                $popular['reject_comment'] = '';
                $popular['fund_status'] = $pop['status'] ?? '';
                $popular['total_investment'] = $pop['total_deposite'] ?? 0;
                $popular['remain_amt'] = $popular['fund_amt'] - ($pop['total_deposite'] ?? 0);
                
                $listpopular[] = $popular;
            }
        }

        // Get nearby funds (5 nearest) - Since campaigns table doesn't have lats/longs, return recent campaigns (exclude expired)
        $listnearby = [];
        if ($lats != 0 && $longs != 0) {
            // Get recent active campaigns since we don't have location coordinates
            $selpop = $this->h->queryfire("SELECT * FROM campaigns WHERE status = 1 AND (end_date IS NULL OR end_date >= '" . $timestamp . "') ORDER BY created_at DESC LIMIT 20");
            
            $fundsWithDistance = [];
            if ($selpop) {
                while ($pop = $selpop->fetch_assoc()) {
                    // Since we don't have lats/longs, assign random distance or skip distance calculation
                    // For now, we'll just include them without distance calculation
                    $pop['distance'] = 0; // No distance calculation possible without coordinates
                    $fundsWithDistance[] = $pop;
                }
            }
            
            // Sort by created date and take top 5
            usort($fundsWithDistance, function($a, $b) {
                return strtotime($b['created_at'] ?? '') <=> strtotime($a['created_at'] ?? '');
            });
            $fundsWithDistance = array_slice($fundsWithDistance, 0, 5);
            
            foreach ($fundsWithDistance as $pop) {
                $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$pop['id'] . " AND status = 1");
                $donatersResult = $funded ? $funded->fetch_assoc() : null;
                $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;
                
                $distance = $pop['distance'] ?? 0;
                
                // Use helper function to format campaign data (exclude_story for list - heavy fields only in fundById)
                $populars = $this->formatCampaignData($pop, [
                    'fund_for' => '',
                    'patient_photo' => [],
                    'total_donaters' => $total_donaters,
                    'exclude_story' => true,
                    'fund_distance' => $distance > 0 ? $distance . ' KM' : ''
                ]);
                
                // Override specific fields for nearby funds
                $populars['lats'] = '';
                $populars['longs'] = '';
                $populars['patient_title'] = '';
                $populars['patient_diagnosis'] = '';
                $populars['fund_plan'] = '';
                $populars['medical_certificate'] = [];
                $populars['reject_comment'] = '';
                $populars['fund_status'] = $pop['status'] ?? '';
                
                $listnearby[] = $populars;
            }
        }

        // Get wallet balance
        if ($uid == 0) {
            $wallet = "0";
        } else {
            $wallet = empty($check_user_verify['wallet']) ? "0" : $check_user_verify['wallet'];
        }

        // Return null for empty arrays instead of empty arrays
        $returnArr = [
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Home Data Get Successfully!!!",
            "category" => !empty($c) ? $c : null,
            'is_block' => $is_block,
            "currency" => !empty($set['site_cur']) ? $set['site_cur'] : (!empty($set['cur_sym']) ? $set['cur_sym'] : 'USD'),
            "featurefund" => !empty($cp) ? $cp : null,
            "Banner" => !empty($cps) ? $cps : null,
            "PopularFund" => !empty($listpopular) ? $listpopular : null,
            'Wallet' => $wallet,
            'listnearby' => !empty($listnearby) ? $listnearby : null,
            'plaform_free' => !empty($set["plaform_free"]) ? $set["plaform_free"] : null
        ];

        return response()->json($returnArr);
    }

    /**
     * Get Notifications
     */
    public function notification(Request $request): JsonResponse
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
        $sel = $this->h->queryfire("select * from tbl_notification where uid=" . (int)$uid . "");
        $myarray = array();
        if ($sel) {
            while ($row = $sel->fetch_assoc()) {
                $myarray[] = $row;
            }
        }

        if (empty($myarray)) {
            return response()->json([
                "notificationdata" => $myarray,
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Notification List Founded!"
            ]);
        } else {
            return response()->json([
                "notificationdata" => $myarray,
                "ResponseCode" => "200",
                "Result" => "false",
                "ResponseMsg" => "Notification List not Founded!"
            ]);
        }
    }
}


