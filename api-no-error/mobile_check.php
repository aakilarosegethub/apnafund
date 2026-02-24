<?php 
require 'Gofund.php';
$h = new Gofund($fund);
$data = json_decode(file_get_contents('php://input'), true);
header('Content-type: text/json');
if($data['mobile'] == '' or $data['ccode'] == '')
{
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $mobile = strip_tags($h->real_string($data['mobile']));
	$code = strip_tags($h->real_string($data['ccode']));
    
    
$chek = $h->queryfire("select * from tbl_user where mobile='".$mobile."' and ccode='".$code."'")->num_rows;

if($chek != 0)
{
	$returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Already Exist Mobile Number!");
}
else 
{
	$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"New Number!");
}
}
echo json_encode($returnArr);