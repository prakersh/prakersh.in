<?php
// Migration script to convert plain text passwords to hashed passwords (ADMIN ACCESS ONLY)
session_start();

// SECURITY: Require admin authentication
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    error_log("Unauthorized password migration attempt from IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
    http_response_code(403);
    die('Access denied. Admin authentication required.');
}

require_once 'includes/db_connect.php';

try {
    $pdo = getDbConnection();
    
    // Get current admin settings
    $stmt = $pdo->prepare('SELECT id, password FROM admin_settings');
    $stmt->execute();
    $admins = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $migrated = 0;
    foreach ($admins as $admin) {
        // Check if password is already hashed (PHP password hashes start with $)
        if (!password_get_info($admin['password'])['algo']) {
            // Plain text password, need to hash it
            $hashedPassword = password_hash($admin['password'], PASSWORD_DEFAULT);
            $updateStmt = $pdo->prepare('UPDATE admin_settings SET password = ? WHERE id = ?');
            $updateStmt->execute([$hashedPassword, $admin['id']]);
            $migrated++;
        }
    }
    
    echo "Password migration completed successfully!<br>";
    echo "Migrated $migrated password(s) to secure hashed format.<br>";
    echo "<a href='admin.php'>Return to Admin Panel</a>";
    
    // Log the migration
    error_log("Password migration completed by admin: " . ($_SESSION['admin_username'] ?? 'unknown') . " - $migrated passwords migrated");
    
} catch (PDOException $e) {
    error_log("Password migration error: " . $e->getMessage());
    echo "Error during password migration. Please try again or contact administrator.";
}
?>