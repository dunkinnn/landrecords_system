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
  <title>User Management</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/user_management.css?v=1.2">
</head>

<body>

<?php include_once '../../layouts/sidebar_admin.php'; ?>

<div class="main-content">
<?php include_once '../../layouts/header.php'; ?>

<div class="container mt-4 px-4">

  <!-- ================= TITLE ================= -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="page-title mb-0">User Management</h2>
  </div>

  <div class="form-container">

    <!-- ================= FILTERS + SEARCH + ADD USER ================= -->
    <div class="form-controls d-flex justify-content-between align-items-center flex-wrap gap-2">

      <!-- FILTERS (LEFT SIDE) -->
      <div class="filter-group d-flex gap-2">

        <select class="form-select filter-input">
          <option value="">Filter by Role</option>
          <option>Admin</option>
          <option>Client</option>
        </select>

        <select class="form-select filter-input">
          <option value="">Filter by Status</option>
          <option>Active</option>
          <option>Inactive</option>
        </select>

      </div>

      <!-- SEARCH + ADD USER (RIGHT SIDE) -->
      <div class="d-flex align-items-center gap-2">

        <!-- SEARCH -->
        <div class="input-group search-box">
          <span class="input-group-text">
            <i class="bi bi-search"></i>
          </span>
          <input type="text" class="form-control search-input" placeholder="Search user...">
        </div>

        <!-- ADD USER BUTTON -->
        <button class="btn btn-success add-btn" data-bs-toggle="modal" data-bs-target="#addUserModal">
          <i class="bi bi-person-plus"></i> Add User
        </button>

      </div>

    </div>

    <!-- ================= USER TABLE ================= -->
    <div class="table-responsive mt-3">

      <table class="table table-striped form-table">

        <thead>
          <tr>
            <th>User ID</th>
            <th>Land Owner ID</th>
            <th>Username</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Last Login</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>USR-001</td>
            <td>LO-1001</td>
            <td>admin_harlene</td>
            <td>Harlene Soriano</td>
            <td>harlene@example.com</td>
            <td>Admin</td>
            <td>Active</td>
            <td>March 14, 2026 09:00 AM</td>
          </tr>

          <tr>
            <td>USR-002</td>
            <td>LO-1002</td>
            <td>lalaine_1002</td>
            <td>Ma. Lalaine Mallannao</td>
            <td>lalaine@example.com</td>
            <td>Client</td>
            <td>Active</td>
            <td>March 13, 2026 02:15 PM</td>
          </tr>

          <tr>
            <td>USR-003</td>
            <td>LO-1003</td>
            <td>maria_1003</td>
            <td>Maria Santos</td>
            <td>maria@example.com</td>
            <td>Client</td>
            <td>Inactive</td>
            <td>March 10, 2026 11:30 AM</td>
          </tr>
        </tbody>

      </table>

    </div>

  </div>

</div>
</div>

<!-- ================= ADD USER MODAL ================= -->
<div class="modal fade" id="addUserModal" tabindex="-1">

  <div class="modal-dialog modal-lg modal-dialog-centered">

    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Create User Account</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <div class="row g-2">

          <div class="col-md-6">
            <input type="text" class="form-control" placeholder="Full Name">
          </div>

          <div class="col-md-6">
            <input type="email" class="form-control" placeholder="Email">
          </div>

          <div class="col-md-6 mt-2">
            <input type="text" class="form-control" placeholder="Username">
          </div>

          <div class="col-md-6 mt-2">
            <input type="password" class="form-control" placeholder="Temporary Password">
          </div>

          <div class="col-md-6 mt-2">
            <select class="form-select">
              <option value="Client">Client</option>
              <option value="Admin">Admin</option>
            </select>
          </div>

          <div class="col-md-6 mt-2">
            <select class="form-select">
              <option value="Active">Active</option>
              <option value="Inactive">Inactive</option>
            </select>
          </div>

        </div>

        <div class="mt-3 text-muted small">
          System will automatically generate a unique Land Owner ID (e.g. LO-1006)
        </div>

      </div>

      <div class="modal-footer">

        <button class="btn btn-secondary" data-bs-dismiss="modal">
          Cancel
        </button>

        <button class="btn btn-primary">
          Save User
        </button>

      </div>

    </div>

  </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>