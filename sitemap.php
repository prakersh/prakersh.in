<?php
/**
 * Dynamic Sitemap.xml Generator
 * Generates sitemap.xml based on resume content and all portfolio pages
 */

require_once 'includes/db_connect.php';

// Set content type
header('Content-Type: application/xml; charset=utf-8');

// Get current site URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$baseURL = $protocol . $_SERVER['HTTP_HOST'];

// Get database data
$pdo = getDbConnection();

$stmt = $pdo->prepare('SELECT * FROM profile WHERE id = 1');
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM projects');
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM experience');
$experiences = $stmt->fetchall(PDO::FETCH_ASSOC);

$stmt = $pdo->query('SELECT * FROM education');
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get last modified time from database file
$dbFile = __DIR__ . '/data/resume.db';
$lastModified = file_exists($dbFile) ? filemtime($dbFile) : time();

// Start XML output
echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Generate function for URL entries
function addUrl($url, $lastmod, $changefreq = 'weekly', $priority = '0.8') {
    echo '  <url>' . "\n";
    echo '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
    echo '    <lastmod>' . date('Y-m-d', $lastmod) . '</lastmod>' . "\n";
    echo '    <changefreq>' . $changefreq . '</changefreq>' . "\n";
    echo '    <priority>' . $priority . '</priority>' . "\n";
    echo '  </url>' . "\n";
}

// Homepage
addUrl($baseURL . '/', $lastModified, 'weekly', '1.0');

// Projects section
addUrl($baseURL . '/#projects', $lastModified, 'weekly', '0.9');

// Individual projects
if ($profile && !empty($projects)) {
    foreach ($projects as $project) {
        $projectName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $project['title']));
        addUrl($baseURL . '/#project-' . $projectName, $lastModified, 'monthly', '0.8');
    }
}

// Experience section
addUrl($baseURL . '/#experience', $lastModified, 'monthly', '0.8');

// Individual experiences
if ($profile && !empty($experiences)) {
    foreach ($experiences as $exp) {
        $expName = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', $exp['job_title'] . '-' . $exp['company']));
        addUrl($baseURL . '/#experience-' . $expName, $lastModified, 'monthly', '0.7');
    }
}

// Education section
addUrl($baseURL . '/#education', $lastModified, 'monthly', '0.7');

// Skills section
addUrl($baseURL . '/#skills', $lastModified, 'monthly', '0.7');

// Achievements section
addUrl($baseURL . '/#achievements', $lastModified, 'monthly', '0.6');

// Profile/About section
addUrl($baseURL . '/#profile', $lastModified, 'monthly', '0.8');

// External links (social profiles) - optional
if ($profile) {
    if (!empty($profile['linkedin'])) {
        addUrl($profile['linkedin'], $lastModified, 'monthly', '0.5');
    }
    if (!empty($profile['github'])) {
        addUrl($profile['github'], $lastModified, 'monthly', '0.5');
    }
    if (!empty($profile['website'])) {
        addUrl($profile['website'], $lastModified, 'monthly', '0.6');
    }
}

// Close XML
echo '</urlset>' . "\n";
?>