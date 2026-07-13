<?php
/**
 * Form Template: 24-FAAS-BUILDING
 * Real Property Field Appraisal & Assessment Sheet — Building & Other Improvements
 * Municipality: San Pablo, Isabela
 */
?>
<div class="row g-3" id="faas_step_front">
    <!-- ============================================================ -->
    <!-- PROPERTY INFORMATION -->
    <!-- ============================================================ -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 fw-bold">Property Information</h6>
    </div>

    <input type="hidden" name="property_id" id="property_id">

    <div class="col-md-4">
        <label class="form-label">ARP No.</label>
        <div class="input-group">
            <input type="text" class="form-control" name="arp_no">
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label">PIN</label>
        <input type="text" class="form-control" name="pin">
    </div>

    <div class="col-md-4">
        <label class="form-label">Transaction Code</label>
        <input type="text" class="form-control" name="transaction_code" value="GR">
    </div>

    <div class="col-md-6">
        <label class="form-label">Owner Name</label>
        <input type="text" class="form-control" name="owner_name" placeholder="Last, First MI">
    </div>

    <div class="col-md-6">
        <label class="form-label">Owner R. Address</label>
        <select class="form-select" name="owner_address" id="owner_address">
            <option value="">— Select Barangay —</option>
            <option value="ANNANUMAN, SAN PABLO, ISABELA">ANNANUMAN, San Pablo, Isabela</option>
            <option value="AUITAN, SAN PABLO, ISABELA">AUITAN, San Pablo, Isabela</option>
            <option value="BALLACAYU, SAN PABLO, ISABELA">BALLACAYU, San Pablo, Isabela</option>
            <option value="BINGUANG, SAN PABLO, ISABELA">BINGUANG, San Pablo, Isabela</option>
            <option value="BUNGAD, SAN PABLO, ISABELA">BUNGAD, San Pablo, Isabela</option>
            <option value="CALAMAGUI, SAN PABLO, ISABELA">CALAMAGUI, San Pablo, Isabela</option>
            <option value="CARALUCUD, SAN PABLO, ISABELA">CARALUCUD, San Pablo, Isabela</option>
            <option value="DALENA, SAN PABLO, ISABELA">DALENA, San Pablo, Isabela</option>
            <option value="GUMINGA, SAN PABLO, ISABELA">GUMINGA, San Pablo, Isabela</option>
            <option value="LIMBAUAN, SAN PABLO, ISABELA">LIMBAUAN, San Pablo, Isabela</option>
            <option value="MINANGA NORTE, SAN PABLO, ISABELA">MINANGA NORTE, San Pablo, Isabela</option>
            <option value="MINANGA SUR, SAN PABLO, ISABELA">MINANGA SUR, San Pablo, Isabela</option>
            <option value="POBLACION, SAN PABLO, ISABELA">POBLACION, San Pablo, Isabela</option>
            <option value="SAN JOSE, SAN PABLO, ISABELA">SAN JOSE, San Pablo, Isabela</option>
            <option value="SAN VICENTE, SAN PABLO, ISABELA">SAN VICENTE, San Pablo, Isabela</option>
            <option value="SIMANU NORTE, SAN PABLO, ISABELA">SIMANU NORTE, San Pablo, Isabela</option>
            <option value="SIMANU SUR, SAN PABLO, ISABELA">SIMANU SUR, San Pablo, Isabela</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Telephone No.</label>
        <input type="text" class="form-control" name="owner_tel">
    </div>

    <div class="col-md-6">
        <label class="form-label">TIN</label>
        <input type="text" class="form-control" name="owner_tin">
    </div>

    <div class="col-md-6">
        <label class="form-label">Administrator / Beneficial User</label>
        <input type="text" class="form-control" name="beneficial_user">
    </div>

    <div class="col-md-6">
        <label class="form-label">Administrator R. Address</label>
        <input type="text" class="form-control" name="beneficial_address" id="beneficial_address" placeholder="Enter complete address">
    </div>

    <div class="col-md-6">
        <label class="form-label">Admin Telephone No.</label>
        <input type="text" class="form-control" name="admin_tel">
    </div>

    <div class="col-md-6">
        <label class="form-label">Admin TIN</label>
        <input type="text" class="form-control" name="admin_tin">
    </div>

    <!-- ============================================================ -->
    <!-- BUILDING LOCATION -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Building Location</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">No. / Street</label>
        <input type="text" class="form-control" name="street">
    </div>

    <div class="col-md-4">
        <label class="form-label">Barangay</label>
        <select class="form-select" name="barangay">
            <option value="">— Select Barangay —</option>
            <option value="POBLACION">POBLACION</option>
            <option value="ANNANUMAN">ANNANUMAN</option>
            <option value="AUITAN">AUITAN</option>
            <option value="BALLACAYU">BALLACAYU</option>
            <option value="BINGUANG">BINGUANG</option>
            <option value="BUNGAD">BUNGAD</option>
            <option value="LIMBAUAN">LIMBAUAN</option>
            <option value="CALAMAGUI">CALAMAGUI</option>
            <option value="CARALUCUD">CARALUCUD</option>
            <option value="DALENA">DALENA</option>
            <option value="GUMINGA">GUMINGA</option>
            <option value="MINANGA NORTE">MINANGA NORTE</option>
            <option value="MINANGA SUR">MINANGA SUR</option>
            <option value="SAN JOSE">SAN JOSE</option>
            <option value="SAN VICENTE">SAN VICENTE</option>
            <option value="SIMANU NORTE">SIMANU NORTE</option>
            <option value="SIMANU SUR">SIMANU SUR</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Municipality</label>
        <input type="text" class="form-control" name="municipality" id="faas_municipality" value="" readonly placeholder="Auto-filled">
    </div>

    <div class="col-md-4">
        <label class="form-label">Province / City</label>
        <input type="text" class="form-control" name="province" id="faas_province" value="" readonly placeholder="Auto-filled">
    </div>

    <!-- ============================================================ -->
    <!-- LAND REFERENCE -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Land Reference</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Lot No.</label>
        <input type="text" class="form-control" name="lot_number">
    </div>

    <div class="col-md-4">
        <label class="form-label">Block No.</label>
        <input type="text" class="form-control" name="block_number">
    </div>

    <div class="col-md-4">
        <label class="form-label">Survey No.</label>
        <input type="text" class="form-control" name="survey_number">
    </div>

    <div class="col-md-6">
        <label class="form-label">OCT / TCT / CLOA No.</label>
        <input type="text" class="form-control" name="oct_tct_no">
    </div>

    <div class="col-md-6">
        <label class="form-label">Land Owner</label>
        <input type="text" class="form-control" name="land_owner">
    </div>

    <div class="col-md-6">
        <label class="form-label">Land Area (sq.m.)</label>
        <input type="number" step="0.01" class="form-control" name="land_area">
    </div>

    <!-- ============================================================ -->
    <!-- GENERAL DESCRIPTION -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">General Description</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Kind of Building</label>
        <input type="text" class="form-control" name="building_kind">
    </div>

    <div class="col-md-4">
        <label class="form-label">Structural Type</label>
        <input type="text" class="form-control" name="structural_type">
    </div>

    <div class="col-md-4">
        <label class="form-label">Building Age</label>
        <input type="number" class="form-control" name="building_age">
    </div>

    <div class="col-md-4">
        <!-- Capped at 4 to match the 4 floor-area fields collected below -->
        <label class="form-label">No. of Storeys (max 4)</label>
        <input type="number" class="form-control" name="storeys" min="1" max="4">
    </div>

    <div class="col-md-4">
        <label class="form-label">Building Permit No.</label>
        <input type="text" class="form-control" name="building_permit_no">
    </div>

    <div class="col-md-4">
        <label class="form-label">Permit Date Issued</label>
        <input type="date" class="form-control" name="permit_date">
    </div>

    <div class="col-md-6">
        <label class="form-label">Condominium Certificate of Title (CCT)</label>
        <input type="text" class="form-control" name="cct_no">
    </div>

    <div class="col-md-6">
        <label class="form-label">Certificate of Completion Issued On</label>
        <input type="date" class="form-control" name="cert_completion_date">
    </div>

    <div class="col-md-6">
        <label class="form-label">Certificate of Occupancy Issued On</label>
        <input type="date" class="form-control" name="cert_occupancy_date">
    </div>

    <!-- Only the 1st floor area is natively required; 2nd-4th are required
         conditionally by JS based on the No. of Storeys value entered above. -->
    <div class="col-md-3">
        <label class="form-label">Area of 1st Floor (sq.m.)</label>
        <input type="number" step="0.01" class="form-control faas-floor-area" name="first_floor_area" id="faas_area_1f">
    </div>

    <div class="col-md-3">
        <label class="form-label">Area of 2nd Floor (sq.m.)</label>
        <input type="number" step="0.01" class="form-control faas-floor-area" name="second_floor_area" id="faas_area_2f">
    </div>

    <div class="col-md-3">
        <label class="form-label">Area of 3rd Floor (sq.m.)</label>
        <input type="number" step="0.01" class="form-control faas-floor-area" name="third_floor_area" id="faas_area_3f">
    </div>

    <div class="col-md-3">
        <label class="form-label">Area of 4th Floor (sq.m.)</label>
        <input type="number" step="0.01" class="form-control faas-floor-area" name="fourth_floor_area" id="faas_area_4f">
    </div>

    <div class="col-md-4">
        <label class="form-label">Total Floor Area (sq.m.)</label>
        <input type="text" readonly class="form-control bg-light" name="total_floor_area" id="faas_total_area">
    </div>

    <div class="col-md-4">
        <label class="form-label">Date Constructed / Completed</label>
        <input type="date" class="form-control" name="date_constructed">
    </div>

    <div class="col-md-4">
        <label class="form-label">Date Occupied</label>
        <input type="date" class="form-control" name="date_occupied">
    </div>

    <!-- ============================================================ -->
    <!-- FLOOR PLAN -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Floor Plan</h6>
    </div>

    <input type="hidden" name="floor_plan_path" id="floor_plan_path">

    <div class="col-md-6">
        <label class="form-label">Attach building plan, sketch, or photograph</label>
        <input type="file" class="form-control" id="floor_plan_file" accept="image/png,image/jpeg,image/gif,image/webp">
        <div class="form-text">JPG, PNG, GIF, or WEBP, up to 5MB.</div>
        <div id="floor_plan_upload_status" class="small mt-1"></div>
    </div>

    <div class="col-md-6">
        <img id="floor_plan_preview" src="" alt="Floor plan preview" class="img-thumbnail d-none" style="max-height:180px;">
    </div>

    <!-- ============================================================ -->
    <!-- STRUCTURAL MATERIALS (Checklist) -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Structural Materials (Checklist) <small class="text-muted fw-normal">(select at least one per group)</small></h6>
    </div>

    <!-- ROOF -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Roof</label>
        <div class="border rounded p-2 bg-light" id="faas_roof_group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Reinforced Concrete" id="roof_rc">
                <label class="form-check-label" for="roof_rc">Reinforced Concrete</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Tiles" id="roof_tiles">
                <label class="form-check-label" for="roof_tiles">Tiles</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="G.I. Sheet" id="roof_gi">
                <label class="form-check-label" for="roof_gi">G.I. Sheet</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Aluminum" id="roof_alum">
                <label class="form-check-label" for="roof_alum">Aluminum</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Asbestos" id="roof_asb">
                <label class="form-check-label" for="roof_asb">Asbestos</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Long Span" id="roof_ls">
                <label class="form-check-label" for="roof_ls">Long Span</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Nipa/Anahaw/Cogon" id="roof_nipa">
                <label class="form-check-label" for="roof_nipa">Nipa / Anahaw / Cogon</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="roof_material[]" value="Others" id="roof_other">
                <label class="form-check-label" for="roof_other">Others (Specify)</label>
            </div>
            <input type="text" class="form-control form-control-sm mt-1" name="roof_material_other" placeholder="Specify other roof material...">
        </div>
    </div>

    <!-- FLOORING -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Flooring</label>
        <div class="border rounded p-2 bg-light" id="faas_floor_group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Reinforced Concrete" id="floor_rc">
                <label class="form-check-label" for="floor_rc">Reinforced Concrete (upper floors)</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Plain Cement" id="floor_pc">
                <label class="form-check-label" for="floor_pc">Plain Cement</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Marble" id="floor_marble">
                <label class="form-check-label" for="floor_marble">Marble</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Wood" id="floor_wood">
                <label class="form-check-label" for="floor_wood">Wood</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Tiles" id="floor_tiles">
                <label class="form-check-label" for="floor_tiles">Tiles</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Bamboo" id="floor_bamboo">
                <label class="form-check-label" for="floor_bamboo">Bamboo</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="floor_material[]" value="Others" id="floor_other">
                <label class="form-check-label" for="floor_other">Others (Specify)</label>
            </div>
            <input type="text" class="form-control form-control-sm mt-1" name="floor_material_other" placeholder="Specify other floor material...">
        </div>
    </div>

    <!-- WALLS & PARTITIONS -->
    <div class="col-md-4">
        <label class="form-label fw-semibold">Walls &amp; Partitions</label>
        <div class="border rounded p-2 bg-light" id="faas_wall_group">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Reinforced Concrete" id="wall_rc">
                <label class="form-check-label" for="wall_rc">Reinforced Concrete</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Plain Cement" id="wall_pc">
                <label class="form-check-label" for="wall_pc">Plain Cement</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Wood" id="wall_wood">
                <label class="form-check-label" for="wall_wood">Wood</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="CHB" id="wall_chb">
                <label class="form-check-label" for="wall_chb">CHB</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="G.I. Sheet" id="wall_gi">
                <label class="form-check-label" for="wall_gi">G.I. Sheet</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Build-a-Wall" id="wall_baw">
                <label class="form-check-label" for="wall_baw">Build-a-Wall</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Sawali" id="wall_sawali">
                <label class="form-check-label" for="wall_sawali">Sawali</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Bamboo" id="wall_bamboo">
                <label class="form-check-label" for="wall_bamboo">Bamboo</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="wall_material[]" value="Others" id="wall_other">
                <label class="form-check-label" for="wall_other">Others (Specify)</label>
            </div>
            <input type="text" class="form-control form-control-sm mt-1" name="wall_material_other" placeholder="Specify other wall material...">
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- PROPERTY APPRAISAL -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Property Appraisal</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Unit Construction Cost (per sq.m.)</label>
        <input type="number" step="0.01" class="form-control" name="unit_construction_cost" id="faas_ucc">
    </div>

    <div class="col-md-4">
        <label class="form-label">Additional Item Cost</label>
        <input type="number" step="0.01" class="form-control" name="additional_item_cost" id="faas_aic">
    </div>

    <div class="col-md-4">
        <label class="form-label">Depreciation Rate (%)</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" name="depreciation_rate" id="faas_dep">
    </div>

    <!-- ============================================================ -->
    <!-- PROPERTY ASSESSMENT -->
    <!-- ============================================================ -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Property Assessment</h6>
    </div>

    <div class="col-md-6">
        <label class="form-label">Actual Use</label>
        <select class="form-select" name="actual_use">
            <option value="">— Select —</option>
            <option value="Residential">Residential</option>
            <option value="Commercial">Commercial</option>
            <option value="Industrial">Industrial</option>
            <option value="Agricultural">Agricultural</option>
            <option value="Special">Special</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Assessment Level (%)</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" name="assessment_level" id="faas_asmnt_level">
    </div>

    <!-- AUTO COMPUTED -->
    <div class="col-md-4">
        <label class="form-label">Building Cost</label>
        <input type="text" readonly class="form-control bg-light" name="building_cost" id="faas_building_cost">
    </div>

    <div class="col-md-4">
        <label class="form-label">Market Value</label>
        <input type="text" readonly class="form-control bg-light" name="market_value" id="faas_market_value">
    </div>

    <div class="col-md-4">
        <label class="form-label">Assessed Value</label>
        <input type="text" readonly class="form-control bg-light" name="assessed_value" id="faas_assessed_value">
    </div>

    <!-- ============================================================ -->
    <!-- FORM NAVIGATION -->
    <!-- ============================================================ -->
    <div class="col-12">
        <div id="faas_validation_summary" class="alert alert-danger d-none"></div>
    </div>
    <div class="col-12 mt-4 d-flex justify-content-end">
        <button type="button" class="btn btn-primary px-4" id="faas_building_next_btn">
            Next
        </button>
    </div>

