<?php
// db.php
$host = "localhost";        // Adjust if needed
$db_username = "root";      // Your DB username
$db_password = "";          // Your DB password
$dbname = "wellness_site";  // Database name

try {
    $conn = new mysqli($host, $db_username, $db_password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
} catch (Exception $e) {
    die("Database error: " . $e->getMessage());
}
?>