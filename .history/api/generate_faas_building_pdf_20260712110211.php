<?php
/**
 * Endpoint: generate_faas_building_pdf.php
 * FPDI coordinate-overlay approach: imports the actual blank
 * "FAAS - BUILDING" PDF (front + back) as a template and writes
 * field values directly on top at fixed coordinates, rather than
 * rebuilding the layout from scratch.
 *
 * SETUP REQUIRED:
 * 1. Place the blank template PDF at: api/pdf_templates/FAAS-BUILDING-blank.pdf
 * 2. FPDI must be installed: composer require setasign/fpdi setasign/fpdf
 *    (if Packagist is blocked on your network, clone from GitHub as done
 *    previously - see docs/activity-log.md for that precedent)
 *
 * COORDINATES: extracted via OCR from the blank template (in points,
 * top-left origin, Letter size 612x792) and cross-checked against the
 * visible row spacing pattern. Most are OCR-confirmed; a few rows where
 * OCR missed text were inferred from the consistent ~18.2pt row
 * spacing nearby. Expect to nudge some x/y values after a real test
 * print - this was not (and cannot be) rendered and visually verified
 * against the actual template in this environment.
 *
 * KNOWN GAP: the real template's Roof checklist includes a "Concrete
 * Desc" option that does not exist anywhere in 24-FAAS-BUILDING.php's
 * roof_material checkbox list. It cannot be marked because the current
 * form never captures it. Flagged, not fabricated.
 *
 * KNOWN LIMITATION: the template has per-floor (1st/2nd/3rd/4th)
 * checkbox columns for Flooring and Walls. Since the form only
 * captures one flat list per material (no per-floor data), any
 * selected material is marked in the "1st Flr" column only, as a
 * best-effort placement - not a claim that it is specifically 1st
 * floor data.
 */
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'faas_pdf_data.php';
require_once '../vendor/autoload.php';

use setasign\Fpdi\Fpdi;

$id = (int) ($_GET['id'] ?? 0);
if (!$id) {
    http_response_code(400);
    die('Missing or invalid id.');
}

$r = faas_pdf_fetch_record($conn, $id);
if (!$r) {
    http_response_code(404);
    die('Record not found.');
}

$templatePath = __DIR__ . '/pdf_templates/FAAS-BUILDING-blank.pdf';
if (!file_exists($templatePath)) {
    http_response_code(500);
    die('Blank template not found at ' . $templatePath . '. Place the PDF there first.');
}

function v($val) { return $val === null ? '' : (string) $val; }
function money($val) { return $val !== null && $val !== '' ? number_format((float) $val, 2) : ''; }
function num($val) { return $val !== null && $val !== '' ? rtrim(rtrim(number_format((float) $val, 2), '0'), '.') : ''; }

$materials = function ($csv) {
    return $csv ? array_map('trim', explode(',', $csv)) : [];
};
$roofMaterials = $materials($r['roof_material']);
$floorMaterials = $materials($r['floor_material']);
$wallMaterials = $materials($r['wall_material']);

$latestSuperseded = $r['superseded'][0] ?? null;

// --- PDF setup: unit 'pt' so all coordinates below match the OCR extraction directly ---
$pdf = new Fpdi('P', 'pt', [612, 792]);
$pdf->SetAutoPageBreak(false);
$pdf->setSourceFile($templatePath);

/**
 * Writes text with $y treated as the TOP of the label (matching how the
 * coordinates were extracted), converting internally to FPDF's
 * baseline-based Text() by nudging down roughly the font's ascent.
 */
function put($pdf, $x, $yTop, $text, $size = 8, $bold = false) {
    if ($text === '' || $text === null) return;
    $pdf->SetFont('Arial', $bold ? 'B' : '', $size);
    $pdf->Text($x, $yTop + ($size * 0.78), $text);
}

function putMulti($pdf, $x, $yTop, $width, $text, $size = 8, $bold = false, $lineHeight = 4.5) {
    if ($text === '' || $text === null) return;
    $pdf->SetFont('Arial', $bold ? 'B' : '', $size);
    $pdf->SetXY($x, $yTop + ($size * 0.78));
    $pdf->MultiCell($width, $lineHeight, $text, 1, 'L');
}

function mark($pdf, $x, $yTop, $selected) {
    if ($selected) {
        put($pdf, $x, $yTop, 'X', 8, true);
    }
}

// ============================================================
// PAGE 1 (front)
// ============================================================
$tpl1 = $pdf->importPage(1);
$pdf->AddPage();
$pdf->useTemplate($tpl1);

