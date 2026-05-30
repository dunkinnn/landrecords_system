<?php
$host = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "db_land_records";

// Create connection
$conn = new mysqli($host, $dbUser, $dbPass, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
