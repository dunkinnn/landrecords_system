<?php
/**
 * Endpoint: faas_floor_plan_upload.php
 * Handles the Floor Plan image upload separately from the main JSON
 * save flow, since file uploads need multipart/form-data. Returns a
 * path to store in the hidden floor_plan_path field, which then rides
 * along with the normal JSON save like any other text field.
 */
require_once '../includes/auth.php';

if (!isset($_FILES['floor_plan']) || $_FILES['floor_plan']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No file uploaded or upload failed.']);
    exit;
}

$file = $_FILES['floor_plan'];

$maxBytes = 5 * 1024 * 1024; // 5MB
if ($file['size'] > $maxBytes) {
    http_response_code(400);
    echo json_encode(['error' => 'File is too large. Maximum size is 5MB.']);
    exit;
}

/* Validate the actual file content, not just the extension or the
   browser-supplied MIME type (both are trivially spoofable). */
$allowedMimes = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/gif' => 'gif', 'image/webp' => 'webp'];
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$detectedMime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!isset($allowedMimes[$detectedMime])) {
    http_response_code(400);
    echo json_encode(['error' => 'Only JPG, PNG, GIF, or WEBP images are allowed.']);
    exit;
}

$uploadDir = __DIR__ . '/../uploads/floor_plans/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

/* Never trust the client's filename - generate a random one with the
   extension matched to the actually-detected MIME type. */
$filename = bin2hex(random_bytes(16)) . '.' . $allowedMimes[$detectedMime];
$destination = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to save the uploaded file.']);
    exit;
}

echo json_encode(['path' => 'uploads/floor_plans/' . $filename]);