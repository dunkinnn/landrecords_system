<?php
/**
 * Form Template: 24-FAAS-BUILDING
 * Used for building property assessment forms
 */
?>
<div class="row g-3">

    <!-- PROPERTY INFORMATION -->
    <div class="col-12">
        <h6 class="border-bottom pb-2 fw-bold">Property Information</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">ARP No.</label>
        <input type="text" class="form-control" name="arp_no">
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
        <input type="text" class="form-control" name="owner_name">
    </div>

    <div class="col-md-6">
        <label class="form-label">Owner Address</label>
        <input type="text" class="form-control" name="owner_address">
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
        <label class="form-label">Administrator Address</label>
        <input type="text" class="form-control" name="beneficial_address">
    </div>

    <!-- BUILDING LOCATION -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Building Location</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Street</label>
        <input type="text" class="form-control" name="street">
    </div>

    <div class="col-md-4">
        <label class="form-label">Barangay</label>
        <input type="text" class="form-control" name="barangay">
    </div>

    <div class="col-md-4">
        <label class="form-label">Municipality</label>
        <input type="text" class="form-control" name="municipality">
    </div>

    <div class="col-md-4">
        <label class="form-label">Province</label>
        <input type="text" class="form-control" name="province">
    </div>

    <!-- LAND REFERENCE -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Land Reference</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Lot Number</label>
        <input type="text" class="form-control" name="lot_number">
    </div>

    <div class="col-md-4">
        <label class="form-label">Block Number</label>
        <input type="text" class="form-control" name="block_number">
    </div>

    <div class="col-md-4">
        <label class="form-label">Survey Number</label>
        <input type="text" class="form-control" name="survey_number">
    </div>

    <div class="col-md-6">
        <label class="form-label">OCT/TCT/CLOA No.</label>
        <input type="text" class="form-control" name="oct_tct_no">
    </div>

    <div class="col-md-6">
        <label class="form-label">Land Area</label>
        <input type="number" step="0.01" class="form-control" name="land_area">
    </div>

    <!-- GENERAL DESCRIPTION -->
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
        <label class="form-label">No. of Storeys</label>
        <input type="number" class="form-control" name="storeys">
    </div>

    <div class="col-md-4">
        <label class="form-label">Building Permit No.</label>
        <input type="text" class="form-control" name="building_permit_no">
    </div>

    <div class="col-md-4">
        <label class="form-label">Permit Date Issued</label>
        <input type="date" class="form-control" name="permit_date">
    </div>

    <div class="col-md-3">
        <label class="form-label">1st Floor Area</label>
        <input type="number" step="0.01" class="form-control" name="first_floor_area">
    </div>

    <div class="col-md-3">
        <label class="form-label">2nd Floor Area</label>
        <input type="number" step="0.01" class="form-control" name="second_floor_area">
    </div>

    <div class="col-md-3">
        <label class="form-label">3rd Floor Area</label>
        <input type="number" step="0.01" class="form-control" name="third_floor_area">
    </div>

    <div class="col-md-3">
        <label class="form-label">4th Floor Area</label>
        <input type="number" step="0.01" class="form-control" name="fourth_floor_area">
    </div>

    <div class="col-md-6">
        <label class="form-label">Date Constructed</label>
        <input type="date" class="form-control" name="date_constructed">
    </div>

    <div class="col-md-6">
        <label class="form-label">Date Occupied</label>
        <input type="date" class="form-control" name="date_occupied">
    </div>

    <!-- STRUCTURAL MATERIALS -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Structural Materials</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Roof Material</label>
        <select class="form-select" name="roof_material">
            <option>G.I. Sheet</option>
            <option>Concrete</option>
            <option>Long Span</option>
            <option>Tiles</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Floor Material</label>
        <select class="form-select" name="floor_material">
            <option>Plain Cement</option>
            <option>Tiles</option>
            <option>Marble</option>
            <option>Wood</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Wall Material</label>
        <select class="form-select" name="wall_material">
            <option>CHB</option>
            <option>Wood</option>
            <option>Concrete</option>
            <option>G.I. Sheet</option>
        </select>
    </div>

    <!-- PROPERTY APPRAISAL -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Property Appraisal</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label">Unit Construction Cost</label>
        <input type="number" step="0.01" class="form-control" name="unit_construction_cost">
    </div>

    <div class="col-md-4">
        <label class="form-label">Additional Item Cost</label>
        <input type="number" step="0.01" class="form-control" name="additional_item_cost">
    </div>

    <div class="col-md-4">
        <label class="form-label">Depreciation Rate (%)</label>
        <input type="number" step="0.01" class="form-control" name="depreciation_rate">
    </div>

    <!-- PROPERTY ASSESSMENT -->
    <div class="col-12 mt-3">
        <h6 class="border-bottom pb-2 fw-bold">Property Assessment</h6>
    </div>

    <div class="col-md-6">
        <label class="form-label">Actual Use</label>
        <select class="form-select" name="actual_use">
            <option>Residential</option>
            <option>Commercial</option>
            <option>Industrial</option>
            <option>Agricultural</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Assessment Level (%)</label>
        <input type="number" step="0.01" class="form-control" name="assessment_level">
    </div>

    <!-- AUTO COMPUTED -->
    <div class="col-md-4">
        <label class="form-label">Building Cost</label>
        <input type="text" readonly class="form-control bg-light" name="building_cost">
    </div>

    <div class="col-md-4">
        <label class="form-label">Market Value</label>
        <input type="text" readonly class="form-control bg-light" name="market_value">
    </div>

    <div class="col-md-4">
        <label class="form-label">Assessed Value</label>
        <input type="text" readonly class="form-control bg-light" name="assessed_value">
    </div>

</div>