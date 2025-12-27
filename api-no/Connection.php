<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Initialize default language
$currentLang = 'en'; // Default language

if(isset($_SESSION["sel_lan"]))
{
$currentLang = $_SESSION["sel_lan"];
include_once dirname(__DIR__) . "/languages/{$currentLang}.php";
}
else
{
// Load default language file if session language is not set
include_once dirname(__DIR__) . "/languages/en.php";
}
// Define constants only if they are not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost'); // Change this to your database host
}
if (!defined('DB_USER')) {
    define('DB_USER', 'root'); // Change this to your database username
}
if (!defined('DB_PASS')) {
    define('DB_PASS', ''); // Change this to your database password (empty for XAMPP default)
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'apnafund'); // Change this to your database name
}
if (!isset($apiKey)) {
    $apiKey = 'google_map_key';
}

// Define Base URL - Change this to your domain URL
if (!defined('BASE_URL')) {
    define('BASE_URL', 'https://gofund.cscodetech.cloud/');
}

?>