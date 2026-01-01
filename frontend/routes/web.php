<?php
// 🔹 Base & Authentication
Router::get('/', 'PageAPI@dashboard', 'home',);
Router::get('/login', 'PageAPI@login', 'login');

Router::group(['middleware' => ['auth']], function () {

    // 🔹 404 Page Not Found
    Router::get('/404', 'PageAPI@notFound', 'not_found');

    // 🔹 403 Forbidden
    Router::get('/forbidden', 'PageAPI@forbidden', 'forbidden');

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
        Router::get('/my', 'PageAPI@myExams', 'my_exams');
        Router::get('/preview/{exam_id}', 'PageAPI@previewExam', 'exam');
        Router::get('/attempt/{id}/register', 'PageAPI@examAttemptRegister', 'exam_register_for_students', ['auth']);
        Router::get('/{hased_rest_url}/attempt/{id}', 'PageAPI@attemptExam', 'attempt_exam');
        Router::get('/results/{exam_id}', 'PageAPI@examResults', 'exam_results');
    });

    // 🔹 Questions
    Router::get('/questions', 'PageAPI@questionBank', 'question_bank');
    Router::get('/my_questions', 'PageAPI@myQuestions', 'my_questions');
    Router::get('/create_questions', 'PageAPI@createQuestions', 'create_questions');
    Router::get('/question_bank', 'PageAPI@questionBank', 'question_bank');

    // 🔹 Past Papers
    Router::get('/past_papers', 'PageAPI@pastPapers', 'past_papers');

    // 🔹 Results
    Router::get('/result/all', 'PageAPI@results', 'all_results');
    Router::get('/result/my', 'PageAPI@myResults', 'my_results');
    Router::get('/result/review/{attempt_id}/{exam_id}/{student_id}', 'PageAPI@examResultsReview', 'exam_results');

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

    // 🔹 Profile
    Router::get('/profile', 'PageAPI@profile', 'profile');

    // 🔹 Settings
    Router::get('/settings', 'PageAPI@settings', 'settings');

    // 🔹 Reset password
    Router::get('/reset-password/{token}', 'PageAPI@resetPassword', 'reset_password');

    // 🔹 forgot password
    Router::get('/forgot-password', 'PageAPI@forgotPassword', 'forgot_password');
});

