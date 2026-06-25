<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/audit_logger.php';

date_default_timezone_set('Asia/Manila');

$profileMessage = "";
$profileSuccessMessage = "";
$userId = $_SESSION['user_id'] ?? 0;
$profilePic = $_SESSION['profile_pic'] ?? '../../assets/img/profile.png';
$fullname = $_SESSION['fullname'] ?? 'Harlene Soriano';
$role = $_SESSION['role'] ?? 'Admin';
$email = $_SESSION['email'] ?? 'admin@email.com';
$username = $_SESSION['username'] ?? 'admin01';
$phone = '09123456789';
$status = 'active';
$currentPasswordHash = '';

if ($userId) {
  $stmt = $conn->prepare("SELECT fullname, username, email, phone, password, role, status FROM tbl_users WHERE user_id = ? LIMIT 1");
  $stmt->bind_param("i", $userId);
  $stmt->execute();
  $stmt->bind_result($dbFullname, $dbUsername, $dbEmail, $dbPhone, $dbPassword, $dbRole, $dbStatus);

  if ($stmt->fetch()) {
    $fullname = $dbFullname ?: $fullname;
    $username = $dbUsername ?: $username;
    $email = $dbEmail ?: $email;
    $phone = $dbPhone ?: $phone;
    $currentPasswordHash = $dbPassword ?? '';
    $role = $dbRole ?: $role;
    $status = $dbStatus ?: $status;
  }

  $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_profile_details"])) {
  $newFullname = trim($_POST["fullname"] ?? "");
  $newUsername = trim($_POST["username"] ?? "");
  $newEmail = trim($_POST["email"] ?? "");
  $newPhone = trim($_POST["phone"] ?? "");

  if (!$userId) {
    $_SESSION["profile_message"] = "<div class='alert alert-danger'>Unable to identify the current user.</div>";
  } elseif ($newFullname === "" || $newUsername === "" || $newEmail === "" || $newPhone === "") {
    $_SESSION["profile_message"] = "<div class='alert alert-warning'>Full name, username, email, and contact number are required.</div>";
  } else {
    $duplicate = $conn->prepare("SELECT user_id FROM tbl_users WHERE (fullname = ? OR username = ? OR email = ?) AND user_id != ? LIMIT 1");
    $duplicate->bind_param("sssi", $newFullname, $newUsername, $newEmail, $userId);
    $duplicate->execute();
    $duplicate->store_result();

    if ($duplicate->num_rows > 0) {
      $_SESSION["profile_message"] = "<div class='alert alert-danger'>Full name, username, or email already exists.</div>";
    } else {
      $changedFields = [];

      if ($newFullname !== $fullname) {
        $changedFields[] = "full name";
      }

      if ($newUsername !== $username) {
        $changedFields[] = "username";
      }

      if ($newEmail !== $email) {
        $changedFields[] = "email";
      }

      if ($newPhone !== $phone) {
        $changedFields[] = "contact number";
      }

      $update = $conn->prepare("UPDATE tbl_users SET fullname = ?, username = ?, email = ?, phone = ? WHERE user_id = ?");
      $update->bind_param("ssssi", $newFullname, $newUsername, $newEmail, $newPhone, $userId);

      if ($update->execute()) {
        $_SESSION["fullname"] = $newFullname;
        $_SESSION["username"] = $newUsername;
        $_SESSION["email"] = $newEmail;
        $_SESSION["profile_success"] = "Profile updated successfully";
        $auditDetails = empty($changedFields)
          ? "Submitted profile update with no field changes."
          : "Updated own profile fields: " . implode(", ", $changedFields) . ".";
        logAuditTrail($conn, "Updated profile", $auditDetails, $userId, $newUsername, $role);
      } else {
        $_SESSION["profile_message"] = "<div class='alert alert-danger'>Unable to update profile. Please try again.</div>";
      }

      $update->close();
    }

    $duplicate->close();
  }

  header("Location: " . $_SERVER["PHP_SELF"]);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["change_password"])) {
  $currentPassword = trim($_POST["current_password"] ?? "");
  $newPassword = trim($_POST["new_password"] ?? "");
  $confirmPassword = trim($_POST["confirm_password"] ?? "");
  $isHashedPassword = password_get_info($currentPasswordHash)["algo"] !== 0;
  $currentPasswordMatches = $isHashedPassword
    ? password_verify($currentPassword, $currentPasswordHash)
    : hash_equals($currentPasswordHash, $currentPassword);

  if (!$userId) {
    $_SESSION["profile_message"] = "<div class='alert alert-danger'>Unable to identify the current user.</div>";
  } elseif ($currentPassword === "" || $newPassword === "" || $confirmPassword === "") {
    $_SESSION["profile_message"] = "<div class='alert alert-warning'>All password fields are required.</div>";
  } elseif (!$currentPasswordMatches) {
    $_SESSION["profile_message"] = "<div class='alert alert-danger'>Current password is incorrect.</div>";
  } elseif ($newPassword !== $confirmPassword) {
    $_SESSION["profile_message"] = "<div class='alert alert-danger'>New password and confirmation do not match.</div>";
  } else {
    $passwordValue = strtolower($role) === "admin" ? $newPassword : password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE tbl_users SET password = ? WHERE user_id = ?");
    $update->bind_param("si", $passwordValue, $userId);

    if ($update->execute()) {
      $_SESSION["profile_success"] = "Password updated successfully";
      logAuditTrail($conn, "Changed password", "Changed own account password.", $userId, $username, $role);
    } else {
      $_SESSION["profile_message"] = "<div class='alert alert-danger'>Unable to change password. Please try again.</div>";
    }

    $update->close();
  }

  header("Location: " . $_SERVER["PHP_SELF"]);
  exit;
}

