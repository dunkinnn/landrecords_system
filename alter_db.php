<?php
$conn = new mysqli('localhost', 'root', '', 'db_land_records');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->query("ALTER TABLE tbl_properties ADD COLUMN tax_dec_no VARCHAR(100) AFTER lot_number");
$conn->query("ALTER TABLE tbl_properties ADD COLUMN market_value DECIMAL(15,2) AFTER area_sqm");

echo "Schema updated successfully!\n";
?>
