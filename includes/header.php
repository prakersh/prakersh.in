<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light dark">
    <title><?php echo htmlspecialchars($profile['name']); ?> - <?php echo htmlspecialchars($profile['job_title']); ?></title>
    <meta name="description" content="Portfolio of <?php echo htmlspecialchars($profile['name']); ?>, <?php echo htmlspecialchars($profile['job_title']); ?> - <?php echo htmlspecialchars(substr($profile['summary'], 0, 150)); ?>...">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="assets/images/favicon.svg">
    <link rel="icon" type="image/png" href="assets/images/favicon.png">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <!-- Google Fonts - Instrument Sans + Source Sans 3 -->
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Source+Sans+3:wght@400;500;600&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Base CSS -->
    <link rel="stylesheet" href="css/style.css">

    <?php
    // Get current theme from database
    try {
        $stmt = $pdo->prepare('SELECT theme FROM admin_settings WHERE id = 1');
        $stmt->execute();
        $theme = $stmt->fetchColumn();

        // Only load theme CSS if not light (light is default in style.css)
        if ($theme && in_array($theme, ['dark', 'peach', 'blue'])) {
            echo '<link rel="stylesheet" href="css/theme-' . htmlspecialchars($theme) . '.css" id="theme-css">';
        }
    } catch (PDOException $e) {
        $theme = 'light';
    }
    ?>

    <!-- Print-specific CSS -->
    <link rel="stylesheet" href="css/print.css" media="print">

    <!-- Theme initialization (prevents flash) -->
    <script>
        (function() {
            const saved = localStorage.getItem('zen-theme');
            if (saved && ['light', 'dark', 'peach', 'blue'].includes(saved)) {
                document.documentElement.setAttribute('data-theme', saved);
            }
        })();
    </script>
</head>
<body>
    <header class="site-header" role="banner">
        <div class="zen-container">
            <div class="site-header__inner">
                <a href="#" class="site-header__brand"><?php
                    $firstName = explode(' ', $profile['name'])[0];
                    echo htmlspecialchars($firstName);
                ?></a>

                <nav class="site-nav" role="navigation" aria-label="Main navigation">
                    <a href="#profile" class="site-nav__link active">About</a>
                    <a href="#experience" class="site-nav__link">Experience</a>
                    <a href="#education" class="site-nav__link">Education</a>
                    <a href="#skills" class="site-nav__link">Skills</a>
                    <a href="#projects" class="site-nav__link">Projects</a>
                    <a href="#achievements" class="site-nav__link">Achievements</a>
                </nav>

                <div class="site-header__actions">
                    <button class="theme-toggle d-print-none" id="theme-toggle" aria-label="Toggle theme">
                        <i class="fas fa-moon"></i>
                    </button>
                    <a href="#" class="btn btn--primary btn--sm d-print-none" id="download-resume-btn">
                        <i class="fas fa-download"></i>
                        <span class="visually-hidden-mobile">Resume</span>
                    </a>
                    <button class="mobile-menu-toggle d-print-none" id="mobile-menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </div>

            <!-- Mobile Navigation -->
            <nav class="mobile-nav" id="mobile-nav" role="navigation" aria-label="Mobile navigation">
                <a href="#profile" class="mobile-nav__link active">About</a>
                <a href="#experience" class="mobile-nav__link">Experience</a>
                <a href="#education" class="mobile-nav__link">Education</a>
                <a href="#skills" class="mobile-nav__link">Skills</a>
                <a href="#projects" class="mobile-nav__link">Projects</a>
                <a href="#achievements" class="mobile-nav__link">Achievements</a>
            </nav>
        </div>
    </header>

    <main role="main">
