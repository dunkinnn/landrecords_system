<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$totalLandOwners = 0;
$transactionsToday = 0;
$recentActivities = [];

$ownersResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_users WHERE role = 'client'");
if ($ownersResult) {
  $totalLandOwners = (int) $ownersResult->fetch_assoc()['total'];
}

$transactionsResult = $conn->query("SELECT COUNT(*) AS total FROM tbl_audit_trial WHERE DATE(created_at) = CURDATE()");
if ($transactionsResult) {
  $transactionsToday = (int) $transactionsResult->fetch_assoc()['total'];
}

$activitiesResult = $conn->query(
  "SELECT username, action, created_at
   FROM tbl_audit_trial
   ORDER BY created_at DESC
   LIMIT 10"
);
if ($activitiesResult) {
  while ($row = $activitiesResult->fetch_assoc()) {
    $recentActivities[] = $row;
  }
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
              <div class="card-value">0</div>
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
                <?php if (!empty($recentActivities)): ?>
                  <?php foreach ($recentActivities as $activity): ?>
                    <tr>
                      <td><?php echo htmlspecialchars(date('M d, Y', strtotime($activity['created_at']))); ?></td>
                      <td><?php echo htmlspecialchars($activity['action']); ?></td>
                      <td><?php echo htmlspecialchars(ucfirst($activity['username'] ?? 'System')); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="3" class="text-center text-muted py-2">No recent activities found.</td>
                  </tr>
                <?php endif; ?>
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
