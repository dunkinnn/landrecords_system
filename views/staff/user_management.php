<?php
session_start();
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/audit_logger.php';

date_default_timezone_set('Asia/Manila');

function redirectWithUserFlash($key, $value) {
  $_SESSION[$key] = $value;
  header("Location: " . $_SERVER["PHP_SELF"]);
  exit;
}

$message = "";
$successMessage = "";

if ($_SERVER["REQUEST_METHOD"] === "GET" && ($_GET["action"] ?? "") === "check_user") {
  header("Content-Type: application/json");

  $fullname = trim($_GET["fullname"] ?? "");
  $username = trim($_GET["username"] ?? "");
  $response = [
    "fullnameExists" => false,
    "usernameExists" => false
  ];

  if ($fullname !== "") {
    $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE fullname = ? LIMIT 1");
    $stmt->bind_param("s", $fullname);
    $stmt->execute();
    $stmt->store_result();
    $response["fullnameExists"] = $stmt->num_rows > 0;
    $stmt->close();
  }

  if ($username !== "") {
    $stmt = $conn->prepare("SELECT user_id FROM tbl_users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $response["usernameExists"] = $stmt->num_rows > 0;
    $stmt->close();
  }

  echo json_encode($response);
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_user"])) {
  $role = strtolower(trim($_POST["role"] ?? ""));
  $fullname = trim($_POST["fullname"] ?? "");
  $username = trim($_POST["username"] ?? "");
  $phone = trim($_POST["phone"] ?? "");
  $password = trim($_POST["password"] ?? "");

  if (!in_array($role, ["staff", "client"], true)) {
    redirectWithUserFlash("user_management_message", "<div class='alert alert-danger'>Please select a valid user type.</div>");
  } elseif ($fullname === "" || $username === "" || $phone === "" || $password === "") {
    redirectWithUserFlash("user_management_message", "<div class='alert alert-warning'>All fields are required.</div>");
  } else {
    $check = $conn->prepare("SELECT user_id, fullname, username FROM tbl_users WHERE fullname = ? OR username = ? LIMIT 1");
    $check->bind_param("ss", $fullname, $username);
    $check->execute();
    $check->store_result();
    $check->bind_result($existingUserId, $existingFullname, $existingUsername);

    if ($check->num_rows > 0) {
      $check->fetch();
      $errorMessage = strcasecmp($existingUsername, $username) === 0
        ? "Username already exists."
        : "Full name already exists.";
      $check->close();
      redirectWithUserFlash("user_management_message", "<div class='alert alert-danger'>" . $errorMessage . "</div>");
    } else {
      $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO tbl_users (username, fullname, phone, password, role) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("sssss", $username, $fullname, $phone, $hashedPassword, $role);

      if ($stmt->execute()) {
        logAuditTrail($conn, "Added user account", "Created " . ucfirst($role) . " account for " . $fullname . ".");
        $stmt->close();
        $check->close();
        redirectWithUserFlash("user_management_success", "Added account successfully");
      } else {
        $stmt->close();
        $check->close();
        redirectWithUserFlash("user_management_message", "<div class='alert alert-danger'>Unable to add user. Please try again.</div>");
      }
    }
  }
}

if (isset($_SESSION["user_management_message"])) {
  $message = $_SESSION["user_management_message"];
  unset($_SESSION["user_management_message"]);
}

if (isset($_SESSION["user_management_success"])) {
  $successMessage = $_SESSION["user_management_success"];
  unset($_SESSION["user_management_success"]);
}

$users = [];
$usersResult = $conn->query("SELECT user_id, fullname, username, phone, role, status FROM tbl_users ORDER BY user_id DESC");
if ($usersResult) {
  while ($row = $usersResult->fetch_assoc()) {
    $users[] = $row;
  }
}
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
      <?php echo $message; ?>

      <div class="form-container">
        <!-- FILTERS + SEARCH -->
        <div class="form-controls">
          <div class="input-group search-box">
            <span class="input-group-text"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control search-input" id="userSearchInput" placeholder="Search user...">
          </div>
          <button type="button" class="btn btn-primary add-user-btn d-flex align-items-center justify-content-center gap-2 text-nowrap" data-bs-toggle="modal" data-bs-target="#addUserModal">
            <i class="bi bi-person-plus"></i>
            <span>Add User</span>
          </button>
        </div>

        <!-- USER TABLE -->
        <div class="table-responsive mt-3">
          <table class="table table-striped form-table">
            <thead>
              <tr>
                <th>User ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Phone Number</th>
                <th>Role</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (!empty($users)): ?>
                <?php foreach ($users as $user): ?>
                  <tr class="user-row">
                    <td><?php echo htmlspecialchars("USR-" . str_pad($user["user_id"], 3, "0", STR_PAD_LEFT)); ?></td>
                    <td><?php echo htmlspecialchars($user["fullname"] ?? ""); ?></td>
                    <td><?php echo htmlspecialchars($user["username"]); ?></td>
                    <td><?php echo htmlspecialchars($user["phone"]); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($user["role"])); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($user["status"])); ?></td>
                  </tr>
                <?php endforeach; ?>
                <tr class="no-search-results d-none">
                  <td colspan="6" class="text-center text-muted">No matching users found.</td>
                </tr>
              <?php else: ?>
              <tr>
                <td colspan="6" class="text-center text-muted">No users found.</td>
              </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if (!empty($users)): ?>
          <div class="table-footer">
            <div class="entries-info text-muted" id="entriesInfo"></div>
            <nav aria-label="User table pagination">
              <ul class="pagination pagination-sm mb-0" id="userPagination"></ul>
            </nav>
          </div>
        <?php endif; ?>
      </div>

    </div>
  </div>

  <!-- SUCCESS MODAL -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title visually-hidden" id="successModalLabel">User Added</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center px-4 pb-4">
          <div class="success-check-icon mx-auto mb-3">
            <i class="bi bi-check-circle-fill"></i>
          </div>
          <h5 class="mb-2">Added account successfully</h5>
          <p class="text-muted mb-0">The new user account is now saved.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ADD USER MODAL -->
  <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered add-user-dialog">
      <div class="modal-content">
        <form action="" method="POST" id="addUserForm">
          <input type="hidden" name="add_user" value="1">
          <div class="modal-header">
            <h5 class="modal-title" id="addUserModalLabel">Add User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label for="userRole" class="form-label">User Type</label>
              <select class="form-select" id="userRole" name="role" required>
                <option value="" selected disabled>Select user type</option>
                <option value="staff">Staff</option>
                <option value="client">Client</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="fullName" class="form-label">Full Name</label>
              <input type="text" class="form-control" id="fullName" name="fullname" required>
              <div class="invalid-feedback" id="fullNameFeedback">Full name already exists.</div>
            </div>
            <div class="mb-3">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required>
              <div class="invalid-feedback" id="usernameFeedback">Username already exists.</div>
            </div>
            <div class="mb-3">
              <label for="phoneNumber" class="form-label">Phone Number</label>
              <input type="tel" class="form-control" id="phoneNumber" name="phone" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary save-user-btn">
              <span class="save-user-text">Save User</span>
              <span class="save-user-loading d-none">
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
    const addUserForm = document.getElementById("addUserForm");
    const fullNameInput = document.getElementById("fullName");
    const usernameInput = document.getElementById("username");
    const userSearchInput = document.getElementById("userSearchInput");
    const userRows = Array.from(document.querySelectorAll(".user-row"));
    const noSearchResultsRow = document.querySelector(".no-search-results");
    const entriesInfo = document.getElementById("entriesInfo");
    const userPagination = document.getElementById("userPagination");
    const saveButton = addUserForm.querySelector(".save-user-btn");
    const saveText = saveButton.querySelector(".save-user-text");
    const saveLoading = saveButton.querySelector(".save-user-loading");
    const addUserModal = document.getElementById("addUserModal");
    let duplicateCheckTimer;
    let hasDuplicateFullname = false;
    let hasDuplicateUsername = false;
    let isCheckingDuplicate = false;
    let currentPage = 1;
    const rowsPerPage = 10;

    function getFilteredRows() {
      const searchTerm = userSearchInput.value.trim().toLowerCase();

      return userRows.filter(function (row) {
        const rowText = row.textContent.toLowerCase();
        return rowText.includes(searchTerm);
      });
    }

    function renderPagination(totalPages) {
      if (!userPagination) {
        return;
      }

      userPagination.innerHTML = "";

      if (totalPages <= 1) {
        return;
      }

      const paginationItems = [
        { label: "Previous", page: currentPage - 1, disabled: currentPage === 1 }
      ];

      for (let page = 1; page <= totalPages; page++) {
        paginationItems.push({ label: String(page), page: page, active: page === currentPage });
      }

      paginationItems.push({
        label: "Next",
        page: currentPage + 1,
        disabled: currentPage === totalPages
      });

      paginationItems.forEach(function (item) {
        const pageItem = document.createElement("li");
        const pageButton = document.createElement("button");

        pageItem.className = "page-item";
        pageButton.type = "button";
        pageButton.className = "page-link";
        pageButton.textContent = item.label;

        pageItem.classList.toggle("active", Boolean(item.active));
        pageItem.classList.toggle("disabled", Boolean(item.disabled));
        pageButton.disabled = Boolean(item.disabled) || Boolean(item.active);

        pageButton.addEventListener("click", function () {
          currentPage = item.page;
          updateUserTable();
        });

        pageItem.appendChild(pageButton);
        userPagination.appendChild(pageItem);
      });
    }

    function updateEntriesInfo(totalRows, startIndex, endIndex) {
      if (!entriesInfo) {
        return;
      }

      if (totalRows === 0) {
        entriesInfo.textContent = "Showing 0 entries";
        return;
      }

      entriesInfo.textContent = `Showing ${startIndex + 1} to ${endIndex} of ${totalRows} entries`;
    }

    function updateUserTable() {
      const filteredRows = getFilteredRows();
      const totalRows = filteredRows.length;
      const totalPages = Math.max(Math.ceil(totalRows / rowsPerPage), 1);

      if (currentPage > totalPages) {
        currentPage = totalPages;
      }

      const startIndex = (currentPage - 1) * rowsPerPage;
      const endIndex = Math.min(startIndex + rowsPerPage, totalRows);

      userRows.forEach(function (row) {
        row.classList.add("d-none");
      });

      filteredRows.slice(startIndex, endIndex).forEach(function (row) {
        row.classList.remove("d-none");
      });

      if (noSearchResultsRow) {
        noSearchResultsRow.classList.toggle("d-none", totalRows > 0);
      }

      updateEntriesInfo(totalRows, startIndex, endIndex);
      renderPagination(totalPages);
    }

    if (userSearchInput) {
      userSearchInput.addEventListener("input", function () {
        currentPage = 1;
        updateUserTable();
      });
    }

    updateUserTable();

    function setFieldError(input, hasError) {
      input.classList.toggle("is-invalid", hasError);
    }

    function updateSaveButtonState() {
      saveButton.disabled = hasDuplicateFullname || hasDuplicateUsername || isCheckingDuplicate;
    }

    function checkDuplicateUserFields() {
      clearTimeout(duplicateCheckTimer);

      duplicateCheckTimer = setTimeout(function () {
        const fullname = fullNameInput.value.trim();
        const username = usernameInput.value.trim();

        if (fullname === "" && username === "") {
          hasDuplicateFullname = false;
          hasDuplicateUsername = false;
          setFieldError(fullNameInput, false);
          setFieldError(usernameInput, false);
          updateSaveButtonState();
          return;
        }

        isCheckingDuplicate = true;
        updateSaveButtonState();

        const params = new URLSearchParams({
          action: "check_user",
          fullname: fullname,
          username: username
        });

        fetch(`${window.location.pathname}?${params.toString()}`, {
          headers: {
            "Accept": "application/json"
          }
        })
          .then(function (response) {
            return response.json();
          })
          .then(function (data) {
            hasDuplicateFullname = Boolean(data.fullnameExists);
            hasDuplicateUsername = Boolean(data.usernameExists);
            setFieldError(fullNameInput, hasDuplicateFullname);
            setFieldError(usernameInput, hasDuplicateUsername);
          })
          .catch(function () {
            hasDuplicateFullname = false;
            hasDuplicateUsername = false;
            setFieldError(fullNameInput, false);
            setFieldError(usernameInput, false);
          })
          .finally(function () {
            isCheckingDuplicate = false;
            updateSaveButtonState();
          });
      }, 350);
    }

    fullNameInput.addEventListener("input", checkDuplicateUserFields);
    usernameInput.addEventListener("input", checkDuplicateUserFields);

    addUserModal.addEventListener("hidden.bs.modal", function () {
      clearTimeout(duplicateCheckTimer);
      hasDuplicateFullname = false;
      hasDuplicateUsername = false;
      isCheckingDuplicate = false;
      setFieldError(fullNameInput, false);
      setFieldError(usernameInput, false);
      saveButton.disabled = false;
      saveText.classList.remove("d-none");
      saveLoading.classList.add("d-none");
    });

    addUserForm.addEventListener("submit", function (event) {
      if (hasDuplicateFullname || hasDuplicateUsername || isCheckingDuplicate) {
        event.preventDefault();
        updateSaveButtonState();
        return;
      }

      event.preventDefault();

      const form = this;
      saveButton.disabled = true;
      saveText.classList.add("d-none");
      saveLoading.classList.remove("d-none");

      setTimeout(function () {
        form.submit();
      }, 900);
    });
  </script>
  <?php if ($successMessage !== ""): ?>
    <script>
      document.addEventListener("DOMContentLoaded", function () {
        const successModal = new bootstrap.Modal(document.getElementById("successModal"));
        successModal.show();

        setTimeout(function () {
          successModal.hide();
        }, 3000);
      });
    </script>
  <?php endif; ?>
</body>
</html>
