<?php

namespace App\Http\Controllers\Api;

use App\Models\Gateway;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaymentController extends BaseApiController
{
    /**
     * Get Gateways List (with currencies, min/max amount)
     * GET /api/gateways
     * Query: ?country=Pakistan (optional)
     */
    public function gateways(Request $request): JsonResponse
    {
        $country = $request->query('country', '');

        $query = Gateway::active()
            ->with(['currencies' => function ($q) {
                $q->where('status', 1)->select('id', 'method_code', 'currency', 'symbol', 'min_amount', 'max_amount');
            }]);

        if (!empty($country)) {
            $query->forCountry($country);
        }

        $gateways = $query->orderBy('id')->get()->map(function ($g) {
            $currencies = $g->currencies->map(function ($c) {
                return [
                    'currency'   => $c->currency,
                    'symbol'     => $c->symbol ?? $c->currency,
                    'min_amount' => (float) $c->min_amount,
                    'max_amount' => (float) $c->max_amount,
                ];
            })->values()->all();
            return [
                'id'         => $g->id,
                'code'       => $g->code,
                'name'       => $g->name,
                'alias'      => $g->alias ?? $g->name,
                'currencies' => $currencies,
            ];
        });

        return response()->json([
            'ResponseCode' => '200',
            'Result'       => 'true',
            'ResponseMsg'  => 'Gateways list.',
            'gateways'     => $gateways,
        ]);
    }

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

