<?php

date_default_timezone_set('Asia/Jakarta');

// Helper to determine the base URL
$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') || 
             (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
define('BASE_URL', $protocol . '://' . $host);
// Database Configuration
$db_host = '127.0.0.1';
$db_user = 'root';
$db_pass = '';
$db_name = 'zentori_db';
$db_port = '3306';

// 1. Try Specific Environment Variables (Railway MySQL Service defaults)
// Check $_ENV, getenv(), and $_SERVER (some SAPIs manipulate these differently)
$envHost = $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') ?? $_SERVER['MYSQLHOST'] ?? null;
if ($envHost) {
    $db_host = $envHost;
    $db_user = $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') ?? $_SERVER['MYSQLUSER'] ?? 'root';
    $db_pass = $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') ?? $_SERVER['MYSQLPASSWORD'] ?? '';
    $db_name = $_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') ?? $_SERVER['MYSQLDATABASE'] ?? 'zentori_db';
    $db_port = $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') ?? $_SERVER['MYSQLPORT'] ?? '3306';
} 
// 2. Try MYSQL_URL or DATABASE_URL (Common in many providers)
elseif (!empty($_ENV['MYSQL_URL']) || getenv('MYSQL_URL') || !empty($_ENV['DATABASE_URL']) || getenv('DATABASE_URL')) {
    $connectionUrl = $_ENV['MYSQL_URL'] ?? getenv('MYSQL_URL') ?? $_SERVER['MYSQL_URL'] ??
                     $_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?? $_SERVER['DATABASE_URL'];
    
    if ($connectionUrl) {
        $url = parse_url($connectionUrl);
        $db_host = $url['host'] ?? $db_host;
        $db_user = $url['user'] ?? $db_user;
        $db_pass = $url['pass'] ?? $db_pass;
        $db_name = ltrim($url['path'] ?? '', '/') ?: $db_name;
        $db_port = $url['port'] ?? $db_port;
    }
}

define('DB_HOST', $db_host);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);
define('DB_NAME', $db_name);
define('DB_PORT', $db_port);
define('APP_NAME', 'Sistem Inventori');
