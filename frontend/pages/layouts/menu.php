<?php
$menuData = [
    // DASHBOARD
    [
        'id' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => '<i class="fa-solid fa-house"></i>',
        'url' => 'dashboard',
        'role' => [1, 2, 3, 4, 5, 6, 7], // all roles
        'permissions' => [],
        'children' => [],
        'open' => ['dashboard']
    ],

    // COURSES
    [
        'id' => 'courses',
        'title' => 'Courses',
        'icon' => '<i class="fa-solid fa-book-open"></i>',
        'url' => '#',
        'role' => [1, 2, 3, 4, 5, 6, 7],
        'permissions' => ['manage_courses', 'view_courses'],
        'open' => ['courses', 'all_courses', 'add_course', 'classes', 'lectures'],
        'children' => [
            [
                'id' => 'all_courses',
                'title' => 'All Courses',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'courses',
                'role' => [1, 2, 3, 4, 5, 6, 7],
                'permissions' => ['manage_courses', 'view_courses'],
                'children' => []
            ],
            [
                'id' => 'add_course',
                'title' => 'Add Course',
                'icon' => '<i class="fa-solid fa-plus"></i>',
                'url' => 'add_course',
                'role' => [1, 2, 5],
                'permissions' => ['manage_courses'],
                'children' => []
            ],
            [
                'id' => 'my_courses',
                'title' => 'My Courses',
                'icon' => '<i class="fa-solid fa-book"></i>',
                'url' => 'my_courses',
                'role' => [1, 3, 6, 7], // student + parent
                'permissions' => ['view_courses'],
                'children' => []
            ]
        ]
    ],

    // LECTURES
    [
        'id' => 'lectures',
        'title' => 'Lectures',
        'icon' => '<i class="fa-solid fa-chalkboard-teacher"></i>',
        'url' => '#',
        'role' => [1, 2, 3, 5, 6, 7],
        'permissions' => ['manage_lectures', 'view_lectures'],
        'children' => [
            [
                'id' => 'all_lectures',
                'title' => 'All Lectures',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'lectures',
                'role' => [1, 2, 5],
                'permissions' => ['manage_lectures'],
                'children' => []
            ],
            [
                'id' => 'my_lectures',
                'title' => 'My Lectures',
                'icon' => '<i class="fa-solid fa-chalkboard"></i>',
                'url' => 'my_lectures',
                'role' => [1, 3, 6, 7],
                'permissions' => ['view_lectures'],
                'children' => []
            ]
        ]
    ],

    // EXAMS
    [
        'id' => 'exams',
        'title' => 'Exams',
        'icon' => '<i class="fa-solid fa-file-circle-check"></i>',
        'url' => '#',
        'role' => [1, 2, 3, 5, 6, 7],
        'permissions' => ['create_exam', 'view_exam', 'delete_exam', 'attempt_exam'],
        'open' => ['exams', 'create_exam', 'all_exams', 'question_bank', 'my_exams'],
        'children' => [
            [
                'id' => 'all_exams',
                'title' => 'All Exams',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'exams',
                'role' => [1, 2, 5],
                'permissions' => ['view_exam'],
                'children' => []
            ],
            [
                'id' => 'create_exam',
                'title' => 'Create Exam',
                'icon' => '<i class="fa-solid fa-plus"></i>',
                'url' => 'create_exam',
                'role' => [1, 2, 5],
                'permissions' => ['create_exam'],
                'children' => []
            ],
            [
                'id' => 'my_exams',
                'title' => 'My Exams',
                'icon' => '<i class="fa-solid fa-clipboard-list"></i>',
                'url' => 'my_exams',
                'role' => [1, 3, 6, 7],
                'permissions' => ['attempt_exam', 'view_exam'],
                'children' => []
            ]
        ]
    ],

    // RESULTS
    [
        'id' => 'results',
        'title' => 'Results',
        'icon' => '<i class="fa-solid fa-chart-simple"></i>',
        'url' => '#',
        'role' => [1, 2, 3, 6, 7],
        'permissions' => ['view_results', 'publish_results'],
        'open' => ['results', 'all_results', 'my_results'],
        'children' => [
            [
                'id' => 'all_results',
                'title' => 'All Results',
                'icon' => '<i class="fa-solid fa-list-check"></i>',
                'url' => 'results',
                'role' => [1, 2, 3],
                'permissions' => ['view_results'],
                'children' => []
            ],
            [
                'id' => 'my_results',
                'title' => 'My Results',
                'icon' => '<i class="fa-solid fa-user-check"></i>',
                'url' => 'my_results',
                'role' => [1, 3, 6, 7],
                'permissions' => ['view_results'],
                'children' => []
            ]
        ]
    ],

    // ATTENDANCE
    [
        'id' => 'attendance',
        'title' => 'Attendance',
        'icon' => '<i class="fa-solid fa-calendar-check"></i>',
        'url' => '#',
        'role' => [1, 2, 3, 6, 7],
        'permissions' => ['manage_attendance', 'view_attendance'],
        'children' => [
            [
                'id' => 'view_attendance',
                'title' => 'View Attendance',
                'icon' => '<i class="fa-solid fa-eye"></i>',
                'url' => 'attendance',
                'role' => [1, 2, 3, 6, 7],
                'permissions' => ['view_attendance'],
                'children' => []
            ],
            [
                'id' => 'mark_attendance',
                'title' => 'Mark Attendance',
                'icon' => '<i class="fa-solid fa-pen"></i>',
                'url' => 'mark_attendance',
                'role' => [1, 2],
                'permissions' => ['manage_attendance'],
                'children' => []
            ]
        ]
    ],

    // NOTIFICATIONS
    [
        'id' => 'notifications',
        'title' => 'Notifications',
        'icon' => '<i class="fa-solid fa-bell"></i>',
        'url' => 'notifications',
        'role' => [1, 2, 3, 4, 5, 6, 7],
        'permissions' => ['send_notification', 'view_notification'],
        'children' => []
    ],

    // USER MANAGEMENT
    [
        'id' => 'users',
        'title' => 'User Management',
        'icon' => '<i class="fa-solid fa-users"></i>',
        'url' => '#',
        'role' => [1, 2, 3], // only developers
        'permissions' => ['manage_users'],
        'open' => ['users', 'create_user'],
        'children' => [
            [
                'id' => 'users_list',
                'title' => 'All Users',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'users',
                'role' => [1, 2, 3],
                'permissions' => ['manage_users', 'manage_students', 'manage_teachers', 'manage_parents'],
                'children' => []
            ],
            [
                'id' => 'users_add',
                'title' => 'Add User',
                'icon' => '<i class="fa-solid fa-user-plus"></i>',
                'url' => 'add_user',
                'role' => [1, 2, 3],
                'permissions' => ['manage_users', 'manage_students', 'manage_teachers', 'manage_parents'],
                'children' => []
            ],
            [
                'id' => 'user_group',
                'title' => 'User Group',
                'icon' => '<i class="fa-solid fa-users"></i>',
                'url' => 'user_group',
                'role' => [1, 2, 3],
                'permissions' => ['manage_users'],
                'children' => []
            ],
        ]
    ],

    // REPORTS
    [
        'id' => 'reports',
        'title' => 'Reports',
        'icon' => '<i class="fa-solid fa-chart-line"></i>',
        'url' => '#',
        'role' => [1, 2, 3],
        'permissions' => ['view_reports'],
        'children' => [
            [
                'id' => 'exam_reports',
                'title' => 'Exam Reports',
                'icon' => '<i class="fa-solid fa-file-invoice"></i>',
                'url' => 'exam_reports',
                'role' => [1, 2, 3],
                'permissions' => ['view_reports'],
                'children' => []
            ],
            [
                'id' => 'student_performance',
                'title' => 'Student Performance',
                'icon' => '<i class="fa-solid fa-chart-pie"></i>',
                'url' => 'student_performance',
                'role' => [1, 2, 3],
                'permissions' => ['view_reports'],
                'children' => []
            ]
        ]
    ],

    // PROFILE
    [
        'id' => 'profile',
        'title' => 'Profile',
        'icon' => '<i class="fa-solid fa-user"></i>',
        'url' => 'profile',
        'role' => [1, 2, 3, 4, 5, 6, 7],
        'permissions' => ['profile'],
        'children' => []
    ],

    // SETTINGS FOR TECHNICAL SUPPORT
    [
        'id' => 'settings',
        'title' => 'Settings',
        'icon' => '<i class="fa-solid fa-gears"></i>',
        'url' => 'settings',
        'role' => [1],
        'permissions' => ['manage_settings'],
        'children' => []
    ],
];

renderMenuOptions($menuData, $collapse);
?>