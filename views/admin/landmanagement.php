<?php
require_once '../../includes/auth.php';
require_once '../../includes/form_template_loader.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$currentMonthName = date('F');
$currentYear = date('Y');
$selectedBarangay = trim($_GET['barangay'] ?? '');
$selectedLandType = trim($_GET['land_type'] ?? '');
$searchTerm = trim($_GET['search'] ?? '');

/**
 * Combined property listing across all FAAS form types.
 * Only Building exists today. To add Land / Machinery / Other
 * Improvements later, append one more "UNION ALL SELECT ..." block
 * below with the same column order/aliases, joining its own table
 * the same way the Building block does.
 */
$sql = "SELECT
            p.id AS property_id,
            p.pin,
            p.arp_no,
            p.owner_name,
            p.barangay,
            b.lot_number,
            b.total_floor_area AS area,
            b.back_actual_use AS land_type,
            b.back_total_assessed_value AS assessed_value,
            'Building' AS form_type,
            b.id AS form_id
        FROM tbl_properties p
        INNER JOIN tbl_faas_building b ON b.property_id = p.id
        WHERE 1=1";

$params = [];
$types = '';

if ($selectedBarangay !== '') {
    $sql .= " AND p.barangay = ?";
    $types .= 's';
    $params[] = $selectedBarangay;
}
if ($selectedLandType !== '') {
    $sql .= " AND b.back_actual_use = ?";
    $types .= 's';
    $params[] = $selectedLandType;
}
if ($searchTerm !== '') {
    $sql .= " AND (b.lot_number LIKE ? OR p.owner_name LIKE ?)";
    $types .= 'ss';
    $like = '%' . $searchTerm . '%';
    $params[] = $like;
    $params[] = $like;
}

$sql .= " ORDER BY p.updated_at DESC";

$stmt = $conn->prepare($sql);
if ($types !== '') {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$landRecords = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
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

						<!-- Aligned to the same 5 options as Building's Actual Use -->
						<select class="form-select filter-input" name="land_type">
							<option value="" disabled selected>Land Type</option>
							<option value="Residential" <?php echo $selectedLandType === 'Residential' ? 'selected' : ''; ?>>Residential</option>
							<option value="Commercial" <?php echo $selectedLandType === 'Commercial' ? 'selected' : ''; ?>>Commercial</option>
							<option value="Industrial" <?php echo $selectedLandType === 'Industrial' ? 'selected' : ''; ?>>Industrial</option>
							<option value="Agricultural" <?php echo $selectedLandType === 'Agricultural' ? 'selected' : ''; ?>>Agricultural</option>
							<option value="Special" <?php echo $selectedLandType === 'Special' ? 'selected' : ''; ?>>Special</option>
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
					<button type="button" class="btn add-btn btn-primary" id="openAddLandModalBtn">
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
							<th>Actions</th>
						</tr>

					</thead>

					<tbody>

						<?php if (empty($landRecords)): ?>
						<tr>
							<td colspan="10" class="text-center text-muted py-2">No land records found.</td>
						</tr>
						<?php else: foreach ($landRecords as $row): ?>
						<tr>
							<td><?php echo htmlspecialchars($row['pin']); ?></td>
							<td><?php echo htmlspecialchars($row['lot_number']); ?></td>
							<td><?php echo htmlspecialchars($row['owner_name']); ?></td>
							<td><?php echo htmlspecialchars($row['barangay']); ?></td>
							<td><?php echo htmlspecialchars($row['land_type']); ?></td>
							<td><?php echo $row['area'] !== null ? number_format($row['area'], 2) : '-'; ?></td>
							<td><?php echo htmlspecialchars($row['arp_no']); ?></td>
							<td><?php echo $row['assessed_value'] !== null ? '₱' . number_format($row['assessed_value'], 2) : '-'; ?></td>
							<td><span class="text-muted">-</span></td>
							<td>
								<!-- Opens the same Add/Edit modal, pre-loaded with this record.
								     Only Building's document type is mapped today; extend
								     docTypeMap in the script below as Land/Machinery/Other
								     Improvements get their own templates. -->
								<button type="button" class="btn btn-sm btn-outline-primary faas-edit-btn"
										data-id="<?php echo htmlspecialchars($row['form_id']); ?>"
										data-form-type="<?php echo htmlspecialchars($row['form_type']); ?>">
									<i class="bi bi-pencil"></i>
								</button>
							</td>
						</tr>
						<?php endforeach; endif; ?>

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
					<h5 class="modal-title" id="addLandModalTitle">Add Land</h5>
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
				
			</form>
		</div>
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
	const documentTypeSelect = document.getElementById("documentTypeSelect");
	const documentFormContainer = document.getElementById("documentFormContainer");
	const addLandModalEl = document.getElementById("addLandModal");
	const addLandModalTitle = document.getElementById("addLandModalTitle");
	const addLandModal = new bootstrap.Modal(addLandModalEl);

	// Maps a row's form_type (from the database) to the document_type
	// value get_form_template.php expects. Only Building exists today;
	// add Land/Machinery/Other Improvements here as their templates
	// are built.
	const docTypeMap = {
		Building: "24-FAAS-BUILDING"
	};

	function loadDocumentForm(selectedType) {
		if (!selectedType) {
			documentFormContainer.innerHTML = '<div class="text-muted">Select a document type to begin.</div>';
			return;
		}

		documentFormContainer.innerHTML = '<div class="text-muted"><i class="bi bi-hourglass-split"></i> Loading form...</div>';

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

				// Scripts injected via innerHTML don't execute.
				// Re-create them as fresh <script> elements so the browser runs them.
				documentFormContainer.querySelectorAll('script').forEach(function (oldScript) {
					var newScript = document.createElement('script');
					if (oldScript.src) {
						newScript.src = oldScript.src;
					} else {
						newScript.textContent = oldScript.textContent;
					}
					oldScript.parentNode.replaceChild(newScript, oldScript);
				});
			} else {
				documentFormContainer.innerHTML = '<div class="alert alert-danger">Error loading form: ' + data.error + '</div>';
			}
		})
		.catch(error => {
			documentFormContainer.innerHTML = '<div class="alert alert-danger">Error loading form: ' + error.message + '</div>';
		});
	}

	documentTypeSelect.addEventListener("change", function () {
		loadDocumentForm(this.value);
	});

	// Add Land: blank form, no record to load.
	document.getElementById("openAddLandModalBtn").addEventListener("click", function () {
		window.faasPreloadId = null;
		addLandModalTitle.textContent = "Add Land";
		documentTypeSelect.value = "";
		documentFormContainer.innerHTML = '<div class="text-muted">Select a document type to begin.</div>';
		addLandModal.show();
	});

	// Edit: same modal, same document type flow, but pre-loads the
	// clicked row's record once the form fragment's script runs.
	document.querySelectorAll(".faas-edit-btn").forEach(function (btn) {
		btn.addEventListener("click", function () {
			var docType = docTypeMap[this.dataset.formType];
			if (!docType) {
				alert("Editing this record type is not supported yet.");
				return;
			}
			window.faasPreloadId = this.dataset.id;
			addLandModalTitle.textContent = "Edit Land";
			documentTypeSelect.value = docType;
			loadDocumentForm(docType);
			addLandModal.show();
		});
	});
});
</script>

</body>
</html>