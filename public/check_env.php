<?php
// Script to check available environment variables on the server
// Usage: Visit /check_env.php?key=YOUR_SECRET_KEY (security optional for dev debug)

header('Content-Type: text/plain');

echo "=== ENVIRONMENT DEBUGGER ===\n\n";

echo "1. Checking Standard Keys:\n";
$keys = [
    'MYSQLHOST', 'MYSQLUSER', 'MYSQLPASSWORD', 'MYSQLDATABASE', 'MYSQLPORT',
    'DATABASE_URL', 'MYSQL_URL', 'PORT'
];

foreach ($keys as $key) {
    $val_env = $_ENV[$key] ?? 'NULL';
    $val_getenv = getenv($key) !== false ? getenv($key) : 'FALSE';
    $val_server = $_SERVER[$key] ?? 'NULL';
    
    // Mask password
    if (strpos($key, 'PASSWORD') !== false || strpos($key, 'URL') !== false) {
        if ($val_env !== 'NULL') $val_env = substr($val_env, 0, 10) . '...[MASKED]';
        if ($val_getenv !== 'FALSE') $val_getenv = substr($val_getenv, 0, 10) . '...[MASKED]';
        if ($val_server !== 'NULL') $val_server = substr($val_server, 0, 10) . '...[MASKED]';
    }

    echo "Key: $key\n";
    echo "  \$_ENV:    $val_env\n";
    echo "  getenv():  $val_getenv\n";
    echo "  \$_SERVER: $val_server\n";
    echo "--------------------------\n";
}

echo "\n2. Full Environment Check (Filtered):\n";
$all_vars = array_merge($_ENV, $_SERVER, getenv());
ksort($all_vars);

foreach ($all_vars as $key => $val) {
    if (is_array($val)) continue;
    if (preg_match('/(pass|secret|key|token|auth)/i', $key)) {
        $val = '***MASKED***';
    }
    // Only show relevant keys to avoid massive output
    if (preg_match('/(SQL|DB|RAILWAY|HOST|PORT|USER|NAME)/i', $key)) {
        echo "$key = $val\n";
    }
}

echo "\n=== END DEBUG ===\n";
