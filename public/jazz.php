<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Production/Sandbox Credentials
$MerchantID = "MC303131"; //Your Merchant from transaction Credentials
$Password = "tghs3du82x"; //Your Password from transaction Credentials
$HashKey = "tcd21z0w1s"; //Your HashKey/integrity salt from transaction Credentials
$ReturnURL = "http://apnacrowdfunding.com/"; //Your Return URL, It must be static

// *** for sandbox testing environment
$PostURL =
"https://sandbox.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform/";
// *** for production environment
//$PostURL =
"https://payments.jazzcash.com.pk/CustomerPortal/transactionmanagement/merchantform";
date_default_timezone_set("Asia/karachi");
$Amount = 1 * 100; //The last two digits will be treated as decimal, so multiply the product
$BillReference = "billRef"; //use AlphaNumeric only
$Description = "Product test description"; //use AlphaNumeric only
$Language = "EN"; // do not change it
$TxnCurrency = "PKR"; // do not change it

$TxnDateTime = date('YmdHis'); // use Pakistan Time Zone GMT+5
$TxnExpiryDateTime = date('YmdHis', strtotime('+3 Days'));
$TxnRefNumber = "T" . date('YmdHis') . mt_rand(10, 100);
$TxnType = "MPAY"; // For card payments
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
<form method="post" action="<?php echo $PostURL; ?>" />
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