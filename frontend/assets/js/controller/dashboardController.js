app.controller('DashboardController', ['$scope', '$http', '$timeout', function ($scope, $http, $timeout) {
    $scope.loading = true;
    $scope.user = {};
    $scope.stats = {};
    $scope.lecturerStats = {};
    $scope.studentStats = {};
    $scope.systemLogs = [];
    $scope.recentUsers = [];
    $scope.upcomingExams = [];
    $scope.myUpcomingExams = [];
    $scope.pendingReviews = [];
    $scope.studentUpcomingExams = [];
    $scope.recentResults = [];
    $scope.scoreDistribution = [];
    $scope.subjectPerformance = [];
    $scope.systemStatus = {};
    $scope.dbStats = {};

    $scope.init = function () {
        // Get user data from session
        if (!$scope.user.id) {
            $http.get(window.baseUrl + '/API/session')
                .then(function (response) {
                    $scope.user = response.data.user;
                    loadDashboardData();
                })
                .catch(function (error) {
                    console.error('Error loading user data:', error);
                    $scope.loading = false;
                });
        } else {
            loadDashboardData();
        }
    };

    function loadDashboardData() {
        // Load role-specific data
        if ($scope.user.role === 1) {
            loadTechDashboard();
        } else if ([2, 3].includes($scope.user.role)) {
            loadAdminDashboard();
        } else if ($scope.user.role === 5) {
            loadLecturerDashboard();
        } else if ($scope.user.role === 6) {
            loadStudentDashboard();
        }
    }

    function loadTechDashboard() {
        $http.get(window.baseUrl + '/API/dashboard/tech')
            .then(function (response) {
                if (response.data.success) {
                    $scope.stats = response.data.stats;
                    $scope.systemLogs = response.data.logs;
                    $scope.systemStatus = response.data.systemStatus;
                    $scope.dbStats = response.data.dbStats;
                } else {
                    // Demo data
                    $scope.stats = {
                        totalUsers: 15,
                        activeUsers: 3,
                        errors: 2,
                        resolvedErrors: 1,
                        apiCalls: 145,
                        apiSuccessRate: '98.6%'
                    };
                    $scope.systemLogs = [
                        { time: '10:30 AM', type: 'info', user: 'System', action: 'Backup Completed', details: 'Database backup successful' },
                        { time: '09:15 AM', type: 'warning', user: 'Student (ID: 8)', action: 'Failed Login', details: '3 failed attempts from IP: 192.168.1.100' },
                        { time: '08:45 AM', type: 'error', user: 'System', action: 'API Error', details: 'GET /api/exams returned 500' },
                        { time: 'Yesterday', type: 'info', user: 'Admin', action: 'User Created', details: 'New user: Saththiyaseelan Keyithan' }
                    ];
                    $scope.systemStatus = {
                        online: true,
                        uptime: '99.9%',
                        responseTime: '125ms'
                    };
                    $scope.dbStats = {
                        size: '45.2 MB',
                        tables: 12,
                        lastBackup: 'Today, 10:30 AM'
                    };
                }
                $scope.loading = false;
            })
            .catch(function (error) {
                console.error('Error loading tech dashboard:', error);
                $scope.loading = false;
            });
    }

    function loadAdminDashboard() {
        $http.get(window.baseUrl + '/API/dashboard/admin')
            .then(function (response) {
                if (response.data.success) {
                    $scope.stats = response.data.stats;
                    $scope.recentUsers = response.data.recentUsers;
                    $scope.upcomingExams = response.data.upcomingExams;
                } else {
                    // Demo data
                    $scope.stats = {
                        totalUsers: 15,
                        students: 8,
                        lecturers: 2,
                        activeExams: 3,
                        todayExams: 1,
                        avgScore: 78,
                        passRate: 85,
                        totalQuestions: 234
                    };
                    $scope.recentUsers = [
                        { id: 15, name: 'Saththiyaseelan Keyithan', email: 'sadmin@nit.com', user_group: 6, created_at: '2025-12-13 05:09:28' },
                        { id: 14, name: 'Saththiyaseelan Keyithan', email: 'nit@tech.lk', user_group: 1, created_at: '2025-12-13 05:05:38' },
                        { id: 13, name: 'Saththiyaseelan Keyithan', email: 'tech@nit.lk', user_group: 6, created_at: '2025-12-13 03:58:20' }
                    ];
                    $scope.upcomingExams = [
                        { id: 1, title: 'Mid-term Mathematics', date: 'Tomorrow', duration: 120, students: 45 },
                        { id: 2, title: 'Physics Final', date: 'Dec 28', duration: 180, students: 32 },
                        { id: 3, title: 'Chemistry Quiz', date: 'Jan 5', duration: 60, students: 28 }
                    ];
                }
                $scope.loading = false;
            })
            .catch(function (error) {
                console.error('Error loading admin dashboard:', error);
                $scope.loading = false;
            });
    }

    function loadLecturerDashboard() {
        $http.get(window.baseUrl + '/API/dashboard/lecturer')
            .then(function (response) {
                if (response.data.success) {
                    $scope.lecturerStats = response.data.stats;
                    $scope.myUpcomingExams = response.data.upcomingExams;
                    $scope.pendingReviews = response.data.pendingReviews;
                } else {
                    // Demo data
                    $scope.lecturerStats = {
                        courses: 3,
                        exams: 5,
                        students: 45,
                        questions: 78
                    };
                    $scope.myUpcomingExams = [
                        { id: 1, title: 'Mathematics Quiz', date: 'Tomorrow', course: 'Math 101', students: 25 },
                        { id: 2, title: 'Calculus Test', date: 'Next Week', course: 'Calculus', students: 20 }
                    ];
                    $scope.pendingReviews = [
                        { attempt_id: 101, exam_id: 1, student_id: 8, student_name: 'Saththiyaseelan Keyithan', exam_title: 'Mathematics Quiz', submitted_date: 'Today' },
                        { attempt_id: 102, exam_id: 2, student_id: 15, student_name: 'Saththiyaseelan Keyithan', exam_title: 'Physics Test', submitted_date: 'Yesterday' }
                    ];
                }
                $scope.loading = false;
            })
            .catch(function (error) {
                console.error('Error loading lecturer dashboard:', error);
                $scope.loading = false;
            });
    }

    function loadStudentDashboard() {
        $http.get(window.baseUrl + '/API/dashboard/student')
            .then(function (response) {
                if (response.data.success) {
                    $scope.studentStats = response.data.stats;
                    $scope.studentUpcomingExams = response.data.upcomingExams;
                    $scope.recentResults = response.data.recentResults;
                    $scope.scoreDistribution = response.data.scoreDistribution;
                    $scope.subjectPerformance = response.data.subjectPerformance;
                } else {
                    // Demo data
                    $scope.studentStats = {
                        courses: 5,
                        examsTaken: 8,
                        avgScore: 85,
                        upcomingExams: 2
                    };
                    $scope.studentUpcomingExams = [
                        { id: 1, title: 'Mathematics Final', date: 'Tomorrow', course: 'Math 101', duration: 120, start_time: '10:00 AM', hash: 'abc123' },
                        { id: 2, title: 'Physics Quiz', date: 'Next Week', course: 'Physics', duration: 60, start_time: '2:00 PM', hash: 'def456' }
                    ];
                    $scope.recentResults = [
                        { attempt_id: 101, exam_id: 1, exam_title: 'Chemistry Test', course: 'Chemistry', score: 92, passed: true, date: 'Nov 15' },
                        { attempt_id: 102, exam_id: 2, exam_title: 'Biology Exam', course: 'Biology', score: 78, passed: true, date: 'Nov 10' },
                        { attempt_id: 103, exam_id: 3, exam_title: 'Math Quiz', course: 'Mathematics', score: 65, passed: false, date: 'Nov 5' }
                    ];
                    $scope.scoreDistribution = [
                        { range: '90-100%', count: 3, percentage: 30 },
                        { range: '80-89%', count: 2, percentage: 20 },
                        { range: '70-79%', count: 2, percentage: 20 },
                        { range: '60-69%', count: 2, percentage: 20 },
                        { range: 'Below 60%', count: 1, percentage: 10 }
                    ];
                    $scope.subjectPerformance = [
                        { name: 'Mathematics', score: 92 },
                        { name: 'Physics', score: 85 },
                        { name: 'Chemistry', score: 78 },
                        { name: 'Biology', score: 88 }
                    ];
                }
                $scope.loading = false;
            })
            .catch(function (error) {
                console.error('Error loading student dashboard:', error);
                $scope.loading = false;
            });
    }

    $scope.getRoleName = function (roleId) {
        const roles = {
            1: 'Technical Support',
            2: 'Super Admin',
            3: 'Admin',
            4: 'Head of Department',
            5: 'Lecturer',
            6: 'Student',
            7: 'Parent'
        };
        return roles[roleId] || 'User';
    };

    $scope.refreshLogs = function () {
        if ($scope.user.role === 1) {
            $scope.systemLogs.unshift({
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                type: 'info',
                user: $scope.user.name,
                action: 'Manual Refresh',
                details: 'Logs refreshed manually'
            });
        }
    };

    $scope.clearCache = function () {
        $http.post(window.baseUrl + '/API/system/clear-cache')
            .then(function (response) {
                showNotification('Cache cleared successfully', 'success');
            })
            .catch(function (error) {
                showNotification('Failed to clear cache', 'error');
            });
    };

    $scope.runBackup = function () {
        $http.post(window.baseUrl + '/API/system/backup')
            .then(function (response) {
                showNotification('Backup started successfully', 'success');
            })
            .catch(function (error) {
                showNotification('Failed to start backup', 'error');
            });
    };

    $scope.checkUpdates = function () {
        $http.get(window.baseUrl + '/API/system/check-updates')
            .then(function (response) {
                if (response.data.updates) {
                    showNotification('System is up to date', 'success');
                } else {
                    showNotification('Updates available', 'info');
                }
            })
            .catch(function (error) {
                showNotification('Failed to check updates', 'error');
            });
    };

    function showNotification(message, type) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full`;

        const typeClasses = {
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white',
            info: 'bg-blue-600 text-white'
        };

        notification.className += ' ' + (typeClasses[type] || typeClasses.info);
        notification.innerHTML = `
            <div class="flex items-center space-x-3">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        $timeout(() => {
            notification.classList.remove('translate-x-full');
            notification.classList.add('translate-x-0');
        }, 10);

        // Remove after 5 seconds
        $timeout(() => {
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            $timeout(() => {
                if (notification.parentElement) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 5000);
    }
}]);