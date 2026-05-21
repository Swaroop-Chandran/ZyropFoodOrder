<?php
// auth_api.php — AJAX Authentication Handler

session_start();
header('Content-Type: application/json');

require_once 'db.php';

// Support both URL-encoded and raw JSON request formats
$data = $_POST;
if (empty($data)) {
    $raw_input = file_get_contents('php://input');
    $decoded = json_decode($raw_input, true);
    if (is_array($decoded)) {
        $data = $decoded;
    }
}

$action = $data['action'] ?? '';

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'Invalid action.']);
    exit();
}

if ($action === 'login') {
    $email = trim($data['email'] ?? '');
    $password = $data['password'] ?? '';

    if (!$email || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please enter all fields.']);
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            echo json_encode([
                'success' => true,
                'message' => 'Welcome back! 🎉',
                'user' => [
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit();
}

if ($action === 'signup') {
    $fname    = trim($data['fname'] ?? '');
    $lname    = trim($data['lname'] ?? '');
    $email    = trim($data['email'] ?? '');
    $phone    = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $address  = trim($data['address'] ?? '');

    $name = trim("$fname $lname");

    if (!$name || !$email || !$phone || !$password) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all required fields.']);
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }

    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
            exit();
        }

        // Hash the password securely
        $hashed_pwd = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashed_pwd, $address]);

        $userId = $pdo->lastInsertId();

        // Automatically log in the user
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => "Welcome to Zyrop, {$name}! 🎉",
            'user' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
    } catch (\Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unsupported action.']);
?>
