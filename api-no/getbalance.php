<?php 
require 'Gofund.php';
$h = new Gofund($fund);
$data = json_decode(file_get_contents('php://input'), true);
header('Content-type: text/json');
if($data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
	$uid = $data['uid'];
	$fundlist = $h->queryfire("SELECT COALESCE(GROUP_CONCAT(id), '0') AS fund_list 
FROM `tbl_fund` 
WHERE uid = " . $uid . " AND fund_status='Completed'")->fetch_assoc();
	$sales = $h->queryfire("SELECT sum(amt) as full_total  
                      FROM `tbl_deposit` 
                      WHERE fund_id IN(" . $fundlist['fund_list'] . ")")->fetch_assoc();
	
	$balance = empty($sales['full_total']) ? 0 : $sales['full_total'];
	
	 $bs = 0;
	$payout =   $h->queryfire("select COALESCE(sum(amt),0) as full_payout from payout_setting where uid=".$uid."")->fetch_assoc();
			$finalpayout = empty($payout['full_payout']) ? 0 : $payout['full_payout'];
			
			if($sales['full_total'] == ''){}else {$bs = number_format((float)($balance)- $finalpayout, 2, '.', ''); }
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Balance Get Successfully!!","Total_Fund"=>floatval($bs),"Total_Withdraw"=>floatval($payout['full_payout']));
}
echo json_encode($returnArr);
?>