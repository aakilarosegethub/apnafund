<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$sel = $h->queryfire("select * from tbl_payment_list");
$myarray = array();
while($row = $sel->fetch_assoc())
{
	$myarray[] = $row;
}
$returnArr = array("paymentdata"=>$myarray,"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Payment Gateway List Founded!");
echo json_encode($returnArr);
?> 