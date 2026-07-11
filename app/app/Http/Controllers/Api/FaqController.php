<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqController extends BaseApiController
{
    /**
     * Get FAQ List
     */
    public function faqList(Request $request): JsonResponse
    {
        // FAQ list is public, no authentication required
        // Fetch FAQs from site_data table (same as admin page /admin/site/sections/faq)
        $check = $this->h->queryfire("select * from site_data where data_key = 'faq.element' order by id asc");

        $op = [];
        if ($check && $check->num_rows > 0) {
            while ($row = $check->fetch_assoc()) {
                if (! $row) {
                    break;
                }

                // Parse JSON data_info to get question and answer
                $dataInfo = json_decode($row['data_info'], true);

                if ($dataInfo && isset($dataInfo['question']) && isset($dataInfo['answer'])) {
                    $op[] = [
                        'id' => $row['id'],
                        'question' => $dataInfo['question'],
                        'answer' => $dataInfo['answer'],
                        'created_at' => $row['created_at'] ?? null,
                        'updated_at' => $row['updated_at'] ?? null,
                    ];
                }
            }
        }

        return response()->json([
            'FaqData' => $op,
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Faq List Get Successfully!!',
        ]);
    }
}
