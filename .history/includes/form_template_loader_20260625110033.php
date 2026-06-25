<?php
/**
 * Form Template Loader
 * Dynamically loads form templates based on document type
 * 
 * Usage:
 *   $htmlForm = loadFormTemplate('24-FAAS-BUILDING');
 */

function loadFormTemplate($documentType) {
    // Define the path to your form templates directory
    $templatesDir = __DIR__ . '../includes/form_templates/';
    
    // Sanitize the document type to prevent directory traversal
    $safeType = preg_replace('/[^a-zA-Z0-9\-_]/', '', $documentType);
    
    // Build the file path
    $templateFile = $templatesDir . $safeType . '.php';
    
    // Check if the template file exists
    if (file_exists($templateFile)) {
        // Use output buffering to capture the template content
        ob_start();
        include $templateFile;
        $content = ob_get_clean();
        return $content;
    }
    
    // Return a fallback message if template doesn't exist
    return '<div class="alert alert-warning">Form template not found for: ' . htmlspecialchars($documentType) . '</div>';
}

/**
 * Get all available form templates
 * Returns an array of document types that have corresponding templates
 */
function getAvailableFormTemplates() {
    $templatesDir = __DIR__ . '../includes/form_templates/';
    $templates = [];
    
    if (is_dir($templatesDir)) {
        $files = glob($templatesDir . '*.php');
        foreach ($files as $file) {
            $filename = basename($file, '.php');
            $templates[] = $filename;
        }
    }
    
    return $templates;
}
?>