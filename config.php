<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'kersa_school_db');

// Create connection
function getConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Set charset to UTF-8
    $conn->set_charset("utf8mb4");
    
    return $conn;
}

// Site configuration
define('SITE_NAME', 'Kersa Secondary School');
define('SITE_EMAIL', 'info@kersaschool.edu.et');
define('SITE_PHONE', '+251 911 234 567');
define('SITE_ADDRESS', 'Kersa District, Oromia Region, Ethiopia');

// Session configuration
session_start();

// Helper functions
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: login.html');
        exit();
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? null;
}

function isAdmin() {
    return getUserRole() === 'admin';
}

function isTeacher() {
    return getUserRole() === 'teacher';
}

function isStudent() {
    return getUserRole() === 'student';
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function displayMessage($message, $type = 'info') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

function getMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