put($pdf, 530, 45, v($r['transaction_code']), 8, true);

put($pdf, 120, 66, v($r['arp_no']), 15, true);
put($pdf, 360, 66, v($r['pin']), 15, true);
put($pdf, 118, 92.6, v($r['owner_name']), 10, true);
put($pdf, 125, 110, v($r['owner_address']), 10);
put($pdf, 112, 128.6, v($r['owner_tel']), 9);
put($pdf, 355, 128.6, v($r['owner_tin']), 9);
put($pdf, 200, 145.5, v($r['beneficial_user']), 10, true);
put($pdf, 125, 164.2, v($r['beneficial_address']), 10);
put($pdf, 112, 181.9, v($r['admin_tel']), 9);
put($pdf, 355, 181.3, v($r['admin_tin']), 9);

// Building Location
put($pdf, 140, 228, v($r['street']), 9);
put($pdf, 140, 251, v($r['barangay']), 9);
put($pdf, 140, 275, v($r['municipality']), 9);
put($pdf, 140, 296.2, v($r['province']), 9);
    
// Land Reference
put($pdf, 375, 228, v($r['land_owner']),9);
put($pdf, 375, 250.6, v($r['oct_tct_no']), 9);
put($pdf, 490, 250, v($r['survey_number']), 9);
put($pdf, 375, 275, v($r['lot_number']), 9);
put($pdf, 490, 275, v($r['block_number']), 9);
put($pdf, 375, 296.2, num($r['land_area']), 9);

// General Description (y positions for the first 3 rows inferred from
// the confirmed ~18.2pt spacing of rows below them - OCR missed these
// specific rows on the blank template, see file header note)
put($pdf, 150, 340, v($r['building_kind']));
put($pdf, 400, 342.5, v($r['building_age']));
put($pdf, 150, 358, v($r['structural_type']));
put($pdf, 400, 360, v($r['storeys']));
put($pdf, 150, 378, v($r['building_permit_no']));
put($pdf, 275, 378, v($r['permit_date']));
put($pdf, 400, 378, num($r['first_floor_area']));
put($pdf, 250, 394.6, v($r['cct_no']));
put($pdf, 400, 395.5, num($r['second_floor_area']));
put($pdf, 250, 412.8, v($r['cert_completion_date']));
put($pdf, 400, 413, num($r['third_floor_area']));
put($pdf, 250, 430.6, v($r['cert_occupancy_date']));
put($pdf, 400, 431, num($r['fourth_floor_area']));
put($pdf, 200, 448.8, v($r['date_constructed']));
put($pdf, 200, 466.6, v($r['date_occupied']));
put($pdf, 400, 466.6, num($r['total_floor_area']) . ' sqm', 8, true);

// Structural Materials - the template already prints every material
// name; we only mark an X where the record has that material selected.
$roofRows = [
    'Reinforced Concrete' => 596.6, 'Tiles' => 616.3, 'G.I. Sheet' => 636.0,
    'Aluminum' => 654.2, 'Asbestos' => 666.7, 'Long Span' => 689.8,
    'Nipa/Anahaw/Cogon' => 725.3, 'Others' => 739,
];
foreach ($roofRows as $name => $y) {
    mark($pdf, 178, $y, in_array($name, $roofMaterials, true));
}
// "Concrete Desc" at y=707.5 on the real template has no corresponding
// checkbox in the data-entry form - cannot be marked, see file header.

$floorRows = [
    'Reinforced Concrete' => 592, 'Plain Cement' => 618, 'Marble' => 634,
    'Wood' => 654, 'Tiles' => 670, 'Others' => 689, 'Bamboo' => 708,
];
foreach ($floorRows as $name => $y) {
    mark($pdf, 330, $y, in_array($name, $floorMaterials, true));
}

$wallRows = [
    'Reinforced Concrete' => 596.6, 'Plain Cement' => 618.5, 'Wood' => 636.5,
    'CHB' => 653.8, 'G.I. Sheet' => 671.5, 'Build-a-Wall' => 689.7,
    'Sawali' => 707.5, 'Bamboo' => 725.8, 'Others' => 743,
];
foreach ($wallRows as $name => $y) {
    mark($pdf, 501, $y, in_array($name, $wallMaterials, true));
}

// ============================================================
// PAGE 2 (back)
// ============================================================
$tpl2 = $pdf->importPage(2);
$pdf->AddPage();
$pdf->useTemplate($tpl2);
 
