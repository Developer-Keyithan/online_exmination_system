<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

ob_start(); // ✅ output buffer ஆரம்பம்
include __DIR__ . '/../product.php'; // form HTML சேர்க்கும் file
$tableHtml = ob_get_clean(); // ✅ HTML capture செய்க
echo json_encode([
'success' => true,
'html' => $tableHtml,
'count' => count($products),
'timestamp' => date('Y-m-d H:i:s')
]);
?>