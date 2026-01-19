<?php
// Include the database connection if not already included
if (!function_exists('getDbConnection')) {
    require_once __DIR__ . '/db_connect.php';
}
$pdo = getDbConnection();

// Fetch education data
$stmt = $pdo->query('SELECT * FROM education ORDER BY end_date DESC');
$educations = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="education" class="bento-section" aria-labelledby="education-title">
    <header class="section-header animate-on-scroll">
        <div class="section-title">
            <span class="section-title__icon">
                <i class="fas fa-graduation-cap"></i>
            </span>
            <h2 id="education-title" class="section-title__text">Education</h2>
        </div>
    </header>

    <div class="bento-grid bento-grid--education">
        <?php foreach ($educations as $index => $education):
            // Decode the JSON description
            $descriptionItems = json_decode($education['description'], true);
        ?>
        <article class="bento-card education-card animate-on-scroll" style="--animation-delay: <?php echo ($index * 0.1); ?>s">
            <div class="education-card__header">
                <div class="education-card__meta">
                    <h3 class="education-card__degree"><?php echo htmlspecialchars($education['degree']); ?></h3>
                    <p class="education-card__institution">
                        <i class="fas fa-university"></i>
                        <?php echo htmlspecialchars($education['institution']); ?>
                    </p>
                </div>
                <span class="education-card__date">
                    <?php echo htmlspecialchars($education['start_date']); ?> &mdash; <?php echo htmlspecialchars($education['end_date']); ?>
                </span>
            </div>

            <div class="education-card__location">
                <i class="fas fa-map-marker-alt"></i>
                <?php echo htmlspecialchars($education['location']); ?>
            </div>

            <?php if (!empty($descriptionItems)): ?>
            <ul class="education-card__list">
                <?php foreach ($descriptionItems as $item): ?>
                <li><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>
