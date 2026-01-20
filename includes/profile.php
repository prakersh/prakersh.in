<?php
// Fetch experience count for stats
if (!isset($pdo)) {
    require_once __DIR__ . '/db_connect.php';
    $pdo = getDbConnection();
}

// Helper function to ensure URLs have https:// prefix
if (!function_exists('ensureHttps')) {
    function ensureHttps($url) {
        if (empty($url)) return '';
        $url = trim($url);
        if (!preg_match('#^https?://#i', $url)) {
            return 'https://' . $url;
        }
        return $url;
    }
}

// Calculate years of experience from first job
$stmt = $pdo->query('SELECT MIN(start_date) as first_date FROM experience');
$firstDate = $stmt->fetchColumn();
$yearsExperience = 0;
if ($firstDate) {
    // Handle different date formats: "MM/YYYY...", "YYYY", or "YYYY-MM-DD"
    try {
        // Extract first MM/YYYY pattern from string (handles "06/2018 to 12/2020" format)
        if (preg_match('/(\d{2})\/(\d{4})/', $firstDate, $matches)) {
            // Format: MM/YYYY (anywhere in string)
            $start = new DateTime($matches[2] . '-' . $matches[1] . '-01');
        } elseif (preg_match('/^(\d{4})/', $firstDate, $matches)) {
            // Format: YYYY at start
            $start = new DateTime($matches[1] . '-01-01');
        } else {
            // Try parsing as-is
            $start = new DateTime($firstDate);
        }
        $now = new DateTime();
        $yearsExperience = $now->diff($start)->y;
    } catch (Exception $e) {
        $yearsExperience = 0;
    }
}

// Count projects
$stmt = $pdo->query('SELECT COUNT(*) FROM projects');
$projectCount = $stmt->fetchColumn();

// Count certifications
$stmt = $pdo->query('SELECT COUNT(*) FROM certificates_conferences');
$certCount = $stmt->fetchColumn();
?>

<section id="profile" class="bento-section" aria-labelledby="profile-title">
    <!-- Profile Hero Card - Spans full width -->
    <article class="bento-card bento-card--hero profile-hero animate-on-scroll">
        <div class="profile-hero__header">
            <div class="profile-hero__image-wrapper">
                <img src="<?php echo htmlspecialchars($profile['profile_image']); ?>"
                     alt="<?php echo htmlspecialchars($profile['name']); ?>"
                     class="profile-hero__image"
                     loading="eager">
                <span class="profile-hero__status" aria-label="Available for opportunities">
                    <span class="profile-hero__status-dot"></span>
                    Open to work
                </span>
            </div>

            <div class="profile-hero__info">
                <h1 id="profile-title" class="profile-hero__name">
                    <?php echo htmlspecialchars($profile['name']); ?>
                </h1>
                <p class="profile-hero__title">
                    <?php echo htmlspecialchars($profile['job_title']); ?>
                </p>

                <div class="profile-hero__actions d-print-none">
                    <div class="social-links">
                        <?php if (!empty($profile['linkedin'])): ?>
                        <a href="<?php echo htmlspecialchars(ensureHttps($profile['linkedin'])); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link"
                           aria-label="LinkedIn Profile">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                        <?php endif; ?>

                        <?php if (!empty($profile['github'])): ?>
                        <a href="<?php echo htmlspecialchars(ensureHttps($profile['github'])); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="social-link"
                           aria-label="GitHub Profile">
                            <i class="fab fa-github"></i>
                        </a>
                        <?php endif; ?>

                        <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>"
                           class="social-link"
                           aria-label="Send Email">
                            <i class="fas fa-envelope"></i>
                        </a>
                    </div>
                </div>
            </div>

            <?php
            // Generate QR code for website URL
            if (!empty($profile['website'])) {
                $websiteUrl = htmlspecialchars(ensureHttps($profile['website']), ENT_QUOTES, 'UTF-8');
                // Use QR Server API to generate QR code
                $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' . urlencode($websiteUrl);
            ?>
            <div class="profile-hero__qr-wrapper">
                <img src="<?php echo $qrCodeUrl; ?>"
                     alt="QR Code for <?php echo $websiteUrl; ?>"
                     class="profile-hero__qr-code"
                     loading="eager">
            </div>
            <?php } ?>
        </div>

        <!-- Summary - Full Width -->
        <p class="profile-hero__summary">
            <?php echo htmlspecialchars($profile['summary']); ?>
        </p>

        <!-- Contact Info Grid -->
        <div class="profile-hero__contact">
            <a href="mailto:<?php echo htmlspecialchars($profile['email']); ?>" class="contact-item">
                <span class="contact-item__icon">
                    <i class="fas fa-envelope"></i>
                </span>
                <span class="contact-item__text">
                    <span class="contact-item__label">Email</span>
                    <span class="contact-item__value print-link"><?php echo htmlspecialchars($profile['email']); ?></span>
                </span>
            </a>

            <a href="tel:<?php echo htmlspecialchars(str_replace(' ', '', $profile['phone'])); ?>" class="contact-item">
                <span class="contact-item__icon">
                    <i class="fas fa-phone"></i>
                </span>
                <span class="contact-item__text">
                    <span class="contact-item__label">Phone</span>
                    <span class="contact-item__value print-link"><?php echo htmlspecialchars($profile['phone']); ?></span>
                </span>
            </a>

            <div class="contact-item">
                <span class="contact-item__icon">
                    <i class="fas fa-map-marker-alt"></i>
                </span>
                <span class="contact-item__text">
                    <span class="contact-item__label">Location</span>
                    <span class="contact-item__value"><?php echo htmlspecialchars($profile['location']); ?></span>
                </span>
            </div>

            <?php if (!empty($profile['website'])): ?>
            <a href="<?php echo htmlspecialchars(ensureHttps($profile['website'])); ?>"
               target="_blank"
               rel="noopener noreferrer"
               class="contact-item">
                <span class="contact-item__icon">
                    <i class="fas fa-globe"></i>
                </span>
                <span class="contact-item__text">
                    <span class="contact-item__label">Website</span>
                    <span class="contact-item__value print-link"><?php echo htmlspecialchars(preg_replace('#^https?://#', '', $profile['website'])); ?></span>
                </span>
            </a>
            <?php endif; ?>
        </div>
    </article>

    <!-- Stats Cards Row -->
    <div class="bento-stats">
        <article class="bento-card bento-card--stat stat-card animate-on-scroll" style="--animation-delay: 0.1s">
            <span class="stat-card__value"><?php echo $yearsExperience; ?>+</span>
            <span class="stat-card__label">Years Experience</span>
        </article>

        <article class="bento-card bento-card--stat stat-card animate-on-scroll" style="--animation-delay: 0.2s">
            <span class="stat-card__value"><?php echo $projectCount; ?></span>
            <span class="stat-card__label">Projects</span>
        </article>

        <article class="bento-card bento-card--stat stat-card animate-on-scroll" style="--animation-delay: 0.3s">
            <span class="stat-card__value"><?php echo $certCount; ?></span>
            <span class="stat-card__label">Certificates</span>
        </article>
    </div>
</section>
