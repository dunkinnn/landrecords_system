<?php
session_start();
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/../includes/audit_logger.php";

logAuditTrail($conn, "Logged out", "User logged out.");

// Destroy session
$_SESSION = [];
session_unset();
session_destroy();

// Remove "Remember Me" cookies if they exist
setcookie('remember_username', '', time() - 3600, '/');
setcookie('remember_password', '', time() - 3600, '/');

// Redirect to login page
header("Location: login.php");
exit();
