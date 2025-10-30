<?php
/**
 * config.php
 * -----------------------------
 * Database connection configuration for the JukeBoxed app.
 * 
 * Adjust the username and password below if your PostgreSQL role differs.
 */

$host = 'localhost';
$db   = 'jukeboxed';
$user = 'anshpathapadu';   // ← your PostgreSQL username (check via SELECT current_user;)
$pass = '';                 // ← leave blank if you don’t use a password
$charset = 'utf8';

$dsn = "pgsql:host=$host;dbname=$db;options='--client_encoding=$charset'";

try {
    // Create a PDO connection
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

} catch (PDOException $e) {
    // Handle connection errors gracefully
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
