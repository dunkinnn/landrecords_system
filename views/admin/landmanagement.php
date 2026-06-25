<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/form_template_loader.php';

date_default_timezone_set('Asia/Manila');

$currentMonthName = date('F');
$currentYear = date('Y');
$selectedBarangay = trim($_GET['barangay'] ?? '');
$selectedLandType = trim($_GET['land_type'] ?? '');
$searchTerm = trim($_GET['search'] ?? '');
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
	<link rel="stylesheet" href="../../assets/css/land_v2.css?v=1.0">
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
							<th>Property ID</th>
							<th>Lot Number</th>
							<th>Owner Name</th>
							<th>Barangay</th>
							<th>Land Type</th>
							<th>Area (sqm)</th>
							<th>Tax Dec. No</th>
							<th>Assessed Value</th>
							<th>Status</th>
							<th>Actions</th>
						</tr>

					</thead>

					<tbody>

						<tr>
							<td colspan="10" class="text-center text-muted py-2">No land records found.</td>
						</tr>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

<!-- ADD LAND MODAL -->
<div class="modal fade" id="addLandModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<form id="addLandForm" method="POST">
				<div class="modal-header">
					<h5 class="modal-title">Add Land</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label fw-semibold">Document Type</label>
						<select class="form-select" name="document_type" id="documentTypeSelect" required>
							<option value="" selected disabled>Select document type</option>
							<option value="24-FAAS-BUILDING">24-FAAS-BUILDING</option>
							<option value="24-LAND-BUILDING">24-LAND-BUILDING</option>
							<option value="24-FAAS-MACHINE-BUILDING">24-FAAS-MACHINE-BUILDING</option>
							<option value="CERTIFIED-TRUE-COPY-OF-TD-SAN-PABLO">CERTIFIED TRUE COPY OF TD SAN PABLO</option>
							<option value="CERT-WITH-IMPROVEMENTS">CERT WITH IMPROVEMENTS</option>
							<option value="CERT-of-NO-IMPROVEMENTS">CERT of NO IMPROVEMENTS</option>
							<option value="PROPERTY-LAND-HOLDINGS">PROPERTY LAND HOLDINGS</option>
						</select>
						<div class="form-text">Choose a document type first to load the matching form fields.</div>
					</div>

					<div id="documentFormContainer" class="border rounded-3 p-3 bg-light">
						<div class="text-muted">Select a document type to begin.</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn add-btn btn-primary">
						<i class="bi bi-save2"></i> Save Land
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	const documentTypeSelect = document.getElementById("documentTypeSelect");
	const documentFormContainer = document.getElementById("documentFormContainer");

	documentTypeSelect.addEventListener("change", function () {
		const selectedType = this.value;
		if (!selectedType) {
			documentFormContainer.innerHTML = '<div class="text-muted">Select a document type to begin.</div>';
			return;
		}

		// Show loading state
		documentFormContainer.innerHTML = '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Loading form...</div>';

		// Fetch the form template via AJAX
		fetch('../../api/get_form_template.php', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
			},
			body: JSON.stringify({
				document_type: selectedType
			})
		})
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				documentFormContainer.innerHTML = data.html;
			} else {
				documentFormContainer.innerHTML = '<div class="alert alert-danger">Error loading form: ' + data.error + '</div>';
			}
		})
		.catch(error => {
			documentFormContainer.innerHTML = '<div class="alert alert-danger">Error loading form: ' + error.message + '</div>';
		});
	});
});
</script>

</body>
</html>