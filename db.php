<?php
// db.php — Database Connection Configuration

$host = '127.0.0.1';
$db   = 'zyrop_food_order';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Return clean JSON if this is an API call
     if (strpos($_SERVER['REQUEST_URI'] ?? '', '_api.php') !== false) {
         header('Content-Type: application/json');
         echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
         exit();
     }
     
     // Otherwise show user-friendly error
     die("Connection failed: " . $e->getMessage());
}
?>
