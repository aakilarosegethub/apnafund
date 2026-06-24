<?php

namespace App\Http\Controllers\Gateway\CardPayment;

use App\Models\Deposit;
use Illuminate\Http\Request;
use App\Constants\ManageStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\PaymentController;
use App\Services\JazzCashApiLoggerService;

class ProcessController extends Controller
{
    public static function process($deposit) 
    {
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $setting = bs();
        
        // Get configuration parameters
        $merchantId = $gatewayAcc->merchant_id;
        $password = $gatewayAcc->password;
        $hashKey = $gatewayAcc->hash_key;
        $returnUrl = $gatewayAcc->return_url;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Prepare payment data using the exact fields from the JazzCash form (public/jazz.php)
        $paymentData = [
            'pp_Version'            => '1.1',
            'pp_TxnType'            => 'MPAY',
            'pp_Language'           => 'EN',
            'pp_MerchantID'         => $merchantId,
            'pp_SubMerchantID'      => '', // Leave empty as per sample
            'pp_Password'           => $password,
            'pp_TxnRefNo'           => $deposit->trx,
            // Amount: last two digits are decimal, so multiply by 100 and cast to int
            'pp_Amount'             => (string)(intval(round($deposit->final_amount * 100))),
            'pp_TxnCurrency'        => $deposit->method_currency,
            'pp_TxnDateTime'        => date('YmdHis'),
            'pp_BillReference'      => $deposit->trx,
            'pp_Description'        => "Donation to " . $setting->site_name,
            'pp_BankID'             => '',
            'pp_ProductID'          => '',
            'pp_TxnExpiryDateTime'  => date('YmdHis', strtotime('+3 Days')),
            'pp_ReturnURL'          => $returnUrl,
            // SecureHash will be calculated below and added to the array
            // Optional fields for extra details
            'ppmpf_1'               => '',
            'ppmpf_2'               => '',
            'ppmpf_3'               => '',
            'ppmpf_4'               => '',
            'ppmpf_5'               => '',
        ];
        
        // Generate hash for security (Page Redirection v1.1 standard)
        $hashString = $merchantId . $deposit->trx . $deposit->final_amount . $deposit->method_currency . $hashKey;
        $paymentData['Hash'] = hash('sha256', $hashString);
        
        // Determine gateway URL based on sandbox mode
        $gatewayUrl = $sandbox ? 'https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/' : 'https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform';
        
        $send['val'] = $paymentData;
        $send['view'] = 'user.payment.cardpayment';
        $send['method'] = 'post';
        $send['url'] = $gatewayUrl;

        app(JazzCashApiLoggerService::class)->logFormRedirect('card_redirect', $gatewayUrl, $paymentData, $deposit);

        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $logger = app(JazzCashApiLoggerService::class);
        $deposit = Deposit::where('trx', $request->TransactionID)->first();
        $logContext = $logger->logIncoming($request, 'ipn/card-payment', 'card_ipn', $deposit);
        
        if (!$deposit) {
            $response = 'Transaction not found';
            $logger->finalizeInbound($logContext, 'failed', $response, 404);
            return response($response, 404);
        }
        
        if ($deposit->status == ManageStatus::PAYMENT_SUCCESS) {
            $response = 'Already processed';
            $logger->finalizeInbound($logContext, 'success', $response, 200);
            return response($response, 200);
        }
        
        // Get gateway configuration
        $gatewayAcc = json_decode($deposit->gatewayCurrency()->gateway_parameter);
        $merchantId = $gatewayAcc->merchant_id;
        $hashKey = $gatewayAcc->hash_key;
        $sandbox = $gatewayAcc->sandbox ?? false;
        
        // Verify the hash (Page Redirection v1.1 standard)
        $expectedHash = hash('sha256', $merchantId . $request->TransactionID . $request->Amount . $request->Currency . $hashKey);
        
        if ($expectedHash !== $request->Hash) {
            $response = 'Invalid hash';
            $logger->finalizeInbound($logContext, 'failed', $response, 400);
            return response($response, 400);
        }
        
        // Verify payment status according to Page Redirection v1.1
        if ($request->Status === 'Success' || $request->Status === 'Completed' || $request->Status === 'APPROVED') {
            PaymentController::campaignDataUpdate($deposit);
            $response = 'Payment processed successfully';
            $logger->finalizeInbound($logContext, 'success', $response, 200);
            return response($response, 200);
        }
        
        $response = 'Payment failed';
        $logger->finalizeInbound($logContext, 'failed', $response, 400);
        return response($response, 400);
    }
}
