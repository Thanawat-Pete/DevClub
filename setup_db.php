<?php
$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Connect without DB name to create it
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = file_get_contents('database.sql');
    $pdo->exec($sql);
    echo "Database setup successfully.";
} catch (PDOException $e) {
    echo "Error setting up database: " . $e->getMessage();
}
