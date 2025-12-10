<?php
// 🔹 Base & Authentication
Router::get('/', 'PageAPI@dashboard', 'home', );
Router::get('/login', 'PageAPI@login', 'login');

Router::group(['middleware' => ['auth']], function () {

    // 🔹 404 Page Not Found
    Router::get('/404', 'PageAPI@notFound', 'not_found');

    // 🔹 Dashboard
    Router::get('/dashboard', 'PageAPI@dashboard', 'dashboard');

    // 🔹 Courses
    Router::get('/courses', 'PageAPI@courses', 'all_courses');
    Router::get('/add_course', 'PageAPI@addCourse', 'add_course');
    Router::get('/my_courses', 'PageAPI@myCourses', 'my_courses');

    // 🔹 Lectures
    Router::get('/lectures', 'PageAPI@lectures', 'all_lectures');
    Router::get('/my_lectures', 'PageAPI@myLectures', 'my_lectures');

    // 🔹 Exams
    Router::group(['prefix' => 'exam'], function () {
        Router::get('/all', 'PageAPI@exams', 'all_exams');
        Router::get('/create', 'PageAPI@createExam', 'create_exam');
        Router::get('/edit/{exam_id}', 'PageAPI@editExam', 'edit_exam');
        Router::get('/my/{user_id}', 'PageAPI@myExams', 'my_exams');
        Router::get('/preview/{exam_id}', 'PageAPI@previewExam', 'exam');
        Router::get('/attempt/{hased_rest_url}', 'PageAPI@attemptExam', 'attempt_exam');
    });

    // 🔹 Questions
    Router::get('/questions', 'PageAPI@questionBank', 'question_bank');
    Router::get('/my_questions', 'PageAPI@myQuestions', 'my_questions');
    Router::get('/create_questions', 'PageAPI@createQuestions', 'create_questions');

    // 🔹 Past Papers
    Router::get('/past_papers', 'PageAPI@pastPapers', 'past_papers');

    // 🔹 Results
    Router::get('/results', 'PageAPI@results', 'all_results');
    Router::get('/my_results', 'PageAPI@myResults', 'my_results');

    // 🔹 Attendance
    Router::get('/attendance', 'PageAPI@attendance', 'view_attendance');
    Router::get('/mark_attendance', 'PageAPI@markAttendance', 'mark_attendance');
    Router::get('/my_attendance', 'PageAPI@myAttendance', 'my_attendance');

    // 🔹 Notifications
    Router::get('/notifications', 'PageAPI@notifications', 'notifications');

    // 🔹 User Management
    Router::get('/users', 'PageAPI@users', 'users_list');
    Router::get('/add_user', 'PageAPI@addUser', 'users_add');
    Router::get('/user_group', 'PageAPI@userGroup', 'user_group');

    // 🔹 Reports
    Router::get('/exam_reports', 'PageAPI@examReports', 'exam_reports');
    Router::get('/student_performance', 'PageAPI@studentPerformance', 'student_performance');

    // 🔹 Settings
    Router::get('/settings', 'PageAPI@settings', 'settings');
});

Router::group(['prefix' => 'API'], function () {
    Router::post('/login', 'AuthAPI@login');
    Router::post('/logout', 'AuthAPI@logout');
    Router::post('/user_groups', 'UserGroupAPI@createUserGroup', 'create_user_group', ['auth']);
    Router::post('/user', 'UserAPI@createUser', 'create_user', ['auth']);
    Router::post('/exams/basic_info/save', 'ExamAPI@saveExamBasicInfo', 'save_basic_info', ['auth']);
    Router::post('/exams/basic_info/{id}', 'ExamAPI@editExamBasicInfo', 'edit_basic_info', ['auth']);
    Router::post('/exams/settings', 'ExamAPI@saveExamSettings', 'save_exam_settings', ['auth']);
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
    Router::get('/exam/data/{id}', 'ExamAPI@getExamDataForPreview', 'get_exam', ['auth']);

    Router::put('/user_groups/{id}/permissions', 'UserGroupAPI@setPermissions', 'set_group_permissions', ['auth']);
    Router::post('/user_groups/{id}', 'UserGroupAPI@updateUserGroup', 'update_user_group', ['auth']);
    Router::patch('/questions/remove/{id}', 'QuestionAPI@removeQuestionFromExam', 'remove_question', ['auth']);
    Router::post('/exams/settings/{id}', 'ExamAPI@editExamSettings', 'edit_exam_settings', ['auth']);
    Router::post('/publish_exam/{id}', 'ExamAPI@publishExam', 'publish_exam', ['auth']);
    Router::post('/unpublish_exam/{id}', 'ExamAPI@unpublishExam', 'unpublish_exam', ['auth']);
    Router::post('/cancel_exam/{id}', 'ExamAPI@cancelExam', 'cancel_exam', ['auth']);

    Router::delete('/user_groups/{id}', 'UserGroupAPI@deleteUserGroup', 'delete_user_group', ['auth']);
    Router::delete('/questions/delete_question/{id}', 'QuestionAPI@deleteQuestion', 'delete_question', ['auth']);
    Router::delete('/sections/delete/{id}', 'SectionAPI@deleteSection', 'delete_section', ['auth']);
});

Router::group(['prefix' => 'modal'], function () {
    Router::get('/{modal}', 'ModalAPI@getModal');
});