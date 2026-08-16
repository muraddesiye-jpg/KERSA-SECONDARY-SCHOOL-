<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter username and password'
        ]);
        exit();
    }
    
    $conn = getConnection();
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT id, username, password, full_name, role, status FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['status'] === 'active') {
            if (password_verify($password, $row['password'])) {
                // Set session variables
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['full_name'] = $row['full_name'];
                $_SESSION['user_role'] = $row['role'];
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Login successful',
                    'user' => [
                        'id' => $row['id'],
                        'name' => $row['full_name'],
                        'role' => $row['role']
                    ]
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Invalid password'
                ]);
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Account is inactive. Please contact administrator.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'User not found'
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
