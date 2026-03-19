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
  <title>Audit Trail</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/audit_trail.css?v=1.2">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Audit Trail</h2>
      
      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <div class="form-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by User</option>
              <option>Admin</option>
              <option>Staff</option>
            </select>
            <select class="form-select filter-input">
              <option value="">Filter by Month</option>
              <option>January</option>
              <option>February</option>
              <option>March</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Search audit...">
            </div>
          </div>
        </div>

        <!-- TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>User</th>
                <th>Action</th>
                <th>Date & Time</th>
              </tr>
          </thead>
            <tbody>

              <tr>
                <td>Admin</td>
                <td>Generated Tax Declaration Form</td>
                <td>March 10, 2026 09:15 AM</td>
              </tr>

              <tr>
                <td>Client</td>
                <td>Viewed Property Assessment Form</td>
                <td>March 11, 2026 01:20 PM</td>
              </tr>

              <tr>
                <td>Admin</td>
                <td>Deleted Old Document</td>
                <td>March 12, 2026 03:45 PM</td>
              </tr>

              <tr>
                <td>Client</td>
                <td>Edited Land Ownership Certificate</td>
                <td>March 13, 2026 10:30 AM</td>
              </tr>

              <tr>
                <td>Admin</td>
                <td>Generated Assessment Report</td>
                <td>March 14, 2026 11:10 AM</td>
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