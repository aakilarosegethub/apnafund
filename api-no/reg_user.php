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

    $password = strip_tags($h->real_string($data['password']));
    $checkmob = $h->queryfire("select * from tbl_user where mobile=" . $mobile . "");
    $checkemail = $h->queryfire("select * from tbl_user where email='" . $email . "'");

    if ($checkmob->num_rows != 0) {
        $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Mobile Number Already Used!"];
    } elseif ($checkemail->num_rows != 0) {
        $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Email Address Already Used!"];
    } else {
        $timestamp = date("Y-m-d H:i:s");

        $table = "tbl_user";
        $field_values = ["name", "email", "mobile", "rdate", "password","ccode"];
        $data_values = ["$fname", "$email", "$mobile", "$timestamp", "$password","$ccode"];

        
        $check = $h->insertDataId_Api($field_values, $data_values, $table);

        $c = $h->queryfire("select * from tbl_user where id=" . $check . "")->fetch_assoc();

        $returnArr = ["UserLogin" => $c, "currency" => $set['currency'], "ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Sign Up Done Successfully!"];
    }
}

echo json_encode($returnArr);
