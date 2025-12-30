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
	$pol = array();
$c = array();
$sel = $h->queryfire("select * from tbl_category where status=1");
while($row = $sel->fetch_assoc())
{
   
		$pol['id'] = $row['id'];
		$pol['title'] = $row['title'];
		
		$c[] = $pol;
	
	
}
$returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Home Data Get Successfully!!!","category"=>$c);
}
echo json_encode($returnArr);
?>