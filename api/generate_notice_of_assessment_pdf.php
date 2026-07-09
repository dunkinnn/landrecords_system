<?php
/**
 * Endpoint: generate_tax_declaration_pdf.php
 * Renders the Tax Declaration of Real Property for the given building
 * record id.
 * NOTE: Boundaries (North/South/East/West) and a distinct TD number are
 * not captured anywhere in the Building form or database, so they are
 * left blank here rather than fabricated. See docs/activity-log.md.
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

function escTD($v) { return htmlspecialchars($v ?? '', ENT_QUOTES); }
function fmtMoneyTD($v) { return $v !== null ? number_format((float) $v, 2) : ''; }
function checkboxTD($checked) { return $checked ? '&#9746;' : '&#9744;'; }

$latestSuperseded = $r['superseded'][0] ?? null;

$html = '<html><head><style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
    .header-line { text-align: center; font-size: 10px; margin: 1px 0; }
    h2 { text-align: center; font-size: 13px; margin: 10px 0 2px 0; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
    td, th { border: 1px solid #333; padding: 3px 5px; vertical-align: top; }
    .no-border td { border: none; padding: 2px 4px; }
    .label { font-weight: bold; font-size: 9px; color: #444; }
    .center { text-align: center; }
    .text-end { text-align: right; }
    .missing { color: #b30000; font-style: italic; }
</style></head><body>

<div class="header-line">Republic of the Philippines</div>
<div class="header-line">Province of Isabela</div>
<div class="header-line">Municipality of San Pablo</div>
<h2>TAX DECLARATION OF REAL PROPERTY</h2>

<table class="no-border">
    <tr>
        <td class="label" style="width:15%;">TD No.</td><td>' . escTD($r['arp_no']) . '</td>
        <td class="label" style="width:20%;">Property Identification No.</td><td>' . escTD($r['pin']) . '</td>
    </tr>
    <tr>
        <td class="label">Owner</td><td>' . escTD($r['owner_name']) . '</td>
        <td class="label">TIN</td><td>' . escTD($r['owner_tin']) . '</td>
    </tr>
    <tr>
        <td class="label">Address</td><td>' . escTD($r['owner_address']) . '</td>
        <td class="label">Tel. No.</td><td>' . escTD($r['owner_tel']) . '</td>
    </tr>
    <tr>
        <td class="label">Beneficial User</td><td>' . escTD($r['beneficial_user']) . '</td>
        <td class="label">TIN</td><td>' . escTD($r['admin_tin']) . '</td>
    </tr>
    <tr>
        <td class="label">Location of Property</td>
        <td colspan="3">' . escTD($r['street']) . ', ' . escTD($r['barangay']) . ', ' . escTD($r['municipality']) . ', ' . escTD($r['province']) . '</td>
    </tr>
    <tr>
        <td class="label">OCT/TCT/CLOA No.</td><td>' . escTD($r['oct_tct_no']) . '</td>
        <td class="label">Survey No.</td><td>' . escTD($r['survey_number']) . '</td>
    </tr>
    <tr>
        <td class="label">CCT</td><td>' . escTD($r['cct_no']) . '</td>
        <td class="label">Lot No. / Blk. No.</td><td>' . escTD($r['lot_number']) . ' / ' . escTD($r['block_number']) . '</td>
    </tr>
</table>

<table>
    <tr><th colspan="4" class="center">BOUNDARIES</th></tr>
    <tr>
        <td class="label" style="width:15%;">North</td><td class="missing">Not captured</td>
        <td class="label" style="width:15%;">South</td><td class="missing">Not captured</td>
    </tr>
    <tr>
        <td class="label">East</td><td class="missing">Not captured</td>
        <td class="label">West</td><td class="missing">Not captured</td>
    </tr>
</table>

<table class="no-border">
    <tr>
        <td class="label" style="width:25%;">Kind of Property Assessed</td>
        <td>' . checkboxTD(false) . ' Land &nbsp;&nbsp; ' . checkboxTD(true) . ' Building &nbsp;&nbsp; ' . checkboxTD(false) . ' Machinery &nbsp;&nbsp; ' . checkboxTD(false) . ' Others</td>
    </tr>
    <tr>
        <td class="label">No. of Storeys</td><td>' . escTD($r['storeys']) . '</td>
    </tr>
    <tr>
        <td class="label">Brief Description</td>
        <td>Constructed on Lot No. ' . escTD($r['lot_number']) . '</td>
    </tr>
</table>

<table>
    <tr>
        <th>Classification</th><th>Area</th><th>Unit Value</th><th>Actual Use</th>
        <th>Market Value</th><th>Assessment Level</th><th>Assessed Value</th>
    </tr>
    <tr>
        <td>' . escTD($r['back_actual_use']) . '</td>
        <td class="text-end">' . escTD($r['total_floor_area']) . '</td>
        <td class="text-end">' . fmtMoneyTD($r['back_unit_construction_cost']) . '</td>
        <td>' . escTD($r['back_actual_use']) . '</td>
        <td class="text-end">' . fmtMoneyTD($r['back_assess_market_value']) . '</td>
        <td class="text-end">' . escTD($r['back_assessment_level']) . '%</td>
        <td class="text-end">' . fmtMoneyTD($r['back_assessed_value']) . '</td>
    </tr>
    <tr>
        <td colspan="4" class="text-end"><strong>Total</strong></td>
        <td class="text-end"><strong>' . fmtMoneyTD($r['back_assess_market_value']) . '</strong></td>
        <td></td>
        <td class="text-end"><strong>PHP ' . fmtMoneyTD($r['back_total_assessed_value']) . '</strong></td>
    </tr>
</table>

<table class="no-border">
    <tr>
        <td class="label" style="width:22%;">Total Assessed Value</td>
        <td>' . escTD(faas_number_to_words($r['back_total_assessed_value'] ?? 0)) . '</td>
    </tr>
    <tr>
        <td class="label">Status</td>
        <td>' . checkboxTD($r['taxability'] === 'Taxable') . ' Taxable &nbsp;&nbsp; ' . checkboxTD($r['taxability'] === 'Exempt') . ' Exempt</td>
    </tr>
    <tr>
        <td class="label">Effectivity of Assessment</td>
        <td>' . escTD($r['effectivity_quarter']) . ' Quarter, ' . escTD($r['effectivity_year']) . '</td>
    </tr>
</table>

<table class="no-border" style="margin-top:14px;">
    <tr>
        <td style="width:60%;">Approved By:</td>
        <td style="width:40%;text-align:center;">_______________________<br>Provincial Assessor</td>
    </tr>
</table>

' . ($latestSuperseded ? '
<table class="no-border">
    <tr>
        <td class="label" style="width:30%;">This declaration cancels TD No.</td>
        <td>' . escTD($latestSuperseded['arp_no']) . '</td>
    </tr>
    <tr>
        <td class="label">Previous Assessed Value</td>
        <td>&#8369; ' . fmtMoneyTD($latestSuperseded['assessed_value']) . '</td>
    </tr>
    <tr>
        <td class="label">Previous Owner</td>
        <td>' . escTD($latestSuperseded['previous_owner']) . '</td>
    </tr>
</table>' : '') . '

<table class="no-border">
    <tr><td class="label" style="width:15%;">Memoranda</td><td>' . escTD($r['memoranda']) . '</td></tr>
</table>

<p style="font-size:8px;color:#555;margin-top:14px;">
Notes: This declaration is for taxation purposes only and the valuation indicated herein are based on the schedule of
unit market values prepared for the purpose and duly enacted into an Ordinance by the Sangguniang Panlalawigan.
It does not and cannot by itself alone confer any ownership or legal title to the property.
</p>

</body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('legal', 'portrait');
$dompdf->render();

faas_pdf_mark_generated($conn, $id);

$dompdf->stream('Tax-Declaration-' . preg_replace('/[^A-Za-z0-9-]/', '', $r['arp_no']) . '.pdf', ['Attachment' => false]);