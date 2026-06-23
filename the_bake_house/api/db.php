<?php
// api/db.php
$host = 'localhost';
$dbname = 'bake';
$user = 'root'; // Default XAMPP user
$pass = '';     // Default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    // Throw exceptions on errors so we can catch them during checkout
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['message' => 'Database connection failed']);
    exit;
}
?>