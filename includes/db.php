<?php
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $host     = '127.0.0.1';
        $port     = '3306';
        $dbname   = 'sdcolourslab';
        $user     = 'root';
        $password = '';          // Change if your MySQL root has a password
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $pdo = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }
    return $pdo;
}
