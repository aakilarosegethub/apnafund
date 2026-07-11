<?php

namespace App\Http\Controllers\Api;

use App\Models\SiteData;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OTPController extends BaseApiController
{
    /**
     * Send MSG91 OTP
     */
    public function msgOTP(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        $set = $this->h->fetchData('SELECT * FROM `tbl_setting`');

        $mobile = $data['mobile'] ?? '';
        if (empty($mobile)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Something Went Wrong!',
            ], 401);
        }

        // Initialize cURL session
        $ch = curl_init();

        // Define the URL
        $url = 'https://control.msg91.com/api/v5/otp?template_id='.$set['otp_id'].'&mobile='.$mobile.'&authkey='.$set['auth_key'].'';

        // Set the cURL options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/JSON']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $otp = rand(111111, 999999);

        // Define the data to send
        $postData = json_encode([
            'otp' => $otp,
        ]);

        // Attach the JSON data to the request
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);

        // Execute the request and store the response
        $response = curl_exec($ch);

        // Close the cURL session
        curl_close($ch);

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'Balance Get Successfully!!',
            'otp' => $otp,
        ]);
    }

    /**
     * Send Twilio OTP
     */
    public function twilioOTP(Request $request): JsonResponse
    {
        $data = $this->getRequestData($request);
        $set = $this->h->fetchData('SELECT * FROM `tbl_setting`');

        $mobile = $data['mobile'] ?? '';
        if (empty($mobile)) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => 'Something Went Wrong!',
            ], 401);
        }

        require base_path('api/src/Twilio/autoload.php');

        $from = $set['twilio_number'];
        $to = $mobile;
        $otp = rand(111111, 999999);

        $sid = $set['acc_id'];
        $token = $set['auth_token'];
        $client = new \Twilio\Rest\Client($sid, $token);

        try {
            $message = $client->messages->create(
                $to,
                [
                    'from' => $set['twilio_number'],
                    'body' => 'Your OTP is #'.$otp.' to verify and proceed.',
                ]
            );

            return response()->json([
                'ResponseCode' => '200',
                'Result' => 'true',
                'ResponseMsg' => 'Balance Get Successfully!!',
                'otp' => $otp,
            ]);
        } catch (\Twilio\Exceptions\RestException $e) {
            return response()->json([
                'ResponseCode' => '401',
                'Result' => 'false',
                'ResponseMsg' => $e->getMessage(),
            ], 401);
        }
    }

    /**
     * Get SMS/OTP Type - returns provider: firebase, twilio, msg91, or legacy sms_type
     */
    public function smsType(Request $request): JsonResponse
    {
        $otpProvider = $this->getOTPProvider();
        $smsType = $this->mapProviderToSmsType($otpProvider);

        return response()->json([
            'ResponseCode' => '200',
            'Result' => 'true',
            'ResponseMsg' => 'type Get Successfully!!',
            'SMS_TYPE' => $smsType,
            'OTP_PROVIDER' => $otpProvider,
        ]);
    }

    /**
     * Get OTP provider from admin Firebase OTP settings or legacy settings
     */
    protected function getOTPProvider(): string
    {
        $firebaseData = SiteData::where('data_key', 'otp_firebase.data')->first();
        if ($firebaseData && ! empty($firebaseData->data_info)) {
            $info = is_array($firebaseData->data_info) ? $firebaseData->data_info : (array) $firebaseData->data_info;
            $provider = $info['otp_provider'] ?? '';
            if (in_array($provider, ['firebase', 'twilio', 'msg91'])) {
                return $provider;
            }
        }

        $set = $this->h->fetchData('SELECT * FROM `settings` LIMIT 1');
        if ($set && isset($set['sms_type'])) {
            return $this->mapSmsTypeToProvider($set['sms_type']);
        }

        return 'twilio';
    }

    protected function mapProviderToSmsType(string $provider): string
    {
        return match ($provider) {
            'firebase' => 'firebase',
            'msg91' => '1',
            'twilio' => '2',
            default => '2',
        };
    }

    protected function mapSmsTypeToProvider(?string $smsType): string
    {
        return match ($smsType) {
            '1' => 'msg91',
            '2' => 'twilio',
            'firebase' => 'firebase',
            default => 'twilio',
        };
    }
}