// ADDITIONAL ITEMS box at the top of page 2 - a separate, smaller box
// (2 rows x 3 columns: description/qty/amount) from the itemized "Cost
// of Additional Items" list inside the Property Appraisal box below.
// Coordinates found geometrically (column/row border detection) on the
// blank template since this box has no printed text to OCR against.
$topItemsY = [24, 36];
foreach (array_slice($r['items'], 0, 2) as $i => $item) {
    put($pdf, 73, $topItemsY[$i], v($item['description']), 7);
    put($pdf, 259, $topItemsY[$i], v($item['qty']), 7);
    put($pdf, 459, $topItemsY[$i], money($item['amount']), 7);
}
 
put($pdf, 195, 87.8, money($r['back_unit_construction_cost']));
put($pdf, 84,  117, num($r['total_floor_area']));
put($pdf, 167, 117, money($r['back_unit_construction_cost']));
put($pdf, 250, 117, money($r['building_core_subtotal']));
put($pdf, 230, 219.8, money($r['building_core_subtotal']));
put($pdf, 480, 219.8, money($r['total_construction_cost']));
put($pdf, 230, 235.7, v($r['back_depreciation_rate']) . '%');
put($pdf, 480, 235.7, money($r['depreciation_cost']). '%');
put($pdf, 230, 253.0, money($r['depreciation_cost']));
put($pdf, 370, 253.0, money($r['back_market_value']));
 
// Cost of Additional Items - itemized list, up to 5 rows before the
// Sub-Total line at y=204. Shows description + qty together since the
// column is too narrow for 3 separate aligned columns; qty was
// previously dropped entirely - now included.
$itemY = 135;
foreach ($r['items'] as $item) {
    if ($itemY > 195) break; // do not overrun the Sub-Total line
    $label = v($item['description']);
    if (v($item['qty']) !== '') {
        $label .= '  (' . v($item['qty']) . ')';
    }
    put($pdf, 340, $itemY, $label, 7);
    put($pdf, 480, $itemY, money($item['amount']), 7);
    $itemY += 17;
}
$itemsTotal = array_sum(array_column($r['items'], 'amount'));
put($pdf, 480, 204, money($itemsTotal), 8, true);
 
// Property Assessment (single row - see activity-log.md re: table only
// supporting one actual-use row currently)
put($pdf, 115, 315, v($r['back_actual_use']));
put($pdf, 230, 315, money($r['back_assess_market_value']));
put($pdf, 345, 315, v($r['back_assessment_level']) . '%');
put($pdf, 475, 315, money($r['back_assessed_value']));
put($pdf, 475, 362.4, money($r['back_total_assessed_value']), 8, true);
 
mark($pdf, 94, 389, $r['taxability'] === 'Taxable');
mark($pdf, 178, 389, $r['taxability'] === 'Exempt');
put($pdf, 466, 388.8, v($r['effectivity_quarter']));
put($pdf, 520, 388.8, v($r['effectivity_year']));
 
put($pdf, 103, 455, v($r['appraised_by_name']), 9, True);
put($pdf, 235, 455, v($r['appraised_by_date']), 8, True);
put($pdf, 370, 455, v($r['recommending_approval_name']), 9, True);
put($pdf, 500, 455, v($r['recommending_approval_date']), 8, True);
 
put($pdf, 175, 514, v($r['approved_by_name']), 9, true);
put($pdf, 330, 515, v($r['approved_by_date']), 8, true);
 
putMulti($pdf, 80, 558, 480, v($r['memoranda']), 8);
 
// Record of Superseded Assessment
put($pdf, 110, 701.8, v($latestSuperseded['pin'] ?? ''));
put($pdf, 400, 701.8, v($latestSuperseded['arp_no'] ?? ''), 8, true);
put($pdf, 170, 720, money($latestSuperseded['assessed_value'] ?? null));
put($pdf, 420, 720, v($latestSuperseded['effectivity'] ?? ''));
put($pdf, 150, 740.2, v($latestSuperseded['previous_owner'] ?? ''));
put($pdf, 385, 740.2, v($latestSuperseded['ar_page'] ?? ''));
put($pdf, 180, 763.7, v($latestSuperseded['recorder'] ?? ''));
put($pdf, 350, 763.7, v($latestSuperseded['record_date'] ?? ''));
 
faas_pdf_mark_generated($conn, $id);
 
$pdf->Output('I', 'FAAS-Building-' . preg_replace('/[^A-Za-z0-9-]/', '', $r['arp_no']) . '.pdf');