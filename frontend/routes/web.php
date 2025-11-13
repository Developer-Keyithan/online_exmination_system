<?php
// 🔹 Base & Authentication
Router::get('/', 'PageAPI@dashboard', 'home', );
Router::get('/login', 'PageAPI@login', 'login');

Router::group(['middleware' => ['auth']], function () {

    // 🔹 Dashboard
    Router::get('/dashboard', 'PageAPI@dashboard', 'dashboard', ['auth']);

    // 🔹 Courses
    Router::get('/courses', 'PageAPI@courses', 'all_courses', ['auth']);
    Router::get('/add_course', 'PageAPI@addCourse', 'add_course', ['auth']);
    Router::get('/my_courses', 'PageAPI@myCourses', 'my_courses', ['auth']);

    // 🔹 Lectures
    Router::get('/lectures', 'PageAPI@lectures', 'all_lectures', ['auth']);
    Router::get('/my_lectures', 'PageAPI@myLectures', 'my_lectures', ['auth']);
    
    // 🔹 Exams
    Router::get('/exams', 'PageAPI@exams', 'all_exams', ['auth']);
    Router::get('/create_exam', 'PageAPI@createExam', 'create_exam', ['auth']);
    Router::get('/my_exams', 'PageAPI@myExams', 'my_exams', ['auth']);

    // 🔹 Qestions
    Router::get('/questions', 'PageAPI@questionBank', 'question_bank', ['auth']);
    Router::get('/question_bank', 'PageAPI@questionBank', 'question_bank', ['auth']);
    Router::get('/my_questions', 'PageAPI@myQuestions', 'my_questions', ['auth']);
    Router::get('/create_questions', 'PageAPI@createQuestions', 'create_questions', ['auth']);

    // 🔹 Past-Papers
    Router::get('/past_papers', 'PageAPI@pastPapers', 'past_papers', ['auth']);

    // 🔹 Results
    Router::get('/results', 'PageAPI@results', 'all_results', ['auth']);
    Router::get('/my_results', 'PageAPI@myResults', 'my_results', ['auth']);

    // 🔹 Attendance
    Router::get('/attendance', 'PageAPI@attendance', 'view_attendance', ['auth']);
    Router::get('/mark_attendance', 'PageAPI@markAttendance', 'mark_attendance', ['auth']);
    Router::get('/my_attendance', 'PageAPI@myAttendance', 'my_attendance', ['auth']);

    // 🔹 Notifications
    Router::get('/notifications', 'PageAPI@notifications', 'notifications', ['auth']);

    // 🔹 User Management
    Router::get('/users', 'PageAPI@users', 'users_list', ['auth']);
    Router::get('/add_user', 'PageAPI@addUser', 'users_add', ['auth']);
    Router::get('/user_group', 'PageAPI@userGroup', 'user_group', ['auth']);

    // 🔹 Reports
    Router::get('/exam_reports', 'PageAPI@examReports', 'exam_reports', ['auth']);
    Router::get('/student_performance', 'PageAPI@studentPerformance', 'student_performance', ['auth']);

    // 🔹 Settings
    Router::get('/settings', 'PageAPI@settings', 'settings', ['auth']);
    // Router::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
//     Router::get('/dashboard', 'DashboardAPI@dashboard', 'dashboard');
// });
});
Router::group(['prefix' => 'API'], function() {
    Router::post('/login', 'AuthAPI@login');
    Router::post('/logout', 'AuthAPI@logout');
});

Router::group(['prefix' => 'model'], function() {
    Router::get('/{model}', 'ModelAPI@getModel');
});