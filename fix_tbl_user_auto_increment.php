<?php
/**
 * Fix tbl_user table to add AUTO_INCREMENT to id field
 * Run this script once to fix the database table structure
 */

require_once __DIR__ . '/api-no/Connection.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check current structure
$result = $conn->query("SHOW COLUMNS FROM tbl_user LIKE 'id'");
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "Current id field structure:\n";
    print_r($row);
    echo "\n";
}

// Fix the id field to be AUTO_INCREMENT
$sql = "ALTER TABLE `tbl_user` MODIFY COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT";

if ($conn->query($sql) === TRUE) {
    echo "✓ Successfully fixed tbl_user table! id field is now AUTO_INCREMENT.\n";
    
    // Verify the change
    $result = $conn->query("SHOW COLUMNS FROM tbl_user LIKE 'id'");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "\nUpdated id field structure:\n";
        print_r($row);
    }
} else {
    echo "✗ Error fixing table: " . $conn->error . "\n";
    
    // Try alternative method
    echo "\nTrying alternative method...\n";
    $sql2 = "ALTER TABLE `tbl_user` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT";
    if ($conn->query($sql2) === TRUE) {
        echo "✓ Successfully fixed using alternative method!\n";
    } else {
        echo "✗ Alternative method also failed: " . $conn->error . "\n";
    }
}

$conn->close();
?>

