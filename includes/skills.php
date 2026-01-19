<?php
// Include the database connection if not already included
if (!function_exists('getDbConnection')) {
    require_once __DIR__ . '/db_connect.php';
}
$pdo = getDbConnection();

// Fetch skills by category
$stmt = $pdo->query('SELECT DISTINCT category FROM skills ORDER BY category');
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Function to get skill level class
function getSkillLevelClass($level) {
    switch($level) {
        case 5: return 'skill-tag--expert';
        case 4: return 'skill-tag--advanced';
        case 3: return 'skill-tag--intermediate';
        default: return 'skill-tag--beginner';
    }
}

// Function to get skill level label
function getSkillLevelLabel($level) {
    switch($level) {
        case 5: return 'Expert';
        case 4: return 'Advanced';
        case 3: return 'Intermediate';
        default: return 'Beginner';
    }
}
?>

<section id="skills" class="bento-section" aria-labelledby="skills-title">
    <header class="section-header animate-on-scroll">
        <div class="section-title">
            <span class="section-title__icon">
                <i class="fas fa-code"></i>
            </span>
            <h2 id="skills-title" class="section-title__text">Skills</h2>
        </div>
    </header>

    <div class="bento-grid bento-grid--skills">
        <?php foreach ($categories as $index => $category):
            // Get skills for this category
            $stmt = $pdo->prepare('SELECT * FROM skills WHERE category = ? ORDER BY level DESC, name');
            $stmt->execute([$category]);
            $skills = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <article class="bento-card skills-card animate-on-scroll" style="--animation-delay: <?php echo ($index * 0.1); ?>s">
            <div class="bento-card__icon">
                <?php
                // Choose icon based on category name
                $icon = 'fas fa-code';
                $catLower = strtolower($category);
                if (strpos($catLower, 'language') !== false || strpos($catLower, 'programming') !== false) {
                    $icon = 'fas fa-code';
                } elseif (strpos($catLower, 'framework') !== false || strpos($catLower, 'web') !== false) {
                    $icon = 'fas fa-layer-group';
                } elseif (strpos($catLower, 'database') !== false) {
                    $icon = 'fas fa-database';
                } elseif (strpos($catLower, 'cloud') !== false || strpos($catLower, 'devops') !== false) {
                    $icon = 'fas fa-cloud';
                } elseif (strpos($catLower, 'tool') !== false) {
                    $icon = 'fas fa-tools';
                } elseif (strpos($catLower, 'soft') !== false) {
                    $icon = 'fas fa-users';
                }
                ?>
                <i class="<?php echo $icon; ?>"></i>
            </div>
            <h3 class="skills-card__title"><?php echo htmlspecialchars($category); ?></h3>

            <div class="skill-tags">
                <?php foreach ($skills as $skill):
                    $levelClass = getSkillLevelClass($skill['level']);
                    $levelLabel = getSkillLevelLabel($skill['level']);
                ?>
                <span class="skill-tag <?php echo $levelClass; ?>"
                      title="<?php echo htmlspecialchars($levelLabel); ?>"
                      aria-label="<?php echo htmlspecialchars($skill['name'] . ' - ' . $levelLabel); ?>">
                    <?php echo htmlspecialchars($skill['name']); ?>
                </span>
                <?php endforeach; ?>
            </div>
        </article>
        <?php endforeach; ?>
    </div>

    <!-- Skill Level Legend -->
    <div class="skills-legend animate-on-scroll d-print-none">
        <span class="skills-legend__item">
            <span class="skill-tag skill-tag--expert skill-tag--legend">Expert</span>
        </span>
        <span class="skills-legend__item">
            <span class="skill-tag skill-tag--advanced skill-tag--legend">Advanced</span>
        </span>
        <span class="skills-legend__item">
            <span class="skill-tag skill-tag--intermediate skill-tag--legend">Intermediate</span>
        </span>
        <span class="skills-legend__item">
            <span class="skill-tag skill-tag--beginner skill-tag--legend">Beginner</span>
        </span>
    </div>
</section>
