<?php
/**
 * Endpoint: faas_building_load.php
 * Loads a full FAAS Building record (property + building + child rows) as JSON.
 */
require_once '../includes/db.php';

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['error' => 'id is required.']);
    exit;
}

$stmt = $conn->prepare("SELECT b.*, p.id AS property_id, p.pin, p.arp_no, p.transaction_code,
    p.owner_name, p.owner_address, p.owner_tel, p.owner_tin, p.beneficial_user, p.beneficial_address,
    p.admin_tel, p.admin_tin, p.street, p.barangay, p.municipality, p.province
    FROM tbl_faas_building b
    JOIN tbl_properties p ON p.id = b.property_id
    WHERE b.id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['error' => 'Record not found.']);
    exit;
}

$row['building_id'] = $row['id'];

$items = $conn->prepare("SELECT description, qty, amount FROM tbl_faas_building_items WHERE faas_building_id = ?");
$items->bind_param('i', $id);
$items->execute();
$itemsResult = $items->get_result();
$row['addl_item_desc'] = [];
$row['addl_item_qty'] = [];
$row['addl_item_amount'] = [];
while ($item = $itemsResult->fetch_assoc()) {
    $row['addl_item_desc'][] = $item['description'];
    $row['addl_item_qty'][] = $item['qty'];
    $row['addl_item_amount'][] = $item['amount'];
}
$items->close();

$superseded = $conn->prepare("SELECT pin, arp_no, assessed_value, effectivity, previous_owner, ar_page, recorder, record_date
    FROM tbl_faas_building_superseded WHERE faas_building_id = ?");
$superseded->bind_param('i', $id);
$superseded->execute();
$supersededResult = $superseded->get_result();
$row['superseded_pin'] = [];
$row['superseded_arp'] = [];
$row['superseded_assessed_value'] = [];
$row['superseded_effectivity'] = [];
$row['superseded_prev_owner'] = [];
$row['superseded_ar_page'] = [];
$row['superseded_recorder'] = [];
$row['superseded_date'] = [];
while ($s = $supersededResult->fetch_assoc()) {
    $row['superseded_pin'][] = $s['pin'];
    $row['superseded_arp'][] = $s['arp_no'];
    $row['superseded_assessed_value'][] = $s['assessed_value'];
    $row['superseded_effectivity'][] = $s['effectivity'];
    $row['superseded_prev_owner'][] = $s['previous_owner'];
    $row['superseded_ar_page'][] = $s['ar_page'];
    $row['superseded_recorder'][] = $s['recorder'];
    $row['superseded_date'][] = $s['record_date'];
}
$superseded->close();

echo json_encode($row);
$conn->close();