<?php
// Include the database connection
require_once 'includes/db_connect.php';
$pdo = getDbConnection();

// Fetch profile data
$stmt = $pdo->prepare('SELECT * FROM profile WHERE id = 1');
$stmt->execute();
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if profile exists (database might not be initialized)
if (!$profile) {
    echo '<!DOCTYPE html><html><head><title>Setup Required</title></head><body style="font-family: sans-serif; padding: 40px; text-align: center;">';
    echo '<h1>Database Not Initialized</h1>';
    echo '<p>Please <a href="init_db.php">click here to initialize the database</a> first.</p>';
    echo '</body></html>';
    exit;
}

// Include the header file
include 'includes/header.php';
?>

<div class="zen-container">
    <div class="zen-content">
        <?php include 'includes/profile.php'; ?>
        <?php include 'includes/experience.php'; ?>
        <?php include 'includes/education.php'; ?>
        <?php include 'includes/skills.php'; ?>
        <?php include 'includes/projects.php'; ?>
        <?php include 'includes/achievements.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
