<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PageController extends BaseApiController
{
    /**
     * Get Page List
     */
    public function pageList(Request $request): JsonResponse
    {
        $pol = array();
        $c = array();
        $sel = $this->h->queryfire("select * from tbl_page where status=1");
        while ($row = $sel->fetch_assoc()) {
            $pol['title'] = $row['title'];
            $pol['description'] = $row['description'];
            $c[] = $pol;
        }

        if (empty($c)) {
            return response()->json([
                "pagelist" => $c,
                "ResponseCode" => "200",
                "Result" => "false",
                "ResponseMsg" => "Pages Not Founded!"
            ]);
        } else {
            return response()->json([
                "pagelist" => $c,
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Pages List Founded!"
            ]);
        }
    }
}

