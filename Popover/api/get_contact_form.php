<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ob_start(); // ✅ output buffer ஆரம்பம்
include __DIR__ . '/../contact_form.php'; // form HTML சேர்க்கும் file
$formHtml = ob_get_clean(); // ✅ HTML capture செய்க

echo json_encode([
    'success' => true,
    'html' => $formHtml
]);
?>