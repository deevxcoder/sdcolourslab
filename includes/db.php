<?php
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = getenv('DATABASE_URL');
        if (!$dsn) {
            throw new RuntimeException('DATABASE_URL environment variable is not set.');
        }
        // Convert postgres:// URL to PDO DSN format if needed
        if (strpos($dsn, 'postgres://') === 0 || strpos($dsn, 'postgresql://') === 0) {
            $parsed = parse_url($dsn);
            $host   = $parsed['host'];
            $port   = $parsed['port'] ?? 5432;
            $dbname = ltrim($parsed['path'], '/');
            $user   = $parsed['user'];
            $pass   = $parsed['pass'];
            $pdoDsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=disable";
            $pdo = new PDO($pdoDsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } else {
            $pdo = new PDO($dsn, null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }
    }
    return $pdo;
}
