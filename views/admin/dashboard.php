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
  <title>Dashboard</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/dashboard.css?v=2.1" />
</head>
<body>

<?php include_once '../../layouts/sidebar_admin.php'; ?>
<div class="main-content">
  <?php include_once '../../layouts/header.php'; ?>

  <div class="container mt-4 px-4">
    <h2>Dashboard</h2>

      <!-- 4 Cards Row -->
      <div class="row g-4">
        <!-- Total Land Owners -->
        <div class="col-md-3 col-sm-6">
          <div class="dashboard-card">
            <div class="card-title">
              <i class="bi bi-people-fill card-icon"></i>
              Total Land Owners
            </div>
            <div class="d-flex align-items-center mt-2">
              <div class="card-value">2.3K</div>
              <div class="trend-badge text-success ms-2">
                11.3% <i class="bi bi-arrow-up"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Registered Lands -->
        <div class="col-md-3 col-sm-6">
          <div class="dashboard-card">
            <div class="card-title">
              <i class="bi bi-house-fill card-icon"></i>
              Total Registered Lands
            </div>
            <div class="d-flex align-items-center mt-2">
              <div class="card-value">450</div>
              <div class="trend-badge text-success ms-2">
                5.8% <i class="bi bi-arrow-up"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Transactions Today -->
        <div class="col-md-3 col-sm-6">
          <div class="dashboard-card">
            <div class="card-title">
              <i class="bi bi-currency-dollar card-icon"></i>
              Transactions Today
            </div>
            <div class="d-flex align-items-center mt-2">
              <div class="card-value">35</div>
              <div class="trend-badge text-danger ms-2">
                -2.3% <i class="bi bi-arrow-down"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Tax Collected -->
        <div class="col-md-3 col-sm-6">
          <div class="dashboard-card">
            <div class="card-title">
              <i class="bi bi-receipt-cutoff card-icon"></i>
              Total Tax Collected
            </div>
            <div class="d-flex align-items-center mt-2">
              <div class="card-value">₱12,500</div>
              <div class="trend-badge text-success ms-2">
                8.1% <i class="bi bi-arrow-up"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Activities Section -->
      <div class="recent-activities mt-4">
        <div class="dashboard-card p-3">
          <h3 class="section-header">Recent Activities</h3>
          <hr class="mb-3">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th scope="col">Date</th>
                  <th scope="col">Activity</th>
                  <th scope="col">User</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>Feb 27, 2026</td>
                  <td>Added new land record</td>
                  <td>Juan Dela Cruz</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Updated tax assessment</td>
                  <td>Maria Santos</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Deleted old record</td>
                  <td>Carlos Reyes</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Deleted old record</td>
                  <td>Carlos Reyes</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Deleted old record</td>
                  <td>Carlos Reyes</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Deleted old record</td>
                  <td>Carlos Reyes</td>
                </tr>
                <tr>
                  <td>Feb 26, 2026</td>
                  <td>Deleted old record</td>
                  <td>Carlos Reyes</td>
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