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
  <link rel="stylesheet" href="../../assets/css/user_management.css?v=1.1">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">User Management</h2>

      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <div class="form-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by Role</option>
              <option>Admin</option>
              <option>Staff</option>
            </select>
            <select class="form-select filter-input">
              <option value="">Filter by Status</option>
              <option>Active</option>
              <option>Inactive</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text"><i class="bi bi-search"></i></span>
              <input type="text" class="form-control search-input" placeholder="Search user...">
            </div>
          </div>
        </div>

        <!-- USER TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>User ID</th>
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
                <td>Harlene Soriano</td>
                <td>harlene@example.com</td>
                <td>Admin</td>
                <td>Active</td>
                <td>March 14, 2026 09:00 AM</td>
              </tr>
              <tr>
                <td>USR-002</td>
                <td>Ma. Lalaine Mallannao</td>
                <td>lalaine@example.com</td>
                <td>Staff</td>
                <td>Active</td>
                <td>March 13, 2026 02:15 PM</td>
              </tr>
              <tr>
                <td>USR-003</td>
                <td>Maria Santos</td>
                <td>maria@example.com</td>
                <td>Staff</td>
                <td>Inactive</td>
                <td>March 10, 2026 11:30 AM</td>
              </tr>
              <tr>
                <td>USR-004</td>
                <td>Pedro Reyes</td>
                <td>pedro@example.com</td>
                <td>Staff</td>
                <td>Active</td>
                <td>March 12, 2026 09:45 AM</td>
              </tr>
              <tr>
                <td>USR-005</td>
                <td>Ana Lopez</td>
                <td>ana@example.com</td>
                <td>Admin</td>
                <td>Active</td>
                <td>March 11, 2026 04:00 PM</td>
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