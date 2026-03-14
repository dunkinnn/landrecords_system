<?php
session_start();
require_once '../../includes/auth.php';

date_default_timezone_set('Asia/Manila');

$currentMonthName = date('F');
$currentYear = date('Y');
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
</head>

<body>

<?php include_once '../../layouts/sidebar_admin.php'; ?>

<div class="main-content">

	<?php include_once '../../layouts/header.php'; ?>

	<div class="container mt-4 px-4">

		<h2 class="page-title mb-3">Land Information Management</h2>

		<div class="land-container">

			<!-- FILTER, SEARCH & ADD -->
			<div class="land-controls">

				<!-- Filters -->
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

				<!-- Search + Add -->
				<div class="search-group">

					<div class="input-group search-box">
						<span class="input-group-text">
							<i class="bi bi-search"></i>
						</span>
						<input type="text"
							   class="form-control search-input"
							   placeholder="Search lot number or owner...">
					</div>

					<button class="btn btn-success add-btn">
						<i class="bi bi-plus-lg"></i> Add Land
					</button>
				</div>
			</div>

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

							<td>PR-001</td>
							<td>LOT-1023</td>
							<td>Juan Dela Cruz</td>
							<td>Poblacion</td>
							<td>Agricultural</td>
							<td>1200</td>
							<td>TD-45821</td>
							<td>₱100,000</td>

							<td>
								<span class="badge bg-success">Active</span>
							</td>

							<td class="text-center">

								<div class="dropdown position-static">

									<button class="btn btn-sm btn-link text-dark"
											type="button"
											data-bs-toggle="dropdown">

										<i class="bi bi-three-dots-vertical"></i>

									</button>

									<ul class="dropdown-menu dropdown-menu-end shadow">

										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-eye me-2"></i> View Property
											</a>
										</li>

										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-pencil me-2"></i> Edit Property
											</a>
										</li>

										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-map me-2"></i> View Map
											</a>
										</li>

										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-folder me-2"></i> Documents
											</a>
										</li>

										<li>
											<a class="dropdown-item" href="#">
												<i class="bi bi-clock-history me-2"></i> History
											</a>
										</li>

										<li>
											<a class="dropdown-item text-danger" href="#">
												<i class="bi bi-archive me-2"></i> Archive
											</a>
										</li>

									</ul>

								</div>

							</td>

						</tr>

					</tbody>

				</table>

			</div>

		</div>

	</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>