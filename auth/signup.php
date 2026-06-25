<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/audit_logger.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $fullname = trim($_POST['fullname']);
    $phone    = trim($_POST['phone']);
    $password = trim($_POST['password']);
    $role     = "client";

    if (!empty($username) && !empty($email) && !empty($fullname) && !empty($phone) && !empty($password)) {

        // Check if username, email, or fullname exists
        $check = $conn->prepare("SELECT user_id, username, email, fullname FROM tbl_users WHERE username = ? OR email = ? OR fullname = ?");
        $check->bind_param("sss", $username, $email, $fullname);
        $check->execute();
        $check->store_result();
        $check->bind_result($existingUserId, $existingUsername, $existingEmail, $existingFullname);

        if ($check->num_rows > 0) {
            $check->fetch();

            $existingUsername = (string)($existingUsername ?? "");
            $existingEmail = (string)($existingEmail ?? "");
            $existingFullname = (string)($existingFullname ?? "");

            if (strcasecmp($existingEmail, $email) === 0) {
                $message = "<div class='alert alert-danger text-center'>Email already exists.</div>";
            } elseif (strcasecmp($existingUsername, $username) === 0) {
                $message = "<div class='alert alert-danger text-center'>Username already exists.</div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Full name already exists.</div>";
            }
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO tbl_users (username, email, fullname, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $username, $email, $fullname, $phone, $hashed_password, $role);

            if ($stmt->execute()) {
                logAuditTrail($conn, "Signed up", "New client account registered.", $stmt->insert_id, $username, $role);
                $message = "<div class='alert alert-success text-center'>Signup successful! You can now login.</div>";
            } else {
                $message = "<div class='alert alert-danger text-center'>Something went wrong.</div>";
            }

            $stmt->close();
        }

        $check->close();
    } else {
        $message = "<div class='alert alert-warning text-center'>All fields are required.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LRMS - Client Signup</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/signup.css?v=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <div class="auth-container">
        
        <img src="../assets/img/logo.png" alt="Logo" class="logo">

        <h1>LRMS</h1>
        <p class="tagline">
            Manage land records. Automate assessments. Serve the community better.
        </p>

        <?php echo $message ?? ''; ?>

        <form method="POST" id="signupForm">
            <div class="form-group">
                <input type="text" name="fullname" placeholder="Full Name" required>
            </div>

            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-group">
                <input type="text" name="phone" placeholder="Phone Number" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <button type="submit" class="auth-btn" id="signupBtn">Sign Up</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Spinner on submit with 5-second delay -->
<script>
document.querySelector("#signupForm").addEventListener("submit", function(e) {
    e.preventDefault(); // prevent immediate submit

    const btn = document.querySelector("#signupBtn");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;

    // 5-second delay before submitting
    setTimeout(() => {
        this.submit();
    }, 3000);
});
</script>

</body>
</html>
