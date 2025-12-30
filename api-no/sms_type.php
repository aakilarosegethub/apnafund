<?php 
require 'Gofund.php';
$h = new Gofund($fund);
$query = "SELECT * FROM `tbl_setting`";
		  $set = $h->fetchData($query);
		  
		  
		  $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"type Get Successfully!!","SMS_TYPE"=>$set['sms_type']);

echo json_encode($returnArr);
?>