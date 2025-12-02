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
    // Router::get('/dashboard', 'DashboardAPI@dashboard', 'dashboard');
});

Router::group(['prefix' => 'API'], function () {
    Router::post('/login', 'AuthAPI@login');
    Router::post('/logout', 'AuthAPI@logout');
    Router::post('/user_groups', 'UserGroupAPI@createUserGroup', 'create_user_group', ['auth']);
    Router::post('/user', 'UserAPI@createUser', 'create_user', ['auth']);
    Router::post('/exams/basic_info/save', 'ExamAPI@saveExamBasicInfo', 'save_basic_info', ['auth']);
    Router::post('/exams/basic_info/{id}', 'ExamAPI@editExamBasicInfo', 'edit_basic_info', ['auth']);
    Router::post('/questions/add_question', 'QuestionAPI@addQuestion', 'save_question', ['auth']);
    Router::post('/questions/edit_question/{id}', 'QuestionAPI@editQuestion', 'edit_question', ['auth']);
    Router::post('/questions/assign_to_section/{id}', 'QuestionAPI@assignQuestionToSection', 'assign_question_to_section', ['auth']);
    Router::post('/questions/unassign_section/{id}', 'QuestionAPI@unassignSection', 'assign_question_to_section', ['auth']);
    Router::post('/sections/add', 'SectionAPI@addSection', 'add_section', ['auth']);
    Router::post('/sections/edit/{id}', 'SectionAPI@updateSection', 'adit_section', ['auth']);

    Router::get('/users', 'UserAPI@getAllUsersHandler', 'get_all_users', ['auth']);
    Router::get('/user_groups', 'UserGroupAPI@getAllGroups', 'get_all_users_groups', ['auth']);
    Router::get('/user_groups/{id}', 'UserGroupAPI@createUserGroup', 'create_user_group', ['auth']);
    Router::get('/user_groups/{id}/permissions', 'UserGroupAPI@getGroupPermissions', 'get_group_permissions', ['auth']);
    Router::get('/auth/logged_user', 'UserAPI@getLoggedUserAccesses', 'get_logged_user_accesses', ['auth']);
    Router::get('/exams/{id}', 'ExamAPI@getExamData', 'get_exam', ['auth']);

    Router::put('/user_groups/{id}/permissions', 'UserGroupAPI@setPermissions', 'set_group_permissions', ['auth']);
    Router::post('/user_groups/{id}', 'UserGroupAPI@updateUserGroup', 'update_user_group', ['auth']);
    Router::patch('/questions/remove/{id}', 'QuestionAPI@removeQuestionFromExam',  'remove_question', ['auth']);

    Router::delete('/user_groups/{id}', 'UserGroupAPI@deleteUserGroup', 'delete_user_group', ['auth']);
    Router::delete('/questions/delete_question/{id}', 'QuestionAPI@deleteQuestion', 'delete_question', ['auth']);
    Router::delete('/sections/delete/{id}', 'SectionAPI@deleteSection', 'delete_section', ['auth']);
});

Router::group(['prefix' => 'modal'], function () {
    Router::get('/{modal}', 'ModalAPI@getModal');
});