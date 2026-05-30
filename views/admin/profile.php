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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Client Dashboard</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/profile_client.css?v=1.1" />
</head>
<body>

<!-- Sidebar & Header -->
<?php include_once '../../layouts/sidebar_admin.php'; ?>
<div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>

  <div class="container mt-4 px-4">
    <h2>Profile</h2>

    <div class="row">

  <!-- PROFILE CARD -->
  <div class="col-md-4">
    <div class="profile-card text-center">

      <img src="../../assets/img/profile.png" class="profile-avatar">

      <h4 class="mt-3">Harlene Soriano</h4>
      <p class="text-muted">Admin</p>

      <button class="btn btn-success btn-sm mt-2">
        <i class="bi bi-pencil"></i> Edit Profile
      </button>

    </div>
  </div>


  <!-- DETAILS -->
  <div class="col-md-8">
    <div class="profile-details">

      <h5 class="mb-3">Personal Information</h5>

      <div class="row profile-info">
        <div class="col-md-6">
          <label>Full Name</label>
          <p>Ma Lallaine Mallanao</p>
        </div>

        <div class="col-md-6">
          <label>Email</label>
          <p>lallaine@email.com</p>
        </div>

        <div class="col-md-6">
          <label>Contact Number</label>
          <p>09123456789</p>
        </div>

        <div class="col-md-6">
          <label>Gender</label>
          <p>Female</p>
        </div>

        <div class="col-md-6">
          <label>Date of Birth</label>
          <p>January 15, 1990</p>
        </div>

        <div class="col-md-6">
          <label>Address</label>
          <p>San Pablo, Isabela</p>
        </div>

      </div>


      <hr>

      <h5 class="mb-3">Account Information</h5>

      <div class="row profile-info">

        <div class="col-md-6">
          <label>Username</label>
          <p>juanclient01</p>
        </div>

        <div class="col-md-6">
          <label>Account Status</label>
          <p class="text-success">Active</p>
        </div>

        <div class="col-md-6">
          <label>Date Registered</label>
          <p>March 10, 2026</p>
        </div>

        <div class="col-md-6">
          <label>Role</label>
          <p>Client</p>
        </div>

      </div>

    </div>
  </div>

</div>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>