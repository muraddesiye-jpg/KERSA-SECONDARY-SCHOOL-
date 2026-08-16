<?php
require_once 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Personal Information
    $firstName = sanitizeInput($_POST['firstName'] ?? '');
    $middleName = sanitizeInput($_POST['middleName'] ?? '');
    $lastName = sanitizeInput($_POST['lastName'] ?? '');
    $gender = sanitizeInput($_POST['gender'] ?? '');
    $dob = sanitizeInput($_POST['dob'] ?? '');
    $grade = sanitizeInput($_POST['grade'] ?? '');
    
    // Parent/Guardian Information
    $parentName = sanitizeInput($_POST['parentName'] ?? '');
    $relationship = sanitizeInput($_POST['relationship'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    
    // Previous Education
    $prevSchool = sanitizeInput($_POST['prevSchool'] ?? '');
    $prevGrade = sanitizeInput($_POST['prevGrade'] ?? '');
    $additionalInfo = sanitizeInput($_POST['additionalInfo'] ?? '');
    
    // Validate required fields
    $requiredFields = [
        'firstName' => $firstName,
        'lastName' => $lastName,
        'gender' => $gender,
        'dob' => $dob,
        'grade' => $grade,
        'parentName' => $parentName,
        'relationship' => $relationship,
        'phone' => $phone,
        'prevSchool' => $prevSchool,
        'prevGrade' => $prevGrade
    ];
    
    $errors = [];
    
    foreach ($requiredFields as $field => $value) {
        if (empty($value)) {
            $errors[] = "$field is required";
        }
    }
    
    if (!empty($errors)) {
        echo json_encode([
            'success' => false,
            'message' => implode(', ', $errors)
        ]);
        exit();
    }
    
    // Validate phone number
    if (!preg_match('/^[0-9+\-\s]+$/', $phone)) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid phone number format'
        ]);
        exit();
    }
    
    $conn = getConnection();
    
    // Generate application number
    $applicationNo = 'APP' . date('Y') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Insert application
    $stmt = $conn->prepare("INSERT INTO admissions (application_no, first_name, middle_name, last_name, gender, date_of_birth, applying_for_grade, parent_name, relationship, phone, email, previous_school, previous_grade, additional_info, application_date, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'pending')");
    
    $stmt->bind_param(
        "ssssssssssssss",
        $applicationNo,
        $firstName,
        $middleName,
        $lastName,
        $gender,
        $dob,
        $grade,
        $parentName,
        $relationship,
        $phone,
        $email,
        $prevSchool,
        $prevGrade,
        $additionalInfo
    );
    
    if ($stmt->execute()) {
        $applicationId = $stmt->insert_id;
        
        echo json_encode([
            'success' => true,
            'message' => 'Application submitted successfully',
            'application_no' => $applicationNo,
            'application_id' => $applicationId
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error submitting application: ' . $conn->error
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
