<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar d-flex flex-column" id="sidebar">
  
  <button id="closeSidebar" class="close-sidebar-btn d-lg-none">&times;</button>

  <!-- ===== HEADER ===== -->
  <div class="sidebar-header px-3 py-3">
    <div class="d-flex align-items-center">
      <img src="../../assets/img/logo.png" alt="Logo" class="system-logo me-2">
      <div class="system-title">
        LAND RECORDS<br>
        MANAGEMENT SYSTEM
      </div>
    </div>
    <hr class="sidebar-divider">
  </div>

  <!-- ===== NAVIGATION ===== -->
  <nav class="nav flex-column w-100 px-2 flex-grow-1">

    <a href="dashboard.php" class="nav-link d-flex align-items-center <?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
      <img src="../../assets/img/icons/dashboard.png" class="sidebar-icon me-3">
      <span>Dashboard</span>
    </a>

    <a href="property_info.php" class="nav-link d-flex align-items-center <?= $current_page === 'property_info.php' ? 'active' : '' ?>">
      <img src="../../assets/img/icons/land.png" class="sidebar-icon me-3">
      <span>Property Information</span>
    </a>

    <a href="form_generation_client.php" class="nav-link d-flex align-items-center <?= $current_page === 'form_generation_client.php' ? 'active' : '' ?>">
        <img src="../../assets/img/icons/google-forms.png" class="sidebar-icon me-3">
        <span>Form Generation</span>
    </a>

  </nav>

  <!-- ===== FOOTER (LOGOUT AT BOTTOM) ===== -->
  <div class="sidebar-footer px-2 pb-3">
    <hr class="footer-divider">

    <a href="../../auth/logout.php" class="nav-link d-flex align-items-center logout-btn">
      <img src="../../assets/img/icons/log-out.png" class="sidebar-icon me-3">
      <span>Logout</span>
    </a>
  </div>

</div>
<!-- Loading Spinner -->
<div id="loadingSpinner" style="
  display: none;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1060;
  justify-content: center;
  align-items: center;
">
  <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>

<!-- Overlay -->
<div id="overlay" class="overlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const closeBtn = document.getElementById('closeSidebar');
  const toggleBtn = document.getElementById('sidebarToggle');
  const navbar = document.querySelector('.navbar');
  const body = document.body;

  // Sidebar toggle
  toggleBtn?.addEventListener('click', () => {
    sidebar.classList.add('show');
    overlay.classList.add('active');
    navbar.classList.add('overlay-active');
  });

  function closeSidebar() {
    sidebar.classList.remove('show');
    overlay.classList.remove('active');
    navbar.classList.remove('overlay-active');
  }

  closeBtn?.addEventListener('click', closeSidebar);
  overlay.addEventListener('click', closeSidebar);

  // Modals
  body.addEventListener('show.bs.modal', () => {
    overlay.classList.remove('active');
    navbar.classList.remove('overlay-active');
    navbar.classList.add('behind-modal');
  });

  body.addEventListener('hidden.bs.modal', () => {
    navbar.classList.remove('behind-modal');
    if (document.querySelectorAll('.modal.show').length === 0 && sidebar.classList.contains('show')) {
      overlay.classList.add('active');
      navbar.classList.add('overlay-active');
    }
  });

  // Logout spinner
  const logoutBtn = document.querySelector('.logout-btn');
  const loadingSpinner = document.getElementById('loadingSpinner');
  logoutBtn?.addEventListener('click', function(e) {
    e.preventDefault();
    loadingSpinner.style.display = 'flex';
    setTimeout(() => {
      window.location.href = this.getAttribute('href');
    }, 1000);
  });
});
</script>

<style>

/* ===== SIDEBAR CONTAINER ===== */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  width: 270px;
  height: 100vh;
  background: #ffffff;
  border-right: 1px solid rgba(0,0,0,0.08);
  transform: translateX(-100%);
  transition: transform 0.3s ease;
  z-index: 1050;
  overflow-y: auto;
}

.sidebar.show { 
  transform: translateX(0); 
}

/* ===== HEADER SECTION ===== */
.sidebar-header {
  padding: 20px 15px;
  background: #ffffff;
}

.system-logo {
  width: 50px;
  height: 50px;
  object-fit: contain;
}

.system-title {
  font-size: 13px;
  font-weight: 700;
  color: #52B028;
  line-height: 1.2;
  letter-spacing: 0.4px;
}

.sidebar-divider {
  margin: 15px -15px 0 -15px;  /* pulls line to full width */
  border-top: 2px solid rgba(0,0,0,0.15);
}
/* Make navigation take remaining height */
.sidebar nav {
  flex-grow: 1;
}

/* Footer sticks to bottom */
.sidebar-footer {
  margin-top: auto;
}

/* Divider above logout */
.footer-divider {
  margin: 15px 0 15px 0;
  border-top: 2px solid rgba(0,0,0,0.15);
}
/* ===== NAVIGATION LINKS ===== */
/* DEFAULT LINK */
.nav-link {
  color: #52B028;
  margin: 4px 10px;
  border-radius: 8px;
  padding: 10px 12px;
  font-weight: 500;
  transition: all 0.2s ease;
}

/* ACTIVE (TAPPED) STATE */
.nav-link.active {
  background-color: #52B028;   /* green background */
  color: #ffffff;              /* white text */
  font-weight: 600;
}

/* MAKE ICON WHITE WHEN ACTIVE */
.nav-link.active .sidebar-icon {
  filter: brightness(0) invert(1);
}

.nav-link:hover {
  background-color: #88d465;
  color: #ffffff;
}

.nav-link:hover .sidebar-icon {
  filter: brightness(0) invert(1);
}

/* ===== LOGOUT BUTTON ===== */
.logout-btn {
  color: #52B028;
}

.logout-btn:hover {
  background-color: #6bbc45;
  color: #ffffff;
}

/* ===== ICONS ===== */
.sidebar-icon { 
  width: 22px; 
  height: 22px; 
}

/* ===== CLOSE BUTTON (MOBILE) ===== */
.close-sidebar-btn { 
  position: absolute; 
  top: 10px; 
  right: 15px; 
  font-size: 1.8rem; 
  background: none; 
  border: none; 
  color: #333; 
  cursor: pointer; 
}

/* ===== OVERLAY ===== */
.overlay { 
  position: fixed; 
  top: 0; 
  left: 0; 
  width: 100%; 
  height: 100%; 
  background: rgba(0,0,0,0.4); 
  opacity: 0; 
  visibility: hidden; 
  transition: opacity 0.3s ease; 
  z-index: 1040; 
  pointer-events: none; 
}

.overlay.active { 
  opacity: 1; 
  visibility: visible; 
  pointer-events: auto; 
}

/* ===== NAVBAR EFFECTS ===== */
.navbar.overlay-active { 
  background-color: rgba(255,255,255,0.7); 
  backdrop-filter: blur(4px); 
}

.navbar.behind-modal { 
  z-index: 1020; 
}

/* ===== DESKTOP MODE ===== */
@media (min-width: 769px) {
  .sidebar { 
    transform: translateX(0); 
  }

  #sidebarToggle { 
    display: none; 
  }

  .overlay { 
    display: none; 
  }
}

</style>