<?php
// Include the database connection if not already included
if (!function_exists('getDbConnection')) {
    require_once __DIR__ . '/db_connect.php';
}
$pdo = getDbConnection();

// Fetch achievements data
$stmt = $pdo->query('SELECT * FROM achievements ORDER BY date DESC');
$achievements = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch certificates and conferences
$stmt = $pdo->query('SELECT * FROM certificates_conferences ORDER BY date DESC');
$certificates_conferences = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to get icon based on type/title
function getAchievementIcon($title, $type = null) {
    $text = strtolower($title);

    if ($type === 'certificate' || strpos($text, 'certif') !== false) {
        return 'fa-certificate';
    } elseif ($type === 'conference' || strpos($text, 'conference') !== false || strpos($text, 'summit') !== false) {
        return 'fa-users';
    } elseif (strpos($text, 'award') !== false || strpos($text, 'winner') !== false) {
        return 'fa-trophy';
    } elseif (strpos($text, 'speaker') !== false || strpos($text, 'talk') !== false) {
        return 'fa-microphone';
    } elseif (strpos($text, 'publication') !== false || strpos($text, 'paper') !== false) {
        return 'fa-file-alt';
    } elseif (strpos($text, 'patent') !== false) {
        return 'fa-lightbulb';
    } else {
        return 'fa-star';
    }
}
?>

<section id="achievements" class="bento-section" aria-labelledby="achievements-title">
    <header class="section-header animate-on-scroll">
        <div class="section-title">
            <span class="section-title__icon">
                <i class="fas fa-trophy"></i>
            </span>
            <h2 id="achievements-title" class="section-title__text">Achievements</h2>
        </div>
    </header>

    <div class="bento-grid bento-grid--achievements">
        <?php
        $cardIndex = 0;

        // Display achievements
        foreach ($achievements as $achievement):
            $iconClass = getAchievementIcon($achievement['title']);
        ?>
        <article class="bento-card achievement-card animate-on-scroll" style="--animation-delay: <?php echo ($cardIndex * 0.1); ?>s">
            <div class="achievement-card__icon">
                <i class="fas <?php echo $iconClass; ?>"></i>
            </div>
            <div class="achievement-card__content">
                <h3 class="achievement-card__title">
                    <?php echo htmlspecialchars($achievement['title']); ?>
                </h3>
                <p class="achievement-card__description">
                    <?php echo htmlspecialchars($achievement['description']); ?>
                </p>
                <?php if (!empty($achievement['date'])): ?>
                <span class="achievement-card__date">
                    <i class="fas fa-calendar-alt"></i>
                    <?php echo htmlspecialchars($achievement['date']); ?>
                </span>
                <?php endif; ?>
            </div>
        </article>
        <?php
            $cardIndex++;
        endforeach;

        // Display certificates and conferences
        foreach ($certificates_conferences as $item):
            $iconClass = getAchievementIcon($item['title'], $item['type']);
        ?>
        <article class="bento-card achievement-card achievement-card--<?php echo htmlspecialchars($item['type']); ?> animate-on-scroll" style="--animation-delay: <?php echo ($cardIndex * 0.1); ?>s">
            <div class="achievement-card__icon">
                <i class="fas <?php echo $iconClass; ?>"></i>
            </div>
            <div class="achievement-card__content">
                <h3 class="achievement-card__title">
                    <?php echo htmlspecialchars($item['title']); ?>
                </h3>
                <p class="achievement-card__description">
                    <?php echo htmlspecialchars($item['description']); ?>
                </p>
                <div class="achievement-card__meta">
                    <span class="achievement-card__issuer">
                        <i class="fas fa-building"></i>
                        <?php echo htmlspecialchars($item['issuer']); ?>
                    </span>
                    <span class="achievement-card__date">
                        <i class="fas fa-calendar-alt"></i>
                        <?php echo htmlspecialchars($item['date']); ?>
                    </span>
                </div>
                <?php if (!empty($item['url'])): ?>
                <a href="<?php echo htmlspecialchars($item['url']); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="achievement-card__link">
                    <i class="fas fa-external-link-alt"></i>
                    View Certificate
                </a>
                <?php endif; ?>
            </div>
        </article>
        <?php
            $cardIndex++;
        endforeach;
        ?>

        <?php if (empty($achievements) && empty($certificates_conferences)): ?>
        <article class="bento-card achievement-card achievement-card--empty">
            <p class="achievement-card__empty-text">No achievements listed yet.</p>
        </article>
        <?php endif; ?>
    </div>
</section>
