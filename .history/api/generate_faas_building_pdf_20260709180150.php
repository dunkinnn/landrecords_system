<?php
/**
 * Endpoint: generate_faas_building_pdf.php
 * Renders the FAAS Building & Other Improvements sheet (front + back)
 * as a single PDF, Letter size, matching the real form's layout as
 * closely as possible from the samples reviewed.
 *
 * Known fidelity gaps (not fabricated, see docs/activity-log.md):
 * - Flooring/Walls per-floor (1st/2nd/3rd/4th) checkbox columns are
 *   drawn but left empty - the Building form only captures one flat
 *   material list with no per-floor breakdown.
 * - The plain-text "Additional Items" description box (top of back
 *   page) is drawn empty - only the costed itemized list is captured
 *   by the current form.
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
function fmtMoney($v) { return $v !== null && $v !== '' ? number_format((float) $v, 2) : ''; }
function fmtNum($v) { return $v !== null && $v !== '' ? rtrim(rtrim(number_format((float) $v, 2), '0'), '.') : ''; }

$materials = function ($csv) {
    return $csv ? array_map('trim', explode(',', $csv)) : [];
};
$roofMaterials = $materials($r['roof_material']);
$floorMaterials = $materials($r['floor_material']);
$wallMaterials = $materials($r['wall_material']);
$mk = function ($list, $value) { return in_array($value, $list, true) ? 'X' : ''; };

$itemsRows = '';
$itemsTotal = 0;
foreach ($r['items'] as $item) {
    $itemsTotal += (float) $item['amount'];
    $itemsRows .= '<tr><td>' . esc($item['description']) . '</td><td class="text-end">' . fmtMoney($item['amount']) . '</td></tr>';
}
$itemsPadRows = '';
for ($i = count($r['items']); $i < 5; $i++) {
    $itemsPadRows .= '<tr><td>&nbsp;</td><td>&nbsp;</td></tr>';
}

$latestSuperseded = $r['superseded'][0] ?? null;

// Roof rows (single list, no per-floor split)
$roofItems = ['Reinforced Concrete', 'Tiles', 'G.I. Sheet', 'Aluminum', 'Asbestos', 'Long Span', 'Nipa/Anahaw/Cogon'];
$roofRowsHtml = '';
foreach ($roofItems as $item) {
    $roofRowsHtml .= '<tr><td>' . esc($item) . '</td><td class="text-center">' . $mk($roofMaterials, $item) . '</td></tr>';
}
$roofRowsHtml .= '<tr><td>Others (Specify): ' . esc($r['roof_material_other']) . '</td><td class="text-center">' . $mk($roofMaterials, 'Others') . '</td></tr>';

// Flooring / Walls rows (with 4 empty per-floor columns each, not captured by the form)
$floorItems = ['Reinforced Concrete (for upper floors)', 'Plain Cement', 'Marble', 'Wood', 'Tiles', 'Bamboo'];
$wallItems = ['Reinforced Concrete', 'Plain Cement', 'Wood', 'CHB', 'G.I. Sheet', 'Build-a-Wall', 'Sawali', 'Bamboo'];
$maxRows = max(count($floorItems), count($wallItems));
$materialGridRows = '';
for ($i = 0; $i < $maxRows; $i++) {
    $floorLabel = $floorItems[$i] ?? '';
    $floorCheck = $floorLabel ? $mk($floorMaterials, $floorLabel === 'Reinforced Concrete (for upper floors)' ? 'Reinforced Concrete' : $floorLabel) : '';
    $wallLabel = $wallItems[$i] ?? '';
    $wallCheck = $wallLabel ? $mk($wallMaterials, $wallLabel) : '';
    $materialGridRows .= '<tr>
        <td>' . esc($floorLabel) . '</td>
        <td class="text-center">' . ($i === 0 ? '' : $floorCheck) . '</td><td></td><td></td><td></td>
        <td>' . esc($wallLabel) . '</td>
        <td class="text-center">' . $wallCheck . '</td><td></td><td></td><td></td>
    </tr>';
}
$materialGridRows .= '<tr>
    <td>Others: ' . esc($r['floor_material_other']) . '</td>
    <td class="text-center">' . $mk($floorMaterials, 'Others') . '</td><td></td><td></td><td></td>
    <td>Others: ' . esc($r['wall_material_other']) . '</td>
    <td class="text-center">' . $mk($wallMaterials, 'Others') . '</td><td></td><td></td><td></td>
</tr>';

$html = '<html><head><style>
    @page { margin: 20px 24px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color:#111; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px dashed #666; padding: 2px 4px; vertical-align: top; }
    .flat td { border: none; padding: 1px 3px; }
    .lbl { font-size: 7px; }
    .section-lbl { font-weight: bold; font-size: 8px; margin: 6px 0 1px 0; }
    h1 { text-align: center; font-size: 11px; margin: 0 0 2px 0; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .top-row td { border: none; }
    .page-break { page-break-before: always; }
    .half { width: 50%; }
    .hl { background: #e2efda; }
    .floorhdr { font-size: 5.5px; padding: 1px; }
</style></head><body>

<h1>REAL PROPERTY FIELD APPRAISAL &amp; ASSESSMENT SHEET&mdash; BUILDING &amp; OTHER IMPROVEMENTS</h1>
<table class="flat top-row"><tr><td class="text-end"><span class="lbl">TRANSACTION CODE:</span> <strong>' . esc($r['transaction_code']) . '</strong></td></tr></table>

<table>
    <tr>
        <td style="width:50%;"><span class="lbl">ARP NO. :</span> <strong>' . esc($r['arp_no']) . '</strong></td>
        <td style="width:50%;"><span class="lbl">P I N :</span> <strong>' . esc($r['pin']) . '</strong></td>
    </tr>
    <tr>
        <td colspan="2"><span class="lbl">OWNER:</span> <strong>' . esc($r['owner_name']) . '</strong></td>
    </tr>
    <tr>
        <td colspan="2"><span class="lbl">R. Address:</span> ' . esc($r['owner_address']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Tel No.:</span> ' . esc($r['owner_tel']) . '</td>
        <td><span class="lbl">T I N:</span> ' . esc($r['owner_tin']) . '</td>
    </tr>
    <tr>
        <td colspan="2"><span class="lbl">Administrator / Beneficial User:</span> ' . esc($r['beneficial_user']) . '</td>
    </tr>
    <tr>
        <td colspan="2"><span class="lbl">R. Address:</span> ' . esc($r['beneficial_address']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Tel No.:</span> ' . esc($r['admin_tel']) . '</td>
        <td><span class="lbl">T I N:</span> ' . esc($r['admin_tin']) . '</td>
    </tr>
</table>

<table class="flat"><tr>
    <td class="section-lbl half">BUILDING LOCATION</td>
    <td class="section-lbl half">LAND REFERENCE</td>
</tr></table>
<table>
    <tr>
        <td style="width:25%;"><span class="lbl">No. / Street:</span></td><td style="width:25%;">' . esc($r['street']) . '</td>
        <td style="width:25%;"><span class="lbl">OWNER:</span></td><td style="width:25%;">' . esc($r['land_owner']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Brgy. / District:</span></td><td>' . esc($r['barangay']) . '</td>
        <td><span class="lbl">OCT/TCT/CLOA:</span></td><td>' . esc($r['oct_tct_no']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Municipality:</span></td><td>' . esc($r['municipality']) . '</td>
        <td><span class="lbl">SURVEY NO.:</span></td><td>' . esc($r['survey_number']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Province / City:</span></td><td>' . esc($r['province']) . '</td>
        <td><span class="lbl">LOT NO. / BLK NO.:</span></td><td>' . esc($r['lot_number']) . ' / ' . esc($r['block_number']) . '</td>
    </tr>
    <tr>
        <td></td><td></td>
        <td><span class="lbl">AREA:</span></td><td>' . fmtNum($r['land_area']) . ' sqm.</td>
    </tr>
</table>

<div class="section-lbl">GENERAL DESCRIPTION</div>
<table>
    <tr>
        <td style="width:25%;"><span class="lbl">Kind of Bldg:</span></td><td style="width:25%;">' . esc($r['building_kind']) . '</td>
        <td style="width:25%;"><span class="lbl">Bldg Age:</span></td><td style="width:25%;">' . esc($r['building_age']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Structural Type:</span></td><td>' . esc($r['structural_type']) . '</td>
        <td><span class="lbl">No. of Storeys:</span></td><td>' . esc($r['storeys']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Bldg. Permit No.:</span></td><td>' . esc($r['building_permit_no']) . '</td>
        <td><span class="lbl">Area of 1st Flr.:</span></td><td>' . fmtNum($r['first_floor_area']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Date Issued:</span></td><td>' . esc($r['permit_date']) . '</td>
        <td><span class="lbl">Area of 2nd Flr.:</span></td><td>' . fmtNum($r['second_floor_area']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Condominium Certificate of Title (CCT):</span></td><td>' . esc($r['cct_no']) . '</td>
        <td><span class="lbl">Area of 3rd Flr.:</span></td><td>' . fmtNum($r['third_floor_area']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Certificate of Completion Issued On:</span></td><td>' . esc($r['cert_completion_date']) . '</td>
        <td><span class="lbl">Area of 4th Flr.:</span></td><td>' . fmtNum($r['fourth_floor_area']) . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Certificate of Occupancy Issued On:</span></td><td>' . esc($r['cert_occupancy_date']) . '</td>
        <td></td><td></td>
    </tr>
    <tr>
        <td><span class="lbl">Date Constructed/Completed:</span></td><td>' . esc($r['date_constructed']) . '</td>
        <td></td><td></td>
    </tr>
    <tr>
        <td><span class="lbl">Date Occupied:</span></td><td>' . esc($r['date_occupied']) . '</td>
        <td><span class="lbl">Total Floor Area:</span></td><td><strong>' . fmtNum($r['total_floor_area']) . ' sqm.</strong></td>
    </tr>
</table>

<div class="section-lbl">FLOOR PLAN</div>
<table>
    <tr><td style="height:26px;color:#555;text-align:center;">Attach the building plan or sketch of floor plan. A photograph may also be attached if necessary.</td></tr>
</table>

<div class="section-lbl">STRUCTURAL MATERIALS (Checklists)</div>
<table>
    <tr>
        <th style="width:20%;">ROOF</th>
        <th style="width:20%;">FLOORING</th>
        <th class="floorhdr">1st<br>Flr</th><th class="floorhdr">2nd<br>Flr</th><th class="floorhdr">3rd<br>Flr</th><th class="floorhdr">4th<br>Flr</th>
        <th style="width:20%;">WALLS &amp; PARTITIONS</th>
        <th class="floorhdr">1st<br>Flr</th><th class="floorhdr">2nd<br>Flr</th><th class="floorhdr">3rd<br>Flr</th><th class="floorhdr">4th<br>Flr</th>
    </tr>
    <tr>
        <td rowspan="' . (count($roofItems) + 2) . '" style="vertical-align:top;">
            <table class="flat">' . $roofRowsHtml . '</table>
        </td>
        <td>Reinforced Concrete (for upper floors)</td><td></td><td></td><td></td><td></td>
        <td>Reinforced Concrete</td><td class="text-center">' . $mk($wallMaterials, 'Reinforced Concrete') . '</td><td></td><td></td><td></td>
    </tr>
    ' . $materialGridRows . '
</table>
<div style="color:#b30000;font-size:6px;margin-top:1px;">Per-floor columns are shown for layout only - the current form captures one material list per category, not per floor.</div>

<div class="page-break"></div>

<div class="section-lbl">ADDITIONAL ITEMS (Use additional sheet if necessary)</div>
<table>
    <tr><td colspan="3" style="color:#888;">Not captured separately from the itemized cost list below.</td></tr>
</table>

<div class="section-lbl">PROPERTY APPRAISAL</div>
<table>
    <tr>
        <td class="half" style="vertical-align:top;">
            <table class="flat">
                <tr><td class="lbl" style="width:55%;">Unit Construction Cost:</td><td class="text-end">P ' . fmtMoney($r['back_unit_construction_cost']) . ' / sq.m.</td></tr>
                <tr><td colspan="2" class="lbl" style="padding-top:6px;">Building Core (use additional sheets if necessary)</td></tr>
                <tr><td colspan="2" class="text-end hl">' . fmtNum($r['total_floor_area']) . ' sqm x P ' . fmtMoney($r['back_unit_construction_cost']) . ' /sqm = ' . fmtMoney($r['building_core_subtotal']) . '</td></tr>
            </table>
        </td>
        <td class="half" style="vertical-align:top;">
            <div class="lbl">Cost of Additional Items</div>
            <table class="flat">' . $itemsRows . $itemsPadRows . '</table>
            <table class="flat"><tr><td class="text-end hl"><strong>Sub-Total P ' . fmtMoney($itemsTotal) . '</strong></td></tr></table>
        </td>
    </tr>
    <tr>
        <td><span class="lbl">Sub - Total P</span> <span class="hl">' . fmtMoney($r['building_core_subtotal']) . '</span></td>
        <td><span class="lbl">Total Construction Cost P</span> <span class="hl">' . fmtMoney($r['total_construction_cost']) . '</span></td>
    </tr>
    <tr>
        <td><span class="lbl">Depreciation Rate</span> <span class="hl">' . esc($r['back_depreciation_rate']) . '%</span></td>
        <td><span class="lbl">Total % Depreciation</span> <span class="hl">' . fmtMoney($r['depreciation_cost']) . '</span></td>
    </tr>
    <tr>
        <td><span class="lbl">Depreciation Cost P</span> <span class="hl">' . fmtMoney($r['depreciation_cost']) . '</span></td>
        <td><span class="lbl">Market Value P</span> <span class="hl">' . fmtMoney($r['back_market_value']) . '</span></td>
    </tr>
</table>

<div class="section-lbl">PROPERTY APPRAISAL</div>
<table>
    <tr>
        <th>Actual Use</th><th>Market Value</th><th>Assessment Level</th><th>Assessed Value</th>
    </tr>
    <tr>
        <td>' . esc($r['back_actual_use']) . '</td>
        <td class="text-end">' . fmtMoney($r['back_assess_market_value']) . '</td>
        <td class="text-center">' . esc($r['back_assessment_level']) . '%</td>
        <td class="text-end">' . fmtMoney($r['back_assessed_value']) . '</td>
    </tr>
    <tr><td colspan="4">&nbsp;</td></tr>
    <tr>
        <td colspan="3" class="text-end"><strong>TOTAL</strong></td>
        <td class="text-end"><strong>' . fmtMoney($r['back_total_assessed_value']) . '</strong></td>
    </tr>
</table>

<table class="flat" style="margin-top:2px;">
    <tr>
        <td style="width:8%;">[' . ($r['taxability'] === 'Taxable' ? 'X' : ' ') . ']</td>
        <td style="width:15%;"><strong>TAXABLE</strong></td>
        <td style="width:8%;">[' . ($r['taxability'] === 'Exempt' ? 'X' : ' ') . ']</td>
        <td style="width:15%;">EXEMPT</td>
        <td style="width:32%;">Effectivity of Assessment /Reassessment:</td>
        <td style="width:11%;text-align:center;"><strong>' . esc($r['effectivity_quarter']) . '</strong><br><span class="lbl">Qtr</span></td>
        <td style="width:11%;text-align:center;"><strong>' . esc($r['effectivity_year']) . '</strong><br><span class="lbl">Year</span></td>
    </tr>
</table>

<table class="flat" style="margin-top:10px;">
    <tr>
        <td class="half"><span class="lbl">APPRAISED / ASSESSED BY :</span></td>
        <td class="half"><span class="lbl">RECOMMENDING APPROVAL &nbsp;:</span></td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td class="text-center">' . esc($r['appraised_by_name']) . ' &nbsp;&nbsp; ' . esc($r['appraised_by_date']) . '<br><span class="lbl">Municipal Assessor &nbsp;&nbsp;&nbsp; Date</span></td>
        <td class="text-center">' . esc($r['recommending_approval_name']) . ' &nbsp;&nbsp; ' . esc($r['recommending_approval_date']) . '<br><span class="lbl">Municipal Assessor &nbsp;&nbsp;&nbsp; Date</span></td>
    </tr>
</table>

<table class="flat" style="margin-top:10px;">
    <tr><td><span class="lbl">APPROVED &nbsp;BY:</span></td></tr>
    <tr><td>&nbsp;</td></tr>
    <tr>
        <td class="text-center"><strong>' . esc($r['approved_by_name']) . '</strong><br><span class="lbl">Provincial Assessor &nbsp;&nbsp;&nbsp; ' . esc($r['approved_by_date']) . '</span></td>
    </tr>
</table>

<div class="section-lbl">MEMORANDA:</div>
<table><tr><td class="hl" style="height:40px;">' . esc($r['memoranda']) . '</td></tr></table>

<table class="flat" style="margin-top:10px;">
    <tr>
        <td style="width:50%;">Date of Entry in the Record of Assessment: _______________<br><span class="lbl" style="margin-left:60px;">Date</span></td>
        <td style="width:50%;">By: _______________<br><span class="lbl" style="margin-left:20px;">Name</span></td>
    </tr>
</table>

<div class="section-lbl">RECORD OF SUPERSEDED ASSESSMENT :</div>
<table>
    <tr>
        <td style="width:15%;"><span class="lbl">P I N :</span></td><td style="width:35%;">' . esc($latestSuperseded['pin'] ?? '') . '</td>
        <td style="width:20%;"><span class="lbl">ARP No./ TD No. :</span></td><td style="width:30%;"><strong>' . esc($latestSuperseded['arp_no'] ?? '') . '</strong></td>
    </tr>
    <tr>
        <td><span class="lbl">Total Assessed Value :</span></td><td>' . fmtMoney($latestSuperseded['assessed_value'] ?? null) . '</td>
        <td><span class="lbl">Effectivity of Assessment:</span></td><td>' . esc($latestSuperseded['effectivity'] ?? '') . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Previous Owner :</span></td><td>' . esc($latestSuperseded['previous_owner'] ?? '') . '</td>
        <td><span class="lbl">AR Page No. :</span></td><td>' . esc($latestSuperseded['ar_page'] ?? '') . '</td>
    </tr>
    <tr>
        <td><span class="lbl">Recording Person :</span></td><td>' . esc($latestSuperseded['recorder'] ?? '') . '</td>
        <td><span class="lbl">Date :</span></td><td>' . esc($latestSuperseded['record_date'] ?? '') . '</td>
    </tr>
</table>

</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();

faas_pdf_mark_generated($conn, $id);

$dompdf->stream('FAAS-Building-' . preg_replace('/[^A-Za-z0-9-]/', '', $r['arp_no']) . '.pdf', ['Attachment' => false]);