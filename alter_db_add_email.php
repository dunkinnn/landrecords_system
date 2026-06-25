<?php
$conn = new mysqli('localhost', 'root', '', 'db_land_records');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$columnCheck = $conn->query("SELECT COUNT(*) AS total FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db_land_records' AND TABLE_NAME = 'tbl_users' AND COLUMN_NAME = 'email'");
$columnExists = false;

if ($columnCheck) {
    $row = $columnCheck->fetch_assoc();
    $columnExists = ((int)($row['total'] ?? 0)) > 0;
    $columnCheck->close();
}

if (!$columnExists) {
    $conn->query("ALTER TABLE tbl_users ADD COLUMN email VARCHAR(255) NULL DEFAULT NULL AFTER username");
    $conn->query("ALTER TABLE tbl_users ADD UNIQUE KEY uq_tbl_users_email (email)");
    echo "Column email added successfully!\n";
} else {
    echo "Column email already exists.\n";
}
?>
