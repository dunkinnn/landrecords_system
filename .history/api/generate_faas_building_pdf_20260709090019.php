<?php
/**
 * Endpoint: generate_faas_building_pdf.php
 * Renders the FAAS Building & Other Improvements sheet (front + back)
 * as a PDF for the given building record id.
 * NOTE: structurally matches the sample provided, not a pixel-exact
 * reproduction of the official form (exact margins/fonts not available).
 */
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';
require_once 'faas_pdf_data.php';
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

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

function esc($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }
function fmtMoney($v) { return $v !== null ? number_format((float) $v, 2) : ''; }
function checkbox($checked) { return $checked ? '&#9746;' : '&#9744;'; }

$materials = function ($csv) {
    return $csv ? array_map('trim', explode(',', $csv)) : [];
};
$roofMaterials = $materials($r['roof_material']);
$floorMaterials = $materials($r['floor_material']);
$wallMaterials = $materials($r['wall_material']);
$has = function ($list, $value) { return in_array($value, $list, true); };

$itemsRows = '';
foreach ($r['items'] as $item) {
    $itemsRows .= '<tr><td>' . esc($item['description']) . '</td><td>' . esc($item['qty']) . '</td><td class="text-end">' . fmtMoney($item['amount']) . '</td></tr>';
}

$supersededRows = '';
foreach ($r['superseded'] as $s) {
    $supersededRows .= '<tr>
        <td>' . esc($s['pin']) . '</td>
        <td>' . esc($s['arp_no']) . '</td>
        <td class="text-end">' . fmtMoney($s['assessed_value']) . '</td>
        <td>' . esc($s['effectivity']) . '</td>
        <td>' . esc($s['previous_owner']) . '</td>
        <td>' . esc($s['ar_page']) . '</td>
        <td>' . esc($s['recorder']) . '</td>
        <td>' . esc($s['record_date']) . '</td>
    </tr>';
}

