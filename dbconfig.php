<?php
$host = 'localhost';         // or your DB host
$db   = 'ksrtc_db';             // your database name
$user = 'root';              // your DB username
$pass = '';                  // your DB password
$charset = 'utf8mb4';
$port = 3306;

// Data Source Name
$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

// PDO options
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // return assoc arrays
    PDO::ATTR_EMULATE_PREPARES   => false,                  // use real prepared statements
];

try {
    $conn = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
?>
