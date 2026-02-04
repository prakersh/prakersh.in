<?php
/**
 * SEO Helper Functions for Dynamic SEO Generation
 * This file provides functions to generate SEO metadata dynamically
 * based on resume content from the database.
 */

require_once __DIR__ . '/db_connect.php';

/**
 * Get all SEO data from the database
 * @return array Associative array containing profile, skills, projects, etc.
 */
function getSeoData() {
    $pdo = getDbConnection();
    $seoData = [];
    
    $stmt = $pdo->prepare('SELECT * FROM profile WHERE id = 1');
    $stmt->execute();
    $seoData['profile'] = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query('SELECT * FROM skills ORDER BY category, name');
    $seoData['skills'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query('SELECT * FROM projects');
    $seoData['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query('SELECT * FROM experience ORDER BY start_date DESC');
    $seoData['experience'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query('SELECT * FROM education ORDER BY end_date DESC');
    $seoData['education'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $stmt = $pdo->query('SELECT * FROM achievements ORDER BY date DESC');
    $seoData['achievements'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    return $seoData;
}

/**
 * Generate meta description based on profile data
 * @param array $profile Profile data from database
 * @return string Optimized meta description
 */
function getMetaDescription($profile) {
    $summary = $profile['summary'] ?? '';
    $jobTitle = $profile['job_title'] ?? '';
    $name = $profile['name'] ?? '';
    
    if (strlen($summary) > 150) {
        $summary = substr($summary, 0, 147) . '...';
    }
    
    return "Portfolio of $name, $jobTitle. $summary";
}

/**
 * Generate keywords from skills and experience
 * @param array $skills Skills data
 * @param array $experience Experience data
 * @return string Comma-separated keywords
 */
function getKeywords($skills, $experience) {
    $keywords = [];
    
    foreach ($skills as $skill) {
        $keywords[] = $skill['name'];
    }
    
    foreach ($experience as $exp) {
        $keywords[] = $exp['job_title'];
        $keywords[] = $exp['company'];
    }
    
    array_unique($keywords);
    return implode(', ', array_slice($keywords, 0, 15));
}

/**
 * Generate Open Graph meta tags
 * @param array $profile Profile data
 * @param string $title Page title
 * @param string $url Page URL
 * @param string $description Page description
 * @return string HTML meta tags
 */
function getOpenGraphTags($profile, $title, $url, $description) {
    $siteName = htmlspecialchars($profile['name'] ?? 'Portfolio');
    $ogType = 'profile';
    
    return <<<HTML
<meta property="og:title" content="$title">
<meta property="og:description" content="$description">
<meta property="og:url" content="$url">
<meta property="og:site_name" content="$siteName">
<meta property="og:type" content="$ogType">
<meta property="og:locale" content="en_US">
HTML;
}

/**
 * Generate Twitter Card meta tags
 * @param string $title Page title
 * @param string $description Page description
 * @param string $url Page URL
 * @return string HTML meta tags
 */
function getTwitterCardTags($title, $description, $url) {
    return <<<HTML
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="$title">
<meta name="twitter:description" content="$description">
<meta name="twitter:url" content="$url">
HTML;
}

/**
 * Generate JSON-LD structured data for Person/Profile schema
 * @param array $seoData Complete SEO data
 * @param string $baseUrl Base URL of the website
 * @return string JSON-LD script
 */
function getJsonLdPerson($seoData, $baseUrl) {
    $profile = $seoData['profile'];
    $skills = $seoData['skills'];
    $experience = $seoData['experience'];
    $projects = $seoData['projects'];
    
    if (!$profile) {
        return '';
    }
    
    $person = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        'name' => $profile['name'],
        'jobTitle' => $profile['job_title'],
        'description' => $profile['summary']
    ];
    
    if (!empty($profile['email'])) {
        $person['email'] = $profile['email'];
    }
    
    if (!empty($profile['phone'])) {
        $person['telephone'] = $profile['phone'];
    }
    
    if (!empty($profile['location'])) {
        $person['address'] = [
            '@type' => 'PostalAddress',
            'addressLocality' => $profile['location']
        ];
    }
    
    if (!empty($profile['linkedin'])) {
        $person['sameAs'][] = $profile['linkedin'];
    }
    
    if (!empty($profile['github'])) {
        $person['sameAs'][] = $profile['github'];
    }
    
    if (!empty($profile['website'])) {
        $person['sameAs'][] = $profile['website'];
        $person['url'] = $profile['website'];
    }
    
    $person['url'] = $baseUrl;
    
    if (!empty($skills)) {
        $person['knowsAbout'] = array_map(function($skill) {
            return $skill['name'];
        }, $skills);
    }
    
    if (!empty($experience)) {
        $person['hasOccupation'] = [
            '@type' => 'Occupation',
            'name' => $profile['job_title'],
            'description' => $profile['summary']
        ];
    }
    
    return '<script type="application/ld+json">' . json_encode($person, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}

/**
 * Generate JSON-LD for Article/Organization
 * @param string $type Type of structured data (Organization, Article, etc.)
 * @param array $data Data for the structured data
 * @param string $baseUrl Base URL of the website
 * @return string JSON-LD script
 */
function getJsonLdCustom($type, $data, $baseUrl) {
    $schema = array_merge([
        '@context' => 'https://schema.org',
        '@type' => $type,
        'url' => $baseUrl
    ], $data);
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}

/**
 * Generate canonical URL
 * @param string $baseUrl Base URL
 * @param string $path Optional path
 * @return string Canonical URL
 */
function getCanonicalUrl($baseUrl, $path = '') {
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Get page title with SEO optimization
 * @param string $name Person name
 * @param string $jobTitle Job title
 * @param string $pageTitle Optional custom page title
 * @return string Optimized page title
 */
function getPageTitle($name, $jobTitle, $pageTitle = '') {
    if ($pageTitle) {
        return "$pageTitle | $name - $jobTitle";
    }
    return "$name - $jobTitle";
}

/**
 * Generate breadcrumb schema
 * @param array $breadcrumbs Array of breadcrumb items with 'name' and 'url'
 * @return string JSON-LD script for breadcrumbs
 */
function getBreadcrumbSchema($breadcrumbs) {
    $itemList = [];
    $position = 1;
    
    foreach ($breadcrumbs as $crumb) {
        $itemList[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $crumb['name'],
            'item' => $crumb['url']
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $itemList
    ];
    
    return '<script type="application/ld+json">' . json_encode($schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</script>';
}
?>