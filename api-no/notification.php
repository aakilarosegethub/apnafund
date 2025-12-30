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
$sel = $h->queryfire("select * from tbl_notification where uid=".$uid."");
$myarray = array();
while($row = $sel->fetch_assoc())
{
	$myarray[] = $row;
}
if(empty($myarray))
{
$returnArr = array("notificationdata"=>$myarray,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Notification List Founded!");
}
else 
{
$returnArr = array("notificationdata"=>$myarray,"ResponseCode"=>"200","Result"=>"false","ResponseMsg"=>"Notification List not Founded!");	
}
}
echo json_encode($returnArr);
?> 