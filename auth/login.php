<?php
session_start();
require_once __DIR__ . "/../includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        // Fetch the user by username
        $stmt = $conn->prepare("SELECT user_id, fullname, password, role FROM tbl_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($user_id, $db_fullname, $db_password, $role);

        if ($stmt->num_rows == 1) {
            $stmt->fetch();

            if (!in_array($role, ["client", "admin"])) {
                $message = "<div class='alert alert-danger text-center'>Access denied.</div>";
            } else {
                $login_success = false;

                if ($role === "client") {
                    $login_success = password_verify($password, $db_password);
                } else if ($role === "admin") {
                    $login_success = ($password === $db_password);
                }

                if ($login_success) {
                    // ✅ Set session with DB fullname
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['username'] = $username;
                    $_SESSION['fullname'] = $db_fullname; // use database value
                    $_SESSION['role'] = $role;

                    // Redirect
                    if ($role === "client") {
                        header("Location: ../views/client/home.php");
                    } else if ($role === "admin") {
                        header("Location: ../views/admin/dashboard.php");
                    }
                    exit;
                } else {
                    $message = "<div class='alert alert-danger text-center'>Incorrect password.</div>";
                }
            }
        } else {
            $message = "<div class='alert alert-danger text-center'>Username not found.</div>";
        }

        $stmt->close();
    } else {
        $message = "<div class='alert alert-warning text-center'>All fields are required.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LRMS - Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assets/css/login.css?v=1.0">
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

        <form method="POST" id="loginForm">

            <div class="form-group">
                <input type="text" name="username" placeholder="Username" required>
            </div>

            <div class="form-group">
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="options">
                <label class="remember">
                    <input type="checkbox" name="remember"> Save Password
                </label>

                <a href="forgot_password.php" class="forgot">Forgot Password?</a>
            </div>

            <button type="submit" class="auth-btn" id="loginBtn">Login</button>
        </form>

        <div class="login-link">
            Don't have an account? <a href="signup.php">Sign Up</a>
        </div>

    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelector("#loginForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const btn = document.querySelector("#loginBtn");
    btn.disabled = true;
    btn.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;

    setTimeout(() => {
        this.submit();
    }, 3000);
});
</script>

</body>
</html>