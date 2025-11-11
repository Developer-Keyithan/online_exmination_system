<?php
header('Content-Type: application/json');

$products = [
    [
        'id' => 1,
        'name' => 'Laptop Pro',
        'sku' => 'LP-001',
        'category' => 'Electronics',
        'price' => 1299.99,
        'stock' => 15,
        'max_stock' => 50,
        'rating' => 4.5,
        'icon' => 'fa-laptop'
    ],
    [
        'id' => 2,
        'name' => 'Wireless Mouse',
        'sku' => 'WM-002',
        'category' => 'Accessories',
        'price' => 29.99,
        'stock' => 42,
        'max_stock' => 100,
        'rating' => 4.2,
        'icon' => 'fa-mouse'
    ]
];

echo json_encode($products);
?>