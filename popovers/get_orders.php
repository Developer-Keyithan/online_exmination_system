<?php
header('Content-Type: application/json');

$status = $_GET['status'] ?? 'all';

$orders = [
    [
        'id' => 1001,
        'customer' => 'John Doe',
        'amount' => 299.99,
        'status' => 'completed',
        'date' => '2023-10-15'
    ],
    [
        'id' => 1002,
        'customer' => 'Jane Smith',
        'amount' => 149.50,
        'status' => 'pending',
        'date' => '2023-10-16'
    ]
];

// Filter by status if provided
if ($status !== 'all') {
    $orders = array_filter($orders, function($order) use ($status) {
        return $order['status'] === $status;
    });
    $orders = array_values($orders); // Reindex array
}

echo json_encode($orders);
?>