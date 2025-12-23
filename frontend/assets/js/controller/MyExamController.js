app.controller('MyExamController', [
    "$scope", "$http", "$compile", "$timeout", "window",
    function ($scope, $http, $compile, $timeout, window) {
        // Initialize scope variables
        $scope.exams = [];
        $scope.filteredExams = [];
        $scope.loading = true;
        $scope.error = null;
        $scope.searchQuery = '';
        $scope.currentFilter = 'all';
        $scope.activeExamMenu = null;
        $scope.theLoggedUser = {};
        $scope.isUserSelect = false;
        $scope.selectedUser = null;
        $scope.selectedStudent = {};
        $scope.selectedLecturer = {};
        $scope.isUserSelect = false;


        // Comprehensive Dummy Data for Exams
        $scope.dummyExams = {
            // Lecturer Dummy Exams (role 1/2)
            lecturer: [
                {
                    id: 101,
                    title: "Web Development Fundamentals",
                    code: "WD101-2024",
                    description: "Basic web development concepts including HTML, CSS, and JavaScript",
                    duration: 120,
                    total_questions: 25,
                    total_marks: 100,
                    passing_marks: 40,
                    status: "published",
                    schedule_type: "anytime",
                    start_time: null,
                    participants_count: 45,
                    completed_count: 32,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: true,
                    created_at: "2024-01-15T10:00:00Z"
                },
                {
                    id: 102,
                    title: "Database Management Systems",
                    code: "DBMS202",
                    description: "Advanced database concepts and SQL queries",
                    duration: 90,
                    total_questions: 20,
                    total_marks: 80,
                    passing_marks: 32,
                    status: "scheduled",
                    schedule_type: "scheduled",
                    start_time: "2024-12-20T09:00:00Z",
                    participants_count: 38,
                    completed_count: 0,
                    shuffle_questions: true,
                    shuffle_options: false,
                    full_screen_mode: true,
                    allow_retake: false,
                    created_at: "2024-01-20T14:30:00Z"
                },
                {
                    id: 103,
                    title: "Software Engineering Principles",
                    code: "SE301-FALL",
                    description: "Software development methodologies and best practices",
                    duration: 180,
                    total_questions: 30,
                    total_marks: 150,
                    passing_marks: 60,
                    status: "draft",
                    schedule_type: "anytime",
                    start_time: null,
                    participants_count: 0,
                    completed_count: 0,
                    shuffle_questions: false,
                    shuffle_options: true,
                    full_screen_mode: false,
                    allow_retake: true,
                    created_at: "2024-02-05T11:15:00Z"
                },
                {
                    id: 104,
                    title: "Network Security Essentials",
                    code: "NSE401",
                    description: "Fundamentals of network security and encryption",
                    duration: 150,
                    total_questions: 25,
                    total_marks: 100,
                    passing_marks: 50,
                    status: "published",
                    schedule_type: "scheduled",
                    start_time: "2024-12-10T13:00:00Z",
                    participants_count: 52,
                    completed_count: 28,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: true,
                    created_at: "2024-02-10T09:45:00Z"
                },
                {
                    id: 105,
                    title: "Mobile App Development",
                    code: "MAD501",
                    description: "Cross-platform mobile application development",
                    duration: 120,
                    total_questions: 22,
                    total_marks: 110,
                    passing_marks: 44,
                    status: "canceled",
                    schedule_type: "scheduled",
                    start_time: "2024-11-30T10:00:00Z",
                    participants_count: 30,
                    completed_count: 15,
                    shuffle_questions: true,
                    shuffle_options: false,
                    full_screen_mode: true,
                    allow_retake: false,
                    created_at: "2024-01-25T16:20:00Z"
                },
                {
                    id: 106,
                    title: "Artificial Intelligence Basics",
                    code: "AI601-SPRING",
                    description: "Introduction to AI concepts and machine learning",
                    duration: 200,
                    total_questions: 35,
                    total_marks: 175,
                    passing_marks: 70,
                    status: "draft",
                    schedule_type: "anytime",
                    start_time: null,
                    participants_count: 0,
                    completed_count: 0,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: true,
                    created_at: "2024-02-15T08:30:00Z"
                }
            ],

            // Student Dummy Exams (role 3)
            student: [
                {
                    id: 201,
                    title: "Web Development Fundamentals",
                    code: "WD101-2024",
                    instructor_name: "Dr. Sarah Johnson",
                    duration: 120,
                    total_questions: 25,
                    total_marks: 100,
                    passing_marks: 40,
                    passing_percentage: 40,
                    schedule_type: "anytime",
                    start_time: null,
                    end_time: "2024-12-31T23:59:00Z",
                    attempt_status: "completed",
                    your_score: 85,
                    percentage: 85,
                    is_passed: true,
                    last_attempt_date: "2024-11-15T14:30:00Z",
                    attempts_remaining: 2,
                    time_remaining: null,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: true
                },
                {
                    id: 202,
                    title: "Database Management Systems",
                    code: "DBMS202",
                    instructor_name: "Prof. Michael Chen",
                    duration: 90,
                    total_questions: 20,
                    total_marks: 80,
                    passing_marks: 32,
                    passing_percentage: 40,
                    schedule_type: "scheduled",
                    start_time: "2024-12-20T09:00:00Z",
                    end_time: "2024-12-20T10:30:00Z",
                    attempt_status: "upcoming",
                    your_score: null,
                    percentage: null,
                    is_passed: null,
                    last_attempt_date: null,
                    attempts_remaining: 1,
                    time_remaining: null,
                    shuffle_questions: true,
                    shuffle_options: false,
                    full_screen_mode: true,
                    allow_retake: false
                },
                {
                    id: 203,
                    title: "Software Engineering Principles",
                    code: "SE301-FALL",
                    instructor_name: "Dr. Emily Williams",
                    duration: 180,
                    total_questions: 30,
                    total_marks: 150,
                    passing_marks: 60,
                    passing_percentage: 40,
                    schedule_type: "anytime",
                    start_time: null,
                    end_time: "2024-12-15T23:59:00Z",
                    attempt_status: "in_progress",
                    your_score: null,
                    percentage: null,
                    is_passed: null,
                    last_attempt_date: "2024-11-20T10:15:00Z",
                    attempts_remaining: 1,
                    time_remaining: "45:30",
                    shuffle_questions: false,
                    shuffle_options: true,
                    full_screen_mode: false,
                    allow_retake: true
                },
                {
                    id: 204,
                    title: "Network Security Essentials",
                    code: "NSE401",
                    instructor_name: "Prof. David Rodriguez",
                    duration: 150,
                    total_questions: 25,
                    total_marks: 100,
                    passing_marks: 50,
                    passing_percentage: 50,
                    schedule_type: "scheduled",
                    start_time: "2024-12-10T13:00:00Z",
                    end_time: "2024-12-10T15:30:00Z",
                    attempt_status: "available",
                    your_score: null,
                    percentage: null,
                    is_passed: null,
                    last_attempt_date: null,
                    attempts_remaining: 1,
                    time_remaining: null,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: false
                },
                {
                    id: 205,
                    title: "Mobile App Development",
                    code: "MAD501",
                    instructor_name: "Dr. Lisa Thompson",
                    duration: 120,
                    total_questions: 22,
                    total_marks: 110,
                    passing_marks: 44,
                    passing_percentage: 40,
                    schedule_type: "scheduled",
                    start_time: "2024-11-30T10:00:00Z",
                    end_time: "2024-11-30T12:00:00Z",
                    attempt_status: "expired",
                    your_score: 65,
                    percentage: 59,
                    is_passed: false,
                    last_attempt_date: "2024-11-30T11:45:00Z",
                    attempts_remaining: 0,
                    time_remaining: null,
                    shuffle_questions: true,
                    shuffle_options: false,
                    full_screen_mode: true,
                    allow_retake: false
                },
                {
                    id: 206,
                    title: "Artificial Intelligence Basics",
                    code: "AI601-SPRING",
                    instructor_name: "Prof. Robert Kim",
                    duration: 200,
                    total_questions: 35,
                    total_marks: 175,
                    passing_marks: 70,
                    passing_percentage: 40,
                    schedule_type: "anytime",
                    start_time: null,
                    end_time: "2025-01-15T23:59:00Z",
                    attempt_status: "available",
                    your_score: null,
                    percentage: null,
                    is_passed: null,
                    last_attempt_date: null,
                    attempts_remaining: 2,
                    time_remaining: null,
                    shuffle_questions: true,
                    shuffle_options: true,
                    full_screen_mode: true,
                    allow_retake: true
                }
            ]
        };

        // Get user session
        $http.get(window.baseUrl + "/API/session")
            .then(function (response) {
                $scope.theLoggedUser = response.data.user || {};
                if ($scope.theLoggedUser.role == '1' || $scope.theLoggedUser.role == 1 ||
                    $scope.theLoggedUser.role == '2' || $scope.theLoggedUser.role == 2 ||
                    $scope.theLoggedUser.role == '3' || $scope.theLoggedUser.role == 3 ||
                    $scope.theLoggedUser.role == '4' || $scope.theLoggedUser.role == 4) {
                    $scope.isUserSelect = false;
                } else {
                    $scope.isUserSelect = true
                    $scope.init();
                }
            })
            .catch(function (err) {
                console.error('Failed to get session:', err);
            });

        $scope.init = () => {
            $scope.loadExams();
        };

        // Load exams based on user role
        $scope.loadExams = function () {
            $scope.loading = true;
            $scope.error = null;
            $scope.isUserSelect = true;

            const apiEndpoint = '/API/exam/my/' + ($scope.selectedUser ? $scope.selectedUser.id : $scope.theLoggedUser.user);
            $http.get(window.baseUrl + apiEndpoint)
                .then(function (response) {
                    if (response.data.status === 'success') {
                        $scope.exams = response.data.exams || [];
                        $scope.filteredExams = [...$scope.exams];
                        $scope.selectedUser = response.data.user || $scope.theLoggedUser.user;
                    } else {
                        $scope.error = response.data.msg;
                        $scope.selectedUser = $scope.selectedUser || $scope.theLoggedUser.user;
                        return;
                    }
                    $scope.loading = false;
                })
                .catch(function (error) {
                    console.error('Error loading exams:', error);
                });
        };

        $scope.selectStudent = function (id, name) {
            $scope.selectedStudent = { id, name, role_id: 6 };
            $scope.selectedLecturer = {};
            $scope.selectedUser = $scope.selectedStudent;
            $scope.isUserSelect = true;
        };

        $scope.selectLecturer = function (id, name) {
            $scope.selectedLecturer = { id, name, role_id: 5 };
            $scope.selectedStudent = {};
            $scope.selectedUser = $scope.selectedLecturer;
            $scope.isUserSelect = true;
        };

        // Helper functions for date calculations
        $scope.getDaysRemaining = function (dateString) {
            if (!dateString) return 'No date set';
            const examDate = new Date(dateString);
            const today = new Date();
            const diffTime = examDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

            if (diffDays < 0) return 'Past due';
            if (diffDays === 0) return 'Today';
            if (diffDays === 1) return 'Tomorrow';
            return `${diffDays} days`;
        };

        $scope.getTimeUntilStart = function (dateString) {
            if (!dateString) return 'Not scheduled';
            const examDate = new Date(dateString);
            const today = new Date();
            const diffTime = examDate - today;

            if (diffTime <= 0) return 'Started';

            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
            const diffHours = Math.floor((diffTime % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));

            if (diffDays > 0) {
                return `${diffDays} day${diffDays > 1 ? 's' : ''} ${diffHours} hour${diffHours > 1 ? 's' : ''}`;
            } else {
                return `${diffHours} hour${diffHours > 1 ? 's' : ''}`;
            }
        };

        // Filter exams based on status (different for lecturers vs students)
        $scope.setFilter = function (filter) {
            $scope.currentFilter = filter;
            $scope.applyFilters();
        };

        // Apply both search and filter
        $scope.applyFilters = function () {
            let filtered = [...$scope.exams];

            // Apply status filter based on user role
            if ($scope.currentFilter !== 'all') {
                filtered = filtered.filter(function (exam) {
                    if ($scope.selectedUser.role_id === '5' || $scope.selectedUser.role_id === 5) {
                        if ($scope.currentFilter === 'published') {
                            return ['live', 'scheduled', 'published'].includes(exam.status);
                        }
                        // Lecturer filters
                        return exam.status === $scope.currentFilter;
                    } else {
                        // Student filters
                        return exam.attempt_status === $scope.currentFilter;
                    }
                });
            }

            // Apply search filter
            if ($scope.searchQuery.trim()) {
                const query = $scope.searchQuery.toLowerCase();
                filtered = filtered.filter(function (exam) {
                    return exam.title?.toLowerCase().includes(query) ||
                        exam.code?.toLowerCase().includes(query) ||
                        exam.description?.toLowerCase().includes(query);
                });
            }

            $scope.filteredExams = filtered;
        };

        // Clear all filters and search
        $scope.clearFilters = function () {
            $scope.searchQuery = '';
            $scope.currentFilter = 'all';
            $scope.filteredExams = [...$scope.exams];
        };

        // Toggle exam dropdown menu (for lecturers only)
        $scope.toggleExamMenu = function (examId) {
            if ($scope.activeExamMenu === examId) {
                $scope.activeExamMenu = null;
            } else {
                $scope.activeExamMenu = examId;
            }
        };

        // Close dropdown when clicking outside
        $scope.closeDropdown = function () {
            $scope.activeExamMenu = null;
        };

        // Create new exam - navigate to create page (for lecturers only)
        $scope.createExam = function () {
            window.location.href = window.baseUrl + '/exam/create';
        };

        // Request exams (for students)
        $scope.requestExams = function () {
            Toast.fire({
                type: 'info',
                title: 'Contact Instructor',
                msg: 'Please contact your instructor to get assigned to exams.'
            });
        };

        // Delete exam (for lecturers only)
        $scope.deleteExam = function (exam) {
            if (!exam || !exam.id) {
                Toast.fire({
                    type: 'error',
                    title: 'Invalid Exam',
                    msg: 'Cannot delete: Invalid exam data'
                });
                return;
            }

            // For real API calls
            Toast.popover({
                type: 'confirm',
                title: 'Delete Exam',
                titleColor: '#f87171',
                content: `
                    <i class="fa-solid fa-trash-can" style="font-size:3rem; color:#f87171"></i><br><br>
                    Are you sure you want to delete "<strong>${exam.title}</strong>"? This action cannot be undone.
                `,
                contentColor: '#fff',
                options: {
                    confirm: {
                        text: 'Yes, Delete',
                        background: '#DC2626',
                        onConfirm: async function () {
                            try {
                                await $http.delete(window.baseUrl + '/API/exam/delete/' + exam.id)
                                    .then(function (response) {
                                        if (response.data.status === 'success') {
                                            Toast.fire({
                                                type: 'success',
                                                title: 'Deleted!',
                                                msg: `"${exam.title}" has been deleted successfully.`
                                            });
                                            $scope.exams = $scope.exams.filter(function (e) {
                                                return e.id !== exam.id;
                                            });
                                            $scope.applyFilters();
                                        } else {
                                            $scope.error = error.data?.message || 'Failed to delete exam';
                                            Toast.fire({
                                                type: 'error',
                                                title: 'Error!',
                                                msg: `An error occurred while deleting "${exam.title}".`
                                            });
                                        }
                                    })
                                $scope.$apply();
                            } catch (err) {
                                Toast.fire({
                                    type: 'error',
                                    title: 'Error!',
                                    msg: 'Failed to delete exam.'
                                });
                                console.error(err);
                            }
                        }
                    },
                    cancel: {
                        text: "Don't Delete",
                        background: '#0E7490',
                        onCancel: function () {
                            Toast.popover({ type: 'close' })
                        }
                    }
                }
            });
        };

        // Format date filter
        $scope.formatDateTime = function (dateString, format) {
            if (!dateString) return 'Not set';
            const date = new Date(dateString);

            if (format === 'MMM DD, YYYY') {
                const options = { month: 'short', day: 'numeric', year: 'numeric' };
                return date.toLocaleDateString('en-US', options);
            } else if (format === 'hh:mm a') {
                return date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
                });
            }
            return dateString;
        };

        // Close dropdown when clicking outside (event listener)
        document.addEventListener('click', function (event) {
            if (!event.target.closest('.relative')) {
                if ($scope.activeExamMenu) {
                    $scope.$apply(function () {
                        $scope.closeDropdown();
                    });
                }
            }
        });

        // Clean up
        $scope.$on('$destroy', function () {
            document.removeEventListener('click', $scope.closeDropdown);
        });
    }
]);