<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$input = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $input['firstName'] ?? '';
    $lastName = $input['lastName'] ?? '';
    $email = $input['contactEmail'] ?? '';
    $subject = $input['subject'] ?? '';
    $message = $input['message'] ?? '';
    $priority = $input['priority'] ?? 'medium';
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($subject) || empty($message)) {
        echo json_encode([
            'success' => false,
            'message' => 'Please fill all required fields.'
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
    
    // Simulate successful message submission
    echo json_encode([
        'success' => true,
        'message' => "Thank you {$firstName} {$lastName}! Your message about '{$subject}' has been received. We'll contact you at {$email} within 24 hours.",
        'data' => [
            'ticketId' => 'TKT-' . rand(1000, 9999),
            'priority' => $priority,
            'submissionTime' => date('Y-m-d H:i:s')
        ]
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
}
?>