if (isset($_SESSION["profile_message"])) {
  $profileMessage = $_SESSION["profile_message"];
  unset($_SESSION["profile_message"]);
}

if (isset($_SESSION["profile_success"])) {
  $profileSuccessMessage = $_SESSION["profile_success"];
  unset($_SESSION["profile_success"]);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Profile</title>

  <link rel="icon" type="image/png" href="../../assets/img/logo.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />

  <link rel="stylesheet" href="../../assets/css/layout.css" />
  <link rel="stylesheet" href="../../assets/css/profile_admin.css?v=1.0" />
</head>
<body>

<!-- Sidebar & Header -->
<?php include_once '../../layouts/sidebar_admin.php'; ?>
<div class="main-content">
    <?php include_once '../../layouts/header.php'; ?>

  <div class="container mt-4 px-4 profile-page">
    <div class="profile-heading">
      <div>
        <h2>Profile</h2>

      <?php echo $profileMessage; ?>

        <div class="row g-4 align-items-start">
          <div class="col-lg-4">
            <aside class="profile-card">
              <div class="avatar-ring">
                <img src="<?= htmlspecialchars($profilePic) ?>" class="profile-avatar" alt="Profile photo">
              </div>

              <h4><?= htmlspecialchars($fullname) ?></h4>
              <span class="role-badge"><?= htmlspecialchars(ucfirst($role)) ?></span>

              <div class="profile-quick-info">
                <div>
                  <i class="bi bi-shield-check"></i>
                  <span>System administrator</span>
                </div>
                <div>
                  <i class="bi bi-circle-fill"></i>
                  <span>Active account</span>
                </div>
              </div>

              <button type="button" class="btn btn-success profile-edit-btn" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                <i class="bi bi-pencil-square"></i>
                <span>Edit Profile</span>
              </button>

              <button type="button" class="btn btn-outline-secondary profile-password-btn" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                <i class="bi bi-key"></i>
                <span>Change Password</span>
              </button>
            </aside>
          </div>

          <div class="col-lg-8">
            <section class="profile-details">
              <div class="section-title">
                <i class="bi bi-person-lines-fill"></i>
                <h5>Personal Information</h5>
              </div>

              <div class="row g-3 profile-info">
                <div class="col-md-6">
                  <div class="info-item">
                    <label>Full Name</label>
                    <p><?= htmlspecialchars($fullname) ?></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Email</label>
                    <p><?= htmlspecialchars($email) ?></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Contact Number</label>
                    <p><?= htmlspecialchars($phone) ?></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Gender</label>
                    <p>Female</p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Date of Birth</label>
                    <p>January 15, 1990</p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Address</label>
                    <p>San Pablo, Isabela</p>
                  </div>
                </div>
              </div>

              <hr>

              <div class="section-title">
                <i class="bi bi-lock-fill"></i>
                <h5>Account Information</h5>
              </div>

              <div class="row g-3 profile-info">
                <div class="col-md-6">
                  <div class="info-item">
                    <label>Username</label>
                    <p><?= htmlspecialchars($username) ?></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Account Status</label>
                    <p class="status-active"><?= htmlspecialchars(ucfirst($status)) ?></p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Date Registered</label>
                    <p>March 10, 2026</p>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="info-item">
                    <label>Role</label>
                    <p><?= htmlspecialchars(ucfirst($role)) ?></p>
                  </div>
                </div>
              </div>
            </section>
          </div>
        </div>
  </div>  
</div>
  
<!-- SUCCESS MODAL -->
<div class="modal fade" id="profileSuccessModal" tabindex="-1" aria-labelledby="profileSuccessModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title visually-hidden" id="profileSuccessModalLabel">Profile Updated</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center px-4 pb-4">
        <div class="success-check-icon mx-auto mb-3">
          <i class="bi bi-check-circle-fill"></i>
        </div>
        <h5 class="mb-2"><?= htmlspecialchars($profileSuccessMessage ?: "Changes saved") ?></h5>
        <p class="text-muted mb-0">Your account changes are now saved.</p>
      </div>
    </div>
  </div>
</div>

<!-- EDIT PROFILE MODAL -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered add-user-dialog">
    <div class="modal-content">
      <form action="" method="POST" id="editProfileForm">
        <input type="hidden" name="update_profile_details" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="profileFullName" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="profileFullName" name="fullname" value="<?= htmlspecialchars($fullname) ?>" required>
          </div>
          <div class="mb-3">
            <label for="profileEmail" class="form-label">Email</label>
            <input type="email" class="form-control" id="profileEmail" name="email" value="<?= htmlspecialchars($email) ?>" required>
          </div>
          <div class="mb-3">
            <label for="profilePhone" class="form-label">Contact Number</label>
            <input type="tel" class="form-control" id="profilePhone" name="phone" value="<?= htmlspecialchars($phone) ?>" required>
          </div>
          <div class="mb-3">
            <label for="profileUsername" class="form-label">Username</label>
            <input type="text" class="form-control" id="profileUsername" name="username" value="<?= htmlspecialchars($username) ?>" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary save-profile-btn">
            <span class="save-profile-text">Save Changes</span>
            <span class="save-profile-loading d-none">
              <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
              Saving...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- CHANGE PASSWORD MODAL -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered add-user-dialog">
    <div class="modal-content">
      <form action="" method="POST" id="changePasswordForm">
        <input type="hidden" name="change_password" value="1">
        <div class="modal-header">
          <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="currentPassword" class="form-label">Current Password</label>
            <input type="password" class="form-control" id="currentPassword" name="current_password" autocomplete="current-password" required>
          </div>
          <div class="mb-3">
            <label for="newPassword" class="form-label">New Password</label>
            <input type="password" class="form-control" id="newPassword" name="new_password" autocomplete="new-password" required>
          </div>
          <div class="mb-0">
            <label for="confirmPassword" class="form-label">Confirm New Password</label>
            <input type="password" class="form-control" id="confirmPassword" name="confirm_password" autocomplete="new-password" required>
            <div class="invalid-feedback">New password and confirmation do not match.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary save-password-btn">
            <span class="save-password-text">Update Password</span>
            <span class="save-password-loading d-none">
              <span class="spinner-border spinner-border-sm me-2" aria-hidden="true"></span>
              Saving...
            </span>
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
  const editProfileForm = document.getElementById("editProfileForm");
  const changePasswordForm = document.getElementById("changePasswordForm");
  const newPasswordInput = document.getElementById("newPassword");
  const confirmPasswordInput = document.getElementById("confirmPassword");

  function showButtonLoading(buttonSelector, textSelector, loadingSelector) {
    const button = document.querySelector(buttonSelector);
    const text = document.querySelector(textSelector);
    const loading = document.querySelector(loadingSelector);

    button.disabled = true;
    text.classList.add("d-none");
    loading.classList.remove("d-none");
  }

  function validatePasswords() {
    const hasMismatch = confirmPasswordInput.value !== "" && newPasswordInput.value !== confirmPasswordInput.value;
    confirmPasswordInput.classList.toggle("is-invalid", hasMismatch);
    return !hasMismatch;
  }

  editProfileForm.addEventListener("submit", function (event) {
    event.preventDefault();
    showButtonLoading(".save-profile-btn", ".save-profile-text", ".save-profile-loading");

    setTimeout(() => {
      editProfileForm.submit();
    }, 700);
  });

  newPasswordInput.addEventListener("input", validatePasswords);
  confirmPasswordInput.addEventListener("input", validatePasswords);

  changePasswordForm.addEventListener("submit", function (event) {
    if (!validatePasswords()) {
      event.preventDefault();
      return;
    }

    event.preventDefault();
    showButtonLoading(".save-password-btn", ".save-password-text", ".save-password-loading");

    setTimeout(() => {
      changePasswordForm.submit();
    }, 700);
  });
</script>
<?php if ($profileSuccessMessage !== ""): ?>
  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const successModal = new bootstrap.Modal(document.getElementById("profileSuccessModal"));
      successModal.show();

      setTimeout(function () {
        successModal.hide();
      }, 3000);
    });
  </script>
<?php endif; ?>
</body>
</html>
