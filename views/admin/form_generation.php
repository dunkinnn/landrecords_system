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
          <div class="controls-left">
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
          </div>

          <div class="controls-right">
            <div class="search-group">
              <div class="input-group search-box">
                <span class="input-group-text">
                  <i class="bi bi-search"></i>
                </span>
                <input type="text" class="form-control search-input" placeholder="Search form...">
              </div>
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
              <tr>
                <td colspan="5" class="text-center text-muted py-2">No generated forms found.</td>
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
