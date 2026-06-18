<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';

date_default_timezone_set('Asia/Manila');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['restore_land'])) {
        $property_id = intval($_POST['property_id']);
        $sql = "UPDATE tbl_properties SET is_deleted = 0, deleted_by = NULL, deleted_at = NULL WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $stmt->close();
        header("Location: recently_deleted.php");
        exit;
    } elseif (isset($_POST['permanent_delete_land'])) {
        $property_id = intval($_POST['property_id']);
        $sql = "DELETE FROM tbl_properties WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $stmt->close();
        header("Location: recently_deleted.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Recently Deleted</title>
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
      <h2 class="page-title mb-3">Recently Deleted</h2>

      <div class="form-container">
        <div class="form-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by Type</option>
              <option>Land Record</option>
              <option>Document</option>
              <option>User Account</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Search deleted records...">
            </div>
          </div>
        </div>

        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>Record Type</th>
                <th>Record Name</th>
                <th>Deleted By</th>
                <th>Date Deleted</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $sql = "SELECT p.*, u.fullname AS deleted_by_name 
                      FROM tbl_properties p
                      LEFT JOIN tbl_users u ON p.deleted_by = u.user_id
                      WHERE p.is_deleted = 1
                      ORDER BY p.deleted_at DESC";
              $result = $conn->query($sql);

              if ($result && $result->num_rows > 0) {
                  while ($row = $result->fetch_assoc()) {
                      $deleted_at = date('M d, Y h:i A', strtotime($row['deleted_at']));
                      echo "<tr>
                        <td>Land Record</td>
                        <td>Lot: " . htmlspecialchars($row['lot_number']) . "</td>
                        <td>" . htmlspecialchars($row['deleted_by_name'] ?? 'Unknown') . "</td>
                        <td>" . $deleted_at . "</td>
                        <td>
                          <form method='POST' class='d-inline'>
                            <input type='hidden' name='property_id' value='" . $row['property_id'] . "'>
                            <button type='submit' name='restore_land' class='btn btn-sm btn-success me-1' title='Restore'>
                              <i class='bi bi-arrow-counterclockwise'></i> Restore
                            </button>
                            <button type='submit' name='permanent_delete_land' class='btn btn-sm btn-danger' title='Permanently Delete' onclick='return confirm(\"Are you sure you want to permanently delete this record? This action cannot be undone.\");'>
                              <i class='bi bi-trash'></i> Delete
                            </button>
                          </form>
                        </td>
                      </tr>";
                  }
              } else {
                  echo "<tr><td colspan='5' class='text-center text-muted py-2'>No recently deleted records found.</td></tr>";
              }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
