<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $subject = sanitizeInput($_POST['subject'] ?? '');
    $message = sanitizeInput($_POST['message'] ?? '');
    
    // Validate inputs
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'All fields are required'
        ]);
        exit();
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        exit();
    }
    
    $conn = getConnection();
    
    // Insert message into database
    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at, status) VALUES (?, ?, ?, ?, NOW(), 'unread')");
    $stmt->bind_param("ssss", $name, $email, $subject, $message);
    
    if ($stmt->execute()) {
        // Send email notification (optional)
        $to = SITE_EMAIL;
        $emailSubject = "New Contact Message: $subject";
        $emailBody = "Name: $name\n";
        $emailBody .= "Email: $email\n";
        $emailBody .= "Subject: $subject\n\n";
        $emailBody .= "Message:\n$message\n";
        $headers = "From: $email\r\n";
        $headers .= "Reply-To: $email
