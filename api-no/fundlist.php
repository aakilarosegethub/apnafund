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
	$status = $data['status'];
	$pol = array();
$c = array();
if($status == 'Pending')
{
	
$sel = $h->queryfire("select * from tbl_fund where uid=".$uid."  and fund_status='Pending'");
}
else if($status == 'Cancelled')
{
$sel = $h->queryfire("select * from tbl_fund where uid=".$uid."  and fund_status='Cancelled'");	
}
else 
{
	$sel = $h->queryfire("select * from tbl_fund where uid=".$uid."  and fund_status='Completed'");
}
while($row = $sel->fetch_assoc())
{
   
   $cherity = $h->queryfire("SELECT * from tbl_charity where id=".$row['charity_id']."")->fetch_assoc();
   
		$pol['id'] = $row['id'];
		$pol['cat_id'] = $row['cat_id'];
		$pol['charity_name'] = empty($cherity['title']) ? "" : $cherity['title'];
		$pol['charity_tinno'] = empty($cherity['tin_no']) ? "" : $cherity['tin_no'];
		$pol['charity_img'] = empty($cherity['charity_img']) ? "" : $cherity['charity_img'];
		$pol['title'] = $row['title'];
		$pol['fund_for'] = $row['fund_for'];
		$pol['fund_photos'] = explode('$;',$row['fund_photos']);
		$pol['exp_date'] = empty($row['exp_date'])?"":$row['exp_date'];
		$pol['fund_amt'] = $row['fund_amt'];
		$pol['full_address'] = $row['full_address'];
		$pol['lats'] = $row['lats'];
		$pol['longs'] = $row['longs'];
		$pol['fund_story'] = $row['fund_story'];
		$pol['fund_date'] = $row['fund_date'];
		$pol['patient_photo'] = explode('$;',$row['patient_photo']);
		$pol['patient_title'] = $row['patient_title'];
		$pol['patient_diagnosis'] = $row['patient_diagnosis'];
		$pol['fund_plan'] = $row['fund_plan'];
		$pol['medical_certificate'] = explode('$;',$row['medical_certificate']);
		$pol['reject_comment'] = empty($row['reject_comment']) ? "" :$row['reject_comment'];
		$pol['fund_status'] = $row['fund_status'];
		$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$row["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
		$pol['total_investment'] = $total_deposite;
		$pol['remain_amt'] = $row['fund_amt'] - $total_deposite;
		$c[] = $pol;
	
	
}
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"fund list Successfully!!!","fundlist"=>$c);
}
echo json_encode($returnArr);
?>