<?php
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$query = "SELECT * FROM `tbl_setting`";
		  $set = $h->fetchData($query);
$data = json_decode(file_get_contents('php://input'), true);
if ($data['fund_id'] == '' or $data['uid'] == '' or $data['amt'] == '' or $data['tip'] == '' or $data['payment_method_id'] == '') {
    $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Something Went Wrong!"];
} else {
    $fund_id = strip_tags($h->real_string($data['fund_id']));
    $uid = strip_tags($h->real_string($data['uid']));
    $amt = strip_tags($h->real_string($data['amt']));
	$wall_amt = strip_tags($h->real_string($data['wall_amt']));
    $tip = strip_tags($h->real_string($data['tip']));
    $payment_method_id = $data['payment_method_id'];
	$transaction_id = $data['transaction_id'];
	$platform_fees = $data['platform_fees'];
	$is_anonymous = $data['is_anonymous'];
	$query = "SELECT * FROM `tbl_setting`";
		  $set = $h->fetchData($query);
    $deposite_date = date("Y-m-d H:i:s");
        $checkdeposite = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_fund from tbl_deposit where fund_id=".$fund_id."")->fetch_assoc();
		$fundrequire = $h->queryfire("SELECT * from tbl_fund where id=".$fund_id."")->fetch_assoc();
		$remain_amt = $fundrequire['fund_amt'] - $checkdeposite['total_fund'];
		if($amt > $remain_amt)
		{
			if($remain_amt <=0.01)
			{
				$table="tbl_fund";
  $field = array('fund_status'=>'Completed');
  $where = "where id=".$fund_id."";
	  $check = $h->updateData_Api($field,$table,$where);
	  
				$returnArr = ["ResponseCode" => "401", "Result" => "false","ResponseMsg" => "fund already completed"];
			}
			else 
			{
			$returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Fund Exist Limit To Deposite Required Only now ".$remain_amt.$set['currency'].". So We Refund To Your Deposite To Wallet."];
			
			$vp = $h->queryfire("select * from tbl_user where id=".$uid."")->fetch_assoc();
	  
  $table="tbl_user";
  $field = array('wallet'=>$vp['wallet']+$amt);
  $where = "where id=".$uid."";
	  $check = $h->updateData_Api($field,$table,$where);
	  
	   $timestamp = date("Y-m-d H:i:s");
	   $timestamps    = date("Y-m-d");
	   $table="wallet_report";
  $field_values=array("uid","message","status","amt","tdate");
  $data_values=array("$uid",'Deposite Refund Of Fund Id#'.$fund_id,'Credit',"$amt","$timestamps");
   
      
	  $checks = $h->insertData_Api($field_values,$data_values,$table);
			}
		}
		else 
		{
			
			 $vp = $h->queryfire("select * from tbl_user where id=".$uid."")->fetch_assoc();
	 if($vp['wallet'] >= $wall_amt)
	 {
		 
		 
		 if($wall_amt != 0)
{

	  $mt = intval($vp['wallet'])-intval($wall_amt);
  $table="tbl_user";
  $field = array('wallet'=>"$mt");
  $where = "where id=".$uid."";

	  $check = $h->updateData_Api($field,$table,$where);
	  $timestamps    = date("Y-m-d");
	  $table="wallet_report";
  $field_values=array("uid","message","status","amt","tdate");
  $data_values=array("$uid",'Wallet Used in Order Id#'.$fund_id,'Debit',"$wall_amt","$timestamps");
   
     
	  $checks = $h->insertData_Api($field_values,$data_values,$table);
}

        $table = "tbl_deposit";
        $field_values = ["fund_id", "uid", "amt", "tip", "payment_method_id","transaction_id","deposite_date","is_anonymous","platform_fees"];
        $data_values = ["$fund_id", "$uid", "$amt", "$tip", "$payment_method_id","$transaction_id","$deposite_date","$is_anonymous","$platform_fees"];
		$check = $h->insertData_Api($field_values, $data_values, $table);
		$fundata = $h->queryfire("SELECT * from tbl_fund where id=".$fund_id."")->fetch_assoc();
		$udata = $h->queryfire("select * from tbl_user where id=".$fundata["uid"]."")->fetch_assoc();
$name = $udata['name'];
$content = array(
       "en" => 'Some One Donate On your Fund#'.$fund_id.'. amount is '.number_format($amt,2).$set["currency"]
   );
$heading = array(
   "en" => "Donation Done!!"
);

$fields = array(
'app_id' => $set['one_key'],
'included_segments' =>  array("Active Users"),
'filters' => array(array('field' => 'tag', 'key' => 'user_id', 'relation' => '=', 'value' => $fundata["uid"])),
'contents' => $content,
'headings' => $heading
);

$fields = json_encode($fields);

 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
curl_setopt($ch, CURLOPT_HTTPHEADER, 
array('Content-Type: application/json; charset=utf-8',
'Authorization: Basic '.$set['one_hash']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
 
$response = curl_exec($ch);
curl_close($ch);
$owner_id = $fundata["uid"];
$timestamp = date("Y-m-d H:i:s");

         $title_main = "Donation Done!!";
         $description = 'Some One Donate On your Fund#'.$fund_id.'. amount is '.number_format($amt,2).$set["currency"];

         $table = "tbl_notification";
         $field_values = ["uid", "datetime", "title", "description"];
         $data_values = ["$owner_id", "$timestamp", "$title_main", "$description"];
         $h->insertData_Api($field_values,$data_values,$table);
        $returnArr = ["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Deposite Done Successfully!"];
	 }
	 else 
	 {
		 $tbwallet = $h->queryfire("select * from tbl_user where id=".$uid."")->fetch_assoc();
$returnArr = array("ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Wallet Balance Not There As Per Fund Deposite Refresh One Time Screen!!!","wallet"=>$tbwallet['wallet']);
	 }
		}
}

echo json_encode($returnArr);
