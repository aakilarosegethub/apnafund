<?php
/**
 * Create users table script
 * Run this file once to create the users table
 */

require_once __DIR__ . '/api-no/Connection.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "Creating users table...\n\n";

$sql = "CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `firstname` varchar(255) DEFAULT NULL,
  `lastname` varchar(255) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `mobile` varchar(255) DEFAULT NULL,
  `country_code` varchar(255) DEFAULT NULL,
  `country_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `kc` tinyint(1) DEFAULT 0,
  `ec` tinyint(1) DEFAULT 0,
  `sc` tinyint(1) DEFAULT 0,
  `ts` tinyint(1) DEFAULT 0,
  `tc` tinyint(1) DEFAULT 0,
  `ref_by` bigint(20) UNSIGNED DEFAULT 0,
  `ver_code` varchar(255) DEFAULT NULL,
  `ver_code_send_at` timestamp NULL DEFAULT NULL,
  `balance` decimal(18,8) DEFAULT 0.00000000,
  `kyc_data` text DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

if ($conn->query($sql) === TRUE) {
    echo "✓ Users table created successfully!\n\n";
    
    // Check if table exists
    $result = $conn->query("SHOW TABLES LIKE 'users'");
    if ($result && $result->num_rows > 0) {
        echo "✓ Table verified: users table exists\n";
        
        // Show table structure
        $result = $conn->query("DESCRIBE users");
        if ($result) {
            echo "\nTable structure:\n";
            echo str_repeat("-", 80) . "\n";
            while ($row = $result->fetch_assoc()) {
                printf("%-20s %-20s %-10s %-10s\n", 
                    $row['Field'], 
                    $row['Type'], 
                    $row['Null'], 
                    $row['Key']
                );
            }
        }
    }
} else {
    echo "✗ Error creating table: " . $conn->error . "\n";
    
    // Check if table already exists
    if (strpos($conn->error, "already exists") !== false) {
        echo "\n✓ Table already exists. You can proceed to use the API.\n";
    }
}

$conn->close();
?>

