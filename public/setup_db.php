<?php
// Simple Database Setup Script
// Usage: Visit /setup_db.php

require_once __DIR__ . '/../app/config/config.php';
require_once __DIR__ . '/../app/config/Database.php';

use App\config\Database;

echo "<h1>Database Setup Tool</h1>";

try {
    $db = Database::connect();
    echo "<p class='success'>✅ Connected to database successfully!</p>";
    
    $sqlFile = __DIR__ . '/../database.sql';
    
    if (!file_exists($sqlFile)) {
        die("<p class='error'>❌ database.sql file not found at: $sqlFile</p>");
    }
    
    echo "<p>Found database.sql...</p>";
    
    $sql = file_get_contents($sqlFile);
    
    // Remove comments to prevent issues
    $lines = explode("\n", $sql);
    $cleanSql = "";
    foreach ($lines as $line) {
        if (substr(trim($line), 0, 2) == '--' || substr(trim($line), 0, 2) == '/*') continue;
        if (trim($line) == '') continue;
        $cleanSql .= $line . "\n";
    }
    
    // Split by semicolon
    $statements = explode(";", $cleanSql);
    $count = 0;
    $errors = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $db->exec($statement);
                $count++;
            } catch (PDOException $e) {
                echo "<p class='warning'>⚠️ Error executing statement: " . htmlspecialchars(substr($statement, 0, 50)) . "... <br>Message: " . $e->getMessage() . "</p>";
                $errors++;
            }
        }
    }
    
    echo "<h2>Migration Complete</h2>";
    echo "<ul>";
    echo "<li>Executed statements: <strong>$count</strong></li>";
    echo "<li>Errors: <strong>$errors</strong> (Ignore 'table already exists' errors if re-running)</li>";
    echo "</ul>";
    
    echo "<p><strong>Now please DELETE this file from your repository or server for security!</strong></p>";
    echo "<a href='/'>Go to Home</a>";

} catch (Exception $e) {
    echo "<p class='error'>❌ Critical Error: " . $e->getMessage() . "</p>";
}

echo "
<style>
    body { font-family: sans-serif; padding: 2rem; max-width: 800px; margin: 0 auto; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; }
    ul { background: #f5f5f5; padding: 1rem 2rem; border-radius: 8px; }
</style>
";
