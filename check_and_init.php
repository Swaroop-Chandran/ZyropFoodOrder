<?php
// check_and_init.php — Setup and Verify Database Schema from CLI

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'zyrop_food_order';
$charset = 'utf8mb4';

echo "=== Zyrop Database CLI Initializer ===\n";

try {
    // 1. Connect to MySQL Server
    echo "Connecting to MySQL server at {$host}...\n";
    $dsn = "mysql:host=$host;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "Connected successfully to MySQL server!\n";

    // 2. Read database.sql schema
    $sqlFile = 'database.sql';
    if (!file_exists($sqlFile)) {
        die("Error: database.sql not found in current directory.\n");
    }
    echo "Reading schema from {$sqlFile}...\n";
    $sql = file_get_contents($sqlFile);

    // 3. Execute Schema
    echo "Initializing database and creating tables...\n";
    $pdo->exec($sql);
    echo "Database '{$db}' initialized successfully! 🎉\n";

    // 4. Verify tables exist and display counts
    $dsnDb = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdoDb = new PDO($dsnDb, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $tables = ['users', 'orders', 'order_items'];
    foreach ($tables as $table) {
        $stmt = $pdoDb->query("SHOW TABLES LIKE '$table'");
        if ($stmt->fetch()) {
            $count = $pdoDb->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
            echo "Table '{$table}': EXISTS (with {$count} records)\n";
        } else {
            echo "Table '{$table}': MISSING! ❌\n";
        }
    }

} catch (\PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "Please check if Apache and MySQL are running in your XAMPP Control Panel.\n";
    exit(1);
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
