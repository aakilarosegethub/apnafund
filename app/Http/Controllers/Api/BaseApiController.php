<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class BaseApiController extends Controller
{
    protected $h;
    protected $apiKey;

    public function __construct()
    {
        // Use Laravel DB facade instead of old Gofund class
        $this->h = new class {
            public function queryfire($query) {
                try {
                    $results = DB::select($query);
                    // Convert to mysqli-like result object for compatibility
                    return new class($results) {
                        private $results;
                        private $position = 0;
                        
                        public function __construct($results) {
                            $this->results = $results;
                        }
                        
                        public function fetch_assoc() {
                            if ($this->position < count($this->results)) {
                                $result = $this->results[$this->position];
                                // Convert stdClass to array recursively
                                $resultArray = json_decode(json_encode($result), true);
                                $this->position++;
                                return $resultArray;
                            }
                            return null;
                        }
                        
                        public function __get($name) {
                            if ($name === 'num_rows') {
                                return count($this->results);
                            }
                            return null;
                        }
                        
                        public function reset() {
                            $this->position = 0;
                        }
                    };
                } catch (\Exception $e) {
                    error_log("Query Error: " . $e->getMessage() . " | Query: " . $query);
                    return false;
                }
            }
            
            public function fetchData($query) {
                try {
                    $results = DB::select($query);
                    if (!empty($results)) {
                        // Convert stdClass to array recursively
                        return json_decode(json_encode($results[0]), true);
                    }
                    return false;
                } catch (\Exception $e) {
                    error_log("Fetch Data Error: " . $e->getMessage() . " | Query: " . $query);
                    return false;
                }
            }
            
            public function real_string($string) {
                if (is_null($string)) {
                    return '';
                }
                // Use addslashes for compatibility with old code
                // In Laravel, we should use parameterized queries, but for compatibility:
                return addslashes($string);
            }
            
            public function insertDataId_Api($field_values, $data_values, $table) {
                try {
                    // Build data array for Laravel's insert
                    $data = [];
                    foreach ($field_values as $index => $field) {
                        $data[$field] = $data_values[$index] ?? null;
                    }
                    
                    // Use Laravel's query builder for safe insertion
                    $id = DB::table($table)->insertGetId($data);
                    return $id;
                } catch (\Exception $e) {
                    error_log("Insert Data Error: " . $e->getMessage());
                    return false;
                }
            }
            
            public function insertData_Api($field_values, $data_values, $table) {
                // Alias for insertDataId_Api for backward compatibility
                try {
                    // Build data array for Laravel's insert
                    $data = [];
                    foreach ($field_values as $index => $field) {
                        $data[$field] = $data_values[$index] ?? null;
                    }
                    
                    // Use Laravel's query builder for safe insertion
                    $id = DB::table($table)->insertGetId($data);
                    return $id;
                } catch (\Exception $e) {
                    error_log("Insert Data Error: " . $e->getMessage());
                    return false;
                }
            }
            
            public function updateData_Api($field, $table, $where) {
                try {
                    // Parse where clause (e.g., "where id=1 and status=1")
                    $whereClause = trim($where);
                    if (stripos($whereClause, 'where') === 0) {
                        $whereClause = substr($whereClause, 5); // Remove "where"
                    }
                    
                    // Build query builder
                    $query = DB::table($table);
                    
                    // Parse where conditions (simple parsing for common cases)
                    $conditions = explode(' and ', $whereClause);
                    foreach ($conditions as $condition) {
                        $condition = trim($condition);
                        if (preg_match('/^`?(\w+)`?\s*=\s*[\'"]?([^\'"]+)[\'"]?$/', $condition, $matches)) {
                            $fieldName = trim($matches[1], '`');
                            $fieldValue = trim($matches[2], '\'"');
                            $query->where($fieldName, $fieldValue);
                        } elseif (preg_match('/^`?(\w+)`?\s*=\s*([0-9]+)$/', $condition, $matches)) {
                            $fieldName = trim($matches[1], '`');
                            $fieldValue = $matches[2];
                            $query->where($fieldName, $fieldValue);
                        }
                    }
                    
                    // Handle field update
                    if (is_array($field)) {
                        $result = $query->update($field);
                    } else {
                        // If field is a string, parse it (e.g., "field1='value1', field2='value2'")
                        $updates = [];
                        $pairs = explode(',', $field);
                        foreach ($pairs as $pair) {
                            $pair = trim($pair);
                            if (preg_match('/^`?(\w+)`?\s*=\s*[\'"]?([^\'"]+)[\'"]?$/', $pair, $matches)) {
                                $fieldName = trim($matches[1], '`');
                                $fieldValue = trim($matches[2], '\'"');
                                $updates[$fieldName] = $fieldValue;
                            }
                        }
                        if (!empty($updates)) {
                            $result = $query->update($updates);
                        } else {
                            return false;
                        }
                    }
                    
                    return $result !== false;
                } catch (\Exception $e) {
                    error_log("Update Data Error: " . $e->getMessage());
                    return false;
                }
            }
        };
        $this->apiKey = 'google_map_key';
    }

    /**
     * Format deposite date to relative time
     */
    protected function formatDepositeDate($depositeDate)
    {
        $depositeDateTime = new \DateTime($depositeDate);
        $currentDate = new \DateTime();
        $interval = $currentDate->diff($depositeDateTime);

        if ($interval->y > 0) {
            return $interval->format('%y year(s) ago');
        } elseif ($interval->m > 0) {
            return $interval->format('%m month(s) ago');
        } elseif ($interval->d > 0) {
            return $interval->format('%d day(s) ago');
        } elseif ($interval->h > 0) {
            return $interval->format('%h hour(s) ago');
        } elseif ($interval->i > 0) {
            return $interval->format('%i minute(s) ago');
        } else {
            return 'Just now';
        }
    }

    /**
     * Get author/creator details for a campaign (user who created the campaign)
     *
     * @param int $userId User ID (campaign creator)
     * @return array Author object with id, name, email, mobile, whatsapp, avatar, etc.
     */
    protected function getAuthorData($userId)
    {
        if (empty($userId)) {
            return null;
        }
        try {
            $user = DB::table('users')
                ->select('id', 'firstname', 'lastname', 'username', 'email', 'mobile', 'whatsapp', 'country_code', 'country_name', 'address', 'avatar', 'image')
                ->where('id', (int)$userId)
                ->first();
            if (!$user) {
                return null;
            }
            $user = (array) $user;
            $fullName = trim(($user['firstname'] ?? '') . ' ' . ($user['lastname'] ?? ''));
            if (empty($fullName)) {
                $fullName = $user['username'] ?? '';
            }
            $profilePic = $user['image'] ?? $user['avatar'] ?? null;
            $profilePic = !empty($profilePic) ? $profilePic : 'images/default.png';
            return [
                'id' => (int)$user['id'],
                'name' => $fullName,
                'username' => $user['username'] ?? '',
                'email' => $user['email'] ?? '',
                'mobile' => $user['mobile'] ?? '',
                'whatsapp' => $user['whatsapp'] ?? ($user['mobile'] ?? ''),
                'country_code' => $user['country_code'] ?? '',
                'country_name' => $user['country_name'] ?? '',
                'address' => $user['address'] ?? '',
                'avatar' => $profilePic,
                'profile_pic' => $profilePic,
            ];
        } catch (\Exception $e) {
            error_log("Get Author Data Error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get donater list for a fund
     */
    protected function getDonaterList($campaignId, $includeAnonymous = true)
    {
        try {
            // Use deposits table with campaign_id, only successful payments
            $funded = $this->h->queryfire("SELECT d.*, u.username, u.firstname, u.lastname, u.image 
                FROM deposits d 
                LEFT JOIN users u ON d.user_id = u.id 
                WHERE d.campaign_id=" . (int)$campaignId . " AND d.status = 1 
                ORDER BY d.created_at DESC");
            
            if (!$funded || $funded->num_rows == 0) {
                return [];
            }

            $donaterList = [];
            while ($p = $funded->fetch_assoc()) {
                if (!$p) break;
                
                // Get user name - check name field first, then firstname/lastname, then username
                $userName = '';
                if (!empty($p['name'])) {
                    $userName = $p['name'];
                } elseif (!empty($p['firstname']) || !empty($p['lastname'])) {
                    $userName = trim(($p['firstname'] ?? '') . ' ' . ($p['lastname'] ?? ''));
                } elseif (!empty($p['username'])) {
                    $userName = $p['username'];
                }
                
                $don = [
                    'name' => $userName ?: 'Anonymous',
                    'profile_pic' => !empty($p['image']) ? $p['image'] : "images/default.png",
                    'amt' => $p['amount'] ?? 0,
                    'deposite_date' => $this->formatDepositeDate($p['created_at'] ?? $p['updated_at'] ?? date('Y-m-d H:i:s'))
                ];
                $donaterList[] = $don;
            }
            
            return $donaterList;
        } catch (\Exception $e) {
            error_log("Get Donater List Error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get fund data with donations
     */
    protected function getFundData($row, $includeDonaters = true)
    {
        $getd = $this->h->fetchData("SELECT COALESCE(SUM(amount), 0) AS total_deposite FROM deposits WHERE campaign_id=" . (int)$row["id"] . " AND status = 1");
        $total_deposite = $getd ? ($getd['total_deposite'] ?? 0) : 0;
        
        $funded = $this->h->queryfire("SELECT COUNT(DISTINCT user_id) as total_donaters FROM deposits WHERE campaign_id=" . (int)$row['id'] . " AND status = 1");
        $donatersResult = $funded ? $funded->fetch_assoc() : null;
        $total_donaters = $donatersResult ? ($donatersResult['total_donaters'] ?? 0) : 0;
        
        $fundData = [
            'id' => $row['id'],
            'cat_id' => $row['cat_id'],
            'title' => $row['title'],
            'fund_for' => $row['fund_for'],
            'fund_photos' => explode('$;', $row['fund_photos']),
            'exp_date' => empty($row['exp_date']) ? "" : $row['exp_date'],
            'fund_amt' => $row['fund_amt'],
            'full_address' => $row['full_address'] ?? '',
            'lats' => $row['lats'],
            'longs' => $row['longs'],
            'fund_story' => $row['fund_story'],
            'fund_date' => $row['fund_date'],
            'patient_photo' => explode('$;', $row['patient_photo']),
            'patient_title' => $row['patient_title'],
            'patient_diagnosis' => $row['patient_diagnosis'],
            'fund_plan' => $row['fund_plan'],
            'medical_certificate' => explode('$;', $row['medical_certificate']),
            'reject_comment' => empty($row['reject_comment']) ? "" : $row['reject_comment'],
            'fund_status' => $row['fund_status'],
            'total_investment' => $total_deposite,
            'remain_amt' => $row['fund_amt'] - $total_deposite,
            'total_donaters' => $total_donaters
        ];
        
        if ($includeDonaters) {
            $fundData['donaterlist'] = $this->getDonaterList($row['id']);
        }
        
        return $fundData;
    }

    /**
     * Get request data (handles both JSON and form data)
     */
    protected function getRequestData(Request $request)
    {
        // Try to get data from request
        $data = $request->all();
        
        // If Content-Type is JSON or request is JSON, try json() method
        if ($request->isJson() || $request->header('Content-Type') === 'application/json') {
            try {
                $jsonData = $request->json()->all();
                if (!empty($jsonData)) {
                    $data = $jsonData;
                }
            } catch (\Exception $e) {
                // If json() fails, try parsing raw content
            }
        }
        
        // If data is still empty, try to parse JSON from raw content
        if (empty($data) && $request->getContent()) {
            $content = $request->getContent();
            if (!empty($content)) {
                $jsonData = json_decode($content, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($jsonData)) {
                    $data = $jsonData;
                }
            }
        }
        
        return $data ?: [];
    }

    /**
     * Get authenticated user ID
     * For protected routes, token is required (no fallback to request uid)
     */
    protected function getUserId(Request $request)
    {
        if (auth()->check()) {
            return auth()->user()->id;
        }
        
        // No fallback - token is required for protected APIs
        return null;
    }

    /**
     * Get authenticated user
     */
    protected function getAuthUser()
    {
        return auth()->user();
    }

    /**
     * Map campaigns table status to old fund_status format
     */
    protected function mapCampaignStatus($status, $endDate, $raisedAmount, $goalAmount)
    {
        // campaigns: 0 = rejected, 1 = approved, 2 = pending
        // old format: Pending, Cancelled, Completed
        if ($status == 0) {
            return 'Cancelled';
        } elseif ($status == 2) {
            return 'Pending';
        } elseif ($status == 1) {
            // Check if completed
            if ($endDate && strtotime($endDate) < time()) {
                return 'Completed';
            }
            if ($goalAmount > 0 && $raisedAmount >= $goalAmount) {
                return 'Completed';
            }
            return 'Pending';
        }
        return 'Pending';
    }

    /**
     * Helper function to format campaign data for API response
     * Takes campaign row from database and returns formatted array
     * 
     * @param array $rows Campaign row from database
     * @param array $options Optional parameters for customization
     * @return array Formatted campaign data
     */
    protected function formatCampaignData($rows, $options = [])
    {
        // Get total deposits for this campaign (only successful payments)
        $depositResult = $this->h->queryfire("SELECT COALESCE(SUM(amount), 0) AS total_deposite FROM deposits WHERE campaign_id=" . (int)$rows["id"] . " AND status = 1");
        $getd = $depositResult ? $depositResult->fetch_assoc() : null;
        $total_deposite = $getd ? ($getd['total_deposite'] ?? 0) : ($rows['raised_amount'] ?? 0);
        $goal_amount = $rows['goal_amount'] ?? $rows['target_amount'] ?? 0;

        // Handle gallery - it's JSON array in campaigns
        $gallery = [];
        if (!empty($rows['gallery'])) {
            $galleryData = is_string($rows['gallery']) ? json_decode($rows['gallery'], true) : $rows['gallery'];
            $gallery = is_array($galleryData) ? $galleryData : [];
        }
        // Add main image to gallery if exists
        if (!empty($rows['image']) && !in_array($rows['image'], $gallery)) {
            array_unshift($gallery, $rows['image']);
        }

        // Default patient_photo handling - can be overridden in options
        $patient_photo = $options['patient_photo'] ?? (!empty($rows['image']) ? $rows['image'] : 'default.jpg');
        
        // Format fund_photos - can be overridden in options
        // If not provided in options, use processed gallery array, or empty array
        if (isset($options['fund_photos'])) {
            $fund_photos = $options['fund_photos'];
        } else {
            $fund_photos = !empty($gallery) ? $gallery : (!empty($rows['image']) ? [$rows['image']] : []);
        }

        // fund_for can be overridden in options
        $fund_for = $options['fund_for'] ?? ($rows['fund_for'] ?? '');

        // main_img = main/first image (same as first fund_photo for app use)
        $main_img = !empty($fund_photos) ? (is_array($fund_photos) ? $fund_photos[0] : $fund_photos) : ($rows['image'] ?? '');

        // Build the formatted array (fund_story excluded from list APIs - only in fundById detail)
        $fundData = [
            'id' => $rows['id'],
            'slug' => $rows['slug'] ?? '',
            'cat_id' => $rows['category_id'] ?? 0,
            'title' => $rows['name'] ?? '',
            'fund_for' => $fund_for,
            'fund_photos' => $fund_photos,
            'main_img' => $main_img,
            'exp_date' => $rows['end_date'] ?? '',
            'fund_amt' => $goal_amount,
            'full_address' => $rows['location'] ?? '',
            'lats' => $rows['latitude'] ?? '',
            'longs' => $rows['longitude'] ?? '',
            'fund_date' => $rows['start_date'] ?? $rows['created_at'] ?? '',
            'patient_photo' => $patient_photo,
            'patient_title' => '',
            'patient_diagnosis' => '',
            'fund_plan' => '',
            'medical_certificate' => '',
            'reject_comment' => $rows['reject_reason'] ?? '',
            'fund_status' => $this->mapCampaignStatus($rows['status'] ?? 1, $rows['end_date'] ?? null, $total_deposite, $goal_amount),
            'total_investment' => $total_deposite,
            'remain_amt' => max(0, $goal_amount - $total_deposite)
        ];

        // fund_story = campaigns.description (excluded from list APIs via exclude_story option)
        if (empty($options['exclude_story'])) {
            $fundData['fund_story'] = $rows['description'] ?? $rows['fund_story'] ?? '';
        }

        // Add optional fields if provided
        if (isset($options['charity_name'])) {
            $fundData['charity_name'] = $options['charity_name'];
        }
        if (isset($options['charity_tinno'])) {
            $fundData['charity_tinno'] = $options['charity_tinno'];
        }
        if (isset($options['charity_img'])) {
            $fundData['charity_img'] = $options['charity_img'];
        }
        if (isset($options['status'])) {
            $fundData['status'] = $options['status'];
        }
        if (isset($options['total_donaters'])) {
            $fundData['total_donaters'] = $options['total_donaters'];
        }
        if (isset($options['donaterlist'])) {
            $fundData['donaterlist'] = $options['donaterlist'];
        }
        if (isset($options['total_donate'])) {
            $fundData['total_donate'] = $options['total_donate'];
        }
        if (isset($options['fund_distance'])) {
            $fundData['fund_distance'] = $options['fund_distance'];
        }
        // Add is_expired for Kickstarter-style "Ended" label on campaign detail page
        $endDate = $rows['end_date'] ?? null;
        $fundData['is_expired'] = $endDate && strtotime($endDate) < time();

        return $fundData;
    }
}

