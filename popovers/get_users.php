<?php
header('Content-Type: application/json');

// Simulate database data
$users = [
    [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john.doe@example.com',
        'role' => 'Admin',
        'status' => 'active',
        'joined_date' => '2023-01-15'
    ],
    [
        'id' => 2,
        'name' => 'Jane Smith',
        'email' => 'jane.smith@example.com',
        'role' => 'User',
        'status' => 'active',
        'joined_date' => '2023-02-20'
    ],
    [
        'id' => 3,
        'name' => 'Bob Johnson',
        'email' => 'bob.johnson@example.com',
        'role' => 'Moderator',
        'status' => 'inactive',
        'joined_date' => '2023-03-10'
    ]
];

echo json_encode($users);
?>