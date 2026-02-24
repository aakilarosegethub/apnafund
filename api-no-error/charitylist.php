<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$sel = $h->queryfire("select * from tbl_charity");
$myarray = array();
while($row = $sel->fetch_assoc())
{
	$myarray[] = $row;
}
$returnArr = array("charitydata"=>$myarray,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Charity List Founded!");
echo json_encode($returnArr);
?> 