<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/audit_logger.php';

date_default_timezone_set('Asia/Manila');

$currentMonthName = date('F');
$currentYear = date('Y');
$selectedBarangay = trim($_GET['barangay'] ?? '');
$selectedLandType = trim($_GET['land_type'] ?? '');
$searchTerm = trim($_GET['search'] ?? '');
$searchLike = '%' . $searchTerm . '%';

/* =========================
   DELETE LAND
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_land'])) {
    $property_id = intval($_POST['property_id']);
    $admin_password = $_POST['admin_password'];
    $user_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("SELECT password FROM tbl_users WHERE user_id = ? AND role = 'admin'");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($db_password);
    if ($stmt->fetch() && $admin_password === $db_password) {
        $stmt->close();
        $deleted_lot_number = '';
        $landStmt = $conn->prepare("SELECT lot_number FROM tbl_properties WHERE property_id = ?");
        $landStmt->bind_param("i", $property_id);
        $landStmt->execute();
        $landStmt->bind_result($deleted_lot_number);
        $landStmt->fetch();
        $landStmt->close();

        $delete_sql = "UPDATE tbl_properties SET is_deleted = 1, deleted_by = ?, deleted_at = NOW() WHERE property_id = ?";
        $del_stmt = $conn->prepare($delete_sql);
        $del_stmt->bind_param("ii", $user_id, $property_id);
        $del_stmt->execute();
        $del_stmt->close();

        logAuditTrail(
            $conn,
            "Deleted land record",
            "Moved lot {$deleted_lot_number} to Recently Deleted."
        );

        $_SESSION['success_modal'] = 'land_deleted';
    } else {
        $stmt->close();
        $_SESSION['error'] = "Incorrect admin password.";
    }
    header("Location: landmanagement.php");
    exit;
}

/* =========================
   SAVE LAND (ADD LAND FORM)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_land'])) {

    $owner_id     = $_POST['owner_id'];
    $lot_number   = $_POST['lot_number'];
    $barangay     = $_POST['barangay'];
    $land_type    = $_POST['land_type'];
    $area_sqm     = floatval($_POST['area_sqm']);
    $unit_value   = floatval($_POST['unit_value']);
    $market_value = $area_sqm * $unit_value;
    $status       = $_POST['status'];
    $boundary_coordinates = $_POST['boundary_coordinates'] ?? '';
    $document_type = $_POST['document_type'] ?? '';

    $sql = "INSERT INTO tbl_properties 
            (owner_id, lot_number, barangay, land_type, area_sqm, unit_value, market_value, status, boundary_coordinates, document_type)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssddssss", $owner_id, $lot_number, $barangay, $land_type, $area_sqm, $unit_value, $market_value, $status, $boundary_coordinates, $document_type);
    $stmt->execute();

    logAuditTrail(
        $conn,
        "Added land record",
        "Created property for lot {$lot_number} in {$barangay} ({$land_type})."
    );

    header("Location: landmanagement.php");
    exit;
}

/* =========================
   EDIT LAND
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_land'])) {
    $property_id = intval($_POST['property_id'] ?? 0);
    $land_type    = $_POST['land_type'] ?? '';
    $area_sqm     = floatval($_POST['area_sqm'] ?? 0);
    $unit_value   = floatval($_POST['unit_value'] ?? 0);
    $market_value = $area_sqm * $unit_value;
    $status       = $_POST['status'] ?? '';
    $document_type = $_POST['document_type'] ?? '';
    $boundary_coordinates = $_POST['boundary_coordinates'] ?? '';

    $oldStmt = $conn->prepare("SELECT lot_number, barangay, land_type, area_sqm, unit_value, status, document_type FROM tbl_properties WHERE property_id = ? LIMIT 1");
    $oldStmt->bind_param("i", $property_id);
    $oldStmt->execute();
    $oldResult = $oldStmt->get_result();
    $oldRow = $oldResult ? $oldResult->fetch_assoc() : null;
    $oldStmt->close();

    $sql = "UPDATE tbl_properties
            SET land_type = ?, area_sqm = ?, unit_value = ?, market_value = ?, status = ?, document_type = ?, boundary_coordinates = ?
            WHERE property_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sdddsssi", $land_type, $area_sqm, $unit_value, $market_value, $status, $document_type, $boundary_coordinates, $property_id);
    $stmt->execute();
    $stmt->close();

    $auditDetails = "Updated editable fields for lot " . ($oldRow['lot_number'] ?? 'N/A') . ".";
    $changes = [];

    if ($oldRow) {
        if (($oldRow['land_type'] ?? '') !== $land_type) {
            $changes[] = "land type: " . ($oldRow['land_type'] ?? 'N/A') . " -> " . $land_type;
        }
        if ((float)($oldRow['area_sqm'] ?? 0) !== $area_sqm) {
            $changes[] = "area: " . ($oldRow['area_sqm'] ?? '0') . " -> " . $area_sqm;
        }
        if ((float)($oldRow['unit_value'] ?? 0) !== $unit_value) {
            $changes[] = "unit value: " . ($oldRow['unit_value'] ?? '0') . " -> " . $unit_value;
        }
        if (($oldRow['status'] ?? '') !== $status) {
            $changes[] = "status: " . ($oldRow['status'] ?? 'N/A') . " -> " . $status;
        }
        if (($oldRow['document_type'] ?? '') !== $document_type) {
            $changes[] = "document type updated";
        }
    }

    if (!empty($changes)) {
        $auditDetails = "Updated lot " . ($oldRow['lot_number'] ?? 'N/A') . ": " . implode(", ", $changes) . ".";
    }

    logAuditTrail($conn, "Edited land record", $auditDetails);

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
	<link rel="stylesheet" href="../../assets/css/landmanagement.css?v=1.0">

	<!-- Leaflet CSS for Map -->
	<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.draw/1.0.4/leaflet.draw.css" />
	<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
</head>

<body>

<?php include_once '../../layouts/sidebar_admin.php'; ?>

<div class="main-content">

	<?php include_once '../../layouts/header.php'; ?>

	<div class="container mt-4 px-4">

		<h2 class="page-title mb-3">Land Information Management</h2>

		<div class="land-container">

			<!-- FILTER + SEARCH + ADD -->
			<form class="land-controls" id="landFilterForm" method="GET">

				<div class="controls-left">
					<div class="filter-group">
						<select class="form-select filter-input" name="barangay">
							<option value="" disabled selected>Filter by Barangay</option>
							<option value="Poblacion" <?php echo $selectedBarangay === 'Poblacion' ? 'selected' : ''; ?>>Poblacion</option>
							<option value="San Roque" <?php echo $selectedBarangay === 'San Roque' ? 'selected' : ''; ?>>San Roque</option>
							<option value="San Jose" <?php echo $selectedBarangay === 'San Jose' ? 'selected' : ''; ?>>San Jose</option>
						</select>

						<select class="form-select filter-input" name="land_type">
							<option value="" disabled selected>Land Type</option>
							<option value="Agricultural" <?php echo $selectedLandType === 'Agricultural' ? 'selected' : ''; ?>>Agricultural</option>
							<option value="Residential" <?php echo $selectedLandType === 'Residential' ? 'selected' : ''; ?>>Residential</option>
							<option value="Commercial" <?php echo $selectedLandType === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
						</select>
					</div>
				</div>

				<div class="controls-right">
					<div class="search-group">
						<div class="input-group search-box">
							<span class="input-group-text">
								<i class="bi bi-search"></i>
							</span>
							<input type="text" class="form-control search-input" name="search"
								   value="<?php echo htmlspecialchars($searchTerm); ?>"
								   placeholder="Search lot number or owner...">
						</div>
					</div>

					<!-- ADD BUTTON -->
					<button type="button" class="btn add-btn btn-primary" data-bs-toggle="modal" data-bs-target="#addLandModal">
						<i class="bi bi-plus-lg"></i> Add Land
					</button>

				</div>

			</form>

			<!-- TABLE -->
			<div class="table-responsive mt-3">

				<table class="table table-striped land-table">

					<thead>
						<tr>
							<th>Owner Name</th>
							<th>Lot Number</th>
							<th>Barangay</th>
							<th>Land Type</th>
							<th>Document Type</th>
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
						$sql = "SELECT p.*, u.fullname 
						        FROM tbl_properties p
						        LEFT JOIN tbl_users u ON p.owner_id = u.user_id
						        WHERE p.is_deleted = 0
						          AND (? = '' OR p.barangay = ?)
						          AND (? = '' OR p.land_type = ?)
						          AND (? = '' OR p.lot_number LIKE ? OR u.fullname LIKE ?)
						        ORDER BY p.property_id DESC";

						$stmt = $conn->prepare($sql);
						if ($stmt) {
							$stmt->bind_param(
								"sssssss",
								$selectedBarangay,
								$selectedBarangay,
								$selectedLandType,
								$selectedLandType,
								$searchTerm,
								$searchLike,
								$searchLike
							);
							$stmt->execute();
							$result = $stmt->get_result();
						}

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
									<td>" . htmlspecialchars($row['document_type'] ?? '') . "</td>
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
													<button class='dropdown-item edit-land-btn'
														data-bs-toggle='modal'
														data-bs-target='#editLandModal'
														data-property-id='" . htmlspecialchars($row['property_id'], ENT_QUOTES, 'UTF-8') . "'
														data-owner-name='" . htmlspecialchars($row['fullname'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-lot-number='" . htmlspecialchars($row['lot_number'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-barangay='" . htmlspecialchars($row['barangay'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-land-type='" . htmlspecialchars($row['land_type'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-area-sqm='" . htmlspecialchars($row['area_sqm'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-unit-value='" . htmlspecialchars($row['unit_value'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-status='" . htmlspecialchars($row['status'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-document-type='" . htmlspecialchars($row['document_type'] ?? '', ENT_QUOTES, 'UTF-8') . "'
														data-boundary-coordinates='" . htmlspecialchars($row['boundary_coordinates'] ?? '', ENT_QUOTES, 'UTF-8') . "'>
														<i class='bi bi-pencil-square me-2'></i> Edit
													</button>
												</li>
												<li><hr class='dropdown-divider'></li>
												<li>
													<button class='dropdown-item text-danger' data-bs-toggle='modal' data-bs-target='#deleteLandModal' onclick='document.getElementById(\"delete_property_id\").value=\"" . $row['property_id'] . "\";'>
														<i class='bi bi-trash me-2'></i> Delete
													</button>
												</li>
											</ul>
										</div>
									</td>
								</tr>";
							}
						} else {
							echo "<tr>
								<td colspan='12' class='text-center text-muted py-2'>
									No land records found.
								</td>
							</tr>";
						}

						if (isset($stmt) && $stmt) {
							$stmt->close();
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

<!-- EDIT LAND MODAL -->
<div class="modal fade" id="editLandModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Edit Land Information</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="edit_land" value="1">
					<input type="hidden" name="property_id" id="edit_property_id">
					<input type="hidden" name="boundary_coordinates" id="edit_boundary_coordinates">

					<div class="mb-2">
						<label class="fw-bold">Owner</label>
						<input type="text" class="form-control bg-light" id="edit_owner_name" readonly>
					</div>

					<div class="mb-2">
						<label>Lot Number</label>
						<input type="text" class="form-control bg-light" id="edit_lot_number" readonly>
					</div>

					<div class="mb-2">
						<label>Barangay</label>
						<input type="text" class="form-control bg-light" id="edit_barangay" readonly>
					</div>

					<div class="mb-2">
						<label>Land Type</label>
						<select name="land_type" id="edit_land_type" class="form-select">
							<option value="Agricultural">Agricultural</option>
							<option value="Residential">Residential</option>
							<option value="Commercial">Commercial</option>
							<option value="Industrial">Industrial</option>
							<option value="Special">Special</option>
						</select>
					</div>

					<div class="mb-2">
						<label>Document Type</label>
						<select name="document_type" id="edit_document_type" class="form-select">
							<option value="">Select Document Type</option>
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
						<input type="number" step="0.01" name="area_sqm" id="edit_area_sqm" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Unit Value (₱/sqm)</label>
						<input type="number" step="0.01" name="unit_value" id="edit_unit_value" class="form-control" required>
					</div>

					<div class="mb-2">
						<label>Market Value (₱)</label>
						<input type="text" id="edit_market_value_display" class="form-control bg-light" readonly>
					</div>

					<div class="mb-2">
						<label>Assessed Value (₱)</label>
						<input type="text" id="edit_assessed_value_display" class="form-control bg-light" readonly>
					</div>

					<div class="mb-2">
						<label>Status</label>
						<select name="status" id="edit_status" class="form-select">
							<option value="Active">Active</option>
							<option value="Inactive">Inactive</option>
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Update</button>
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

<!-- DELETE LAND MODAL -->
<div class="modal fade" id="deleteLandModal" tabindex="-1">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST">
				<div class="modal-header">
					<h5 class="modal-title text-danger">Confirm Deletion</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" name="delete_land" value="1">
					<input type="hidden" name="property_id" id="delete_property_id">
					<p>Are you sure you want to delete this land record? It will be moved to Recently Deleted.</p>
					<div class="mb-3">
						<label class="form-label">Enter Admin Password to Confirm</label>
						<input type="password" name="admin_password" class="form-control" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-danger">Delete</button>
				</div>
			</form>
		</div>
	</div>
</div>

<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title visually-hidden" id="successModalLabel">
                    Land Deleted
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center px-4 pb-4">
                <div class="success-check-icon mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h5 class="mb-2">Land record deleted successfully</h5>
                <p class="text-muted mb-0">
                    The land information has been moved to Recently Deleted.
                </p>
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
	const landFilterForm = document.getElementById("landFilterForm");
	const filterInputs = landFilterForm.querySelectorAll("select");
	const searchInput = landFilterForm.querySelector(".search-input");
	let searchTimer;

	filterInputs.forEach(function (input) {
		input.addEventListener("change", function () {
			landFilterForm.submit();
		});
	});

	searchInput.addEventListener("input", function () {
		clearTimeout(searchTimer);
		searchTimer = setTimeout(function () {
			landFilterForm.submit();
		}, 500);
	});

    const areaInput = document.querySelector('#addLandModal input[name="area_sqm"]');
    const unitInput = document.querySelector('#addLandModal input[name="unit_value"]');
    const landTypeSelect = document.querySelector('#addLandModal select[name="land_type"]');
    const editModal = document.getElementById('editLandModal');
    const editAreaInput = document.getElementById('edit_area_sqm');
    const editUnitInput = document.getElementById('edit_unit_value');
    const editLandTypeSelect = document.getElementById('edit_land_type');
    const editStatusSelect = document.getElementById('edit_status');
    const editDocumentTypeSelect = document.getElementById('edit_document_type');
    const editMarketDisplay = document.getElementById('edit_market_value_display');
    const editAssessedDisplay = document.getElementById('edit_assessed_value_display');
    
    document.querySelectorAll('.view-map-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const geojsonStr = this.getAttribute('data-geojson');
            viewMap(geojsonStr);
        });
    });

    document.querySelectorAll('.edit-land-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_property_id').value = this.dataset.propertyId || '';
            document.getElementById('edit_owner_name').value = this.dataset.ownerName || '';
            document.getElementById('edit_lot_number').value = this.dataset.lotNumber || '';
            document.getElementById('edit_barangay').value = this.dataset.barangay || '';
            document.getElementById('edit_boundary_coordinates').value = this.dataset.boundaryCoordinates || '';

            editLandTypeSelect.value = this.dataset.landType || '';
            editStatusSelect.value = this.dataset.status || 'Active';
            editDocumentTypeSelect.value = this.dataset.documentType || '';
            editAreaInput.value = this.dataset.areaSqm || '';
            editUnitInput.value = this.dataset.unitValue || '';
            calculateEditValues();
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

    function calculateEditValues() {
        const area = parseFloat(editAreaInput.value) || 0;
        const unit = parseFloat(editUnitInput.value) || 0;
        const landType = editLandTypeSelect.value;

        let rate = 0;
        if (landType === 'Residential') rate = 0.06;
        else if (landType === 'Agricultural') rate = 0.07;
        else if (landType === 'Commercial') rate = 0.14;
        else if (landType === 'Industrial') rate = 0.14;
        else if (landType === 'Special') rate = 0.05;

        const marketValue = area * unit;
        const assessedValue = marketValue * rate;

        editMarketDisplay.value = marketValue > 0 ? marketValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
        editAssessedDisplay.value = assessedValue > 0 ? assessedValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
    }

    editAreaInput.addEventListener('input', calculateEditValues);
    editUnitInput.addEventListener('input', calculateEditValues);
    editLandTypeSelect.addEventListener('change', calculateEditValues);

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

<?php if (isset($_SESSION['success_modal']) && $_SESSION['success_modal'] === 'land_deleted'): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var successModal = new bootstrap.Modal(document.getElementById('successModal'));
    successModal.show();

    setTimeout(function () {
        successModal.hide();
    }, 3000);
});
</script>
<?php unset($_SESSION['success_modal']); endif; ?>
</body>
</html>
