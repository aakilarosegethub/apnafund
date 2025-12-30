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
$getfundlist = $h->queryfire("select group_concat(`id`) as fundlist from tbl_fund where uid=".$uid."")->fetch_assoc();
$fund_id = $getfundlist['fundlist'];
if(empty($fund_id))
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"true","ResponseMsg"=>"No Donation Found!!","activitylist"=>[]);
}
else 
{
$actionlist = $h->queryfire("SELECT * FROM `tbl_deposit` where fund_id IN(".$fund_id.") order by deposite_date desc");
$pol = array();
$zol = array();
while($row = $actionlist->fetch_assoc())
{
	$fundlist = $h->queryfire("select * from tbl_fund where id=".$row['fund_id']."")->fetch_assoc();
	$udata = $h->queryfire("select * from tbl_user where id=".$row["uid"]."")->fetch_assoc();
	$pol['donator_id'] = $udata['id'];
	$pol['fund_title'] = $fundlist['title'];
	$pol['profile_pic'] = $udata['profile_pic'];
	if($row['is_anonymous'] == 1)
	{
	$pol['name'] = 'Anonymous';
	}
	else 
	{
     $pol['name'] = $udata['name'];
	}
	$pol['donation_amt'] = $row['amt'];
	$zol[] = $pol;
}
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"activity  list get Successfully!!!","activitylist"=>$zol);
}
}
echo json_encode($returnArr);
?>