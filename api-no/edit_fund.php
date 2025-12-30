<?php
require 'Gofund.php';
$h = new Gofund($fund);
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
	$record_id = $_POST['record_id'];
	$imlist = $_POST['imlist'];
	$imlists = $_POST['imlists'];
	$imlistss = $_POST['imlistss'];
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
	
	$imageList = '';
        if (empty($_FILES['fundpic0']['name'][0]) && $imlist != "0") {
            // No new image was uploaded, and there are existing images
            $imageList = $imlist;
        } elseif (empty($_FILES['fundpic0']['name'][0]) && $imlist == "0") {
            // No new image was uploaded, and there are no existing images
            $imageList = $imlist;
        } elseif ($imlist == "0") {
            // New images were uploaded, and there are no existing images
            $imageList = $multifile;
        } else {
            // New images were uploaded, and there are existing images
            $imageList = $imlist . '$;' . $multifile;
        }
		
		
		$imageLists = '';
        if (empty($_FILES['petpic0']['name'][0]) && $imlists != "0") {
            // No new image was uploaded, and there are existing images
            $imageLists = $imlists;
        } elseif (empty($_FILES['petpic0']['name'][0]) && $imlists == "0") {
            // No new image was uploaded, and there are no existing images
            $imageLists = $imlists;
        } elseif ($imlists == "0") {
            // New images were uploaded, and there are no existing images
            $imageLists = $multifiles;
        } else {
            // New images were uploaded, and there are existing images
            $imageLists = $imlists . '$;' . $multifiles;
        }
		
		
		$imageListss = '';
        if (empty($_FILES['certpic0']['name'][0]) && $imlistss != "0") {
            // No new image was uploaded, and there are existing images
            $imageListss = $imlistss;
        } elseif (empty($_FILES['certpic0']['name'][0]) && $imlistss == "0") {
            // No new image was uploaded, and there are no existing images
            $imageListss = $imlistss;
        } elseif ($imlistss == "0") {
            // New images were uploaded, and there are no existing images
            $imageListss = $multifiless;
        } else {
            // New images were uploaded, and there are existing images
            $imageListss = $imlistss . '$;' . $multifiless;
        }
		
		
	if(empty($exp_date))
	{
		$table = "tbl_fund";
                $field = [
                    "cat_id" => $cat_id,
					"title"   =>  $title,
                    "fund_for" => $fund_for,
                    "fund_amt" => $fund_amt,
                    "full_address" => $full_address,
					"lats" => $lats,
					"longs" => $longs,
                    "fund_story" => $fund_story,
                    "patient_title" => $patient_title,
                    "patient_diagnosis" => $patient_diagnosis,
					"fund_plan" => $fund_plan,
					"status" => $status,
					"fund_photos" => $imageList,
					"patient_photo" => $imageLists,
					"medical_certificate" => $imageListss,
					'is_approve'=>0,
                ];
	}
	else 
	{
	$table = "tbl_fund";
                $field = [
                    "cat_id" => $cat_id,
					"title"   =>  $title,
                    "fund_for" => $fund_for,
                    "fund_amt" => $fund_amt,
                    "full_address" => $full_address,
					"lats" => $lats,
					"longs" => $longs,
                    "fund_story" => $fund_story,
                    "exp_date" => $exp_date,
                    "patient_title" => $patient_title,
                    "patient_diagnosis" => $patient_diagnosis,
					"fund_plan" => $fund_plan,
					"status" => $status,
					"fund_photos" => $imageList,
					"patient_photo" => $imageLists,
					"medical_certificate" => $imageListss,
					'is_approve'=>0,
                ];
	}
                $where =
                    "where uid=" . $uid . " and id=".$record_id."";
                
                $check = $h->updateData_Api($field, $table, $where);
            
			
			$returnArr = ["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Fund Update Successfully Wait For Approval!!!!!"];
	
}
echo json_encode($returnArr);
?>