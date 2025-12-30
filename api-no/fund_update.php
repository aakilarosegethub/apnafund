<?php
require 'Gofund.php';
$h = new Gofund($fund);
header('Content-type: text/json');
define('BASE_PATH', dirname(dirname(__FILE__)));
define('IMAGE_PATH', '/images/fund_update/');
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
if ($_POST['fund_id'] == '' or $_POST['description'] == '') {
    $returnArr = ["ResponseCode" => "401", "Result" => "false", "ResponseMsg" => "Something Went Wrong!"];
} else {
	$fund_id = strip_tags($h->real_string($_POST['fund_id']));
    $description = strip_tags($h->real_string($_POST['description']));
	$uid = $_POST["uid"];
	$size = isset($_POST['size']) ? (int) $_POST['size'] : 0;
	$timestamp = date("Y-m-d H:i:s");
	if ($size > 0) {
        // Process single file uploads
        $uploadedFiles = processFileUploads('fundupdate', $size, IMAGE_PATH);
        $multifile = implode('$;', $uploadedFiles);
		
		$table = "fund_update";
    $field_values = ["fund_id", "uid", "photo", "update_desc", "update_date"];
    $data_values = ["$fund_id", "$uid", "$multifile", "$description", "$timestamp"];
	

    $check = $h->insertData_Api($field_values, $data_values, $table);
    }
	else 
	{
		$table = "fund_update";
    $field_values = ["fund_id", "uid", "update_desc", "update_date"];
    $data_values = ["$fund_id", "$uid", "$description", "$timestamp"];
	
	
    $check = $h->insertData_Api($field_values, $data_values, $table);
	}
	

	
    $returnArr = ["ResponseCode" => "200", "Result" => "true", "ResponseMsg" => "Fund Update Send Successfully!!"];
}
echo json_encode($returnArr);
?>