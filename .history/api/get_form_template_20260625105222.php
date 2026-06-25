<?php
/**
 * API Endpoint: Get Form Template
 * Path: /api/get_form_template.php
 * 
 * Receives a POST request with document_type and returns the corresponding form HTML
 */

header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed. Use POST.'
    ]);
    exit;
}

// Include the template loader
require_once '../includes/form_template_loader.php';

// Get the JSON payload
$input = json_decode(file_get_contents('php://input'), true);
$documentType = $input['document_type'] ?? null;

if (!$documentType) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => 'document_type is required'
    ]);
    exit;
}

// Sanitize input
$documentType = preg_replace('/[^a-zA-Z0-9\-]/', '', $documentType);

try {
    // Load the form template
    $html = loadFormTemplate($documentType);
    
    echo json_encode([
        'success' => true,
        'html' => $html,
        'document_type' => $documentType
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Error loading template: ' . $e->getMessage()
    ]);
}
?>