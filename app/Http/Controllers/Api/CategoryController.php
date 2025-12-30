<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CategoryController extends BaseApiController
{
    /**
     * Get Category List
     */
    public function categoryList(Request $request): JsonResponse
    {
        // Category list is public, no authentication required
        $pol = array();
        $c = array();
        
        // Use categories table (status field: 1 = active, 0 = inactive)
        $sel = $this->h->queryfire("select * from categories where status=1 order by id asc");
        
        if ($sel && $sel->num_rows > 0) {
            while ($row = $sel->fetch_assoc()) {
                if (!$row) break;
                $pol = array();
                $pol['id'] = $row['id'];
                $pol['title'] = $row['name'] ?? $row['title'] ?? ''; // Use 'name' field from categories table
                $c[] = $pol;
            }
        }

        return response()->json([
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Home Data Get Successfully!!!",
            "category" => $c
        ]);
    }

    /**
     * Get Charity List
     */
    public function charityList(Request $request): JsonResponse
    {
        $sel = $this->h->queryfire("select * from tbl_charity");
        $myarray = array();
        while ($row = $sel->fetch_assoc()) {
            $myarray[] = $row;
        }

        return response()->json([
            "charitydata" => $myarray,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Charity List Founded!"
        ]);
    }
}

