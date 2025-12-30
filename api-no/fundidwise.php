<?php 
require 'Gofund.php';
$h = new Gofund($fund);
$data = json_decode(file_get_contents('php://input'), true);
if($data['uid'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
	$uid = $data['uid'];
	$fund_id = $data['fund_id'];
	$status = $data['status'];
	$pol = array();
$c = array();
if($status == 'Home')
{
	$sel = $h->queryfire("select * from tbl_fund where  id=".$fund_id."");
}
else 
{
$sel = $h->queryfire("select * from tbl_fund where uid=".$uid." and id=".$fund_id."");
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
		$photos = explode('$;',$row['patient_photo']);
		$pol['patient_photo'] = empty($photos[0]) ? ['images/default.png'] : $photos;
		$pol['patient_title'] = $row['patient_title'];
		$pol['patient_diagnosis'] = $row['patient_diagnosis'];
		$pol['fund_plan'] = $row['fund_plan'];
		$pol['medical_certificate'] = explode('$;',$row['medical_certificate']);
		$pol['reject_comment'] = $row['reject_comment'];
		$pol['fund_status'] = $row['fund_status'];
		$pol['status'] = $row['status'];
		$getd = $h->queryfire("SELECT COALESCE(SUM(`amt`), 0) AS total_deposite FROM tbl_deposit WHERE fund_id=".$row["id"])->fetch_assoc();
        $total_deposite = $getd['total_deposite'];
		$pol['total_investment'] = $total_deposite;
		$pol['remain_amt'] = sprintf("%.2f", $row['fund_amt'] - $total_deposite);
		$funded = $h->queryfire("select * from tbl_deposit where fund_id=".$row['id']."");
		$pol['total_donaters'] = $funded->num_rows;
		if($funded->num_rows != 0)
		{
			
$don = array();
$lp = array();
while($pp = $funded->fetch_assoc())
{

	$getuser = $h->queryfire("select profile_pic,name from tbl_user where id=".$pp['uid']."")->fetch_assoc();
	if($pp['is_anonymous'] == 1)
	{
	$don['name'] = 'Anonymous';
	}
	else 
	{
     $don['name'] = $getuser['name'];
	}
	$don['profile_pic'] = empty($getuser['profile_pic']) ? "images/default.png" : $getuser['profile_pic'];
	$don['amt'] = $pp['amt'];
	$depositeDate = new DateTime($pp['deposite_date']);
$currentDate = new DateTime();
$interval = $currentDate->diff($depositeDate);

if ($interval->y > 0) {
    $don['deposite_date'] = $interval->format('%y year ago');
} elseif ($interval->m > 0) {
    $don['deposite_date'] = $interval->format('%m month ago');
} elseif ($interval->d > 0) {
    $don['deposite_date'] = $interval->format('%d day ago');
} elseif ($interval->h > 0) {
    $don['deposite_date'] = $interval->format('%h hour ago');
} elseif ($interval->i > 0) {
    $don['deposite_date'] = $interval->format('%i minute ago');
} else {
    $don['deposite_date'] = 'Just now';
}
	$lp[] = $don;
}
$pol['donaterlist'] = $lp;
		}
		else 
		{
			$pol['donaterlist'] = [];
		}
		$c[] = $pol;
	
	
}
$up = $h->queryfire("SELECT * from fund_update where fund_id=".$fund_id." order by update_date desc");
$ko = array();
$lop = array();
while($row = $up->fetch_assoc())
{
	$ko["id"] = $row["id"];
	$ko["photo"] = empty($row["photo"]) ? [] : explode('$;',$row["photo"]);
	$ko["update_desc"] = $row["update_desc"];
	$ko["update_date"] = $row["update_date"];
	$lop[] = $ko;
}

$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"fund Data Get Successfully!!!","funddata"=>$c,"fundupdate"=>$lop);
}
echo json_encode($returnArr);
?>