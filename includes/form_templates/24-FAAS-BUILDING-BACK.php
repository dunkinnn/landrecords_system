<?php
/**
 * Form Template: 24-FAAS-BUILDING (BACK)
 * Real Property Field Appraisal & Assessment Sheet — Building & Other Improvements
 * Property Appraisal / Assessment / Memoranda / Superseded Assessment / Approval
 * Municipality: San Pablo, Isabela
 */
?>
<div class="row g-3" id="faas_building_back" style="display:none">
    <input type="hidden" name="building_id" id="building_id">
    <!-- ============================================================ -->
    <!-- ADDITIONAL ITEMS -->
    <!-- ============================================================ -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 fw-bold">Additional Items <small class="text-muted fw-normal">(use additional sheet if necessary)</small></h6>
    </div>
    <div class="col-12">
        <table class="table table-bordered table-sm align-middle" id="faas_addl_items_table">
            <thead class="table-light">
                <tr>
                    <th style="width:45%">Description</th>
                    <th style="width:20%">Area / Qty</th>
                    <th style="width:20%">Amount (₱)</th>
                    <th style="width:15%"></th>
                </tr>
            </thead>
            <tbody>
                <tr class="faas-addl-item-row">
                    <td><input type="text" class="form-control form-control-sm" name="addl_item_desc[]" placeholder="e.g. STEEL WINDOW w/o GRILLE 4 x 1.5"></td>
                    <td><input type="text" class="form-control form-control-sm" name="addl_item_qty[]" placeholder="e.g. 30.00 sqm"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm faas-addl-item-amount" name="addl_item_amount[]" placeholder="0.00"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger faas-remove-row">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="faas_add_item_btn">+ Add Item</button>
    </div>
    <!-- ============================================================ -->
    <!-- PROPERTY APPRAISAL (SUMMARY) -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 fw-bold">Property Appraisal</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label">Unit Construction Cost (per sq.m.)</label>
        <input type="number" step="0.01" class="form-control" name="back_unit_construction_cost" id="back_ucc">
    </div>
    <div class="col-md-4">
        <label class="form-label">Building Core Sub-Total (₱)</label>
        <input type="number" step="0.01" class="form-control" name="building_core_subtotal" id="back_subtotal" placeholder="e.g. 180,000.00">
    </div>
    <div class="col-md-4">
        <label class="form-label">Cost of Additional Items (₱)</label>
        <input type="text" readonly class="form-control bg-light" id="back_addl_items_total" name="addl_items_total">
    </div>
    <div class="col-md-4">
        <label class="form-label">Total Construction Cost (₱)</label>
        <input type="text" readonly class="form-control bg-light" id="back_total_construction_cost" name="total_construction_cost">
    </div>
    <div class="col-md-4">
        <label class="form-label">Depreciation Rate (%)</label>
        <input type="number" step="0.01" min="0" max="100" class="form-control" name="back_depreciation_rate" id="back_dep_rate">
    </div>
    <div class="col-md-4">
        <label class="form-label">Depreciation Cost (₱)</label>
        <input type="text" readonly class="form-control bg-light" id="back_depreciation_cost" name="depreciation_cost">
    </div>
    <div class="col-md-4">
        <label class="form-label">Market Value (₱)</label>
        <input type="text" readonly class="form-control bg-light" id="back_market_value" name="back_market_value">
    </div>
    <!-- ============================================================ -->
    <!-- PROPERTY ASSESSMENT -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 fw-bold">Property Assessment</h6>
    </div>
    <div class="col-12">
        <table class="table table-bordered table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Actual Use</th>
                    <th>Market Value (₱)</th>
                    <th>Assessment Level (%)</th>
                    <th>Assessed Value (₱)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select class="form-select form-select-sm" name="back_actual_use">
                            <option value="">— Select —</option>
                            <option value="Residential">Residential</option>
                            <option value="Commercial">Commercial</option>
                            <option value="Industrial">Industrial</option>
                            <option value="Agricultural">Agricultural</option>
                            <option value="Special">Special</option>
                        </select>
                    </td>
                    <td><input type="text" readonly class="form-control form-control-sm bg-light" id="back_assess_market_value" name="back_assess_market_value"></td>
                    <td><input type="number" step="0.01" min="0" max="100" class="form-control form-control-sm" name="back_assessment_level" id="back_assess_level"></td>
                    <td><input type="text" readonly class="form-control form-control-sm bg-light" id="back_assessed_value" name="back_assessed_value"></td>
                </tr>
                <tr class="table-light fw-semibold">
                    <td colspan="3" class="text-end">TOTAL</td>
                    <td><input type="text" readonly class="form-control form-control-sm bg-light" id="back_total_assessed_value" name="back_total_assessed_value"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6">
        <label class="form-label d-block">Taxability</label>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="taxability" id="tax_taxable" value="Taxable" checked>
            <label class="form-check-label" for="tax_taxable">Taxable</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="taxability" id="tax_exempt" value="Exempt">
            <label class="form-check-label" for="tax_exempt">Exempt</label>
        </div>
    </div>
    <div class="col-md-3">
        <label class="form-label">Effectivity Quarter</label>
        <select class="form-select" name="effectivity_quarter">
            <option value="">— Select —</option>
            <option value="1st">1st</option>
            <option value="2nd">2nd</option>
            <option value="3rd">3rd</option>
            <option value="4th">4th</option>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Effectivity Year</label>
        <input type="number" class="form-control" name="effectivity_year" placeholder="e.g. 2026">
    </div>
    <!-- ============================================================ -->
    <!-- MEMORANDA -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 fw-bold">Memoranda</h6>
    </div>
    <div class="col-12">
        <select class="form-select" name="memoranda">
            <option value="" selected disabled>— Select Memoranda —</option>
            <option value="Revised pursuant to Section 219 of R.A. 7160 and in accordance with the approved 2024 Schedule of Fair Market Values.">Revised pursuant to Section 219 of R.A. 7160 and in accordance with the approved 2024 Schedule of Fair Market Values.</option>
            <option value="Declared and assessed during General Revision pursuant to Section 219 of R.A. 7160 and in accordance with the approved 2024 Schedule of Fair Market Values.">Declared and assessed during General Revision pursuant to Section 219 of R.A. 7160 and in accordance with the approved 2024 Schedule of Fair Market Values.</option>
        </select>
    </div>
    <!-- ============================================================ -->
    <!-- RECORD OF SUPERSEDED ASSESSMENT -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 fw-bold">Record of Superseded Assessment</h6>
    </div>
    <div class="col-12">
        <table class="table table-bordered table-sm align-middle" id="faas_superseded_table">
            <thead class="table-light">
                <tr>
                    <th>PIN</th>
                    <th>ARP No. / TD No.</th>
                    <th>Total Assessed Value (₱)</th>
                    <th>Effectivity of Assessment</th>
                    <th>Previous Owner</th>
                    <th>AR Page No.</th>
                    <th>Recording Person</th>
                    <th>Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr class="faas-superseded-row">
                    <td><input type="text" class="form-control form-control-sm" name="superseded_pin[]"></td>
                    <td><input type="text" class="form-control form-control-sm" name="superseded_arp[]"></td>
                    <td><input type="number" step="0.01" class="form-control form-control-sm" name="superseded_assessed_value[]"></td>
                    <td><input type="text" class="form-control form-control-sm" name="superseded_effectivity[]" placeholder="e.g. 2019"></td>
                    <td><input type="text" class="form-control form-control-sm" name="superseded_prev_owner[]"></td>
                    <td><input type="text" class="form-control form-control-sm" name="superseded_ar_page[]"></td>
                    <td><input type="text" class="form-control form-control-sm" name="superseded_recorder[]"></td>
                    <td><input type="date" class="form-control form-control-sm" name="superseded_date[]"></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-danger faas-remove-row">&times;</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="faas_add_superseded_btn">+ Add Row</button>
    </div>
    <!-- ============================================================ -->
    <!-- SIGNATURES / APPROVAL -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4">
        <h6 class="border-bottom pb-2 fw-bold">Appraised, Recommended, and Approved By</h6>
    </div>
    <div class="col-md-4">
        <label class="form-label">Appraised / Assessed By</label>
        <input type="text" class="form-control mb-2" name="appraised_by_name" placeholder="Name">
        <input type="date" class="form-control" name="appraised_by_date">
    </div>
    <div class="col-md-4">
        <label class="form-label">Recommending Approval (Municipal Assessor)</label>
        <input type="text" class="form-control mb-2" name="recommending_approval_name" placeholder="Name">
        <input type="date" class="form-control" name="recommending_approval_date">
    </div>
    <div class="col-md-4">
        <label class="form-label">Approved By (Provincial Assessor)</label>
        <input type="text" class="form-control mb-2" name="approved_by_name" placeholder="Name">
        <input type="date" class="form-control" name="approved_by_date">
    </div>
    <!-- ============================================================ -->
    <!-- FORM NAVIGATION -->
    <!-- ============================================================ -->
    <div class="col-12 mt-4 d-flex justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary px-4" id="faas_building_back_btn">
            Back
        </button>
        <button type="button" class="btn btn-success px-4" id="faas_building_save_btn">
            Save
        </button>
    </div>

    <!-- Save loading/success overlay. Moved to document.body via JS on init
         so position:fixed is always relative to the real viewport, not
         affected by any ancestor's transform/flex/overflow context. -->
    <div id="faas_save_overlay" style="
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        margin: 0;
        padding: 0;
        background: rgba(15, 23, 42, 0.75);
        backdrop-filter: blur(2px);
        z-index: 1060;
        justify-content: center;
        align-items: center;
    ">
        <div id="faas_save_overlay_content" class="text-center text-light"></div>
    </div>

    <style>
        #faas_save_overlay .faas-check-circle {
            stroke: #28a745;
            stroke-width: 3;
            fill: none;
            stroke-dasharray: 151;
            stroke-dashoffset: 151;
            animation: faas-draw-circle 0.5s ease-out forwards;
        }
        #faas_save_overlay .faas-check-mark {
            stroke: #28a745;
            stroke-width: 4;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 36;
            stroke-dashoffset: 36;
            animation: faas-draw-check 0.35s ease-out 0.45s forwards;
        }
        @keyframes faas-draw-circle {
            to { stroke-dashoffset: 0; }
        }
        @keyframes faas-draw-check {
            to { stroke-dashoffset: 0; }
        }
        #faas_save_overlay .faas-success-wrap {
            animation: faas-pop-in 0.3s ease-out;
        }
        @keyframes faas-pop-in {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</div>
<!-- ============================================================ -->
<!-- AUTO-COMPUTATION SCRIPT -->
<!-- ============================================================ -->
<script>
(function () {
    function fmt(n) {
        if (isNaN(n) || n === '') return '';
        return Number(n).toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    /* Sum all Amount cells in the Additional Items table. */
    function sumAdditionalItems() {
        var total = 0;
        document.querySelectorAll('.faas-addl-item-amount').forEach(function (el) {
            total += parseFloat(el.value) || 0;
        });
        return total;
    }

    /* Recompute the whole appraisal to assessment chain. */
    function computeBack() {
        var subtotal   = parseFloat(document.getElementById('back_subtotal').value) || 0;
        var addlTotal  = sumAdditionalItems();
        document.getElementById('back_addl_items_total').value = addlTotal > 0 ? fmt(addlTotal) : '';
        var totalConstruction = subtotal + addlTotal;
        document.getElementById('back_total_construction_cost').value = totalConstruction > 0 ? fmt(totalConstruction) : '';
        var depRate = parseFloat(document.getElementById('back_dep_rate').value) || 0;
        var depCost = totalConstruction * (depRate / 100);
        document.getElementById('back_depreciation_cost').value = depCost > 0 ? fmt(depCost) : '';
        var marketValue = totalConstruction - depCost;
        document.getElementById('back_market_value').value = marketValue > 0 ? fmt(marketValue) : '';
        // Mirror market value into the assessment table.
        document.getElementById('back_assess_market_value').value = marketValue > 0 ? fmt(marketValue) : '';
        var assessLevel = parseFloat(document.getElementById('back_assess_level').value) || 0;
        var assessedValue = marketValue * (assessLevel / 100);
        document.getElementById('back_assessed_value').value = assessedValue > 0 ? fmt(assessedValue) : '';
        document.getElementById('back_total_assessed_value').value = assessedValue > 0 ? fmt(assessedValue) : '';
    }

    var triggerIds = ['back_ucc', 'back_subtotal', 'back_dep_rate', 'back_assess_level'];
    triggerIds.forEach(function (id) {
        var el = document.getElementById(id);
        if (el) el.addEventListener('input', computeBack);
    });

    /* Additional Items: add and remove rows, recompute on input. */
    var addlBody = document.querySelector('#faas_addl_items_table tbody');
    var addItemBtn = document.getElementById('faas_add_item_btn');
    if (addItemBtn) {
        addItemBtn.addEventListener('click', function () {
            var row = document.createElement('tr');
            row.className = 'faas-addl-item-row';
            row.innerHTML =
                '<td><input type="text" class="form-control form-control-sm" name="addl_item_desc[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="addl_item_qty[]"></td>' +
                '<td><input type="number" step="0.01" class="form-control form-control-sm faas-addl-item-amount" name="addl_item_amount[]" placeholder="0.00"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger faas-remove-row">&times;</button></td>';
            addlBody.appendChild(row);
        });
    }

    /* Superseded assessment: add rows. */
    var supersededBody = document.querySelector('#faas_superseded_table tbody');
    var addSupersededBtn = document.getElementById('faas_add_superseded_btn');
    if (addSupersededBtn) {
        addSupersededBtn.addEventListener('click', function () {
            var row = document.createElement('tr');
            row.className = 'faas-superseded-row';
            row.innerHTML =
                '<td><input type="text" class="form-control form-control-sm" name="superseded_pin[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="superseded_arp[]"></td>' +
                '<td><input type="number" step="0.01" class="form-control form-control-sm" name="superseded_assessed_value[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="superseded_effectivity[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="superseded_prev_owner[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="superseded_ar_page[]"></td>' +
                '<td><input type="text" class="form-control form-control-sm" name="superseded_recorder[]"></td>' +
                '<td><input type="date" class="form-control form-control-sm" name="superseded_date[]"></td>' +
                '<td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger faas-remove-row">&times;</button></td>';
            supersededBody.appendChild(row);
        });
    }

    /* Delegate row removal and live recompute for dynamically added rows. */
    document.getElementById('faas_building_back').addEventListener('click', function (e) {
        if (e.target.classList.contains('faas-remove-row')) {
            var row = e.target.closest('tr');
            var body = row.parentElement;
            // Keep at least one row in each table.
            if (body.querySelectorAll('tr').length > 1) {
                row.remove();
                computeBack();
            }
        }
    });
    document.getElementById('faas_building_back').addEventListener('input', function (e) {
        if (e.target.classList.contains('faas-addl-item-amount')) {
            computeBack();
        }
    });

    /* Back button: return to the front (building) page. */
    var backBtn = document.getElementById('faas_building_back_btn');
    if (backBtn) {
        backBtn.addEventListener('click', function () {
            var frontStep = document.getElementById('faas_step_front');
            var backStep  = document.getElementById('faas_building_back');
            if (backStep)  backStep.style.display  = 'none';
            if (frontStep) frontStep.style.display = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
            document.dispatchEvent(new CustomEvent('faas:back', { detail: 'building-back' }));
        });
    }

    /* Collect every named field from both form pages into one payload. */
    function collectFormData() {
        var data = {};
        document.querySelectorAll('#faas_step_front [name], #faas_building_back [name]').forEach(function (el) {
            var name = el.name;
            if (!name) return;
            var isArray = name.slice(-2) === '[]';
            var key = isArray ? name.slice(0, -2) : name;
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) return;
            var value = el.value;
            if (isArray) {
                if (!data[key]) data[key] = [];
                data[key].push(value);
            } else {
                data[key] = value;
            }
        });
        return data;
    }

    /* Populate both form pages from a loaded record. */
    function populateForm(data) {
        Object.keys(data).forEach(function (key) {
            if (Array.isArray(data[key])) return;
            document.querySelectorAll('[name="' + key + '"]').forEach(function (el) {
                if (el.type === 'radio') {
                    el.checked = (el.value === data[key]);
                } else {
                    el.value = data[key] === null ? '' : data[key];
                }
            });
        });

        ['roof_material', 'floor_material', 'wall_material'].forEach(function (key) {
            var values = (data[key] || '').split(',');
            document.querySelectorAll('[name="' + key + '[]"]').forEach(function (cb) {
                cb.checked = values.indexOf(cb.value) !== -1;
            });
        });

        if (Array.isArray(data.addl_item_desc) && data.addl_item_desc.length) {
            for (var i = 1; i < data.addl_item_desc.length; i++) addItemBtn.click();
            document.querySelectorAll('.faas-addl-item-row').forEach(function (row, i) {
                row.querySelector('[name="addl_item_desc[]"]').value = data.addl_item_desc[i] || '';
                row.querySelector('[name="addl_item_qty[]"]').value = data.addl_item_qty[i] || '';
                row.querySelector('[name="addl_item_amount[]"]').value = data.addl_item_amount[i] || '';
            });
        }

        if (Array.isArray(data.superseded_pin) && data.superseded_pin.length) {
            for (var j = 1; j < data.superseded_pin.length; j++) addSupersededBtn.click();
            document.querySelectorAll('.faas-superseded-row').forEach(function (row, i) {
                row.querySelector('[name="superseded_pin[]"]').value = data.superseded_pin[i] || '';
                row.querySelector('[name="superseded_arp[]"]').value = data.superseded_arp[i] || '';
                row.querySelector('[name="superseded_assessed_value[]"]').value = data.superseded_assessed_value[i] || '';
                row.querySelector('[name="superseded_effectivity[]"]').value = data.superseded_effectivity[i] || '';
                row.querySelector('[name="superseded_prev_owner[]"]').value = data.superseded_prev_owner[i] || '';
                row.querySelector('[name="superseded_ar_page[]"]').value = data.superseded_ar_page[i] || '';
                row.querySelector('[name="superseded_recorder[]"]').value = data.superseded_recorder[i] || '';
                row.querySelector('[name="superseded_date[]"]').value = data.superseded_date[i] || '';
            });
        }

        computeBack();
    }

    /* Move the overlay to document.body so position:fixed is always
       relative to the real viewport, regardless of ancestor CSS. */
    var saveOverlay = document.getElementById('faas_save_overlay');
    var saveOverlayContent = document.getElementById('faas_save_overlay_content');
    if (saveOverlay && saveOverlay.parentElement !== document.body) {
        document.body.appendChild(saveOverlay);
    }

    var SPINNER_HTML =
        '<div class="spinner-border text-light mb-3" role="status" style="width:3rem;height:3rem;">' +
        '<span class="visually-hidden">Loading...</span></div>' +
        '<p class="mb-0">Saving Building record...</p>';

    function successHtml(arpNo) {
        return '' +
            '<div class="faas-success-wrap">' +
            '<svg width="72" height="72" viewBox="0 0 52 52" class="mb-3">' +
            '<circle class="faas-check-circle" cx="26" cy="26" r="24" />' +
            '<path class="faas-check-mark" d="M14 27l7 7 16-16" />' +
            '</svg>' +
            '<h5 class="mb-1">Building Record Saved</h5>' +
            '<p class="mb-0 text-light" style="opacity:0.75;">' + (arpNo ? 'ARP No. ' + arpNo : 'FAAS Building') + ' has been saved to the system.</p>' +
            '</div>';
    }

    function errorHtml(message) {
        return '<p class="text-danger mb-0">' + message + '</p>';
    }

    /* Save button: persist the full record. Shows an overlay with a
       spinner for a guaranteed minimum 5 seconds (even if the save
       finishes faster), then an animated success state, then refreshes
       the page. */
    var saveBtn = document.getElementById('faas_building_save_btn');

    if (saveBtn) {
        saveBtn.addEventListener('click', function () {
            saveBtn.disabled = true;
            saveOverlayContent.innerHTML = SPINNER_HTML;
            saveOverlay.style.display = 'flex';

            var startTime = Date.now();
            var minDuration = 5000;
            var payload = collectFormData();

            fetch('/landrecords_system/api/faas_building_save.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
                .then(function (res) { return res.json(); })
                .then(function (result) {
                    var remaining = Math.max(0, minDuration - (Date.now() - startTime));
                    setTimeout(function () {
                        if (result.error) {
                            saveOverlayContent.innerHTML = errorHtml('Save failed: ' + result.error);
                            saveBtn.disabled = false;
                            setTimeout(function () { saveOverlay.style.display = 'none'; }, 2000);
                            return;
                        }
                        document.getElementById('property_id').value = result.property_id;
                        document.getElementById('building_id').value = result.building_id;
                        var arpField = document.querySelector('[name="arp_no"]');
                        saveOverlayContent.innerHTML = successHtml(arpField ? arpField.value : '');
                        document.dispatchEvent(new CustomEvent('faas:save-building', { detail: result }));
                        setTimeout(function () {
                            window.location.reload();
                        }, 1800);
                    }, remaining);
                })
                .catch(function () {
                    var remaining = Math.max(0, minDuration - (Date.now() - startTime));
                    setTimeout(function () {
                        saveOverlayContent.innerHTML = errorHtml('Save failed. Check your connection and try again.');
                        saveBtn.disabled = false;
                        setTimeout(function () { saveOverlay.style.display = 'none'; }, 2000);
                    }, remaining);
                });
        });
    }

    /* Load an existing record either from the URL's ?id= (standalone
       page use) or from window.faasPreloadId (set by landmanagement.php
       right before this fragment is injected into the Add/Edit modal,
       since a modal has no URL of its own to carry an id). */
    var loadId = new URLSearchParams(window.location.search).get('id') || window.faasPreloadId || null;
    window.faasPreloadId = null;
    if (loadId) {
        fetch('/landrecords_system/api/faas_building_load.php?id=' + encodeURIComponent(loadId))
            .then(function (res) { return res.json(); })
            .then(function (data) {
                if (data.error) {
                    alert(data.error);
                    return;
                }
                populateForm(data);
            })
            .catch(function () {
                alert('Failed to load record.');
            });
    }
})();
</script>