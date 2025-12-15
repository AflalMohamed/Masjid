<?php
// includes/db.php
// Secure, simple PDO connection

$host = "localhost";   // or 127.0.0.1
$user = "root";        // default XAMPP username
$pass = "";            // default XAMPP password (empty)
$dbname = "masjid_db"; // your database name

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Database connection failed: " . $e->getMessage());
}
?>
