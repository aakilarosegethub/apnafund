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
}