Router::group(['prefix' => 'API'], function () {
    Router::post('/login', 'AuthAPI@login');
    Router::post('/logout', 'AuthAPI@logout');
    Router::post('/user_groups', 'UserGroupAPI@createUserGroup', 'create_user_group', ['auth']);
    Router::post('/user', 'UserAPI@createUser', 'create_user', ['auth']);

    Router::post('/exams/basic_info/save', 'ExamAPI@saveExamBasicInfo', 'save_basic_info', ['auth']);
    Router::post('/exams/basic_info/{id}', 'ExamAPI@editExamBasicInfo', 'edit_basic_info', ['auth']);
    Router::post('/exams/settings', 'ExamAPI@saveExamSettings', 'save_exam_settings', ['auth']);
    Router::post('/exam/registration/{id}', 'ExamAPI@saveExamRegistrationData', 'save_exam_registration_data', ['auth']);
    Router::post('/exam/submit/{exam_id}/{attempt_id}', 'ExamAPI@submitExam', 'submit_exam', ['auth']);
    Router::post('/exam/{exam_id}/attempt/{attempt_id}/question/{question_id}/answer', 'ExamAPI@saveExamAnswer', 'save_exam_answer', ['auth']);
    Router::post('/exam/register', 'ExamAPI@registerExam', 'register_exam', ['auth']);

    Router::post('/sections/edit/{id}', 'SectionAPI@updateSection', 'adit_section', ['auth']);
    Router::post('/sections/add', 'SectionAPI@addSection', 'add_section', ['auth']);
    Router::post('/sections/edit/{id}', 'SectionAPI@updateSection', 'adit_section', ['auth']);

    Router::post('/questions/add_question', 'QuestionAPI@addQuestion', 'save_question', ['auth']);
    Router::post('/questions/edit_question/{id}', 'QuestionAPI@editQuestion', 'edit_question', ['auth']);
    Router::post('/questions/assign_to_section/{id}', 'QuestionAPI@assignQuestionToSection', 'assign_question_to_section', ['auth']);
    Router::post('/questions/unassign_section/{id}', 'QuestionAPI@unassignSection', 'assign_question_to_section', ['auth']);

    Router::post('/reset/send-email', 'AuthAPI@sendResetLink', 'resend_reset_link', ['auth']);
    Router::post('/reset/{token}', 'AuthAPI@updateResetInfos', 'update_reset_infos');


    Router::get('/users', 'UserAPI@getAllUsersHandler', 'get_all_users', ['auth']);
    Router::get('/user_groups', 'UserGroupAPI@getAllGroups', 'get_all_users_groups', ['auth']);
    Router::get('/user_groups/{id}', 'UserGroupAPI@createUserGroup', 'create_user_group', ['auth']);
    Router::get('/user_groups/{id}/permissions', 'UserGroupAPI@getGroupPermissions', 'get_group_permissions', middleware: ['auth']);
    Router::get('/auth/logged_user', 'UserAPI@getLoggedUserAccesses', 'get_logged_user_accesses', ['auth']);
    Router::get('/student/info', 'UserAPI@getStudentInfo', 'get_student_info', ['auth']);

    Router::get('/exam/all', 'ExamAPI@getAllExams', 'get_all_exams', ['auth']);
    Router::get('/exam/my/{user_id}', 'ExamAPI@getExamsByRole', 'get_user_exams', ['auth']);
    Router::get('/exam/data/{exam_id}', 'ExamAPI@getExamDataForPreview', 'get_exam', ['auth']);
    Router::get('/exam/registration/{exam_id}', 'ExamAPI@getExamRegistrationData', 'get_exam_registration_data', ['auth']);
    Router::get('/exam/register/{exam_id}', 'ExamAPI@configRegistration', 'config_registration', ['auth']);
    Router::get('/exam/attempt/meta_data/{exam_id}', 'ExamAPI@getExamMetaData', 'get_exam_meta_data_for_attempt', ['auth']);
    Router::get('/exam/attempt/{exam_id}', 'ExamAPI@getExamDataForAttempt', 'get_exam_data_for_attempt', ['auth']);
    Router::get('/exam/{exam_id}', 'ExamAPI@getExamData', 'get_exam', ['auth']);
    Router::get('/exam/eligibility/{exam_id}', 'ExamAPI@checkExamEligibility', 'check_exam_eligibility', ['auth']);

    Router::get('/session', 'AuthAPI@getSession', 'get_session', ['auth']);
    Router::get('/reset/{token}', 'AuthAPI@getResetInfos', 'get_reset_infos', ['auth']);

    Router::get('/results/lecturer/{lecturer_id}', 'ResultsAPI@getLecturerResults', 'get_lecturer_results', ['auth']);
    Router::get('/results/student/{student_id}', 'ResultsAPI@getStudentResults', 'get_student_results', ['auth']);
    Router::get('/results/review/{attempt_id}/{exam_id}/{student_id}', 'ResultsAPI@getStudentResultsWithQuestions', 'get_exam_results', ['auth']);
    Router::get('/results/admin', 'ResultsAPI@getAllResultsForAdmin', 'get_all_results_for_admin', ['auth']);

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
    Router::delete('/exam/delete/{id}', 'ExamAPI@deleteExam', 'delete_exam', ['auth']);









    // Auth
    Router::get('/auth/user', 'AuthAPI@getLoggedInUser', 'get_logged_in_user', ['auth']);

    // Profile routes
    Router::get('/profile', 'ProfileAPI@getProfile');
    Router::post('/profile/change-password', 'ProfileAPI@changePassword');
    Router::get('/profile/sessions', 'ProfileAPI@getSessions');
    Router::get('/profile/last-login', 'ProfileAPI@getLastLogin');
    Router::get('/profile/export-data', 'ProfileAPI@exportData');
    Router::delete('/profile/delete-account', 'ProfileAPI@deleteAccount');

    // Dashboard routes
    Router::get('/dashboard/admin', 'DashboardAPI@adminDashboard');
    Router::get('/dashboard/lecturer', 'DashboardAPI@lecturerDashboard');
    Router::get('/dashboard/student', 'DashboardAPI@studentDashboard');

    // Mail
    Router::post('test-mail', 'UserAPI@testMail', 'test_mail');
});

Router::group(['prefix' => 'modal'], function () {
    Router::get('/{modal}', 'ModalAPI@getModal');
});
