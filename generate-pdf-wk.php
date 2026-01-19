<?php
/**
 * PDF Generation using wkhtmltopdf
 * 
 * Generates a PDF of the resume with print.css styles applied
 */

// Check for wkhtmltopdf installation
if (!file_exists('/usr/bin/wkhtmltopdf') && !file_exists('/usr/local/bin/wkhtmltopdf')) {
    die("Please install wkhtmltopdf first. See wkhtmltopdf-setup.md for instructions.");
}

// Find the wkhtmltopdf binary
$wkhtmltopdf = file_exists('/usr/bin/wkhtmltopdf') ? '/usr/bin/wkhtmltopdf' : '/usr/local/bin/wkhtmltopdf';

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    // Include the database connection
    require_once 'includes/db_connect.php';
    $pdo = getDbConnection();
    
    // Fetch profile data
    $stmt = $pdo->prepare('SELECT * FROM profile WHERE id = 1');
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Create temporary files
    $tempHtml = __DIR__ . '/temp_resume.html';
    $tempPdf = __DIR__ . '/temp_resume.pdf';
    
    // Get base URL for absolute paths (needed for wkhtmltopdf)
    $baseDir = __DIR__;

    // Start output buffer to capture PHP output
    ob_start();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($profile['name']); ?> - Resume</title>
        <!-- Google Fonts - same as main site -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Source+Sans+3:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <!-- Base CSS - using absolute file:// paths for wkhtmltopdf -->
        <link rel="stylesheet" href="file://<?php echo $baseDir; ?>/css/style.css">
        <!-- Print CSS - loaded directly (not media="print") since we want it always applied -->
        <link rel="stylesheet" href="file://<?php echo $baseDir; ?>/css/print.css">
        <style>
            /* Force print styles to apply for PDF generation */
            /* These mirror the critical @media print rules from print.css */
            html, body {
                width: 100% !important;
                height: auto !important;
                margin: 0 !important;
                padding: 0 !important;
                background: #fff !important;
                font-family: 'Source Sans 3', -apple-system, BlinkMacSystemFont, sans-serif !important;
                font-size: 9pt !important;
                line-height: 1.4 !important;
                color: #333 !important;
                overflow: visible !important;
            }

            /* Hide non-print elements */
            .d-print-none, .site-header, .site-footer, .theme-toggle,
            .mobile-menu-toggle, .mobile-nav, .social-links, .btn,
            .skills-legend, .project-card__actions, .achievement-card__link,
            .bento-stats, .experience-card__duration-bar, .experience-card__duration,
            .profile-hero__status, .profile-hero__actions, nav {
                display: none !important;
            }
        </style>
    </head>
    <body class="is-printing">
        <div class="zen-container">
            <div class="zen-content">
                <?php
                // Include core sections - same order as index.php
                include 'includes/profile.php';
                include 'includes/experience.php';
                include 'includes/education.php';
                include 'includes/skills.php';
                include 'includes/projects.php';
                include 'includes/achievements.php';
                ?>
            </div>
        </div>
    </body>
    </html>
    <?php
    // Get the HTML content and end buffering
    $htmlContent = ob_get_clean();
    
    // Write the HTML to a temp file
    file_put_contents($tempHtml, $htmlContent);
    
    // Build the wkhtmltopdf command with options
    // Match print.css @page settings: margin: 0.5cm (≈5mm)
    $options = [
        '--page-size A4',
        '--margin-top 5mm',
        '--margin-right 5mm',
        '--margin-bottom 5mm',
        '--margin-left 5mm',
        '--encoding UTF-8',
        '--enable-local-file-access',
        '--no-stop-slow-scripts',
        '--javascript-delay 500',
        '--disable-smart-shrinking',    // CRITICAL: Prevent auto-shrinking that compresses content
        '--dpi 96',                     // Match screen DPI for consistent sizing
        '--zoom 1.0',                   // No zoom - render at 100%
        '--image-quality 100'           // High quality images
    ];
    
    $command = escapeshellcmd($wkhtmltopdf) . ' ' . implode(' ', $options) . ' ' . 
               escapeshellarg($tempHtml) . ' ' . escapeshellarg($tempPdf);
    
    // Execute the command
    exec($command, $output, $returnCode);
    
    // If the PDF was successfully generated
    if ($returnCode === 0 && file_exists($tempPdf)) {
        // Set headers for the PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . htmlspecialchars($profile['name']) . '_Resume.pdf"');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        
        // Output the PDF file
        readfile($tempPdf);
        
        // Clean up temporary files
        unlink($tempHtml);
        unlink($tempPdf);
    } else {
        throw new Exception("PDF generation failed. Return code: $returnCode");
    }
} catch (Exception $e) {
    // Log error
    error_log('PDF Generation Error: ' . $e->getMessage());
    
    // Clean up any temp files
    if (isset($tempHtml) && file_exists($tempHtml)) unlink($tempHtml);
    if (isset($tempPdf) && file_exists($tempPdf)) unlink($tempPdf);
    
    // Display user-friendly error
    header('Content-Type: text/html; charset=utf-8');
    echo '<h1>Error Generating PDF</h1>';
    echo '<p>We encountered a problem generating your PDF. Please try again later.</p>';
    echo '<p><a href="/">Return to Home</a></p>';
}

exit; 