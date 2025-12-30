<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
	$uid = $data['uid'];
		$pol = array();
        $c = array();
		
	$fund = $h->queryfire("SELECT * FROM `tbl_deposit` WHERE uid=".$uid."");
	while($row = $fund->fetch_assoc())
{
	
   $sel = $h->queryfire("select * from tbl_fund where id=".$row['fund_id']."")->fetch_assoc();
   $dona = $h->queryfire("select COALESCE(SUM(amt), 0) as total_amount from tbl_deposit WHERE fund_id=".$row['fund_id']." and uid=".$uid."")->fetch_assoc();
		$pol['id'] = $sel['id'];
		$pol['cat_id'] = $sel['cat_id'];
		$pol['title'] = $sel['title'];
		$pol['fund_for'] = $sel['fund_for'];
		$pol['fund_photos'] = explode('$;',$sel['fund_photos']);
		$pol['exp_date'] = empty($sel['exp_date'])?"":$sel['exp_date'];
		$pol['fund_amt'] = $sel['fund_amt'];
		$pol['total_donate'] = $dona['total_amount'];
		$pol['full_address'] = $sel['full_address'];
		$pol['lats'] = $sel['lats'];
		$pol['longs'] = $sel['longs'];
		$pol['fund_story'] = $sel['fund_story'];
		$pol['fund_date'] = $sel['fund_date'];
		$pol['patient_photo'] = explode('$;',$sel['patient_photo']);
		$pol['patient_title'] = $sel['patient_title'];
		$pol['patient_diagnosis'] = $sel['patient_diagnosis'];
		$pol['fund_plan'] = $sel['fund_plan'];
		$pol['medical_certificate'] = explode('$;',$sel['medical_certificate']);
		$pol['reject_comment'] = empty($sel['reject_comment']) ? "" : $sel['reject_comment'];
		$pol['fund_status'] = $sel['fund_status'];
		$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$sel["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
		$pol['total_investment'] = $total_deposite;
		$pol['remain_amt'] = $sel['fund_amt'] - $total_deposite;
		$c[] = $pol;
	
	
}
	
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"fund list Successfully!!!","fundlist"=>$c);
}
echo json_encode($returnArr);
?>