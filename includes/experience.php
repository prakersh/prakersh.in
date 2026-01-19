<?php
// Include the database connection if not already included
if (!isset($pdo)) {
    require_once __DIR__ . '/db_connect.php';
    $pdo = getDbConnection();
}

// Fetch experience data
$stmt = $pdo->query('SELECT * FROM experience ORDER BY start_date DESC');
$experiences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to parse date in various formats (MM/YYYY, YYYY, or full date)
function parseExperienceDate($dateStr) {
    if (strtolower($dateStr) === 'present') {
        return new DateTime();
    }
    if (preg_match('/^(\d{2})\/(\d{4})$/', $dateStr, $matches)) {
        // Format: MM/YYYY
        return new DateTime($matches[2] . '-' . $matches[1] . '-01');
    } elseif (preg_match('/^\d{4}$/', $dateStr)) {
        // Format: YYYY
        return new DateTime($dateStr . '-01-01');
    } else {
        // Try parsing as-is
        return new DateTime($dateStr);
    }
}

// Calculate duration in years for each experience
function calculateDuration($startDate, $endDate) {
    try {
        $start = parseExperienceDate($startDate);
        $end = parseExperienceDate($endDate);
        $diff = $start->diff($end);
        $years = $diff->y;
        $months = $diff->m;

        if ($years > 0 && $months > 0) {
            return $years . ' yr ' . $months . ' mo';
        } elseif ($years > 0) {
            return $years . ' yr' . ($years > 1 ? 's' : '');
        } else {
            return $months . ' mo' . ($months > 1 ? 's' : '');
        }
    } catch (Exception $e) {
        return '';
    }
}

// Extract year from date string
function extractYear($dateStr) {
    if (strtolower($dateStr) === 'present') {
        return (int)date('Y');
    }
    if (preg_match('/(\d{4})/', $dateStr, $matches)) {
        return (int)$matches[1];
    }
    return (int)date('Y');
}
?>

<section id="experience" class="bento-section" aria-labelledby="experience-title">
    <header class="section-header animate-on-scroll">
        <div class="section-title">
            <span class="section-title__icon">
                <i class="fas fa-briefcase"></i>
            </span>
            <h2 id="experience-title" class="section-title__text">Experience</h2>
        </div>
    </header>

    <div class="bento-grid bento-grid--experience">
        <?php foreach ($experiences as $index => $experience):
            // Decode the JSON description
            $descriptionItems = json_decode($experience['description'], true);
            $duration = calculateDuration($experience['start_date'], $experience['end_date']);

            // Calculate a visual duration bar (max 10 years = 100%)
            $startYear = extractYear($experience['start_date']);
            $endYear = extractYear($experience['end_date']);
            $durationYears = max(0, $endYear - $startYear);
            $durationPercent = min(($durationYears / 10) * 100, 100);
        ?>
        <article class="bento-card experience-card animate-on-scroll" style="--animation-delay: <?php echo ($index * 0.1); ?>s">
            <div class="experience-card__header">
                <div class="experience-card__meta">
                    <h3 class="experience-card__title"><?php echo htmlspecialchars($experience['job_title']); ?></h3>
                    <p class="experience-card__company">
                        <i class="fas fa-building"></i>
                        <?php echo htmlspecialchars($experience['company']); ?>
                    </p>
                </div>
                <span class="experience-card__date">
                    <?php echo htmlspecialchars($experience['start_date']); ?> &mdash; <?php echo htmlspecialchars($experience['end_date']); ?>
                </span>
            </div>

            <div class="experience-card__location">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($experience['location']); ?>
                <span class="experience-card__duration"><?php echo $duration; ?></span>
            </div>

            <!-- Duration visual bar -->
            <div class="experience-card__duration-bar" role="progressbar" aria-valuenow="<?php echo $durationPercent; ?>" aria-valuemin="0" aria-valuemax="100">
                <div class="experience-card__duration-fill" style="width: <?php echo $durationPercent; ?>%"></div>
            </div>

            <?php if (!empty($descriptionItems)): ?>
            <ul class="experience-card__list">
                <?php foreach ($descriptionItems as $item): ?>
                <li><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>
