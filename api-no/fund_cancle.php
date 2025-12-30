<?php 
require 'Gofund.php';
$h = new Gofund($fund);

header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if($data['fund_id'] == '')
{
 $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");    
}
else
{
 $fund_id = $data['fund_id'];  
 $uid = $data['uid'];
 $reject_comment = $data['reject_comment'];
   $table = "tbl_fund";
                $field = [
                    "fund_status" => 'Cancelled',
					"reject_comment"   =>  $reject_comment
                ];
				
				$where =
                    "where id=" . $fund_id . " and uid=".$uid."";
					
      
                $check = $h->updateData_Api($field, $table, $where);
				
				
 $returnArr = array("ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Fund Cancelled Successfully!!");

}
echo  json_encode($returnArr);
?>