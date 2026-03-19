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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client Dashboard</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/home_client.css?v=1.1" />
</head>
<body>

<?php include_once '../../layouts/sidebar_client.php'; ?>

<div class="main-content">
  <?php include_once '../../layouts/header_client.php'; ?>

  <div class="container mt-4 px-4">
    <h2 class="page-title">Dashboard</h2>

    <!-- SUMMARY CARDS -->
    <div class="row g-4">

      <!-- Total Properties -->
      <div class="col-md-3 col-sm-6">
        <div class="dashboard-card">
          <div class="card-title">
            <i class="bi bi-house-fill card-icon"></i>
            Total Properties
          </div>
          <div class="card-value">3</div>
        </div>
      </div>

      <!-- Issued Certificates -->
      <div class="col-md-3 col-sm-6">
        <div class="dashboard-card">
          <div class="card-title">
            <i class="bi bi-file-earmark-text card-icon"></i>
            Issued Certificates
          </div>
          <div class="card-value">5</div>
        </div>
      </div>

      <!-- Available Documents -->
      <div class="col-md-3 col-sm-6">
        <div class="dashboard-card">
          <div class="card-title">
            <i class="bi bi-folder-fill card-icon"></i>
            Available Documents
          </div>
          <div class="card-value">8</div>
        </div>
      </div>

      <!-- Last Record Update -->
      <div class="col-md-3 col-sm-6">
        <div class="dashboard-card">
          <div class="card-title">
            <i class="bi bi-clock-history card-icon"></i>
            Last Record Update
          </div>
          <div class="card-value">March 2026</div>
        </div>
      </div>

    </div>

    <!-- RECENT ACTIVITIES -->
    <div class="recent-activities mt-4">
      <div class="dashboard-card p-3">
        <h3 class="section-header">Recent Activities</h3>
        <hr class="mb-3">

        <div class="table-responsive">
          <table class="table table-striped mb-0">
            <thead>
              <tr>
                <th>Date</th>
                <th>Activity</th>
                <th>Property</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Mar 15, 2026</td>
                <td>Viewed Property Record</td>
                <td>Lot 88PT</td>
              </tr>
              <tr>
                <td>Mar 12, 2026</td>
                <td>Viewed Tax Declaration</td>
                <td>Lot 5272</td>
              </tr>
              <tr>
                <td>Mar 10, 2026</td>
                <td>Viewed Certificate</td>
                <td>Lot 530</td>
              </tr>
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>