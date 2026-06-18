<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$currentMonthName = date('F');
$currentYear = date('Y');

/* =========================
   SAVE LAND (ADD LAND FORM)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_land'])) {

    $owner_id     = $_POST['owner_id'];
    $lot_number   = $_POST['lot_number'];
    $tax_dec_no   = $_POST['tax_dec_no'];
    $barangay     = $_POST['barangay'];
    $land_type    = $_POST['land_type'];
    $area_sqm     = floatval($_POST['area_sqm']);
    $unit_value   = floatval($_POST['unit_value']);
    $market_value = $area_sqm * $unit_value;
    $status       = $_POST['status'];
    $boundary_coordinates = $_POST['boundary_coordinates'] ?? '';
    $document_type = $_POST['document_type'] ?? '';

    $sql = "INSERT INTO tbl_properties 
            (owner_id, lot_number, tax_dec_no, barangay, land_type, area_sqm, unit_value, market_value, status, boundary_coordinates, document_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssddsss", $owner_id, $lot_number, $tax_dec_no, $barangay, $land_type, $area_sqm, $unit_value, $market_value, $status, $boundary_coordinates, $document_type);
    $stmt->execute();

    header("Location: landmanagement.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Land Information Management</title>

	<link rel="icon" type="image/png" href="../../assets/img/logo.png">

	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

	<link rel="stylesheet" href="../../assets/css/layout.css">
	<link rel="stylesheet" href="../../assets/css/landmanagement.css">

	<!-- Leaflet CSS for Map -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
	<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
	<style>
		#addLandMap { height: 300px; width: 100%; margin-bottom: 15px; z-index: 1; border-radius: 6px; }
		#viewLandMap { height: 400px; width: 100%; z-index: 1; border-radius: 6px; }
	</style>
</head>

<body>

<?php include_once '../../layouts/sidebar_admin.php'; ?>

<div class="main-content">

	<?php include_once '../../layouts/header.php'; ?>

	<div class="container mt-4 px-4">

		<h2 class="page-title mb-3">Land Information Management</h2>

		<div class="land-container">

			<!-- FILTER + SEARCH + ADD -->
			<div class="land-controls">

				<div class="filter-group">

					<select class="form-select filter-input">
						<option value="">Filter by Barangay</option>
						<option>Poblacion</option>
						<option>San Roque</option>
						<option>San Jose</option>
					</select>

					<select class="form-select filter-input">
						<option value="">Land Type</option>
						<option>Agricultural</option>
						<option>Residential</option>
						<option>Commercial</option>
					</select>

				</div>

				<div class="search-group d-flex gap-2">

					<div class="input-group search-box">
						<span class="input-group-text">
							<i class="bi bi-search"></i>
						</span>
						<input type="text" class="form-control search-input"
							   placeholder="Search lot number or owner...">
					</div>

					<!-- ADD BUTTON -->
					<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandModal">
						<i class="bi bi-plus-lg"></i> Add Land
					</button>

				</div>

			</div>

			<!-- TABLE -->
			<div class="table-responsive mt-3">

				<table class="table table-striped land-table">

					<thead>
						<tr>
							<th>Owner Name</th>
							<th>Lot Number</th>
							<th>Barangay</th>
							<th>Land Type</th>
							<th>Area (sqm)</th>
							<th>Unit Val.</th>
							<th>Market Val.</th>
							<th>Rate</th>
							<th>Assessed Val.</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>
					</thead>

					<tbody>

						<?php
						$sql = "SELECT p.*, u.fullname 
						        FROM tbl_properties p
						        LEFT JOIN tbl_users u ON p.owner_id = u.user_id";

						$result = $conn->query($sql);

						if ($result->num_rows > 0) {
							while ($row = $result->fetch_assoc()) {

								$market = floatval($row['market_value'] ?? 0);
								$unit = floatval($row['unit_value'] ?? 0);
								$type = $row['land_type'];
								$rate = 0;
								if ($type === 'Residential') $rate = 0.06;
								elseif ($type === 'Agricultural') $rate = 0.07;
								elseif ($type === 'Commercial') $rate = 0.14;
								elseif ($type === 'Industrial') $rate = 0.14;
								elseif ($type === 'Special') $rate = 0.05;
								
								$assessed_value = $market * $rate;
								$formatted_market = number_format($market, 2);
								$formatted_unit = number_format($unit, 2);
								$formatted_assessed = number_format($assessed_value, 2);
								$rate_pct = ($rate * 100) . '%';

								echo "<tr>
									<td>" . htmlspecialchars($row['fullname'] ?? '') . "</td>
									<td>" . htmlspecialchars($row['lot_number']) . "</td>
									<td>" . htmlspecialchars($row['barangay']) . "</td>
									<td>" . htmlspecialchars($row['land_type']) . "</td>
									<td>" . htmlspecialchars($row['area_sqm']) . "</td>
									<td>₱ {$formatted_unit}</td>
									<td>₱ {$formatted_market}</td>
									<td>{$rate_pct}</td>
									<td>₱ {$formatted_assessed}</td>
									<td>" . htmlspecialchars($row['status']) . "</td>
									<td>
										<div class='dropdown position-static'>
											<button class='btn btn-sm btn-link text-dark' data-bs-toggle='dropdown'>
												<i class='bi bi-three-dots-vertical'></i>
											</button>
											<ul class='dropdown-menu dropdown-menu-end shadow'>
												<li>
													<button class='dropdown-item view-map-btn' data-geojson='" . htmlspecialchars($row['boundary_coordinates'] ?? '', ENT_QUOTES, 'UTF-8') . "'>
														<i class='bi bi-map me-2'></i> View Map
													</button>
												</li>
												<li>
													<button class='dropdown-item'>
														<i class='bi bi-pencil-square me-2'></i> Edit
													</button>
												</li>
											</ul>
										</div>
									</td>
								</tr>";
							}
						} else {
							echo "<tr>
								<td colspan='11' class='text-center text-muted py-2'>
									No land records found.
								</td>
							</tr>";
						}
						?>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

<!-- ADD LAND MODAL -->
<div class="modal fade" id="addLandModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">

			<form method="POST">

				<div class="modal-header">
					<h5 class="modal-title">Add Land Information</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>

				<div class="modal-body">

					<input type="hidden" name="save_land" value="1">
					<input type="hidden" name="boundary_coordinates" id="boundary_coordinates">

					<div class="mb-2">
						<label class="fw-bold">Draw Land Boundary</label>
						<div id="addLandMap"></div>
						<small class="text-muted">Use the polygon tool on the map to draw the boundaries for reference. The exact Area (sqm) should be manually inputted below from official documents (e.g., Survey Plan, Tax Dec).</small>
					</div>

					<div class="mb-2">
						<label>Owner</label>
						<select name="owner_id" class="form-select" required>
							<option value="" disabled selected>Select an Owner</option>
							<?php
								$user_sql = "SELECT user_id, fullname FROM tbl_users WHERE role = 'Client' ORDER BY fullname ASC";
							$user_res = $conn->query($user_sql);
							if ($user_res && $user_res->num_rows > 0) {
								while ($u = $user_res->fetch_assoc()) {
									echo "<option value='{$u['user_id']}'>" . htmlspecialchars($u['fullname']) . "</option>";
								}
							}
							?>
						</select>
					</div>

					<div class="mb-2">
						<label>Lot Number</label>
						<input type="text" name="lot_number" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Barangay</label>
						<input type="text" name="barangay" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Land Type</label>
						<select name="land_type" class="form-select">
							<option value="" disabled selected>Select Land Type</option>
							<option>Agricultural</option>
							<option>Residential</option>
							<option>Commercial</option>
							<option>Industrial</option>
							<option>Special</option>
						</select>
					</div>

					<div class="mb-2">
						<label>Document Type (For Printing)</label>
						<select name="document_type" class="form-select">
							<option value="" disabled selected>Select Document Type</option>
							<option value="24-FAAS-BUILDING">24-FAAS-BUILDING</option>
							<option value="24-LAND-BUILDING">24-LAND-BUILDING</option>
							<option value="24-FAAS-MACHINE-BUILDING">24-FAAS-MACHINE-BUILDING</option>
							<option value="CERTIFIED TRUE COPY OF TD SAN PABLO">CERTIFIED TRUE COPY OF TD SAN PABLO (TAX DECLARATION)</option>
							<option value="CERT WITH IMPROVEMENTS">CERTIFICATION WITH IMPROVEMENTS</option>
							<option value="CERT of NO IMPROVEMENTS">CERTIFICATION of NO IMPROVEMENTS</option>
							<option value="PROPERTY LAND HOLDINGS">PROPERTY LAND HOLDINGS</option>
						</select>
					</div>

					<div class="mb-2">
						<label>Area (sqm)</label>
						<input type="number" step="0.01" name="area_sqm" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Unit Value (₱/sqm)</label>
						<input type="number" step="0.01" name="unit_value" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Market Value (₱)</label>
						<input type="text" id="market_value_display" class="form-control bg-light" readonly>
					</div>

					<div class="mb-2">
						<label>Assessed Value (₱)</label>
						<input type="text" id="assessed_value_display" class="form-control bg-light" readonly>
					</div>

					<div class="mb-2">
						<label>Status</label>
						<select name="status" class="form-select">
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
						</select>
					</div>

				</div>

				<div class="modal-footer">
					<button type="submit" class="btn btn-primary">Save</button>
				</div>

			</form>

		</div>
	</div>
</div>

<!-- VIEW MAP MODAL -->
<div class="modal fade" id="viewMapModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Property Location</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body p-0">
				<div id="viewLandMap"></div>
			</div>
		</div>
	</div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let addMap, drawControl, drawnItems;
let viewMapInstance, viewDrawnItems;
let currentGeojsonStr = null;

function viewMap(geojsonStr) {
    currentGeojsonStr = geojsonStr;
    const modalEl = document.getElementById('viewMapModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) {
        modal = new bootstrap.Modal(modalEl);
    }
    modal.show();
}

document.addEventListener("DOMContentLoaded", function() {
    const areaInput = document.querySelector('input[name="area_sqm"]');
    const unitInput = document.querySelector('input[name="unit_value"]');
    const landTypeSelect = document.querySelector('select[name="land_type"]');
    
    document.querySelectorAll('.view-map-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const geojsonStr = this.getAttribute('data-geojson');
            viewMap(geojsonStr);
        });
    });

    const marketDisplay = document.getElementById('market_value_display');
    const assessedDisplay = document.getElementById('assessed_value_display');

    function calculateValues() {
        const area = parseFloat(areaInput.value) || 0;
        const unit = parseFloat(unitInput.value) || 0;
        const landType = landTypeSelect.value;
        
        let rate = 0;
        if (landType === 'Residential') rate = 0.06;
        else if (landType === 'Agricultural') rate = 0.07;
        else if (landType === 'Commercial') rate = 0.14;
        else if (landType === 'Industrial') rate = 0.14;
        else if (landType === 'Special') rate = 0.05;

        const marketValue = area * unit;
        const assessedValue = marketValue * rate;

        marketDisplay.value = marketValue > 0 ? marketValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
        assessedDisplay.value = assessedValue > 0 ? assessedValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
    }

    areaInput.addEventListener('input', calculateValues);
    unitInput.addEventListener('input', calculateValues);
    landTypeSelect.addEventListener('change', calculateValues);

    // Setup Add Land Map
    const addLandModal = document.getElementById('addLandModal');
    addLandModal.addEventListener('shown.bs.modal', function () {
        if (!addMap) {
            addMap = L.map('addLandMap').setView([16.9754, 121.8107], 10); // Default to Isabela
            
            const osmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxNativeZoom: 19, maxZoom: 24 });
            const satelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxNativeZoom: 20, maxZoom: 24 });
            
            // Default to Satellite View for easier boundary drawing
            satelliteLayer.addTo(addMap);
            
            L.control.layers({
                "Satellite View": satelliteLayer,
                "Street Map": osmLayer
            }).addTo(addMap);

            // Add Search Bar (Geocoder)
            L.Control.geocoder({
                defaultMarkGeocode: false
            }).on('markgeocode', function(e) {
                const bbox = e.geocode.bbox;
                const poly = L.polygon([
                    bbox.getSouthEast(),
                    bbox.getNorthEast(),
                    bbox.getNorthWest(),
                    bbox.getSouthWest()
                ]);
                addMap.fitBounds(poly.getBounds());
            }).addTo(addMap);
            
            drawnItems = new L.FeatureGroup();
            addMap.addLayer(drawnItems);
            
            drawControl = new L.Control.Draw({
                edit: { featureGroup: drawnItems },
                draw: {
                    polygon: true,
                    polyline: false,
                    rectangle: false,
                    circle: false,
                    marker: false,
                    circlemarker: false
                }
            });
            addMap.addControl(drawControl);
            
            addMap.on(L.Draw.Event.CREATED, function (event) {
                const layer = event.layer;
                drawnItems.clearLayers(); // Only allow one polygon per property
                drawnItems.addLayer(layer);
                
                const geojson = layer.toGeoJSON();
                document.getElementById('boundary_coordinates').value = JSON.stringify(geojson);
                
                // Do not auto-calculate area to ensure manual encoding from official documents
            });
        }
        setTimeout(() => {
            addMap.invalidateSize();
        }, 100);
    });

    // Setup View Map Modal
    const viewMapModalEl = document.getElementById('viewMapModal');
    viewMapModalEl.addEventListener('shown.bs.modal', function () {
        if (!viewMapInstance) {
            viewMapInstance = L.map('viewLandMap').setView([16.9754, 121.8107], 10); // Default to Isabela
            
            const viewOsmLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxNativeZoom: 19, maxZoom: 24 });
            const viewSatelliteLayer = L.tileLayer('https://mt1.google.com/vt/lyrs=y&x={x}&y={y}&z={z}', { maxNativeZoom: 20, maxZoom: 24 });
            
            viewSatelliteLayer.addTo(viewMapInstance);
            
            L.control.layers({
                "Satellite View": viewSatelliteLayer,
                "Street Map": viewOsmLayer
            }).addTo(viewMapInstance);
            viewDrawnItems = new L.FeatureGroup();
            viewMapInstance.addLayer(viewDrawnItems);
        }
        viewMapInstance.invalidateSize();
        viewDrawnItems.clearLayers();
        
        if (currentGeojsonStr && currentGeojsonStr !== "null" && currentGeojsonStr !== "") {
            try {
                let parsed = JSON.parse(currentGeojsonStr);
                const layer = L.geoJSON(parsed);
                viewDrawnItems.addLayer(layer);
                viewMapInstance.fitBounds(layer.getBounds(), { padding: [20, 20] });
            } catch(e) { console.error("Invalid geometry data", e); }
        }
    });
});
</script>

</body>
</html>