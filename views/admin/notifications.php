<?php
session_start();
require_once '../../includes/auth.php';

date_default_timezone_set('Asia/Manila');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notifications</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/notifications.css?v=1.0" />
</head>

<body>
<?php include_once '../../layouts/sidebar_admin.php'; ?>
<div class="main-content">
  <?php include_once '../../layouts/header_client.php'; ?>

  <div class="container mt-4 px-4">
    <h2>Notifications</h2>

    <!-- Nested Container for Tabs & List -->
    <div class="notifications-card">
      <!-- Tabs -->
      <div class="notification-tabs">
        <button class="active" onclick="showTab('all')">All</button>
        <button onclick="showTab('new')">New</button>
        <button onclick="showTab('unread')">Unread</button>
      </div>

      <!-- Notification Lists -->
      <div id="all" class="tab-content">
        <div class="notification-item unread">
          <div class="notif-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="notif-details">
            <div class="notif-message">Your <strong>Land Appraisal Sheet</strong> is now available for download.</div>
            <div class="notif-time">March 15, 2026 • 10:30 AM</div>
          </div>
        </div>

        <div class="notification-item">
          <div class="notif-icon"><i class="bi bi-house"></i></div>
          <div class="notif-details">
            <div class="notif-message">Your property record for <strong>Lot 5272</strong> has been updated by the assessor.</div>
            <div class="notif-time">March 12, 2026 • 3:15 PM</div>
          </div>
        </div>

        <div class="notification-item">
          <div class="notif-icon"><i class="bi bi-folder-check"></i></div>
          <div class="notif-details">
            <div class="notif-message">Your uploaded <strong>Tax Declaration</strong> has been successfully verified.</div>
            <div class="notif-time">March 10, 2026 • 9:00 AM</div>
          </div>
        </div>
      </div>

      <div id="new" class="tab-content" style="display:none;">
        <div class="notification-item unread">
          <div class="notif-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="notif-details">
            <div class="notif-message">Your <strong>Land Appraisal Sheet</strong> is now available for download.</div>
            <div class="notif-time">March 15, 2026 • 10:30 AM</div>
          </div>
        </div>
      </div>

      <div id="unread" class="tab-content" style="display:none;">
        <div class="notification-item unread">
          <div class="notif-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="notif-details">
            <div class="notif-message">Your <strong>Land Appraisal Sheet</strong> is now available for download.</div>
            <div class="notif-time">March 15, 2026 • 10:30 AM</div>
          </div>
        </div>
      </div>
    </div> <!-- End notifications-card -->

  </div>
</div>

<script>
function showTab(tabId){
    document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
    document.querySelectorAll('.notification-tabs button').forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabId).style.display = 'block';
    event.currentTarget.classList.add('active');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>