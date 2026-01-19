<?php
// Include the database connection if not already included
if (!function_exists('getDbConnection')) {
    require_once __DIR__ . '/db_connect.php';
}
$pdo = getDbConnection();

// Fetch projects data
$stmt = $pdo->query('SELECT * FROM projects');
$projects = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Helper function to get icon class based on project keywords
function getProjectIconClass($title, $description) {
    $text = strtolower($title . ' ' . $description);

    if (strpos($text, 'lock') !== false || strpos($text, 'security') !== false) {
        return 'fa-lock';
    } elseif (strpos($text, 'encrypt') !== false || strpos($text, 'cipher') !== false) {
        return 'fa-shield-alt';
    } elseif (strpos($text, 'progress') !== false || strpos($text, 'task') !== false) {
        return 'fa-tasks';
    } elseif (strpos($text, 'short') !== false || strpos($text, 'url') !== false) {
        return 'fa-link';
    } elseif (strpos($text, 'point of sale') !== false || strpos($text, 'pos') !== false || strpos($text, 'shop') !== false) {
        return 'fa-shopping-cart';
    } elseif (strpos($text, 'portfolio') !== false || strpos($text, 'resume') !== false) {
        return 'fa-user';
    } elseif (strpos($text, 'api') !== false || strpos($text, 'backend') !== false) {
        return 'fa-server';
    } elseif (strpos($text, 'mobile') !== false || strpos($text, 'app') !== false) {
        return 'fa-mobile-alt';
    } elseif (strpos($text, 'database') !== false || strpos($text, 'data') !== false) {
        return 'fa-database';
    } elseif (strpos($text, 'automation') !== false || strpos($text, 'script') !== false) {
        return 'fa-cogs';
    } else {
        return 'fa-code';
    }
}
?>

<section id="projects" class="bento-section" aria-labelledby="projects-title">
    <header class="section-header animate-on-scroll">
        <div class="section-title">
            <span class="section-title__icon">
                <i class="fas fa-folder-open"></i>
            </span>
            <h2 id="projects-title" class="section-title__text">Projects</h2>
        </div>
    </header>

    <div class="bento-grid bento-grid--projects">
        <?php foreach ($projects as $index => $project):
            // Decode the technologies JSON
            $technologies = json_decode($project['technologies'], true);
            $iconClass = getProjectIconClass($project['title'], $project['description']);
        ?>
        <article class="bento-card project-card animate-on-scroll" style="--animation-delay: <?php echo ($index * 0.1); ?>s">
            <div class="project-card__icon">
                <i class="fas <?php echo $iconClass; ?>"></i>
            </div>

            <h3 class="project-card__title"><?php echo htmlspecialchars($project['title']); ?></h3>
            <p class="project-card__description"><?php echo htmlspecialchars($project['description']); ?></p>

            <?php if (!empty($technologies)): ?>
            <div class="project-card__tech">
                <?php foreach ($technologies as $tech): ?>
                <span class="tech-badge"><?php echo htmlspecialchars($tech); ?></span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <?php if (!empty($project['link'])): ?>
            <div class="project-card__actions">
                <a href="<?php echo htmlspecialchars($project['link']); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="btn btn--outline btn--sm">
                    <i class="fab fa-github"></i>
                    View Code
                </a>
            </div>
            <?php endif; ?>
        </article>
        <?php endforeach; ?>
    </div>
</section>
