<?php
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
$cid = $data['keyword'];
$uid = $data['uid'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$timestamp = date("Y-m-d");
if($cid == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
$selpop = $h->queryfire("SELECT  id , cat_id , title , fund_for , fund_photos , exp_date , fund_amt,fund_story,full_address,lats,longs,
fund_date,patient_photo,patient_title,patient_diagnosis,fund_plan,medical_certificate,reject_comment,fund_status
FROM `tbl_fund`
WHERE fund_status = 'Pending'
AND (exp_date IS NULL OR exp_date > $timestamp and title COLLATE utf8mb4_general_ci like '%".$cid."%')
");
$populars = array();
$listnearby = array();
while($pop = $selpop->fetch_assoc())
{
	$populars['id'] = $pop['id'];
		$populars['cat_id'] = $pop['cat_id'];
		$populars['title'] = $pop['title'];
		$populars['fund_for'] = $pop['fund_for'];
		$populars['fund_photos'] = explode('$;',$pop['fund_photos']);
		$populars['exp_date'] = empty($pop['exp_date'])?"":$pop['exp_date'];
		$populars['fund_amt'] = $pop['fund_amt'];
		$populars['full_address'] = $pop['full_address'];
		$populars['lats'] = $pop['lats'];
		$populars['longs'] = $pop['longs'];
		$populars['fund_story'] = $pop['fund_story'];
		$populars['fund_date'] = $pop['fund_date'];
		$populars['patient_photo'] = explode('$;',$pop['patient_photo']);
		$populars['patient_title'] = $pop['patient_title'];
		$populars['patient_diagnosis'] = $pop['patient_diagnosis'];
		$populars['fund_plan'] = $pop['fund_plan'];
		$populars['medical_certificate'] = explode('$;',$pop['medical_certificate']);
		$populars['reject_comment'] = empty($pop['reject_comment']) ? "" :$pop['reject_comment'];
		$populars['fund_status'] = $pop['fund_status'];
		
	$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$pop["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
        
		$populars['total_investment'] = $total_deposite;
		$populars['remain_amt'] = $pop['fund_amt'] - $total_deposite;
		$funded = $h->queryfire("select * from tbl_deposit where fund_id=".$pop['id']."");
		$populars['total_donaters'] = $funded->num_rows;
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
$populars['donaterlist'] = $lp;
		}
		else 
		{
			$populars['donaterlist'] = [];
		}
		$listnearby[] = $populars;
}
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Fund Data Get Successfully!!!","fundlist"=>$listnearby);	
}
echo json_encode($returnArr);