$html = '<html><head><style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    h2 { text-align: center; font-size: 13px; margin-bottom: 2px; }
    .sub { text-align: center; font-size: 10px; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
    td, th { border: 1px solid #333; padding: 3px 5px; vertical-align: top; }
    .no-border td { border: none; padding: 2px 4px; }
    .label { font-weight: bold; font-size: 9px; color: #444; }
    .section-title { background: #eee; font-weight: bold; padding: 4px; margin-top: 10px; border: 1px solid #333; }
    .text-end { text-align: right; }
    .page-break { page-break-before: always; }
</style></head><body>

<h2>REAL PROPERTY FIELD APPRAISAL &amp; ASSESSMENT SHEET</h2>
<div class="sub">BUILDING &amp; OTHER IMPROVEMENTS &mdash; Municipality of San Pablo, Isabela</div>

<table class="no-border">
    <tr>
        <td class="label">ARP No.</td><td>' . esc($r['arp_no']) . '</td>
        <td class="label">PIN</td><td>' . esc($r['pin']) . '</td>
        <td class="label">Transaction Code</td><td>' . esc($r['transaction_code']) . '</td>
    </tr>
</table>

<div class="section-title">Property Information</div>
<table class="no-border">
    <tr><td class="label" style="width:18%;">Owner</td><td>' . esc($r['owner_name']) . '</td></tr>
    <tr><td class="label">R. Address</td><td>' . esc($r['owner_address']) . '</td></tr>
    <tr><td class="label">Tel. No. / TIN</td><td>' . esc($r['owner_tel']) . ' / ' . esc($r['owner_tin']) . '</td></tr>
    <tr><td class="label">Administrator</td><td>' . esc($r['beneficial_user']) . '</td></tr>
    <tr><td class="label">R. Address</td><td>' . esc($r['beneficial_address']) . '</td></tr>
</table>

<div class="section-title">Building Location</div>
<table class="no-border">
    <tr>
        <td class="label" style="width:18%;">No./Street</td><td>' . esc($r['street']) . '</td>
        <td class="label" style="width:14%;">Barangay</td><td>' . esc($r['barangay']) . '</td>
    </tr>
    <tr>
        <td class="label">Municipality</td><td>' . esc($r['municipality']) . '</td>
        <td class="label">Province</td><td>' . esc($r['province']) . '</td>
    </tr>
</table>

<div class="section-title">Land Reference</div>
<table class="no-border">
    <tr>
        <td class="label" style="width:14%;">Lot No.</td><td>' . esc($r['lot_number']) . '</td>
        <td class="label" style="width:14%;">Block No.</td><td>' . esc($r['block_number']) . '</td>
        <td class="label" style="width:14%;">Survey No.</td><td>' . esc($r['survey_number']) . '</td>
    </tr>
    <tr>
        <td class="label">OCT/TCT/CLOA</td><td>' . esc($r['oct_tct_no']) . '</td>
        <td class="label">Land Owner</td><td>' . esc($r['land_owner']) . '</td>
        <td class="label">Area</td><td>' . esc($r['land_area']) . ' sqm</td>
    </tr>
</table>

<div class="section-title">General Description</div>
<table class="no-border">
    <tr>
        <td class="label" style="width:18%;">Kind of Bldg.</td><td>' . esc($r['building_kind']) . '</td>
        <td class="label" style="width:18%;">Structural Type</td><td>' . esc($r['structural_type']) . '</td>
    </tr>
    <tr>
        <td class="label">Bldg. Age</td><td>' . esc($r['building_age']) . '</td>
        <td class="label">No. of Storeys</td><td>' . esc($r['storeys']) . '</td>
    </tr>
    <tr>
        <td class="label">1st Flr. Area</td><td>' . esc($r['first_floor_area']) . '</td>
        <td class="label">2nd Flr. Area</td><td>' . esc($r['second_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="label">3rd Flr. Area</td><td>' . esc($r['third_floor_area']) . '</td>
        <td class="label">4th Flr. Area</td><td>' . esc($r['fourth_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="label">Total Floor Area</td><td>' . esc($r['total_floor_area']) . ' sqm</td>
        <td class="label">Date Constructed</td><td>' . esc($r['date_constructed']) . '</td>
    </tr>
</table>

<div class="section-title">Structural Materials</div>
<table>
    <tr><th>Roof</th><th>Flooring</th><th>Walls &amp; Partitions</th></tr>
    <tr>
        <td>' . checkbox($has($roofMaterials, 'Reinforced Concrete')) . ' Reinforced Concrete<br>
            ' . checkbox($has($roofMaterials, 'G.I. Sheet')) . ' G.I. Sheet<br>
            ' . checkbox($has($roofMaterials, 'Tiles')) . ' Tiles<br>
            ' . checkbox($has($roofMaterials, 'Others')) . ' Others: ' . esc($r['roof_material_other']) . '</td>
        <td>' . checkbox($has($floorMaterials, 'Reinforced Concrete')) . ' Reinforced Concrete<br>
            ' . checkbox($has($floorMaterials, 'Plain Cement')) . ' Plain Cement<br>
            ' . checkbox($has($floorMaterials, 'Tiles')) . ' Tiles<br>
            ' . checkbox($has($floorMaterials, 'Others')) . ' Others: ' . esc($r['floor_material_other']) . '</td>
        <td>' . checkbox($has($wallMaterials, 'Reinforced Concrete')) . ' Reinforced Concrete<br>
            ' . checkbox($has($wallMaterials, 'CHB')) . ' CHB<br>
            ' . checkbox($has($wallMaterials, 'Wood')) . ' Wood<br>
            ' . checkbox($has($wallMaterials, 'Others')) . ' Others: ' . esc($r['wall_material_other']) . '</td>
    </tr>
</table>

<div class="page-break"></div>

<div class="section-title">Additional Items</div>
<table>
    <tr><th>Description</th><th>Area/Qty</th><th>Amount (&#8369;)</th></tr>
    ' . ($itemsRows ?: '<tr><td colspan="3" style="text-align:center;color:#888;">None</td></tr>') . '
</table>

<div class="section-title">Property Appraisal</div>
<table class="no-border">
    <tr>
        <td class="label" style="width:25%;">Unit Construction Cost</td><td>&#8369; ' . fmtMoney($r['back_unit_construction_cost']) . '</td>
        <td class="label" style="width:25%;">Building Core Sub-Total</td><td>&#8369; ' . fmtMoney($r['building_core_subtotal']) . '</td>
    </tr>
    <tr>
        <td class="label">Cost of Additional Items</td><td>&#8369; ' . fmtMoney($r['addl_items_total']) . '</td>
        <td class="label">Total Construction Cost</td><td>&#8369; ' . fmtMoney($r['total_construction_cost']) . '</td>
    </tr>
    <tr>
        <td class="label">Depreciation Rate</td><td>' . esc($r['back_depreciation_rate']) . '%</td>
        <td class="label">Depreciation Cost</td><td>&#8369; ' . fmtMoney($r['depreciation_cost']) . '</td>
    </tr>
    <tr>
        <td class="label">Market Value</td><td colspan="3">&#8369; ' . fmtMoney($r['back_market_value']) . '</td>
    </tr>
</table>

<div class="section-title">Property Assessment</div>
<table>
    <tr><th>Actual Use</th><th>Market Value (&#8369;)</th><th>Assessment Level (%)</th><th>Assessed Value (&#8369;)</th></tr>
    <tr>
        <td>' . esc($r['back_actual_use']) . '</td>
        <td class="text-end">' . fmtMoney($r['back_assess_market_value']) . '</td>
        <td class="text-end">' . esc($r['back_assessment_level']) . '</td>
        <td class="text-end">' . fmtMoney($r['back_assessed_value']) . '</td>
    </tr>
    <tr><td colspan="3" class="text-end"><strong>TOTAL</strong></td><td class="text-end"><strong>' . fmtMoney($r['back_total_assessed_value']) . '</strong></td></tr>
</table>

<table class="no-border">
    <tr>
        <td class="label" style="width:20%;">Taxability</td>
        <td>' . checkbox($r['taxability'] === 'Taxable') . ' Taxable &nbsp;&nbsp; ' . checkbox($r['taxability'] === 'Exempt') . ' Exempt</td>
        <td class="label" style="width:25%;">Effectivity</td>
        <td>' . esc($r['effectivity_quarter']) . ' Quarter, ' . esc($r['effectivity_year']) . '</td>
    </tr>
</table>

<div class="section-title">Memoranda</div>
<table class="no-border"><tr><td>' . esc($r['memoranda']) . '</td></tr></table>

' . ($supersededRows ? '
<div class="section-title">Record of Superseded Assessment</div>
<table>
    <tr><th>PIN</th><th>ARP/TD No.</th><th>Assessed Value</th><th>Effectivity</th><th>Prev. Owner</th><th>AR Page</th><th>Recorder</th><th>Date</th></tr>
    ' . $supersededRows . '
</table>' : '') . '

<table class="no-border" style="margin-top:20px;">
    <tr>
        <td style="width:33%;text-align:center;">' . esc($r['appraised_by_name']) . '<br>_______________________<br>Appraised/Assessed By</td>
        <td style="width:33%;text-align:center;">' . esc($r['recommending_approval_name']) . '<br>_______________________<br>Recommending Approval</td>
        <td style="width:33%;text-align:center;">' . esc($r['approved_by_name']) . '<br>_______________________<br>Approved By</td>
    </tr>
</table>

</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('legal', 'portrait');
$dompdf->render();

faas_pdf_mark_generated($conn, $id);

$dompdf->stream('FAAS-Building-' . preg_replace('/[^A-Za-z0-9-]/', '', $r['arp_no']) . '.pdf', ['Attachment' => false]);