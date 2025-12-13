app.controller('ExamRegistrationController', [
    "$scope", "$http", "$compile", "$timeout", "window", "$sce", "$location",
    function ($scope, $http, $compile, $timeout, window, $sce, $location) {
        // Initialize scope variables
        $scope.examId = getIdFromUrl(2);
        $scope.loading = true;
        $scope.error = null;
        $scope.examData = null;
        $scope.studentInfo = {};
        $scope.isSubmitting = false;
        $scope.showSuccessModal = false;
        $scope.showAlreadyRegisteredModal = false;
        $scope.showPassword = false;
        $scope.showExamUnavailableModal = false;
        $scope.registrationId = null;
        $scope.registrationDate = null;
        $scope.existingRegistration = null;
        $scope.registrationError = null;
        $scope.registrationData = {
            agree_terms: false,
            preferred_language: null,
            special_accommodations: null,
            receive_notifications: false
        };
        $scope.attemptUrl = null;


        // Initialize
        $scope.init = function () {
            $scope.loadExamData();
            $scope.loadStudentInfo();
        };

        // Load exam data
        $scope.loadExamData = function () {
            $http.get(window.baseUrl + '/API/exam/register/' + $scope.examId)
                .then(function (response) {
                    if (response.data.status === 'success') {
                        $scope.examData = response.data.exam_data;
                        $scope.examData.instructions = $sce.trustAsHtml(typeof $scope.examData.instructions === 'string' ? $scope.examData.instructions : 'No instructions provided.');

                        // Check if already registered
                        if (response.data.already_registered) {
                            $scope.existingRegistration = response.data.registration;
                            $scope.showAlreadyRegisteredModal = true;
                        }

                        // Check if exam is available for registration
                        if (!$scope.isExamAvailableForRegistration()) {
                            $scope.registrationError = response.data.message || 'Exam is not available for registration.';
                            $scope.showExamUnavailableModal = true;
                        }

                        $scope.loading = false;
                    } else {
                        throw new Error(response.data.message || 'Failed to load exam data');
                    }
                })
                .catch(function (error) {
                    console.error('Error loading exam data:', error);
                    $scope.error = error.data?.message || 'Failed to load exam. Please try again.';
                    $scope.loading = false;
                });
        };

        // Load student information
        $scope.loadStudentInfo = function () {
            $http.get(window.baseUrl + '/API/student/info')
                .then(function (response) {
                    if (response.data.status === 'success') {
                        $scope.studentInfo = response.data.student_info;
                    } else {
                        throw new Error(response.data.msg || 'Failed to load student information');
                    }
                })
                .catch(function (error) {
                    Toast.fire({
                        type: 'error',
                        title: "Error!",
                        msg: error.msg || 'Failed to load student information. Please try again.'
                    })
                });
        };

        // Check if exam is available for registration
        $scope.isExamAvailableForRegistration = function () {
            if (!$scope.examData) return false;
            // Check exam status
            if ($scope.examData.status !== 'published' && $scope.examData.status !== 'scheduled' && $scope.examData.status !== 'live') {
                return false;
            }

            // Check registration deadline for scheduled exams
            if ($scope.examData.schedule_type === 'scheduled') {
                const now = new Date();
                const registrationDeadline = new Date($scope.examData.start_time);

                // Allow registration up to 1 hour before exam start
                // registrationDeadline.setDate(registrationDeadline.getDate() - 3); // 3 Days before exam
                // registrationDeadline.setHours(registrationDeadline.getHours() - 1); // 1 Hour before exam
                registrationDeadline.setMinutes(registrationDeadline.getMinutes() - 5); // 5 Minutes before exam
                console.log(registrationDeadline)
                console.log(now > registrationDeadline);
                if (now > registrationDeadline) {
                    return false;
                }
            }

            // Check maximum registrations
            // if ($scope.examData.max_registrations && 
            //     $scope.examData.current_registrations >= $scope.examData.max_registrations) {
            //     return false;
            // }

            return true;
        };

        // Check if exam is available to start
        $scope.isExamAvailable = function () {
            if (!$scope.examData) return false;

            if ($scope.examData.schedule_type === 'anytime') {
                return true;
            }

            if ($scope.examData.schedule_type === 'scheduled') {
                const now = new Date();
                const startTime = new Date($scope.examData.start_time);
                const endTime = $scope.getEndTime();

                return now >= startTime && now <= endTime;
            }

            return false;
        };

        // Calculate exam end time
        $scope.getEndTime = function () {
            if (!$scope.examData || !$scope.examData.start_time) return new Date();

            const startTime = new Date($scope.examData.start_time);
            const endTime = new Date(startTime.getTime() + ($scope.examData.duration * 60000));
            return endTime;
        };

        // Submit registration
        $scope.submitRegistration = function () {
            if ($scope.isSubmitting) return;

            $scope.isSubmitting = true;

            const registrationData = {
                exam_id: $scope.examId,
                student_id: $scope.studentInfo.id || $scope.studentInfo.student_id,
                agree_terms: $scope.registrationData.agree_terms,
                preferred_language: $scope.registrationData.preferred_language,
                special_accommodations: $scope.registrationData.special_accommodations,
                receive_notifications: $scope.registrationData.receive_notifications
            };

            $http.post(window.baseUrl + '/API/exam/register', $('#registrationForm').serialize())
                .then(function (response) {
                    if (response.data.status === 'success') {
                        $scope.registrationId = response.data.registration_id;
                        $scope.attemptUrl = response.data.url;
                        $scope.registrationDate = new Date();

                        if (response.data.code) {
                            $scope.existingRegistration = response.data.existingRegistration;
                            $scope.existingRegistration.date = new Date(response.data.existingRegistration.date);
                            $scope.showAlreadyRegisteredModal = true;
                            Toast.fire({
                                type: 'info',
                                title: 'Information!',
                                msg: response.data.msg || 'You have already registered for this exam.'
                            })
                            return;
                        }

                        $scope.showSuccessModal = true;
                        Toast.fire({
                            type: 'success',
                            title: 'Registration Successful',
                            msg: 'You have been registered for the exam.'
                        });
                    } else {
                        const err = new Error(response.data.message);
                        err.code = response.data.code;
                        console.log(err)
                        throw err;
                    }
                })
                .catch(function (error) {
                    console.error('Error submitting registration:', error);

                    if (error.data && error.data.code === 'ALREADY_REGISTERED') {
                        $scope.existingRegistration = error.data.registration;
                        $scope.showAlreadyRegisteredModal = true;
                    } else if (error.data && error.data.code === 'EXAM_UNAVAILABLE') {
                        $scope.registrationError = error.data.message;
                        $scope.showExamUnavailableModal = true;
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: 'Registration Failed',
                            msg: error.data?.msg || 'Failed to register. Please try again.'
                        });
                    }
                })
                .finally(function () {
                    $scope.isSubmitting = false;
                });
        };

        // Cancel registration
        $scope.cancelRegistration = function () {
            $location.path('/exams');
        };

        // View exam details
        $scope.viewExamDetails = function () {
            window.location.href = window.baseUrl + '/exam/preview/' + $scope.examId;
        };

        // Contact instructor
        $scope.contactInstructor = function () {
            const email = $scope.examData.instructor_email || 'instructor@university.edu';
            const subject = `Regarding Exam Registration: ${$scope.examData.code}`;
            const body = `Dear Instructor,\n\nI am having trouble registering for the exam "${$scope.examData.title}" (${$scope.examData.code}).\n\nStudent ID: ${$scope.studentInfo.student_id}\nName: ${$scope.studentInfo.name}\n\nPlease assist me with this issue.\n\nBest regards,\n${$scope.studentInfo.name}`;

            window.location.href = `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
        };

        // Safe HTML filter
        $scope.safeHtml = function (text) {
            return $sce.trustAsHtml(text);
        };

        // Initialize
        $scope.init();
    }
]);