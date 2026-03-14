<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Database connection
require_once '../../includes/db.php';

// Get session data with fallback
$profilePic = $_SESSION['profile_pic'] ?? '../../assets/img/profile.png';
$fullname   = $_SESSION['fullname'] ?? 'No Fullname Set';
$role       = $_SESSION['role'] ?? 'No Role Set';
$user_id    = $_SESSION['user_id'] ?? 0;

// Notifications DB query disabled to prevent rendering glitches and extra load.
// Keep a default value to avoid undefined variable notices in the template.
$unreadCount = 0;

/*
if ($user_id && isset($conn)) {
  $sqlNotif = "SELECT COUNT(*) AS unread FROM tbl_notifications WHERE user_id = ? AND is_read = 0";
  if ($stmtNotif = $conn->prepare($sqlNotif)) {
    $stmtNotif->bind_param("i", $user_id);
    $stmtNotif->execute();
    $resultNotif = $stmtNotif->get_result();
    $rowNotif = $resultNotif->fetch_assoc();
    $unreadCount = $rowNotif['unread'] ?? 0;
    $stmtNotif->close();
  }
}
*/

?>

<!-- Header HTML -->
<nav class="navbar">
  <button id="sidebarToggle" class="btn d-lg-none me-3" aria-label="Toggle Sidebar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="header-user-info">
    <!-- <?php if ($notifLink): ?>
      <div class="notification-wrapper">
        <a href="<?= htmlspecialchars($notifLink) ?>" aria-label="Notifications">
          <img src="../../assets/img/icons/notification.png" alt="Notifications" class="notification-icon" title="Notifications">
          <?php if ($unreadCount > 0): ?>
            <span class="badge"><?= $unreadCount ?></span>
          <?php endif; ?>
        </a>
      </div>
    <?php endif; ?>
    -->

    <div class="profile-info">
      <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="profile-pic">
      <div class="user-text">
        <div class="user-fullname"><?= htmlspecialchars($fullname) ?></div>
        <div class="user-role"><?= htmlspecialchars($role) ?></div>
      </div>
    </div>
  </div>
</nav>

<!-- Header CSS -->
<style>
.navbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  background-color: #fff;
  padding: 0 1rem;
  height: 70px;
  width: 100%;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  box-sizing: border-box;
}
#sidebarToggle {
  background: none;
  border: none;
  cursor: pointer;
}
.header-user-info {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-left: auto;
}
.notification-wrapper {
  position: relative;
}
.notification-icon {
  width: 24px;
  height: 24px;
  cursor: pointer;
  flex-shrink: 0;
}
.badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background-color: red;
  color: white;
  border-radius: 50%;
  font-size: 10px;
  width: 16px;
  height: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: 1px solid white;
}
.profile-info {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #52B028;
  flex-shrink: 0;
}
.user-text {
  display: flex;
  flex-direction: column;
  justify-content: center;
  white-space: nowrap;
}
.user-fullname {
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.2;
  color: #000;
}
.user-role {
  font-size: 0.85rem;
  color: #6c757d;
  text-transform: capitalize;
  margin-top: 2px;
  margin-left: 20px;
}
@media (max-width: 560px) {
  .profile-info {
    display: none;
  }
}
</style>