<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/audit_logger.php';

date_default_timezone_set('Asia/Manila');

if (!isset($_SESSION['delete_error'])) {
    $_SESSION['delete_error'] = '';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['restore_land'])) {
        $property_id = intval($_POST['property_id']);
        $restored_lot_number = '';
        $landStmt = $conn->prepare("SELECT lot_number FROM tbl_properties WHERE property_id = ?");
        $landStmt->bind_param("i", $property_id);
        $landStmt->execute();
        $landStmt->bind_result($restored_lot_number);
        $landStmt->fetch();
        $landStmt->close();

        $sql = "UPDATE tbl_properties SET is_deleted = 0, deleted_by = NULL, deleted_at = NULL WHERE property_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $property_id);
        $stmt->execute();
        $stmt->close();

        logAuditTrail(
            $conn,
            "Restored land record",
            "Restored lot {$restored_lot_number} from Recently Deleted."
        );

        header("Location: recently_deleted.php");
        exit;
    } elseif (isset($_POST['permanent_delete_land'])) {
        $property_id = intval($_POST['property_id']);
        $admin_password = trim($_POST['admin_password'] ?? '');
        $user_id = $_SESSION['user_id'] ?? 0;

        $stmt = $conn->prepare("SELECT password FROM tbl_users WHERE user_id = ? AND role = 'admin'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->bind_result($db_password);

        if ($stmt->fetch() && $admin_password === $db_password) {
            $stmt->close();
            $deleted_lot_number = '';
            $landStmt = $conn->prepare("SELECT lot_number FROM tbl_properties WHERE property_id = ?");
            $landStmt->bind_param("i", $property_id);
            $landStmt->execute();
            $landStmt->bind_result($deleted_lot_number);
            $landStmt->fetch();
            $landStmt->close();

            $sql = "DELETE FROM tbl_properties WHERE property_id = ?";
            $del_stmt = $conn->prepare($sql);
            $del_stmt->bind_param("i", $property_id);
            $del_stmt->execute();
            $del_stmt->close();

            logAuditTrail(
                $conn,
                "Permanently deleted land record",
                "Permanently removed lot {$deleted_lot_number} from the system."
            );

            $_SESSION['delete_success'] = 'permanent_land_deleted';
        } else {
            $stmt->close();
            $_SESSION['delete_error'] = 'Incorrect admin password.';
        }

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
  <link rel="stylesheet" href="../../assets/css/recently_deleted.css?v=1.1">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Recently Deleted</h2>

      <?php if (!empty($_SESSION['delete_error'])): ?>
        <div class="alert alert-danger">
          <?php echo htmlspecialchars($_SESSION['delete_error']); ?>
        </div>
        <?php unset($_SESSION['delete_error']); ?>
      <?php endif; ?>

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
                            <button type='button' class='btn btn-sm btn-danger' title='Permanently Delete' data-bs-toggle='modal' data-bs-target='#permanentDeleteLandModal' onclick='document.getElementById(\"permanent_delete_property_id\").value=\"" . $row['property_id'] . "\";'>
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

<!-- SUCCESS MODAL -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title visually-hidden" id="successModalLabel">
                    Land Deleted
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body text-center px-4 pb-4">
                <div class="success-check-icon mx-auto mb-3">
                    <i class="bi bi-check-circle-fill"></i>
                </div>

                <h5 class="mb-2">Land record deleted successfully</h5>
                <p class="text-muted mb-0">
                    The land information has been moved to Recently Deleted.
                </p>
            </div>
        </div>
    </div>
</div>

  <!-- PERMANENT DELETE LAND MODAL -->
  <div class="modal fade" id="permanentDeleteLandModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST">
          <div class="modal-header">
            <h5 class="modal-title text-danger">Confirm Permanent Deletion</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="permanent_delete_land" value="1">
            <input type="hidden" name="property_id" id="permanent_delete_property_id">
            <p>Are you sure you want to permanently delete this record? This cannot be undone.</p>
            <div class="mb-3">
              <label class="form-label">Enter Admin Password to Confirm</label>
              <input type="password" name="admin_password" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Delete Permanently</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <?php if (isset($_SESSION['delete_success']) && $_SESSION['delete_success'] === 'permanent_land_deleted'): ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var successModal = new bootstrap.Modal(document.getElementById('successModal'));
      successModal.show();

      setTimeout(function () {
        successModal.hide();
      }, 3000);
    });
  </script>
  <?php unset($_SESSION['delete_success']); endif; ?>
</body>
</html>
