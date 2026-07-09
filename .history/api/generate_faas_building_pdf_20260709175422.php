<?php
/**
 * Endpoint: generate_faas_building_pdf.php
 * Renders the FAAS Building & Other Improvements sheet (front + back)
 * as a single PDF, Letter size, matching the two-column grid layout of
 * the actual form.
 *
 * Known fidelity gaps (not fabricated, see docs/activity-log.md):
 * - The real form has per-floor (1st/2nd/3rd/4th) checkbox columns for
 *   Flooring and Walls. The Building form only captures one flat list
 *   per material with no per-floor breakdown, so this PDF shows one
 *   unified checklist per material instead of per-floor columns.
 * - The real form has a separate plain-text "Additional Items"
 *   description list next to the costed itemized list. Only the costed
 *   list is captured, so only that one is rendered.
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
function mark($checked) { return $checked ? 'X' : ''; }

$materials = function ($csv) {
    return $csv ? array_map('trim', explode(',', $csv)) : [];
};
$roofMaterials = $materials($r['roof_material']);
$floorMaterials = $materials($r['floor_material']);
$wallMaterials = $materials($r['wall_material']);
$has = function ($list, $value) use (&$mark) { return mark(in_array($value, $list, true)); };

$itemsRows = '';
$itemsTotal = 0;
foreach ($r['items'] as $item) {
    $itemsTotal += (float) $item['amount'];
    $itemsRows .= '<tr><td>' . esc($item['description']) . '</td><td class="text-end">' . fmtMoney($item['amount']) . '</td></tr>';
}
if (!$itemsRows) {
    $itemsRows = '<tr><td colspan="2" style="text-align:center;color:#888;">None</td></tr>';
}

$latestSuperseded = $r['superseded'][0] ?? null;

$html = '<html><head><style>
    @page { margin: 22px 26px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 8px; color:#111; }
    table { width: 100%; border-collapse: collapse; }
    td, th { border: 1px solid #333; padding: 2px 4px; vertical-align: top; }
    .flat td { border: none; padding: 1px 3px; }
    .lbl { font-size: 7px; color: #333; }
    h1 { text-align: center; font-size: 11px; margin: 0 0 2px 0; }
    .owner-line { text-align: center; font-size: 10px; font-weight: bold; margin-bottom: 4px; }
    .outer { border: 1.5px solid #000; }
    .section-hdr { background: #e9e9e9; font-weight: bold; text-align: center; font-size: 8px; }
    .text-end { text-align: right; }
    .text-center { text-align: center; }
    .top-row td { border: none; }
    .page-break { page-break-before: always; }
    .half { width: 50%; }
</style></head><body>

<table class="flat top-row">
    <tr>
        <td style="width:70%;"></td>
        <td class="text-end" style="width:30%;"><span class="lbl">TRANSACTION CODE:</span> <strong>' . esc($r['transaction_code']) . '</strong></td>
    </tr>
</table>
<h1>REAL PROPERTY FIELD APPRAISAL &amp; ASSESSMENT SHEET&mdash; BUILDING &amp; OTHER IMPROVEMENTS</h1>
<div class="owner-line">' . esc($r['owner_name']) . '</div>

<table class="outer">
    <tr>
        <td style="width:50%;"><span class="lbl">ARP NO.:</span> ' . esc($r['arp_no']) . '</td>
        <td style="width:50%;"><span class="lbl">P I N:</span> ' . esc($r['pin']) . '</td>
    </tr>
    <tr>
        <td colspan="2"><span class="lbl">OWNER:</span> ' . esc($r['owner_name']) . '</td>
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

<table class="outer" style="margin-top:-1px;">
    <tr>
        <td class="section-hdr half">BUILDING LOCATION</td>
        <td class="section-hdr half">LAND REFERENCE</td>
    </tr>
    <tr>
        <td style="vertical-align:top;">
            <table class="flat">
                <tr><td class="lbl" style="width:40%;">No. / Street:</td><td>' . esc($r['street']) . '</td></tr>
                <tr><td class="lbl">Brgy. / District:</td><td>' . esc($r['barangay']) . '</td></tr>
                <tr><td class="lbl">Municipality:</td><td>' . esc($r['municipality']) . '</td></tr>
                <tr><td class="lbl">Province / City:</td><td>' . esc($r['province']) . '</td></tr>
            </table>
        </td>
        <td style="vertical-align:top;">
            <table class="flat">
                <tr><td class="lbl" style="width:40%;">OWNER:</td><td>' . esc($r['land_owner']) . '</td></tr>
                <tr><td class="lbl">OCT/TCT/CLOA:</td><td>' . esc($r['oct_tct_no']) . '</td></tr>
                <tr><td class="lbl">SURVEY NO.:</td><td>' . esc($r['survey_number']) . '</td></tr>
                <tr><td class="lbl">LOT NO.:</td><td>' . esc($r['lot_number']) . '</td></tr>
                <tr><td class="lbl">BLK NO.:</td><td>' . esc($r['block_number']) . '</td></tr>
                <tr><td class="lbl">AREA:</td><td>' . fmtNum($r['land_area']) . ' sqm.</td></tr>
            </table>
        </td>
    </tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td colspan="4" class="section-hdr">GENERAL DESCRIPTION</td></tr>
    <tr>
        <td class="lbl" style="width:22%;">Kind of Bldg:</td><td style="width:28%;">' . esc($r['building_kind']) . '</td>
        <td class="lbl" style="width:22%;">Bldg Age:</td><td style="width:28%;">' . esc($r['building_age']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Structural Type:</td><td>' . esc($r['structural_type']) . '</td>
        <td class="lbl">No. of Storeys:</td><td>' . esc($r['storeys']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Bldg. Permit No. / Date Issued:</td><td>' . esc($r['building_permit_no']) . ' / ' . esc($r['permit_date']) . '</td>
        <td class="lbl">Area of 1st Flr.:</td><td>' . fmtNum($r['first_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Condominium Certificate of Title (CCT):</td><td>' . esc($r['cct_no']) . '</td>
        <td class="lbl">Area of 2nd Flr.:</td><td>' . fmtNum($r['second_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Certificate of Completion Issued On:</td><td>' . esc($r['cert_completion_date']) . '</td>
        <td class="lbl">Area of 3rd Flr.:</td><td>' . fmtNum($r['third_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Certificate of Occupancy Issued On:</td><td>' . esc($r['cert_occupancy_date']) . '</td>
        <td class="lbl">Area of 4th Flr.:</td><td>' . fmtNum($r['fourth_floor_area']) . '</td>
    </tr>
    <tr>
        <td class="lbl">Date Constructed/Completed:</td><td>' . esc($r['date_constructed']) . '</td>
        <td class="lbl"></td><td></td>
    </tr>
    <tr>
        <td class="lbl">Date Occupied:</td><td>' . esc($r['date_occupied']) . '</td>
        <td class="lbl">Total Floor Area:</td><td><strong>' . fmtNum($r['total_floor_area']) . ' sqm.</strong></td>
    </tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td class="section-hdr">FLOOR PLAN</td></tr>
    <tr><td style="height:28px;font-size:7px;color:#666;">Attach the building plan or sketch of floor plan. A photograph may also be attached if necessary.</td></tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td colspan="3" class="section-hdr">STRUCTURAL MATERIALS (Checklist)</td></tr>
    <tr>
        <td class="section-hdr" style="width:33%;">ROOF</td>
        <td class="section-hdr" style="width:33%;">FLOORING</td>
        <td class="section-hdr" style="width:34%;">WALLS &amp; PARTITIONS</td>
    </tr>
    <tr>
        <td style="vertical-align:top;">
            [' . $has($roofMaterials, 'Reinforced Concrete') . '] Reinforced Concrete<br>
            [' . $has($roofMaterials, 'Tiles') . '] Tiles<br>
            [' . $has($roofMaterials, 'G.I. Sheet') . '] G.I. Sheet<br>
            [' . $has($roofMaterials, 'Aluminum') . '] Aluminum<br>
            [' . $has($roofMaterials, 'Asbestos') . '] Asbestos<br>
            [' . $has($roofMaterials, 'Long Span') . '] Long Span<br>
            [' . $has($roofMaterials, 'Nipa/Anahaw/Cogon') . '] Nipa/Anahaw/Cogon<br>
            [' . $has($roofMaterials, 'Others') . '] Others: ' . esc($r['roof_material_other']) . '
        </td>
        <td style="vertical-align:top;">
            [' . $has($floorMaterials, 'Reinforced Concrete') . '] Reinforced Concrete (upper floors)<br>
            [' . $has($floorMaterials, 'Plain Cement') . '] Plain Cement<br>
            [' . $has($floorMaterials, 'Marble') . '] Marble<br>
            [' . $has($floorMaterials, 'Wood') . '] Wood<br>
            [' . $has($floorMaterials, 'Tiles') . '] Tiles<br>
            [' . $has($floorMaterials, 'Bamboo') . '] Bamboo<br>
            [' . $has($floorMaterials, 'Others') . '] Others: ' . esc($r['floor_material_other']) . '
            <div style="color:#b30000;font-size:6px;margin-top:2px;">Per-floor breakdown not captured by the current form.</div>
        </td>
        <td style="vertical-align:top;">
            [' . $has($wallMaterials, 'Reinforced Concrete') . '] Reinforced Concrete<br>
            [' . $has($wallMaterials, 'Plain Cement') . '] Plain Cement<br>
            [' . $has($wallMaterials, 'Wood') . '] Wood<br>
            [' . $has($wallMaterials, 'CHB') . '] CHB<br>
            [' . $has($wallMaterials, 'G.I. Sheet') . '] G.I. Sheet<br>
            [' . $has($wallMaterials, 'Build-a-Wall') . '] Build-a-Wall<br>
            [' . $has($wallMaterials, 'Sawali') . '] Sawali<br>
            [' . $has($wallMaterials, 'Bamboo') . '] Bamboo<br>
            [' . $has($wallMaterials, 'Others') . '] Others: ' . esc($r['wall_material_other']) . '
            <div style="color:#b30000;font-size:6px;margin-top:2px;">Per-floor breakdown not captured by the current form.</div>
        </td>
    </tr>
</table>

<div class="page-break"></div>

<table class="outer">
    <tr>
        <td class="section-hdr half">PROPERTY APPRAISAL</td>
        <td class="section-hdr half">COST OF ADDITIONAL ITEMS</td>
    </tr>
    <tr>
        <td style="vertical-align:top;">
            <table class="flat">
                <tr><td class="lbl" style="width:55%;">Unit Construction Cost:</td><td class="text-end">P ' . fmtMoney($r['back_unit_construction_cost']) . ' / sq.m.</td></tr>
                <tr><td colspan="2" class="lbl" style="padding-top:6px;">Building Core (use additional sheets if necessary)</td></tr>
                <tr><td colspan="2" class="text-end">' . fmtNum($r['total_floor_area']) . ' sqm x P ' . fmtMoney($r['back_unit_construction_cost']) . ' /sqm = ' . fmtMoney($r['building_core_subtotal']) . '</td></tr>
                <tr><td class="lbl" style="padding-top:6px;"><strong>Sub-Total</strong></td><td class="text-end"><strong>P ' . fmtMoney($r['building_core_subtotal']) . '</strong></td></tr>
            </table>
        </td>
        <td style="vertical-align:top;">
            <table>
                ' . $itemsRows . '
                <tr><td class="text-end"><strong>Sub-Total</strong></td><td class="text-end"><strong>P ' . fmtMoney($itemsTotal) . '</strong></td></tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="lbl">Depreciation Rate: <strong>' . esc($r['back_depreciation_rate']) . '%</strong></td>
        <td class="lbl">Total Construction Cost: <strong>P ' . fmtMoney($r['total_construction_cost']) . '</strong></td>
    </tr>
    <tr>
        <td class="lbl">Depreciation Cost: <strong>P ' . fmtMoney($r['depreciation_cost']) . '</strong></td>
        <td class="lbl">Market Value: <strong>P ' . fmtMoney($r['back_market_value']) . '</strong></td>
    </tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td colspan="4" class="section-hdr">PROPERTY ASSESSMENT</td></tr>
    <tr>
        <th style="width:25%;">Actual Use</th>
        <th style="width:25%;">Market Value</th>
        <th style="width:25%;">Assessment Level</th>
        <th style="width:25%;">Assessed Value</th>
    </tr>
    <tr>
        <td>' . esc($r['back_actual_use']) . '</td>
        <td class="text-end">' . fmtMoney($r['back_assess_market_value']) . '</td>
        <td class="text-end">' . esc($r['back_assessment_level']) . '%</td>
        <td class="text-end">' . fmtMoney($r['back_assessed_value']) . '</td>
    </tr>
    <tr>
        <td colspan="3" class="text-end"><strong>TOTAL</strong></td>
        <td class="text-end"><strong>' . fmtMoney($r['back_total_assessed_value']) . '</strong></td>
    </tr>
</table>

<table class="flat" style="margin-top:4px;">
    <tr>
        <td style="width:20%;">[' . ($r['taxability'] === 'Taxable' ? 'X' : ' ') . '] TAXABLE</td>
        <td style="width:20%;">[' . ($r['taxability'] === 'Exempt' ? 'X' : ' ') . '] EXEMPT</td>
        <td class="text-end" style="width:60%;">Effectivity of Assessment/Reassessment: <strong>' . esc($r['effectivity_quarter']) . '</strong> Qtr &nbsp; <strong>' . esc($r['effectivity_year']) . '</strong> Year</td>
    </tr>
</table>

<table class="outer" style="margin-top:6px;">
    <tr>
        <td class="half text-center" style="padding-top:20px;">
            ' . esc($r['appraised_by_name']) . '<br>
            _____________________________<br>
            <span class="lbl">APPRAISED / ASSESSED BY &nbsp;&nbsp; Date: ' . esc($r['appraised_by_date']) . '</span>
        </td>
        <td class="half text-center" style="padding-top:20px;">
            ' . esc($r['recommending_approval_name']) . '<br>
            _____________________________<br>
            <span class="lbl">RECOMMENDING APPROVAL &nbsp;&nbsp; Date: ' . esc($r['recommending_approval_date']) . '</span>
        </td>
    </tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td class="section-hdr">APPROVED BY</td></tr>
    <tr>
        <td class="text-center" style="padding-top:16px;">
            ' . esc($r['approved_by_name']) . '<br>
            _____________________________<br>
            <span class="lbl">Provincial Assessor &nbsp;&nbsp; Date: ' . esc($r['approved_by_date']) . '</span>
        </td>
    </tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td class="section-hdr">MEMORANDA</td></tr>
    <tr><td style="min-height:20px;">' . esc($r['memoranda']) . '</td></tr>
</table>

<table class="outer" style="margin-top:-1px;">
    <tr><td colspan="2" class="section-hdr">RECORD OF SUPERSEDED ASSESSMENT</td></tr>
    <tr>
        <td class="lbl" style="width:20%;">P I N:</td><td>' . esc($latestSuperseded['pin'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">ARP No. / TD No.:</td><td>' . esc($latestSuperseded['arp_no'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">Total Assessed Value:</td><td>' . fmtMoney($latestSuperseded['assessed_value'] ?? null) . '</td>
    </tr>
    <tr>
        <td class="lbl">Effectivity of Assessment:</td><td>' . esc($latestSuperseded['effectivity'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">Previous Owner:</td><td>' . esc($latestSuperseded['previous_owner'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">AR Page No.:</td><td>' . esc($latestSuperseded['ar_page'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">Recording Person:</td><td>' . esc($latestSuperseded['recorder'] ?? '') . '</td>
    </tr>
    <tr>
        <td class="lbl">Date:</td><td>' . esc($latestSuperseded['record_date'] ?? '') . '</td>
    </tr>
</table>

</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'portrait');
$dompdf->render();

faas_pdf_mark_generated($conn, $id);

$dompdf->stream('FAAS-Building-' . preg_replace('/[^A-Za-z0-9-]/', '', $r['arp_no']) . '.pdf', ['Attachment' => false]);