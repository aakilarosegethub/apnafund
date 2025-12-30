<?php 
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);

if($data['uid'] == '' or $data['wallet'] == '')
{
    
    $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"Something Went Wrong!");
}
else
{
    $wallet = strip_tags($h->real_string($data['wallet']));
$uid =  strip_tags($h->real_string($data['uid']));
$checkimei = $h->queryfire("select * from tbl_user where  `id`=".$uid."")->num_rows;

if($checkimei != 0)
    {
		
		
      $vp = $h->queryfire("select * from tbl_user where id=".$uid."")->fetch_assoc();
	  
  $table="tbl_user";
  $field = array('wallet'=>$vp['wallet']+$wallet);
  $where = "where id=".$uid."";
	  $check = $h->updateData_Api($field,$table,$where);
	  
	   $timestamp = date("Y-m-d H:i:s");
	   $timestamps    = date("Y-m-d");
	   $table="wallet_report";
  $field_values=array("uid","message","status","amt","tdate");
  $data_values=array("$uid",'Wallet Balance Added!!','Credit',"$wallet","$timestamps");
   
      
	  $checks = $h->insertData_Api($field_values,$data_values,$table);
	  
	  
	   $wallet = $h->queryfire("select * from tbl_user where id=".$uid."")->fetch_assoc();
        $returnArr = array("wallet"=>$wallet['wallet'],"ResponseCode"=>"200","Result"=>"true","ResponseMsg"=>"Wallet Update successfully!");
        
    
	}
    else
    {
      $returnArr = array("ResponseCode"=>"401","Result"=>"false","ResponseMsg"=>"User Deactivate By Admin!!!!");  
    }
    
}

echo json_encode($returnArr);