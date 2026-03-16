<?php
session_start();
require_once '../../includes/auth.php';

date_default_timezone_set('Asia/Manila');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Property Information</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/forms.css?v=1.0">
</head>
<body>
  <?php include_once '../../layouts/sidebar_client.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Property Information</h2>
      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <div class="form-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by Property Type</option>
              <option>Residential</option>
              <option>Commercial</option>
              <option>Agricultural</option>
            </select>
            <select class="form-select filter-input">
              <option value="">Filter by Status</option>
              <option>Active</option>
              <option>Inactive</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Search property...">
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>Property ID</th>
                <th>Type</th>
                <th>Location</th>
                <th>Size</th>
                <th>Assessed Value</th>
                <th>Documents</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>PR-001</td>
                <td>Residential</td>
                <td>Brgy. San Pedro, City A</td>
                <td>250 sqm</td>
                <td>₱120,000.00</td>
                <td>
                  <i class="bi bi-file-earmark-text me-1"></i> Tax Declaration
                  <i class="bi bi-file-earmark-text ms-2"></i> Ownership Certificate
                </td>
                <td>Active</td>
              </tr>
              <tr>
                <td>PR-002</td>
                <td>Commercial</td>
                <td>Brgy. San Juan, City B</td>
                <td>500 sqm</td>
                <td>₱350,000.00</td>
                <td>
                  <i class="bi bi-file-earmark-text me-1"></i> Tax Declaration
                  <i class="bi bi-file-earmark-text ms-2"></i> Ownership Certificate
                </td>
                <td>Active</td>
              </tr>
              <tr>
                <td>PR-003</td>
                <td>Agricultural</td>
                <td>Brgy. Sta. Maria, City C</td>
                <td>1,200 sqm</td>
                <td>₱200,000.00</td>
                <td>
                  <i class="bi bi-file-earmark-text me-1"></i> Tax Declaration
                  <i class="bi bi-file-earmark-text ms-2"></i> Ownership Certificate
                </td>
                <td>Inactive</td>
              </tr>
              <!-- Add more properties here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>