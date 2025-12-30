<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['cat_id'] == '' || $data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
$cat_id = $data['cat_id'];	
$uid = $data['uid'];
if($cat_id != 0)
{
$sel = $h->queryfire("select * from tbl_fund where  cat_id=".$cat_id." and is_approve=1 and fund_status='Pending'");
}
else 
{
	$sel = $h->queryfire("select * from tbl_fund where  is_approve=1 and fund_status='Pending'");
}
$pols = array();
$cp = array();
$timestamp = date("Y-m-d");
while($rows = $sel->fetch_assoc())
{
   if(empty($rows['exp_date']))
   {
		$pols['id'] = $rows['id'];
		$pols['cat_id'] = $rows['cat_id'];
		$pols['title'] = $rows['title'];
		$pols['fund_for'] = $rows['fund_for'];
		$pols['fund_photos'] = explode('$;',$rows['fund_photos']);
		$pols['exp_date'] = empty($rows['exp_date'])?"":$rows['exp_date'];
		$pols['fund_amt'] = $rows['fund_amt'];
		$pols['full_address'] = $rows['full_address'];
		$pols['lats'] = $rows['lats'];
		$pols['longs'] = $rows['longs'];
		$pols['fund_story'] = $rows['fund_story'];
		$pols['fund_date'] = $rows['fund_date'];
		$pols['patient_photo'] = explode('$;',$rows['patient_photo']);
		$pols['patient_title'] = $rows['patient_title'];
		$pols['patient_diagnosis'] = $rows['patient_diagnosis'];
		$pols['fund_plan'] = $rows['fund_plan'];
		$pols['medical_certificate'] = explode('$;',$rows['medical_certificate']);
		$pols['reject_comment'] = empty($rows['reject_comment']) ? "" :$rows['reject_comment'];
		$pols['fund_status'] = $rows['fund_status'];
		$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$rows["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
		$pols['total_investment'] = $total_deposite;
		$pols['remain_amt'] = $rows['fund_amt'] - $total_deposite;
		$funded = $h->queryfire("select * from tbl_deposit where fund_id=".$rows['id']."");
		$pols['total_donaters'] = $funded->num_rows;
		if($funded->num_rows != 0)
		{
			
$don = array();
$lp = array();
while($pp = $funded->fetch_assoc())
{

	$getuser = $h->queryfire("select profile_pic,name from tbl_user where id=".$pp['uid']."")->fetch_assoc();
	$don['name'] = $getuser['name'];
	$don['profile_pic'] = empty($getuser['profile_pic']) ? "images/default.png" : $getuser['profile_pic'];
	$don['amt'] = $pp['amt'];
	$don['deposite_date'] = $p['deposite_date'];
	$lp[] = $don;
}
$pols['donaterlist'] = $lp;
		}
		else 
		{
			$pols['donaterlist'] = [];
		}
		$cp[] = $pols;
   }
   else 
   {
	   if($timestamp > $rows['exp_date'])
	   {}
   else 
   {
	    $pols['id'] = $rows['id'];
		$pols['cat_id'] = $rows['cat_id'];
		$pols['title'] = $rows['title'];
		$pols['fund_for'] = $rows['fund_for'];
		$pols['fund_photos'] = explode('$;',$rows['fund_photos']);
		$pols['exp_date'] = empty($rows['exp_date'])?"":$rows['exp_date'];
		$pols['fund_amt'] = $rows['fund_amt'];
		$pols['postcode'] = $rows['postcode'];
		$pols['fund_story'] = $rows['fund_story'];
		$pols['fund_date'] = $rows['fund_date'];
		$pols['patient_photo'] = explode('$;',$rows['patient_photo']);
		$pols['patient_title'] = $rows['patient_title'];
		$pols['patient_diagnosis'] = $rows['patient_diagnosis'];
		$pols['fund_plan'] = $rows['fund_plan'];
		$pols['medical_certificate'] = explode('$;',$rows['medical_certificate']);
		$pols['reject_comment'] = empty($rows['reject_comment']) ? "" :$rows['reject_comment'];
		$pols['fund_status'] = $rows['fund_status'];
		$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$rows["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
		$pols['total_investment'] = $total_deposite;
		$pols['remain_amt'] = $rows['fund_amt'] - $total_deposite;
		$funded = $h->queryfire("select * from tbl_deposit where fund_id=".$rows['id']."");
		$pols['total_donaters'] = $funded->num_rows;
		if($funded->num_rows != 0)
		{
			
$don = array();
$lp = array();
while($pp = $funded->fetch_assoc())
{

	$getuser = $h->queryfire("select profile_pic,name from tbl_user where id=".$pp['uid']."")->fetch_assoc();
	$don['name'] = $getuser['name'];
	$don['profile_pic'] = empty($getuser['profile_pic']) ? "images/default.png" : $getuser['profile_pic'];
	$don['amt'] = $pp['amt'];
	$depositeDate = new DateTime($pp['deposite_date']);
$currentDate = new DateTime();
$interval = $currentDate->diff($depositeDate);

if ($interval->y > 0) {
    $don['deposite_date'] = $interval->format('%y year(s) ago');
} elseif ($interval->m > 0) {
    $don['deposite_date'] = $interval->format('%m month(s) ago');
} elseif ($interval->d > 0) {
    $don['deposite_date'] = $interval->format('%d day(s) ago');
} elseif ($interval->h > 0) {
    $don['deposite_date'] = $interval->format('%h hour(s) ago');
} elseif ($interval->i > 0) {
    $don['deposite_date'] = $interval->format('%i minute(s) ago');
} else {
    $don['deposite_date'] = 'Just now';
}
	 
	$lp[] = $don;
}
$pols['donaterlist'] = $lp;
		}
		else 
		{
			$pols['donaterlist'] = [];
		}
		$cp[] = $pols;
   }
   }
		
	
	
}

$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Home Data Get Successfully!!!","catwisefund"=>$cp);	
}
echo json_encode($returnArr);
?>