<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '' or $data['amt'] == '' or $data['r_type'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
	$uid = $data['uid'];
	$amt = $data['amt'];
	$r_type = $data['r_type'];
	$acc_number = $data['acc_number'];
	$bank_name = $data['bank_name'];
	$acc_name = $data['acc_name'];
	$ifsc_code = $data['ifsc_code'];
	$upi_id = $data['upi_id'];
	$paypal_id = $data['paypal_id'];
	$fundlist = $h->queryfire("SELECT COALESCE(GROUP_CONCAT(id), '0') AS fund_list 
FROM `tbl_fund` 
WHERE uid = " . $uid . " AND fund_status='Completed'")->fetch_assoc();
	$sales = $h->queryfire("SELECT sum(amt) as full_total  
                      FROM `tbl_deposit` 
                      WHERE fund_id IN(" . $fundlist['fund_list'] . ")")->fetch_assoc();
	
	$without_cod = empty($sales['full_total']) ? 0 : $sales['full_total'];

	
	
	
	
	
	
            $payout =   $h->queryfire("select sum(amt) as full_payout from payout_setting where uid=".$uid."")->fetch_assoc();
			$finalpayout = empty($payout['full_payout']) ? 0 : $payout['full_payout'];
                 $bs = 0;
				
				
				 if($sales['full_total'] == ''){}else {$bs = number_format((float)($without_cod)- $finalpayout, 2, '.', ''); }
				 
				 
				 
				 if(floatval($amt) > floatval($bs))
				 {
					 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"You can't Withdraw Above Your Fund Received!!"); 
				 }
				 else 
				 {
					 $timestamp = date("Y-m-d H:i:s");
					 $table="payout_setting";
  $field_values=array("uid","amt","status","r_date","r_type","acc_number","bank_name","acc_name","ifsc_code","upi_id","paypal_id");
  $data_values=array("$uid","$amt","pending","$timestamp","$r_type","$acc_number","$bank_name","$acc_name","$ifsc_code","$upi_id","$paypal_id");
  
      
	  $check = $h->insertData_Api($field_values,$data_values,$table);
	  $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Withdraw Request Submit Successfully!!");
				 }
}
echo json_encode($returnArr);
?>