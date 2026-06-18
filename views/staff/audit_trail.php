<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$selectedRole = trim($_GET['role'] ?? '');
$selectedMonth = (int) ($_GET['month'] ?? 0);
$searchTerm = trim($_GET['search'] ?? '');
$searchLike = '%' . $searchTerm . '%';

$auditRecords = [];
$stmt = $conn->prepare(
  "SELECT username, role, action, details, created_at
   FROM tbl_audit_trial
   WHERE (? = '' OR role = ?)
     AND (? = 0 OR MONTH(created_at) = ?)
     AND (? = '' OR username LIKE ? OR role LIKE ? OR action LIKE ? OR details LIKE ?)
   ORDER BY created_at DESC
   LIMIT 200"
);

if ($stmt) {
  $stmt->bind_param(
    "ssiisssss",
    $selectedRole,
    $selectedRole,
    $selectedMonth,
    $selectedMonth,
    $searchTerm,
    $searchLike,
    $searchLike,
    $searchLike,
    $searchLike
  );
  $stmt->execute();
  $result = $stmt->get_result();

  while ($row = $result->fetch_assoc()) {
    $auditRecords[] = $row;
  }

  $stmt->close();
}
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
  <link rel="stylesheet" href="../../assets/css/audit_trail.css?v=1.3">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Audit Trail</h2>
      
      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <form class="form-controls" method="GET" id="auditFilterForm">
          <div class="filter-group">
            <select class="form-select filter-input" name="role">
              <option value="">Filter by User</option>
              <option value="admin" <?php echo $selectedRole === 'admin' ? 'selected' : ''; ?>>Admin</option>
              <option value="staff" <?php echo $selectedRole === 'staff' ? 'selected' : ''; ?>>Staff</option>
              <option value="client" <?php echo $selectedRole === 'client' ? 'selected' : ''; ?>>Client</option>
            </select>
            <select class="form-select filter-input" name="month">
              <option value="">Filter by Month</option>
              <?php for ($month = 1; $month <= 12; $month++): ?>
                <option value="<?php echo $month; ?>" <?php echo $selectedMonth === $month ? 'selected' : ''; ?>>
                  <?php echo date('F', mktime(0, 0, 0, $month, 1)); ?>
                </option>
              <?php endfor; ?>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" name="search" value="<?php echo htmlspecialchars($searchTerm); ?>" placeholder="Search audit...">
            </div>
          </div>
        </form>

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
              <?php if (!empty($auditRecords)): ?>
                <?php foreach ($auditRecords as $record): ?>
                  <tr>
                    <td><?php echo htmlspecialchars(ucfirst($record['username'] ?? 'System')); ?></td>
                    <td>
                      <?php echo htmlspecialchars($record['action']); ?>
                      <?php if (!empty($record['details'])): ?>
                        <div class="text-muted small"><?php echo htmlspecialchars($record['details']); ?></div>
                      <?php endif; ?>
                    </td>
                    <td><?php echo htmlspecialchars(date('F d, Y h:i A', strtotime($record['created_at']))); ?></td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
              <tr>
                <td colspan="3" class="text-center text-muted py-2">No audit records found.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const auditFilterForm = document.getElementById("auditFilterForm");
    const auditFilterInputs = auditFilterForm.querySelectorAll("select");
    const auditSearchInput = auditFilterForm.querySelector(".search-input");
    let auditSearchTimer;

    auditFilterInputs.forEach(function (input) {
      input.addEventListener("change", function () {
        auditFilterForm.submit();
      });
    });

    auditSearchInput.addEventListener("input", function () {
      clearTimeout(auditSearchTimer);
      auditSearchTimer = setTimeout(function () {
        auditFilterForm.submit();
      }, 600);
    });
  </script>
</body>
</html>
