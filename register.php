<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['fullName'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';
    $role = sanitizeInput($_POST['role'] ?? 'student');
    
    // Validate inputs
    $errors = [];
    
    if (empty($fullName)) {
        $errors[] = 'Full name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($username)) {
        $errors[] = 'Username is required';
    }
    
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    $conn = getConnection();
    
    // Check if username or email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo json_encode([
            'success' => false,
            'message' => 'Username or email already exists'
        ]);
        exit();
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, username, password, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
    $stmt->bind_param("sssss", $fullName, $email, $username, $hashedPassword, $role);
    
    if ($stmt->execute()) {
        $userId = $stmt->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['username'] = $username;
        $_SESSION['full_name'] = $fullName;
        $_SESSION['user_role'] = $role;
        
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful',
            'user' => [
                'id' => $userId,
                'name' => $fullName,
                'role' => $role
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error creating account: ' . $conn->error
        ]);
    }
    
    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>
