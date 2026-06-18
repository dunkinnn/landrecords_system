<?php
$conn = new mysqli('localhost', 'root', '', 'db_land_records');
if ($conn->connect_error) die("Connection failed");

$conn->query("ALTER TABLE tbl_properties ADD COLUMN boundary_coordinates TEXT AFTER land_type");
echo "Column boundary_coordinates added successfully!\n";
?>
