<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$totalLandOwners = 0;
$totalRegisteredLands = 0;
$transactionsToday = 0;
$monthLabels = [];
$emptyMonthlyData = [];

for ($month = 1; $month <= 12; $month++) {
  $monthLabels[] = date('M', mktime(0, 0, 0, $month, 1));
  $emptyMonthlyData[] = 0;
}

$ownersResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_users WHERE role = 'client'");
if ($ownersResult) {
  $totalLandOwners = (int) $ownersResult->fetch_assoc()['total'];
}

$landsResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_properties");
if ($landsResult) {
  $totalRegisteredLands = (int) $landsResult->fetch_assoc()['total'];
}

$transactionsResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_audit_trial WHERE DATE(created_at) = CURDATE()");
if ($transactionsResult) {
  $transactionsToday = (int) $transactionsResult->fetch_assoc()['total'];
}

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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/dashboard.css?v=2.2" />
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
              <div class="card-value"><?php echo number_format($totalLandOwners); ?></div>
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
              <div class="card-value"><?php echo number_format($totalRegisteredLands); ?></div>
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
              <div class="card-value"><?php echo number_format($transactionsToday); ?></div>
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
              <div class="card-value">&#8369;0</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Monthly Payment Transactions -->
      <div class="dashboard-card chart-card mt-4">
        <h3 class="section-header">Monthly Payment Transactions</h3>
        <div class="chart-wrap">
          <canvas id="monthlyPaymentChart"></canvas>
        </div>
      </div>

      <div class="row g-4 mt-1">
        <div class="col-lg-6">
          <div class="dashboard-card chart-card">
            <h3 class="section-header">Land Type Distribution</h3>
            <div class="chart-wrap">
              <canvas id="landTypeChart"></canvas>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="dashboard-card chart-card">
            <h3 class="section-header">Monthly Tax Collection</h3>
            <div class="chart-wrap">
              <canvas id="monthlyTaxChart"></canvas>
            </div>
          </div>
        </div>
      </div>

      <!-- Land Registration Trend -->
      <div class="dashboard-card chart-card mt-4 mb-4">
        <h3 class="section-header">Land Registration Trend</h3>
        <div class="chart-wrap">
          <canvas id="landRegistrationChart"></canvas>
        </div>
      </div>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const monthLabels = <?php echo json_encode($monthLabels); ?>;
  const emptyMonthlyData = <?php echo json_encode($emptyMonthlyData); ?>;

  const chartGridColor = "rgba(0, 0, 0, 0.06)";
  const chartTextColor = "#666";

  function makeLineChart(canvasId, label, borderColor) {
    new Chart(document.getElementById(canvasId), {
      type: "line",
      data: {
        labels: monthLabels,
        datasets: [{
          label: label,
          data: emptyMonthlyData,
          borderColor: borderColor,
          backgroundColor: "rgba(82, 176, 40, 0.12)",
          tension: 0.35,
          fill: true,
          pointRadius: 3
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: {
            ticks: { color: chartTextColor },
            grid: { color: chartGridColor }
          },
          y: {
            beginAtZero: true,
            ticks: { color: chartTextColor, precision: 0 },
            grid: { color: chartGridColor }
          }
        }
      }
    });
  }

  makeLineChart("monthlyPaymentChart", "Payments", "#52B028");
  makeLineChart("landRegistrationChart", "Registrations", "#2F80ED");

  new Chart(document.getElementById("landTypeChart"), {
    type: "pie",
    data: {
      labels: ["Agricultural", "Residential", "Commercial"],
      datasets: [{
        data: [0, 0, 0],
        backgroundColor: ["#52B028", "#2F80ED", "#F2C94C"],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: "bottom",
          labels: { color: chartTextColor }
        }
      }
    }
  });

  new Chart(document.getElementById("monthlyTaxChart"), {
    type: "bar",
    data: {
      labels: monthLabels,
      datasets: [{
        label: "Tax Collection",
        data: emptyMonthlyData,
        backgroundColor: "#52B028",
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: {
          ticks: { color: chartTextColor },
          grid: { display: false }
        },
        y: {
          beginAtZero: true,
          ticks: { color: chartTextColor, precision: 0 },
          grid: { color: chartGridColor }
        }
      }
    }
  });
</script>
</body>
</html>
