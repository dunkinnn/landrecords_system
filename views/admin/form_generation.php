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
  <title>Automated Form Generation</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/forms.css?v=1.0">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Automated Form Generation</h2>
      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <div class="form-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by Form Type</option>
              <option>Land Sheet</option>
              <option>Building/Improvements Sheet</option>
              <option>Machinery Sheet</option>
              <option>Certification</option>
            </select>
            <select class="form-select filter-input">
              <option value="">Filter by Year</option>
              <option>2026</option>
              <option>2025</option>
              <option>2024</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Search form...">
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>Form ID</th>
                <th>Form Name</th>
                <th>Description</th>
                <th>Last Generated</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <!-- Real Property Appraisal - Land -->
              <tr>
                <td>FRM-001</td>
                <td>Real Property Field Appraisal Sheet – Land</td>
                <td>Printable assessment sheet for land properties</td>
                <td>March 10, 2026</td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="generate_land_sheet.php"><i class="bi bi-file-earmark-text me-2"></i> Generate</a></li>
                      <li><a class="dropdown-item" href="preview_land_sheet.php"><i class="bi bi-eye me-2"></i> Preview</a></li>
                      <li><a class="dropdown-item" href="print_land_sheet.php" target="_blank"><i class="bi bi-printer me-2"></i> Print</a></li>
                    </ul>
                  </div>
                </td>
              </tr>

              <!-- Real Property Appraisal - Building/Improvements -->
              <tr>
                <td>FRM-002</td>
                <td>Real Property Field Appraisal Sheet – Building/Improvements</td>
                <td>Printable assessment sheet for buildings and other improvements</td>
                <td>March 12, 2026</td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="generate_building_sheet.php"><i class="bi bi-file-earmark-text me-2"></i> Generate</a></li>
                      <li><a class="dropdown-item" href="preview_building_sheet.php"><i class="bi bi-eye me-2"></i> Preview</a></li>
                      <li><a class="dropdown-item" href="print_building_sheet.php" target="_blank"><i class="bi bi-printer me-2"></i> Print</a></li>
                    </ul>
                  </div>
                </td>
              </tr>

              <!-- Real Property Appraisal - Machinery -->
              <tr>
                <td>FRM-003</td>
                <td>Real Property Field Appraisal Sheet – Machinery</td>
                <td>Printable assessment sheet for machinery</td>
                <td>March 8, 2026</td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="generate_machinery_sheet.php"><i class="bi bi-file-earmark-text me-2"></i> Generate</a></li>
                      <li><a class="dropdown-item" href="preview_machinery_sheet.php"><i class="bi bi-eye me-2"></i> Preview</a></li>
                      <li><a class="dropdown-item" href="print_machinery_sheet.php" target="_blank"><i class="bi bi-printer me-2"></i> Print</a></li>
                    </ul>
                  </div>
                </td>
              </tr>

              <!-- Certification No Improvements -->
              <tr>
                <td>FRM-004</td>
                <td>Certification (No Improvements)</td>
                <td>Official certification for properties without improvements</td>
                <td>March 9, 2026</td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="generate_cert_no_improvement.php"><i class="bi bi-file-earmark-text me-2"></i> Generate</a></li>
                      <li><a class="dropdown-item" href="preview_cert_no_improvement.php"><i class="bi bi-eye me-2"></i> Preview</a></li>
                      <li><a class="dropdown-item" href="print_cert_no_improvement.php" target="_blank"><i class="bi bi-printer me-2"></i> Print</a></li>
                    </ul>
                  </div>
                </td>
              </tr>

              <!-- Certification With Improvements -->
              <tr>
                <td>FRM-005</td>
                <td>Certification (With Improvements)</td>
                <td>Official certification for properties with improvements</td>
                <td>March 9, 2026</td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li><a class="dropdown-item" href="generate_cert_with_improvement.php"><i class="bi bi-file-earmark-text me-2"></i> Generate</a></li>
                      <li><a class="dropdown-item" href="preview_cert_with_improvement.php"><i class="bi bi-eye me-2"></i> Preview</a></li>
                      <li><a class="dropdown-item" href="print_cert_with_improvement.php" target="_blank"><i class="bi bi-printer me-2"></i> Print</a></li>
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