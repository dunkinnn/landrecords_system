<?php
$conn = new mysqli('localhost', 'root', '', 'db_land_records');
if ($conn->connect_error) die("Connection failed");

$conn->query("ALTER TABLE tbl_properties ADD COLUMN unit_value DECIMAL(15,2) AFTER area_sqm");
echo "Column unit_value added successfully!\n";
?>
