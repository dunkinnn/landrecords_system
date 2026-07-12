<?php
/**
 * Endpoint: faas_building_save.php
 * Saves a full FAAS Building record across tbl_properties, tbl_faas_building,
 * and its two child tables. Upserts based on property_id / building_id.
 */
require_once '../includes/db.php';

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    echo json_encode(['error' => 'Invalid request body.']);
    exit;
}

/* Convert empty strings to null so blank dates/numbers do not fail on insert. */
function normalizeEmpty($arr) {
    foreach ($arr as $k => $v) {
        if ($v === '') $arr[$k] = null;
    }
    return $arr;
}

/* Generic upsert. Column names come from a fixed whitelist below, never
   directly from client input, so this is safe from column injection. */
function upsert($conn, $table, $data, $idField, $idValue) {
    $columns = array_keys($data);
    $values = array_values($data);
    $types = str_repeat('s', count($values));

    if ($idValue) {
        $set = implode(', ', array_map(function ($c) { return "$c = ?"; }, $columns));
        $stmt = $conn->prepare("UPDATE $table SET $set WHERE $idField = ?");
        $stmt->bind_param($types . 'i', ...array_merge($values, [$idValue]));
        $stmt->execute();
        $stmt->close();
        return (int) $idValue;
    }
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $stmt = $conn->prepare("INSERT INTO $table (" . implode(', ', $columns) . ") VALUES ($placeholders)");
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $newId = $conn->insert_id;
    $stmt->close();
    return (int) $newId;
}

$conn->begin_transaction();

$propertyFields = ['pin', 'arp_no', 'transaction_code', 'owner_name', 'owner_address', 'owner_tel',
    'owner_tin', 'beneficial_user', 'beneficial_address', 'admin_tel', 'admin_tin',
    'street', 'barangay', 'municipality', 'province'];
$propertyData = [];
foreach ($propertyFields as $f) {
    $propertyData[$f] = $input[$f] ?? null;
}

/* Municipality/Province are always SAN PABLO, ISABELA for every record in
   this system - set authoritatively here instead of trusting the browser
   to have fired the barangay dropdown's change handler. This fixes blank
   municipality/province on edit (populateForm() sets the select's value
   via JS, which does not trigger a native change event) and any other
   path where barangay ends up set without the frontend auto-fill running. */
if (!empty($propertyData['barangay'])) {
    $propertyData['municipality'] = 'SAN PABLO';
    $propertyData['province'] = 'ISABELA';
}

$propertyId = upsert($conn, 'tbl_properties', normalizeEmpty($propertyData), 'id', $input['property_id'] ?? null);

$buildingFields = ['lot_number', 'block_number', 'survey_number', 'oct_tct_no', 'land_owner', 'land_area',
    'building_kind', 'structural_type', 'building_age', 'storeys', 'building_permit_no', 'permit_date',
    'cct_no', 'cert_completion_date', 'cert_occupancy_date', 'first_floor_area', 'second_floor_area',
    'third_floor_area', 'fourth_floor_area', 'total_floor_area', 'date_constructed', 'date_occupied',
    'roof_material_other', 'floor_material_other', 'wall_material_other',
    'unit_construction_cost', 'additional_item_cost', 'depreciation_rate', 'actual_use', 'assessment_level',
    'building_cost', 'market_value', 'assessed_value',
    'back_unit_construction_cost', 'building_core_subtotal', 'addl_items_total', 'total_construction_cost',
    'back_depreciation_rate', 'depreciation_cost', 'back_market_value',
    'back_actual_use', 'back_assess_market_value', 'back_assessment_level', 'back_assessed_value',
    'back_total_assessed_value', 'taxability', 'effectivity_quarter', 'effectivity_year', 'memoranda',
    'appraised_by_name', 'appraised_by_date', 'recommending_approval_name', 'recommending_approval_date',
    'approved_by_name', 'approved_by_date'];
$buildingData = ['property_id' => $propertyId];
foreach ($buildingFields as $f) {
    $buildingData[$f] = $input[$f] ?? null;
}
$buildingData['roof_material'] = isset($input['roof_material']) ? implode(',', (array) $input['roof_material']) : null;
$buildingData['floor_material'] = isset($input['floor_material']) ? implode(',', (array) $input['floor_material']) : null;
$buildingData['wall_material'] = isset($input['wall_material']) ? implode(',', (array) $input['wall_material']) : null;

$buildingId = upsert($conn, 'tbl_faas_building', normalizeEmpty($buildingData), 'id', $input['building_id'] ?? null);

/* Child rows are replaced wholesale on every save, simplest correct approach
   for a small admin form (no partial-row diffing needed). */
$conn->query("DELETE FROM tbl_faas_building_items WHERE faas_building_id = " . $buildingId);
if (!empty($input['addl_item_desc'])) {
    $stmt = $conn->prepare("INSERT INTO tbl_faas_building_items (faas_building_id, description, qty, amount) VALUES (?, ?, ?, ?)");
    foreach ($input['addl_item_desc'] as $i => $desc) {
        if ($desc === '') continue;
        $qty = $input['addl_item_qty'][$i] ?? '';
        $amount = $input['addl_item_amount'][$i] ?? 0;
        $stmt->bind_param('issd', $buildingId, $desc, $qty, $amount);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->query("DELETE FROM tbl_faas_building_superseded WHERE faas_building_id = " . $buildingId);
if (!empty($input['superseded_pin'])) {
    $stmt = $conn->prepare("INSERT INTO tbl_faas_building_superseded
        (faas_building_id, pin, arp_no, assessed_value, effectivity, previous_owner, ar_page, recorder, record_date)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    foreach ($input['superseded_pin'] as $i => $pin) {
        if ($pin === '') continue;
        $arp = $input['superseded_arp'][$i] ?? '';
        $value = $input['superseded_assessed_value'][$i] ?? 0;
        $eff = $input['superseded_effectivity'][$i] ?? '';
        $prevOwner = $input['superseded_prev_owner'][$i] ?? '';
        $arPage = $input['superseded_ar_page'][$i] ?? '';
        $recorder = $input['superseded_recorder'][$i] ?? '';
        $date = $input['superseded_date'][$i] ?: null;
        $stmt->bind_param('issdsssss', $buildingId, $pin, $arp, $value, $eff, $prevOwner, $arPage, $recorder, $date);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->commit();

echo json_encode(['property_id' => $propertyId, 'building_id' => $buildingId]);
$conn->close();