</div>


<!-- ============================================================ -->
<!-- AUTO-COMPUTATION SCRIPT -->
<!-- ============================================================ -->
<script>
(function () {

    /* Format number as Philippine locale */
    function fmt(n) {
        if (isNaN(n) || n === '') return '';
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* Auto-computation: floor areas -> building cost -> market value -> assessed value */
    function computeFAAS() {
        var a1 = parseFloat(document.getElementById('faas_area_1f').value) || 0;
        var a2 = parseFloat(document.getElementById('faas_area_2f').value) || 0;
        var a3 = parseFloat(document.getElementById('faas_area_3f').value) || 0;
        var a4 = parseFloat(document.getElementById('faas_area_4f').value) || 0;
        var totalArea = a1 + a2 + a3 + a4;
        document.getElementById('faas_total_area').value = totalArea > 0 ? totalArea.toFixed(2) : '';

        var ucc = parseFloat(document.getElementById('faas_ucc').value) || 0;
        var aic = parseFloat(document.getElementById('faas_aic').value) || 0;
        var buildingCost = (totalArea * ucc) + aic;
        document.getElementById('faas_building_cost').value = buildingCost > 0 ? fmt(buildingCost) : '';

        var dep = parseFloat(document.getElementById('faas_dep').value) || 0;
        var marketValue = buildingCost * (1 - (dep / 100));
        document.getElementById('faas_market_value').value = marketValue > 0 ? fmt(marketValue) : '';

        var al = parseFloat(document.getElementById('faas_asmnt_level').value) || 0;
        var assessedValue = marketValue * (al / 100);
        document.getElementById('faas_assessed_value').value = assessedValue > 0 ? fmt(assessedValue) : '';
    }

    var triggerIds = [
        'faas_area_1f', 'faas_area_2f', 'faas_area_3f', 'faas_area_4f',
        'faas_ucc', 'faas_aic', 'faas_dep', 'faas_asmnt_level'
    ];
    triggerIds.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', computeFAAS);
    });

    /* Floor Plan image: upload immediately on selection, store the
       returned path in the hidden field that rides along with the
       normal JSON save, and show a thumbnail preview. */
    var floorPlanFile = document.getElementById('floor_plan_file');
    var floorPlanPathField = document.getElementById('floor_plan_path');
    var floorPlanPreview = document.getElementById('floor_plan_preview');
    var floorPlanStatus = document.getElementById('floor_plan_upload_status');

    if (floorPlanFile) {
        floorPlanFile.addEventListener('change', function () {
            var file = floorPlanFile.files[0];
            if (!file) return;

            floorPlanStatus.textContent = 'Uploading...';
            floorPlanStatus.className = 'small mt-1 text-muted';

            var formData = new FormData();
            formData.append('floor_plan', file);

            fetch('/landrecords_system/api/faas_floor_plan_upload.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (res) {
                    return res.text().then(function (text) {
                        try {
                            return text ? JSON.parse(text) : {};
                        } catch (e) {
                            return { error: text || 'Upload failed. Check your connection and try again.' };
                        }
                    });
                })
                .then(function (result) {
                    if (result.error) {
                        floorPlanStatus.textContent = result.error;
                        floorPlanStatus.className = 'small mt-1 text-danger';
                        return;
                    }
                    floorPlanPathField.value = result.path;
                    floorPlanPreview.src = '/landrecords_system/' + result.path;
                    floorPlanPreview.classList.remove('d-none');
                    floorPlanStatus.textContent = 'Uploaded.';
                    floorPlanStatus.className = 'small mt-1 text-success';
                })
                .catch(function () {
                    floorPlanStatus.textContent = 'Upload failed. Check your connection and try again.';
                    floorPlanStatus.className = 'small mt-1 text-danger';
                });
        });
    }

    /* Required-field validation before moving to the back page.
       Floor areas 2-4 and the "Others" specify text fields are required
       conditionally, since an unconditional requirement would make valid
       records (e.g. a 1-storey building, or not selecting "Others")
       impossible to submit. */
    function validateFront() {
        var invalid = [];

        document.querySelectorAll('#faas_step_front .is-invalid').forEach(function (el) {
            el.classList.remove('is-invalid');
        });
        document.querySelectorAll('#faas_step_front .border-danger').forEach(function (el) {
            el.classList.remove('border-danger');
        });

        document.querySelectorAll('#faas_step_front [required]').forEach(function (el) {
            if (!el.value || !el.value.trim()) {
                el.classList.add('is-invalid');
                invalid.push(el);
            }
        });

        var storeys = parseInt(document.querySelector('[name="storeys"]').value, 10) || 0;
        var floorIds = ['faas_area_2f', 'faas_area_3f', 'faas_area_4f'];
        for (var i = 0; i < floorIds.length; i++) {
            if (storeys >= i + 2) {
                var floorEl = document.getElementById(floorIds[i]);
                if (!floorEl.value || !floorEl.value.trim()) {
                    floorEl.classList.add('is-invalid');
                    invalid.push(floorEl);
                }
            }
        }

        ['roof', 'floor', 'wall'].forEach(function (group) {
            var groupEl = document.getElementById('faas_' + group + '_group');
            var checked = groupEl.querySelectorAll('input[type="checkbox"]:checked');
            if (checked.length === 0) {
                groupEl.classList.add('border-danger');
                invalid.push(groupEl);
            }
            var othersBox = groupEl.querySelector('input[value="Others"]');
            var otherText = groupEl.querySelector('input[name="' + group + '_material_other"]');
            if (othersBox.checked && (!otherText.value || !otherText.value.trim())) {
                otherText.classList.add('is-invalid');
                invalid.push(otherText);
            }
        });

        var summary = document.getElementById('faas_validation_summary');
        if (invalid.length) {
            summary.textContent = 'Please fill in all required fields before proceeding (' + invalid.length + ' missing).';
            summary.classList.remove('d-none');
        } else {
            summary.classList.add('d-none');
        }

        return invalid.length === 0;
    }

    /* Recompute and update (or hide) the validation summary message. */
    function updateValidationSummary() {
        var summary = document.getElementById('faas_validation_summary');
        var remaining = document.querySelectorAll('#faas_step_front .is-invalid, #faas_step_front .border-danger').length;
        if (remaining === 0) {
            summary.classList.add('d-none');
        } else {
            summary.textContent = 'Please fill in all required fields before proceeding (' + remaining + ' missing).';
        }
    }

    /* Clear a field's invalid state as soon as the user fixes it, instead of
       waiting until the next validation pass. */
    (function () {
        function sanitizeAndPreserveCursor(el) {
            if (!el || (el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA')) return;
            var val = el.value;
            if (!val || !/[<>]/.test(val)) return;
            var before = val.slice(0, el.selectionStart || 0);
            var removedBefore = (before.match(/[<>]/g) || []).length;
            var cleaned = val.replace(/[<>]/g, '');
            var newPos = Math.max(0, (el.selectionStart || 0) - removedBefore);
            el.value = cleaned;
            try { el.setSelectionRange(newPos, newPos); } catch (err) {}
        }

        document.addEventListener('input', function (e) {
            var el = e.target;
            if (!el || !el.closest) return;
            if (!el.closest('#faas_step_front')) return;
            sanitizeAndPreserveCursor(el);
            if (el.classList && el.classList.contains('is-invalid') && el.value && el.value.trim()) {
                el.classList.remove('is-invalid');
                updateValidationSummary();
            }
        });

        document.addEventListener('paste', function (e) {
            var el = e.target;
            if (!el || !el.closest) return;
            if (!el.closest('#faas_step_front')) return;
            if (el.tagName !== 'INPUT' && el.tagName !== 'TEXTAREA') return;
            e.preventDefault();
            var paste = (e.clipboardData || window.clipboardData).getData('text') || '';
            paste = paste.replace(/[<>]/g, '');
            var start = el.selectionStart || 0;
            var end = el.selectionEnd || 0;
            var newVal = el.value.slice(0, start) + paste + el.value.slice(end);
            el.value = newVal;
            var pos = start + paste.length;
            try { el.setSelectionRange(pos, pos); } catch (err) {}
            if (el.classList && el.classList.contains('is-invalid') && el.value && el.value.trim()) {
                el.classList.remove('is-invalid');
                updateValidationSummary();
            }
        });

        document.addEventListener('change', function (e) {
            var el = e.target;
            if (!el || !el.closest) return;
            if (!el.closest('#faas_step_front')) return;

            if (el.tagName === 'SELECT' && el.classList && el.classList.contains('is-invalid') && el.value) {
                el.classList.remove('is-invalid');
                updateValidationSummary();
            }

            if (el.type === 'checkbox') {
                var group = el.closest('.border');
                if (group) {
                    var checked = group.querySelectorAll('input[type="checkbox"]:checked');
                    if (checked.length > 0) {
                        group.classList.remove('border-danger');
                    }
                    var othersBox = group.querySelector('input[value="Others"]');
                    var otherText = group.querySelector('input[name$="_material_other"]');
                    if (otherText && (!othersBox.checked || (otherText.value && otherText.value.trim()))) {
                        otherText.classList.remove('is-invalid');
                    }
                    updateValidationSummary();
                }
            }
        });

        try { console.debug('FAAS input sanitizer attached (delegated)'); } catch (e) {}
    })();

    /* Next button: validate, then move forward to the next step */
    var nextBtn = document.getElementById('faas_building_next_btn');
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (!validateFront()) {
                document.getElementById('faas_validation_summary').scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            var frontStep = document.getElementById('faas_step_front');
            var backStep  = document.getElementById('faas_building_back');
            if (frontStep) frontStep.style.display = 'none';
            if (backStep)  backStep.style.display  = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.dispatchEvent(new CustomEvent('faas:next', { detail: 'building' }));
        });
    }

    /* Building Location Barangay -> auto-fill Municipality & Province */
    var locationBrgyEl = document.querySelector('select[name="barangay"]');
    if (locationBrgyEl) {
        locationBrgyEl.addEventListener('change', function () {
            var municipalityEl = document.getElementById('faas_municipality');
            var provinceEl     = document.getElementById('faas_province');
            if (this.value) {
                if (municipalityEl) municipalityEl.value = 'SAN PABLO';
                if (provinceEl)     provinceEl.value     = 'ISABELA';
            } else {
                if (municipalityEl) municipalityEl.value = '';
                if (provinceEl)     provinceEl.value     = '';
            }
        });
    }

    /* Show the floor plan preview if a record with one is loaded (edit mode). */
    document.addEventListener('faas:populated', function () {
        if (floorPlanPathField.value) {
            floorPlanPreview.src = '/landrecords_system/' + floorPlanPathField.value;
            floorPlanPreview.classList.remove('d-none');
        }
    });

})();
</script>