<?php
$server = 'mysql'; // or 'localhost' if you're not using Docker
$username = 'student';//vsvshcshdy
$password = 'student';//cdkncdkcb dmj
$database = 'carbuy_db';//cksndcd,cdncnl

try {
    $pdo = new PDO("mysql:host=$server;dbname=$database", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Connected to the database"; // Uncomment for testing
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit();
}
?>
