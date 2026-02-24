<?php
require 'Gofund.php';
$h = new Gofund($fund);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/json');
define('BASE_PATH', dirname(dirname(__FILE__)));
define('IMAGE_PATH', '/images/fund_photo/');
define('IMAGE_PATHS', '/images/fund_certificate/');
define('IMAGE_PATHSS', '/images/pet_photo/');
function processFileUploads($prefix, $count, $url)
{
    $targetPath = BASE_PATH . $url;
    $uploadedFiles = [];

    for ($i = 0; $i < $count; $i++) {
        $newName = uniqid() . date('YmdHis') . mt_rand() . '.jpg';
        $fileUrl = $url . $newName;

        // Remove leading '/' from each file URL
        $fileUrl = ltrim($fileUrl, '/');

        $uploadedFiles[] = $fileUrl;

        // Move uploaded file and check for errors
        if (!move_uploaded_file($_FILES[$prefix . $i]['tmp_name'], $targetPath . $newName)) {
            // Handle upload error here (e.g., provide feedback to the user)
        }
    }

    return $uploadedFiles;
}
if ($_POST['cat_id'] == '' or $_POST['title'] == '' or $_POST['fund_for'] == '' or $_POST['fund_amt'] == '') {
    $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Something Went Wrong!"];
} else {
    $cat_id = strip_tags($h->real_string($_POST['cat_id']));
    $title = strip_tags($h->real_string($_POST['title']));
    $fund_for = strip_tags($h->real_string($_POST['fund_for']));
    $fund_amt = strip_tags($h->real_string($_POST['fund_amt']));
    $full_address = strip_tags($h->real_string($_POST['full_address']));
	$lats = strip_tags($h->real_string($_POST['lats']));
	$longs = strip_tags($h->real_string($_POST['longs']));
    $fund_story = strip_tags($h->real_string($_POST['fund_story']));
    $exp_date = strip_tags($h->real_string($_POST['exp_date']));
    $patient_title = strip_tags($h->real_string($_POST['patient_title']));
    $patient_diagnosis = strip_tags($h->real_string($_POST['patient_diagnosis']));
    $fund_plan = strip_tags($h->real_string($_POST['fund_plan']));
	$status = $_POST['status'];
	$charity_id = $_POST['charity_id'];
    $fundsize = isset($_POST['fundsize']) ? (int) $_POST['fundsize'] : 0;
    $petientsize = isset($_POST['petientsize']) ? (int) $_POST['petientsize'] : 0;
    $certicatesize = isset($_POST['certicatesize']) ? (int) $_POST['certicatesize'] : 0;
    $fund_date = date("Y-m-d");
	$uid = $_POST["uid"];
    if ($fundsize > 0) {
        // Process single file uploads
        $uploadedFiles = processFileUploads('fundpic', $fundsize, IMAGE_PATH);
        $multifile = implode('$;', $uploadedFiles);
    }

    if ($petientsize > 0) {
        // Process single file uploads
        $uploadedFiless = processFileUploads('petpic', $petientsize, IMAGE_PATHS);
        $multifiles = implode('$;', $uploadedFiless);
    }

    if ($certicatesize > 0) {
        // Process single file uploads
        $uploadedFilesss = processFileUploads('certpic', $certicatesize, IMAGE_PATHSS);
        $multifiless = implode('$;', $uploadedFilesss);
    }
   if(empty($exp_date))
   {
	      $table = "tbl_fund";
    $field_values = ["cat_id", "title", "fund_for", "fund_amt", "fund_story", "fund_photos", "fund_date", "patient_photo", "patient_title", "patient_diagnosis", "fund_plan", "medical_certificate","uid","status","charity_id","longs","lats","full_address"];
    $data_values = ["$cat_id", "$title", "$fund_for", "$fund_amt", "$fund_story", "$multifile", "$fund_date", "$multifiles", "$patient_title", "$patient_diagnosis", "$fund_plan", "$multifiless","$uid","$status","$charity_id","$longs","$lats","$full_address"];
   }
   else 
   {
    $table = "tbl_fund";
    $field_values = ["cat_id", "title", "fund_for", "fund_amt", "fund_story", "fund_photos", "exp_date", "fund_date", "patient_photo", "patient_title", "patient_diagnosis", "fund_plan", "medical_certificate","uid","status","charity_id","longs","lats","full_address"];
    $data_values = ["$cat_id", "$title", "$fund_for", "$fund_amt", "$fund_story", "$multifile", "$exp_date", "$fund_date", "$multifiles", "$patient_title", "$patient_diagnosis", "$fund_plan", "$multifiless","$uid","$status","$charity_id","$longs","$lats","$full_address"];
   }
    
    $check = $h->insertData_Api($field_values, $data_values, $table);
    $returnArr = ["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Fund Raise Submited Wait For Approval!!"];
}
echo json_encode($returnArr);
?>
