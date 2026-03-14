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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Documents & Images</title>
  <link rel="icon" type="image/png" href="../../assets/img/logo.png">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link rel="stylesheet" href="../../assets/css/layout.css">
  <link rel="stylesheet" href="../../assets/css/document.css?v=1.1">
</head>
<body>
  <?php include_once '../../layouts/sidebar_admin.php'; ?>
  <div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>
    <div class="container mt-4 px-4">
      <h2 class="page-title mb-3">Documents & Images</h2>
      <div class="document-container">
        <!-- CONTROLS -->
        <div class="document-controls">
          <div class="filter-group">
            <select class="form-select filter-input">
              <option value="">Filter by Document Type</option>
              <option>Tax Declaration</option>
              <option>Deed of Sale</option>
              <option>Survey Plan</option>
              <option>Land Title</option>
              <option>Property Image</option>
            </select>
            <select class="form-select filter-input">
              <option value="">Filter by Barangay</option>
              <option>Poblacion</option>
              <option>San Jose</option>
              <option>San Roque</option>
            </select>
          </div>
          <div class="search-group">
            <div class="input-group search-box">
              <span class="input-group-text">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control search-input" placeholder="Search property or owner...">
            </div>
            <button class="btn btn-success add-btn">
              <i class="bi bi-upload"></i> Upload Document
            </button>
          </div>
        </div>
        <!-- TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped document-table">
            <thead>
              <tr>
                <th>Document ID</th>
                <th>Property ID</th>
                <th>Owner</th>
                <th>Document Type</th>
                <th>File Name</th>
                <th>Uploaded Date</th>
                <th>Status</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>DOC-001</td>
                <td>PR-001</td>
                <td>Juan Dela Cruz</td>
                <td>Tax Declaration</td>
                <td>tax_declaration_1023.pdf</td>
                <td>March 12, 2026</td>
                <td>
                  <span class="badge bg-success">Active</span>
                </td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-eye me-2"></i> Preview
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-download me-2"></i> Download
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-pencil me-2"></i> Edit Info
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item text-danger" href="#">
                          <i class="bi bi-trash me-2"></i> Delete
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
              </tr>
              <tr>
                <td>DOC-002</td>
                <td>PR-002</td>
                <td>Maria Santos</td>
                <td>Survey Plan</td>
                <td>survey_plan_lot45.jpg</td>
                <td>March 10, 2026</td>
                <td>
                  <span class="badge bg-success">Stored</span>
                </td>
                <td class="text-center">
                  <div class="dropdown position-static">
                    <button class="btn btn-sm btn-link text-dark" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-eye me-2"></i> Preview
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-download me-2"></i> Download
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="#">
                          <i class="bi bi-pencil me-2"></i> Edit Info
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item text-danger" href="#">
                          <i class="bi bi-trash me-2"></i> Delete
                        </a>
                      </li>
                    </ul>
                  </div>
                </td>
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