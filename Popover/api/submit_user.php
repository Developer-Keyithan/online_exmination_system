<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

// Simulate form validation and database insertion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $input['fullName'] ?? '';
    $email = $input['email'] ?? '';
    $phone = $input['phone'] ?? '';
    $department = $input['department'] ?? '';
    $role = $input['role'] ?? '';
    $agreeTerms = isset($input['agreeTerms']);
    
    // Basic validation
    if (empty($fullName) || empty($email) || empty($department) || empty($role) || !$agreeTerms) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill all required fields and agree to terms.'
        ]);
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please enter a valid email address.'
        ]);
        exit;
    }
    
    // Simulate successful registration
    echo json_encode([
        'success' => true,
        'message' => "User {$fullName} has been successfully registered in the {$department} department as {$role}.",
        'data' => [
            'fullName' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'department' => $department,
            'role' => $role,
            'registrationDate' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>