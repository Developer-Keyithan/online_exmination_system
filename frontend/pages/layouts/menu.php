<?php
$menuData = [

    // DASHBOARD SECTION
    [
        'id' => 'dashboard',
        'title' => 'Dashboard',
        'icon' => '<i class="fa-solid fa-house"></i>',
        'url' => 'dashboard',
        'role' => [1,2,3,4,5,6,7],
        'permissions' => ['dashboard.view'],
        'children' => [],
        'open' => ['dashboard']
    ],

    // COURSES SECTION
    // [
    //     'id' => 'courses',
    //     'title' => 'Courses',
    //     'icon' => '<i class="fa-solid fa-book-open"></i>',
    //     'url' => '#',
    //     'role' => [1,2,3,4,5,6,7],
    //     'permissions' => ['courses.manage','courses.view','courses.add','courses.my_courses'],
    //     'children' => [
    //         [
    //             'id' => 'all_courses',
    //             'title' => 'All Courses',
    //             'icon' => '<i class="fa-solid fa-list"></i>',
    //             'url' => 'courses',
    //             'role' => [1,2,3,4,5,6,7],
    //             'permissions' => ['courses.manage','courses.view'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'add_course',
    //             'title' => 'Add Course',
    //             'icon' => '<i class="fa-solid fa-plus"></i>',
    //             'url' => 'add_course',
    //             'role' => [1,2,4],
    //             'permissions' => ['courses.manage','courses.add'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'my_courses',
    //             'title' => 'My Courses',
    //             'icon' => '<i class="fa-solid fa-book"></i>',
    //             'url' => 'my_courses',
    //             'role' => [1,3,5,6,7],
    //             'permissions' => ['courses.my_courses'],
    //             'children' => []
    //         ]
    //     ]
    // ],

    // LECTURES SECTION
    // [
    //     'id' => 'lectures',
    //     'title' => 'Lectures',
    //     'icon' => '<i class="fa-solid fa-chalkboard-teacher"></i>',
    //     'url' => '#',
    //     'role' => [1,2,3,4,6,7],
    //     'permissions' => ['lectures.manage','lectures.view','lectures.all','lectures.my'],
    //     'children' => [
    //         [
    //             'id' => 'all_lectures',
    //             'title' => 'All Lectures',
    //             'icon' => '<i class="fa-solid fa-list"></i>',
    //             'url' => 'lectures',
    //             'role' => [1,2,4],
    //             'permissions' => ['lectures.manage','lectures.all'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'my_lectures',
    //             'title' => 'My Lectures',
    //             'icon' => '<i class="fa-solid fa-chalkboard"></i>',
    //             'url' => 'my_lectures',
    //             'role' => [1,3,6,7],
    //             'permissions' => ['lectures.view','lectures.my'],
    //             'children' => []
    //         ]
    //     ]
    // ],

    // EXAMS SECTION
    [
        'id' => 'exams',
        'title' => 'Exams',
        'icon' => '<i class="fa-solid fa-file-circle-check"></i>',
        'url' => '#',
        'role' => [1,2,3,4,5,6,7],
        'permissions' => ['exams.create','exams.view','exams.delete','exams.attempt','exams.all','exams.my'],
        'children' => [
            [
                'id' => 'all_exams',
                'title' => 'All Exams',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'exam/all',
                'role' => [1,2,3,4,5],
                'permissions' => ['exams.view','exams.all'],
                'children' => []
            ],
            [
                'id' => 'create_exam',
                'title' => 'Create Exam',
                'icon' => '<i class="fa-solid fa-plus"></i>',
                'url' => 'exam/create',
                'role' => [1,5],
                'permissions' => ['exams.create'],
                'children' => []
            ],
            [
                'id' => 'my_exams',
                'title' => 'My Exams',
                'icon' => '<i class="fa-solid fa-clipboard-list"></i>',
                'url' => 'exam/my',
                'role' => [1,5,6,7],
                'permissions' => ['exams.my'],
                'children' => []
            ]
        ]
    ],

    // // QUESTIONS SECTION
    // [
    //     'id' => 'questions',
    //     'title' => 'Questions',
    //     'icon' => '<i class="fa-solid fa-book-bookmark"></i>',
    //     'url' => '#',
    //     'role' => [1,2,3,4,5],
    //     'permissions' => ['questions.create','questions.view','questions.edit','questions.delete','questions.bank','questions.my'],
    //     'children' => [
    //         [
    //             'id' => 'question_bank',
    //             'title' => 'Question Bank',
    //             'icon' => '<i class="fa-solid fa-book"></i>',
    //             'url' => 'question_bank',
    //             'role' => [1,2,3,4,5],
    //             'permissions' => ['questions.bank'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'create_questions',
    //             'title' => 'Create Question',
    //             'icon' => '<i class="fa-solid fa-plus"></i>',
    //             'url' => 'create_questions',
    //             'role' => [1,5],
    //             'permissions' => ['questions.create'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'my_questions',
    //             'title' => 'My Questions',
    //             'icon' => '<i class="fa-solid fa-book-open-reader"></i>',
    //             'url' => 'my_questions',
    //             'role' => [1,5],
    //             'permissions' => ['questions.my'],
    //             'children' => []
    //         ]
    //     ]
    // ],

    // PAST PAPERS SECTION
    // [
    //     'id' => 'past_papers',
    //     'title' => 'Past Papers',
    //     'icon' => '<i class="fa-solid fa-file-lines"></i>',
    //     'url' => 'past_papers',
    //     'role' => [1,2,3,4,5,6,7],
    //     'permissions' => ['past_papers.view'],
    //     'children' => []
    // ],

    // RESULTS SECTION
    [
        'id' => 'results',
        'title' => 'Results',
        'icon' => '<i class="fa-solid fa-chart-simple"></i>',
        'url' => '#',
        'role' => [1,2,3,4,5,6,7],
        'permissions' => ['results.view','results.publish','results.all','results.my'],
        'children' => [
            [
                'id' => 'all_results',
                'title' => 'All Results',
                'icon' => '<i class="fa-solid fa-list-check"></i>',
                'url' => 'result/all',
                'role' => [1,2,3,4,5],
                'permissions' => ['results.view','results.all'],
                'children' => []
            ],
            [
                'id' => 'my_results',
                'title' => 'My Results',
                'icon' => '<i class="fa-solid fa-user-check"></i>',
                'url' => 'result/my',
                'role' => [1,6,7],
                'permissions' => ['results.my'],
                'children' => []
            ]
        ]
    ],

    // ATTENDANCE SECTION
    // [
    //     'id' => 'attendance',
    //     'title' => 'Attendance',
    //     'icon' => '<i class="fa-solid fa-calendar-check"></i>',
    //     'url' => '#',
    //     'role' => [1,2,3,4,5,6,7],
    //     'permissions' => ['attendance.manage','attendance.view','attendance.mark','attendance.my'],
    //     'children' => [
    //         [
    //             'id' => 'view_attendance',
    //             'title' => 'View Attendance',
    //             'icon' => '<i class="fa-solid fa-eye"></i>',
    //             'url' => 'attendance',
    //             'role' => [1,2,3,4,5],
    //             'permissions' => ['attendance.view'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'mark_attendance',
    //             'title' => 'Mark Attendance',
    //             'icon' => '<i class="fa-solid fa-pen"></i>',
    //             'url' => 'mark_attendance',
    //             'role' => [1,5],
    //             'permissions' => ['attendance.mark'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'my_attendance',
    //             'title' => 'My Attendance',
    //             'icon' => '<i class="fa-solid fa-user"></i>',
    //             'url' => 'my_attendance',
    //             'role' => [1,6,7],
    //             'permissions' => ['attendance.my'],
    //             'children' => []
    //         ]
    //     ]
    // ],

    // NOTIFICATIONS SECTION
    // [
    //     'id' => 'notifications',
    //     'title' => 'Notifications',
    //     'icon' => '<i class="fa-solid fa-bell"></i>',
    //     'url' => 'notifications',
    //     'role' => [1,2,3,4,5,6,7],
    //     'permissions' => ['notifications.view'],
    //     'children' => []
    // ],

    // USER MANAGEMENT SECTION
    [
        'id' => 'users',
        'title' => 'User Management',
        'icon' => '<i class="fa-solid fa-users"></i>',
        'url' => '#',
        'role' => [1,2,3,4],
        'permissions' => ['users.manage','users.create','users.edit','users.delete','users.view','students.manage','teachers.manage','parents.manage','groups.manage'],
        'children' => [
            [
                'id' => 'users_list',
                'title' => 'All Users',
                'icon' => '<i class="fa-solid fa-list"></i>',
                'url' => 'users',
                'role' => [1,2,3],
                'permissions' => ['users.manage','students.manage','teachers.manage','parents.manage'],
                'children' => []
            ],
            [
                'id' => 'users_add',
                'title' => 'Add User',
                'icon' => '<i class="fa-solid fa-user-plus"></i>',
                'url' => 'add_user',
                'role' => [1,2,3,4],
                'permissions' => ['users.create','students.manage','teachers.manage','parents.manage'],
                'children' => []
            ],
            [
                'id' => 'user_group',
                'title' => 'User Group',
                'icon' => '<i class="fa-solid fa-user-group"></i>',
                'url' => 'user_group',
                'role' => [1],
                'permissions' => ['groups.manage'],
                'children' => []
            ]
        ]
    ],

    // REPORTS SECTION
    // [
    //     'id' => 'reports',
    //     'title' => 'Reports',
    //     'icon' => '<i class="fa-solid fa-chart-line"></i>',
    //     'url' => '#',
    //     'role' => [1,2,3,4,5],
    //     'permissions' => ['reports.view','reports.exam','reports.performance'],
    //     'children' => [
    //         [
    //             'id' => 'exam_reports',
    //             'title' => 'Exam Reports',
    //             'icon' => '<i class="fa-solid fa-file-invoice"></i>',
    //             'url' => 'exam_reports',
    //             'role' => [1,2,3,4,5],
    //             'permissions' => ['reports.exam'],
    //             'children' => []
    //         ],
    //         [
    //             'id' => 'student_performance',
    //             'title' => 'Student Performance',
    //             'icon' => '<i class="fa-solid fa-chart-pie"></i>',
    //             'url' => 'student_performance',
    //             'role' => [1,2,3,4,5],
    //             'permissions' => ['reports.performance'],
    //             'children' => []
    //         ]
    //     ]
    // ],

    // PROFILE SECTION
    [
        'id' => 'profile',
        'title' => 'Profile',
        'icon' => '<i class="fa-solid fa-user"></i>',
        'url' => 'profile',
        'role' => [1,2,3,4,5,6,7],
        'permissions' => ['profile.view','profile.edit'],
        'children' => []
    ],

    // SETTINGS SECTION
    [
        'id' => 'settings',
        'title' => 'Settings',
        'icon' => '<i class="fa-solid fa-gears"></i>',
        'url' => 'settings',
        'role' => [1],
        'permissions' => [],
        'children' => []
    ],

];

renderMenuOptions($menuData, $collapse);
?>