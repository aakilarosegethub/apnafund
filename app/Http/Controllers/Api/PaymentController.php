<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends BaseApiController
{
    /**
     * Get Payment Gateway List
     */
    public function paymentGatewayList(Request $request): JsonResponse
    {
        // Get active payment gateways (using gateways table)
        // Status is boolean: 1 = active, 0 = inactive
        $sel = $this->h->queryfire("select id, code, name, alias, status from gateways where status = 1 order by id asc");
        
        if (!$sel) {
            return response()->json([
                "paymentdata" => [],
                "ResponseCode" => "200",
                "Result" => "true",
                "ResponseMsg" => "Payment Gateway List Founded!"
            ]);
        }
        
        $myarray = array();
        if ($sel->num_rows > 0) {
            while ($row = $sel->fetch_assoc()) {
                if (!$row) break;
                $myarray[] = $row;
            }
        }

        return response()->json([
            "paymentdata" => $myarray,
            "ResponseCode" => "200",
            "Result" => "true",
            "ResponseMsg" => "Payment Gateway List Founded!"
        ]);
    }
}

