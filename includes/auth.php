<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $isApiRequest = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);

    if ($isApiRequest) {
        http_response_code(401);
        echo json_encode(['error' => 'Your session has expired. Please log in again.']);
    } else {
        header("Location: /landrecords_system/auth/login.php");
    }
    exit();
}
