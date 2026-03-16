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

// Example unread notifications count
$unreadCount = 3; // change this with DB query later
?>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<!-- Header HTML -->
<nav class="navbar">

  <button id="sidebarToggle" class="btn d-lg-none me-3" aria-label="Toggle Sidebar">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="header-user-info">

    <!-- Notification Bell -->
    <div class="notification-wrapper" onclick="toggleNotifications()">
      <img src="../../assets/img/icons/bell.png" class="notification-icon" alt="Notifications">
      <?php if($unreadCount > 0): ?>
        <span class="notification-badge"><?= $unreadCount ?></span>
      <?php endif; ?>
    </div>

    <!-- Profile Dropdown -->
    <div class="profile-dropdown">

      <div class="profile-info" onclick="toggleProfileMenu()">

        <div class="profile-wrapper">
          <img src="<?= htmlspecialchars($profilePic) ?>" alt="Profile" class="profile-pic">

          <div class="dropdown-circle">
            <i class="fa fa-chevron-down"></i>
          </div>
        </div>
      </div>

      <div id="profileMenu" class="dropdown-menu">

        <div class="dropdown-user">
          <img src="<?= htmlspecialchars($profilePic) ?>" class="dropdown-pic">
          <div>
            <div class="dropdown-name"><?= htmlspecialchars($fullname) ?></div>
            <div class="dropdown-role"><?= htmlspecialchars($role) ?></div>
          </div>
        </div>

        <hr>
        <a href="../profile/profile.php">
          <img src="../../assets/img/icons/user.png" class="menu-icon"> Profile
        </a>
        <a href="../settings/settings.php">
          <img src="../../assets/img/icons/settings.png" class="menu-icon"> Settings
        </a>
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
}

#sidebarToggle {
  background: none;
  border: none;
  cursor: pointer;
}

.header-user-info {
  display: flex;
  align-items: center;
  margin-left: auto;
}

/* Notification */
.notification-wrapper {
  position: relative;
  margin-right: 15px;
  cursor: pointer;
}

.notification-icon {
  width: 28px;
  height: 28px;
  object-fit: contain;
}

.notification-badge {
  position: absolute;
  top: -5px;
  right: -5px;
  background: #e74c3c;
  color: white;
  font-size: 12px;
  font-weight: 600;
  width: 18px;
  height: 18px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  pointer-events: none;
}

/* Profile Dropdown */
.profile-dropdown{
  position: relative;
}

.profile-wrapper{
  position: relative;
  display: inline-block;
  cursor: pointer;
}

.profile-pic {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #52B028;
}

.dropdown-circle{
  position: absolute;
  bottom: -2px;
  right: -2px;
  width: 20px;
  height: 20px;
  background: #3a3b3c;
  border-radius: 50%;
  border: 2px solid #fff;
  display: flex;
  align-items: center;
  justify-content: center;
}

.dropdown-circle i{
  font-size: 10px;
  color: white;
}

/* Dropdown menu */
.dropdown-menu{
  position: absolute;
  right: 0;
  top: 60px;
  width: 230px;
  background: white;
  border-radius: 10px;
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  padding: 10px 0;
  display: none;
  z-index: 999;
}

.dropdown-menu a{
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 18px;
  text-decoration: none;
  color: #333;
  font-size: 14px;
}

.menu-icon{
  font-weight: 700;
  width: 18px;
  height: 18px;
  object-fit: contain;
}

.dropdown-menu a:hover{
  background: #f3f4f6;
}

.dropdown-menu hr{
  border: none;
  border-top: 1px solid #eee;
  margin: 6px 0;
}

/* Dropdown profile header */
.dropdown-user{
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
}

.dropdown-pic{
  width: 45px;
  height: 45px;
  border-radius: 50%;
}

.dropdown-name{
  font-weight: 600;
}

.dropdown-role{
  font-size: 12px;
  color: #777;
}

</style>

<!-- Dropdown Script -->
<script>
function toggleProfileMenu(){
  const menu = document.getElementById("profileMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}

window.onclick = function(event){
  if(!event.target.closest('.profile-dropdown')){
    document.getElementById("profileMenu").style.display = "none";
  }
}
</script>