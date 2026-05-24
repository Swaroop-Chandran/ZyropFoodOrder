<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

$data = $_POST;
if (empty($data)) {
    $rawInput = file_get_contents('php://input');
    $decoded = json_decode($rawInput, true);
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
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                'success' => true,
                'message' => 'Welcome back!',
                'user' => [
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
    }
    exit();
}

if ($action === 'signup') {
    $fname = trim($data['fname'] ?? '');
    $lname = trim($data['lname'] ?? '');
    $email = trim($data['email'] ?? '');
    $phone = trim($data['phone'] ?? '');
    $password = $data['password'] ?? '';
    $address = trim($data['address'] ?? '');
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
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode(['success' => false, 'message' => 'Email is already registered.']);
            exit();
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, $hashedPassword, $address]);

        $userId = $pdo->lastInsertId();

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        echo json_encode([
            'success' => true,
            'message' => "Welcome to Zesto, {$name}!",
            'user' => [
                'name' => $name,
                'email' => $email
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()]);
    }
    exit();
}

if ($action === 'google_login') {
    $idToken = $data['id_token'] ?? '';
    if (!$idToken) {
        echo json_encode(['success' => false, 'message' => 'Missing ID token.']);
        exit();
    }

    $url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . urlencode($idToken);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 200 || !$response) {
        echo json_encode(['success' => false, 'message' => 'Failed to verify Google ID token.']);
        exit();
    }

    $payload = json_decode($response, true);
    if (!isset($payload['email'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid Google ID token payload.']);
        exit();
    }

    $aud = $payload['aud'] ?? '';
    if (defined('GOOGLE_CLIENT_ID') && GOOGLE_CLIENT_ID !== 'YOUR_GOOGLE_CLIENT_ID.apps.googleusercontent.com') {
        if ($aud !== GOOGLE_CLIENT_ID) {
            echo json_encode(['success' => false, 'message' => 'Google Client ID mismatch.']);
            exit();
        }
    }

    $email = trim($payload['email']);
    $name = trim($payload['name'] ?? '');

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];

            echo json_encode([
                'success' => true,
                'message' => 'Welcome back!',
                'user' => [
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ]);
        } else {
            $placeholderPhone = '0000000000';
            $randomPassword = bin2hex(random_bytes(16));
            $hashedPassword = password_hash($randomPassword, PASSWORD_DEFAULT);
            $address = '';

            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password, address) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$name, $email, $placeholderPhone, $hashedPassword, $address]);

            $userId = $pdo->lastInsertId();

            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            echo json_encode([
                'success' => true,
                'message' => "Welcome to Zesto, {$name}!",
                'user' => [
                    'name' => $name,
                    'email' => $email
                ]
            ]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Google authentication failed: ' . $e->getMessage()]);
    }
    exit();
}

echo json_encode(['success' => false, 'message' => 'Unsupported action.']);
?>
