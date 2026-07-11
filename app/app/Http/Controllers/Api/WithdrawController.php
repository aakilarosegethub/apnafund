<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WithdrawController extends BaseApiController
{
    /**
     * Request Withdraw
     */
    public function requestWithdraw(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        if (empty($data['amt']) || empty($data['r_type'])) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Something Went Wrong!',
            ], 401);
        }

        // Get user ID from authenticated user
        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unauthorized! Please login first.',
            ], 401);
        }
        $amt = $data['amt'];
        $r_type = $data['r_type'];
        $acc_number = $data['acc_number'] ?? '';
        $bank_name = $data['bank_name'] ?? '';
        $acc_name = $data['acc_name'] ?? '';
        $ifsc_code = $data['ifsc_code'] ?? '';
        $upi_id = $data['upi_id'] ?? '';
        $paypal_id = $data['paypal_id'] ?? '';

        $fundlist = $this->h->queryfire("SELECT COALESCE(GROUP_CONCAT(id), '0') AS fund_list 
            FROM `tbl_fund` 
            WHERE uid = ".$uid." AND fund_status='Completed'")->fetch_assoc();
        $sales = $this->h->queryfire('SELECT sum(amt) as full_total  
            FROM `tbl_deposit` 
            WHERE fund_id IN('.$fundlist['fund_list'].')')->fetch_assoc();

        $without_cod = empty($sales['full_total']) ? 0 : $sales['full_total'];

        $payout = $this->h->queryfire('select sum(amt) as full_payout from payout_setting where uid='.$uid.'')->fetch_assoc();
        $finalpayout = empty($payout['full_payout']) ? 0 : $payout['full_payout'];
        $bs = 0;

        if ($sales['full_total'] != '') {
            $bs = number_format((float) ($without_cod) - $finalpayout, 2, '.', '');
        }

        if (floatval($amt) > floatval($bs)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => "You can't Withdraw Above Your Fund Received!!",
            ], 401);
        } else {
            $timestamp = date('Y-m-d H:i:s');
            $table = 'payout_setting';
            $field_values = ['uid', 'amt', 'status', 'r_date', 'r_type', 'acc_number', 'bank_name', 'acc_name', 'ifsc_code', 'upi_id', 'paypal_id'];
            $data_values = [$uid, $amt, 'pending', $timestamp, $r_type, $acc_number, $bank_name, $acc_name, $ifsc_code, $upi_id, $paypal_id];

            $check = $this->h->insertData_Api($field_values, $data_values, $table);

            return response()->json([
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'Withdraw Request Submit Successfully!!',
            ]);
        }
    }

    /**
     * Get Payout List
     */
    public function payoutList(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);

        // Get user ID from authenticated user
        $uid = $this->getUserId($request);

        if (empty($uid)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Unauthorized! Please login first.',
            ], 401);
        }

        $count = $this->h->queryfire('select * from payout_setting where uid='.$uid.'')->num_rows;
        if ($count != 0) {
            $cy = $this->h->queryfire('select * from payout_setting where uid='.$uid.'');
            $q = [];
            while ($adata = $cy->fetch_assoc()) {
                $p = [
                    'payout_id' => $adata['id'],
                    'amt' => $adata['amt'],
                    'status' => $adata['status'],
                    'proof' => $adata['proof'] ?? '',
                    'r_date' => $adata['r_date'],
                    'r_type' => $adata['r_type'],
                    'acc_number' => $adata['acc_number'],
                    'bank_name' => $adata['bank_name'],
                    'acc_name' => $adata['acc_name'],
                    'ifsc_code' => $adata['ifsc_code'],
                    'upi_id' => $adata['upi_id'],
                    'paypal_id' => $adata['paypal_id'],
                ];
                $q[] = $p;
            }

            return response()->json([
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'Payout List Get Successfully!!!',
                'Payoutlist' => $q,
            ]);
        } else {
            return response()->json([
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'Payout List Not Found!!',
                'Payoutlist' => [],
            ]);
        }
    }
}
