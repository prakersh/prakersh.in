<?php
/**
 * Dynamic Robots.txt Generator
 * Generates robots.txt based on resume content and site structure
 */

require_once 'includes/db_connect.php';

// Set content type
header('Content-Type: text/plain');

// Get current site URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$siteUrl = $protocol . $_SERVER['HTTP_HOST'];

// Output robots.txt content
echo "# Robots.txt for " . $siteUrl . "\n";
echo "# Generated dynamically on " . date('Y-m-d H:i:s') . "\n\n";

// User-agent directives
echo "User-agent: *\n";
echo "Allow: /\n\n";

// Disallow admin and sensitive directories
echo "# Disallow admin and sensitive areas\n";
echo "Disallow: /admin.php\n";
echo "Disallow: /init_db.php\n";
echo "Disallow: /reset_db.php\n";
echo "Disallow: /update_db.php\n";
echo "Disallow: /migrate_passwords.php\n";
echo "Disallow: /check_theme.php\n";
echo "Disallow: /data/\n";
echo "Disallow: /includes/\n";
echo "Disallow: /.git/\n\n";

# Sitemap location
echo "# Sitemap\n";
echo "Sitemap: " . $siteUrl . "/sitemap.xml\n";

# Crawl delay (optional - be nice to servers)
echo "\n# Crawl delay\n";
echo "Crawl-delay: 1\n";

?>