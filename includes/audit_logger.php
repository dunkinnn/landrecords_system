<?php
function logAuditTrail($conn, $action, $details = null, $userId = null, $username = null, $role = null) {
    if (!$conn || trim($action) === "") {
        return false;
    }

    $auditUserId = $userId ?? ($_SESSION['user_id'] ?? null);
    $auditUsername = $username ?? ($_SESSION['username'] ?? null);
    $auditRole = $role ?? ($_SESSION['role'] ?? null);
    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? null;
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? null;

    if ($userAgent !== null) {
        $userAgent = substr($userAgent, 0, 255);
    }

    $stmt = $conn->prepare(
        "INSERT INTO tbl_audit_trial (user_id, username, role, action, details, ip_address, user_agent)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );

    if (!$stmt) {
        return false;
    }

    $stmt->bind_param(
        "issssss",
        $auditUserId,
        $auditUsername,
        $auditRole,
        $action,
        $details,
        $ipAddress,
        $userAgent
    );

    $success = $stmt->execute();
    $stmt->close();

    return $success;
}
?>
