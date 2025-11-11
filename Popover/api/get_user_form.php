<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

ob_start(); // ✅ output buffer ஆரம்பம்
include __DIR__ . '/../user.php'; // form HTML சேர்க்கும் file
$formHtml = ob_get_clean(); // ✅ HTML capture செய்க

echo json_encode([
    'success' => true,
    'html' => $formHtml,
    'timestamp' => date('Y-m-d H:i:s')
]);