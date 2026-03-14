<?php
if (!isset($_SESSION['user_id'])) {
    header("Location: /landrecords_system/auth/login.php");
    exit();
}
