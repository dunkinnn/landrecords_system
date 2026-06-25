<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

$clientUserId = (int)($_SESSION['user_id'] ?? 0);

$clientProperties = [];
$totalPropertiesOwned = 0;
$activeProperties = 0;
$barangaysCovered = 0;

$propertySql = "SELECT property_id, lot_number, barangay, land_type, status, boundary_coordinates
                FROM tbl_properties
                WHERE owner_id = ? AND is_deleted = 0
                ORDER BY property_id DESC";
$propertyStmt = $conn->prepare($propertySql);

if ($propertyStmt) {
    $propertyStmt->bind_param("i", $clientUserId);
    $propertyStmt->execute();
    $propertyResult = $propertyStmt->get_result();

    $barangaySet = [];
    while ($row = $propertyResult->fetch_assoc()) {
        $clientProperties[] = $row;

        if (strcasecmp($row['status'] ?? '', 'Active') === 0) {
            $activeProperties++;
        }

        $barangay = trim($row['barangay'] ?? '');
        if ($barangay !== '') {
            $barangaySet[$barangay] = true;
        }
    }

    $propertyStmt->close();

    $totalPropertiesOwned = count($clientProperties);
    $barangaysCovered = count($barangaySet);
}
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
  <link rel="stylesheet" href="../../assets/css/home_client.css?v=1.0" />
</head>
<body>

<!-- Sidebar & Header -->
<?php include_once '../../layouts/sidebar_client.php'; ?>
<div class="main-content">
  <?php include_once '../../layouts/header_client.php'; ?>
  <div class="container-fluid mt-4 px-4">
    <h2>Dashboard</h2>

    <div class="row g-4">
      <div class="col-12">
        <div class="row g-4">
          <div class="col-md-4 col-sm-6">
            <div class="dashboard-card">
              <div class="card-title">
                <i class="bi bi-house-fill card-icon"></i>
                Properties Owned
              </div>
              <div class="card-value"><?php echo number_format($totalPropertiesOwned); ?></div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="dashboard-card">
              <div class="card-title">
                <i class="bi bi-check2-circle card-icon"></i>
                Active Properties
              </div>
              <div class="card-value"><?php echo number_format($activeProperties); ?></div>
            </div>
          </div>

          <div class="col-md-4 col-sm-6">
            <div class="dashboard-card">
              <div class="card-title">
                <i class="bi bi-geo-alt-fill card-icon"></i>
                Barangays Covered
              </div>
              <div class="card-value"><?php echo number_format($barangaysCovered); ?></div>
            </div>
          </div>
        </div>

        <div class="dashboard-card p-3 mt-4">
          <h3 class="section-header">Recent Property Records</h3>
          <hr class="mb-3">
          <div class="table-responsive">
            <table class="table table-striped mb-0">
              <thead>
                <tr>
                  <th>Lot Number</th>
                  <th>Barangay</th>
                  <th>Land Type</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($clientProperties)): ?>
                  <?php foreach (array_slice($clientProperties, 0, 5) as $property): ?>
                    <tr>
                      <td><?php echo htmlspecialchars($property['lot_number'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($property['barangay'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($property['land_type'] ?? ''); ?></td>
                      <td><?php echo htmlspecialchars($property['status'] ?? ''); ?></td>
                    </tr>
                  <?php endforeach; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted py-3">No property records found.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
