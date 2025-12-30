<?php
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
$data = json_decode(file_get_contents('php://input'), true);
if ($data['name'] == '' or $data['email'] == '' or $data['mobile'] == '' or $data['password'] == '' or $data['ccode'] == '') {
    $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Something Went Wrong!"];
} else {
    $fname = strip_tags($h->real_string($data['name']));
    $email = strip_tags($h->real_string($data['email']));
    $mobile = strip_tags($h->real_string($data['mobile']));
    $ccode = strip_tags($h->real_string($data['ccode']));
    $uid = $data['uid'];
    $password = strip_tags($h->real_string($data['password']));
    $checkmob = $h->queryfire("select * from tbl_user where mobile=" . $mobile . " and id!=".$uid."");
    $checkemail = $h->queryfire("select * from tbl_user where email='" . $email . "' and id!=".$uid."");

    if ($checkmob->num_rows != 0) {
        $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Mobile Number Already Used!"];
    } elseif ($checkemail->num_rows != 0) {
        $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Email Address Already Used!"];
    } else {
		$table = "tbl_user";
                $field = [
                    "name" => $fname,
					"email"   =>  $email,
                    "mobile" => $mobile,
                    "password" => $password,
                    "ccode" => $ccode
                ];
				
				$where =
                    "where id=" . $uid . "";
					

                $check = $h->updateData_Api($field, $table, $where);

        $c = $h->queryfire("select * from tbl_user where id=" . $uid . "")->fetch_assoc();

        $returnArr = ["UserLogin" => $c, "currency" => $set['currency'], "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Profile Update  Successfully!"];
    }
}

echo json_encode($returnArr);
