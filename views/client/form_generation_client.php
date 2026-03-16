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
    <title>Certificates / Forms</title>

    <link rel="icon" type="image/png" href="../../assets/img/logo.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <link rel="stylesheet" href="../../assets/css/layout.css">
    <link rel="stylesheet" href="../../assets/css/forms_client.css">
</head>

<body>

<?php include_once '../../layouts/sidebar_client.php'; ?>

<div class="main-content">

    <?php include_once '../../layouts/header.php'; ?>

    <div class="container mt-4 px-4">

        <h2 class="page-title mb-3">Certificates / Forms</h2>

        <div class="form-container">

            <!-- FILTER + SEARCH -->
            <div class="form-controls">

                <div class="filter-group">
                    <select class="form-select filter-input">
                        <option value="">Filter by Form Type</option>
                        <option>Land Appraisal Sheet</option>
                        <option>Building Appraisal Sheet</option>
                        <option>Machinery Sheet</option>
                        <option>Certification</option>
                    </select>

                    <select class="form-select filter-input">
                        <option value="">Filter by Year</option>
                        <option>2026</option>
                        <option>2025</option>
                        <option>2024</option>
                    </select>
                </div>

                <div class="search-group">
                    <div class="input-group search-box">
                        <span class="input-group-text">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control search-input" placeholder="Search certificate...">
                    </div>
                </div>

            </div>

            <!-- TABLE -->
            <div class="table-responsive mt-3">

                <table class="table table-striped form-table">

                    <thead>
                        <tr>
                            <th>Certificate ID</th>
                            <th>Form Name</th>
                            <th>Description</th>
                            <th>Date Issued</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>CERT-001</td>
                            <td>Real Property Field Appraisal Sheet – Land</td>
                            <td>Assessment sheet for land property</td>
                            <td>March 10, 2026</td>

                            <td class="text-center">

                                <div class="dropdown position-static">

                                    <button class="btn btn-sm btn-link text-dark" data-bs-toggle="dropdown">
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
                                            <a class="dropdown-item" href="#" target="_blank">
                                                <i class="bi bi-printer me-2"></i> Print
                                            </a>
                                        </li>

                                    </ul>

                                </div>

                            </td>
                        </tr>

                        <tr>
                            <td>CERT-002</td>
                            <td>Certification (No Improvements)</td>
                            <td>Official certification for property without improvements</td>
                            <td>March 12, 2026</td>

                            <td class="text-center">

                                <div class="dropdown position-static">

                                    <button class="btn btn-sm btn-link text-dark" data-bs-toggle="dropdown">
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
                                            <a class="dropdown-item" href="#" target="_blank">
                                                <i class="bi bi-printer me-2"></i> Print
                                            </a>
                                        </li>

                                    </ul>

                                </div>

                            </td>
                        </tr>

                        <tr>
                            <td>CERT-003</td>
                            <td>Certification (With Improvements)</td>
                            <td>Official certification for property with improvements</td>
                            <td>March 9, 2026</td>

                            <td class="text-center">

                                <div class="dropdown position-static">

                                    <button class="btn btn-sm btn-link text-dark" data-bs-toggle="dropdown">
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
                                            <a class="dropdown-item" href="#" target="_blank">
                                                <i class="bi bi-printer me-2"></i> Print
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