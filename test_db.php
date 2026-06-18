<?php
$conn = new mysqli('localhost', 'root', '', 'db_land_records');
$res = $conn->query("SELECT property_id, boundary_coordinates FROM tbl_properties ORDER BY property_id DESC LIMIT 1");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
?>
