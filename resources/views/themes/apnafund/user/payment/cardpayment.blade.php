@php

$url = $data->url;
$method = $data->method;
$data = $data->val;
$amount = $data->pp_Amount;
$currency = $data->pp_TxnCurrency;
$transaction_id = $data->pp_TxnRefNo;
$description = $data->pp_Description;
    $activeTheme = 'themes.apnafund.';
    $activeThemeTrue = 'themes.apnafund.';
@endphp
@extends($activeTheme . 'layouts.frontend')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="text-center">@lang('Card Payment - Page Redirection v1.1')</h4>
                    <p class="text-center text-muted">@lang('Secure card payment processing')</p>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="payment-info">
                                <h5>@lang('Payment Details')</h5>
                                <p><strong>@lang('Amount'):</strong> {{ $amount }} {{ $currency }}</p>
                                <p><strong>@lang('Transaction ID'):</strong> {{ $transaction_id }}</p>
                                <p><strong>@lang('Description'):</strong> {{ $description }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="security-info">
                                <h5>@lang('Security Features')</h5>
                                <ul class="list-unstyled">
                                    <li><i class="ti ti-shield-check text-success"></i> @lang('SSL Encrypted')</li>
                                    <li><i class="ti ti-lock text-primary"></i> @lang('PCI DSS Compliant')</li>
                                    <li><i class="ti ti-credit-card text-info"></i> @lang('Card Payments v1.1')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">@lang('Redirecting...')</span>
                        </div>
                        <p class="mt-3">@lang('Redirecting to secure payment gateway...')</p>
                        <p class="text-muted small">@lang('Please do not close this window or refresh the page.')</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
// Production/Sandbox Credentials
$MerchantID =$data->pp_MerchantID; //Your Merchant from transaction Credentials
$Password = $data->pp_Password; //Your Password from transaction Credentials
$HashKey = "tcd21z0w1s"; //Your HashKey/integrity salt from transaction Credentials
$ReturnURL = $data->pp_ReturnURL; //Your Return URL, It must be static pp_ReturnURL

// *** for sandbox testing environment
$PostURL =
"https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/";
// *** for production environment
//$PostURL =
"https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform";
$PostURL = $url;
date_default_timezone_set("Asia/karachi");
$Amount = $data->pp_Amount; //The last two digits will be treated as decimal, so multiply the product
$BillReference = "billRef"; //use AlphaNumeric only
$Description = "Product test description"; //use AlphaNumeric only
$Language = "EN"; // do not change it
$TxnCurrency = "PKR"; // do not change it

$TxnDateTime = date('YmdHis'); // use Pakistan Time Zone GMT+5
$TxnExpiryDateTime = date('YmdHis', strtotime('+3 Days'));
$TxnRefNumber = "T" . date('YmdHis') . mt_rand(10, 100);
$TxnType = "MWALLET"; // For card payments
$Version = '1.1';
$SubMerchantID = ""; // Leave it empty
$BankID = ""; // Leave it empty
$ProductID = ""; // Leave it empty
$ppmpf_1 = ""; // use to store extra details (use AlphaNumeric only)
$ppmpf_2 = ""; // use to store extra details (use AlphaNumeric only)
$ppmpf_3 = ""; // use to store extra details (use AlphaNumeric only)
$ppmpf_4 = ""; // use to store extra details (use AlphaNumeric only)
$ppmpf_5 = ""; // use to store extra details (use AlphaNumeric only)
$HashArray = [$Amount, $BankID, $BillReference, $Description, $Language, $MerchantID,
$Password, $ProductID, $ReturnURL, $TxnCurrency, $TxnDateTime, $TxnExpiryDateTime,
$TxnRefNumber, $TxnType, $Version, $ppmpf_1, $ppmpf_2, $ppmpf_3, $ppmpf_4,
$ppmpf_5];
$SortedArray = $HashKey;
for ($i = 0; $i < count($HashArray); $i++) {
if ($HashArray[$i] != 'undefined' and $HashArray[$i] != null and $HashArray[$i] != "") {
$SortedArray .= "&" . $HashArray[$i];
}
}
$Securehash = hash_hmac('sha256', $SortedArray, $HashKey);
?>
<div id="header">
<form method="post" id="cardPaymentForm" action="<?php echo $PostURL; ?>" />
<input type="text" name="pp_Version" value="<?php echo $Version; ?>" />
<input type="text" name="pp_TxnType" placeholder="TxnType" value="<?php echo
$TxnType; ?>" />
<input type="text" name="pp_Language" value="<?php echo $Language; ?>" />
<input type="text" name="pp_MerchantID" value="<?php echo $MerchantID; ?>" />
<input type="hidden" name="pp_SubMerchantID" value="<?php echo $SubMerchantID;
?>" />
<input type="password" name="pp_Password" value="<?php echo $Password; ?>" />
<input type="text" name="pp_TxnRefNo" value="<?php echo $TxnRefNumber; ?>" />
<input type="text" name="pp_Amount" value="<?php echo $Amount; ?>" />
<input type="text" name="pp_TxnCurrency" value="<?php echo $TxnCurrency; ?>" />
<input type="text" name="pp_TxnDateTime" value="<?php echo $TxnDateTime; ?>" />
<input type="text" name="pp_BillReference" value="<?php echo $BillReference ?>" />

<input type="text" name="pp_Description" value="<?php echo $Description; ?>" />
<input type="hidden" id="pp_BankID" name="pp_BankID" value="<?php echo $BankID ?>">
<input type="hidden" id="pp_ProductID" name="pp_ProductID" value="<?php echo
$ProductID ?>">
<input type="text" name="pp_TxnExpiryDateTime" value="<?php
echo $TxnExpiryDateTime; ?>" />
<input type="text" name="pp_ReturnURL" value="<?php echo $ReturnURL; ?>" />
<input type="text" name="pp_SecureHash" value="<?php echo $Securehash; ?>" />
<input type="text" name="ppmpf_1" placeholder="ppmpf_1" value="<?php echo
$ppmpf_1; ?>" />
<input type="text" name="ppmpf_2" placeholder="ppmpf_2" value="<?php echo
$ppmpf_2; ?>" />
<input type="text" name="ppmpf_3" placeholder="ppmpf_3" value="<?php echo
$ppmpf_3; ?>" />
<input type="text" name="ppmpf_4" placeholder="ppmpf_4" value="<?php echo
$ppmpf_4; ?>" />
<input type="text" name="ppmpf_5" placeholder="ppmpf_5" value="<?php echo
$ppmpf_5; ?>" /><br> <br> <br>
<button id="submit" type="submit">submit</button>
</form>
</div>


<!--<form id="cardPaymentForm" action="{{ $url }}" method="{{ $method }}" style="display: none;">-->
<!--    @foreach($data as $key => $value)-->
<!--        <input type="hidden" name="{{ $key }}" value="{{ $value }}">-->
<!--    @endforeach-->
<!--</form>-->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show countdown before redirect
    let countdown = 3;
    const countdownElement = document.querySelector('.mt-3');
    
    const timer = setInterval(function() {
        countdownElement.textContent = `Redirecting to secure payment gateway in ${countdown} seconds...`;
        countdown--;
        
        if (countdown < 0) {
            clearInterval(timer);
            document.getElementById('cardPaymentForm').submit();
        }
    }, 1000);
});
</script>

<style>
.payment-info, .security-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.security-info ul li {
    padding: 5px 0;
}

.card {
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    border: none;
}

.card-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
}
</style>
@endsection
