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
            $http.get(window.baseUrl + '/API/auth/user')
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
                if (response.data.status === 'success') {
                    $scope.stats = response.data.stats;
                    $scope.recentUsers = response.data.recentUsers;
                    $scope.upcomingExams = response.data.upcomingExams;
                } else {
                    $scope.stats = {};
                    $scope.recentUsers = [];
                    $scope.upcomingExams = [];
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
                if (response.data.status === 'success') {
                    $scope.lecturerStats = response.data.stats;
                    $scope.myUpcomingExams = response.data.upcomingExams;
                    $scope.recentAttempts = response.data.recentAttempts;
                } else {
                    // Demo data
                    $scope.lecturerStats = {};
                    $scope.myUpcomingExams = [];
                    $scope.recentAttempts = [];
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
                if (response.data.status === 'success') {
                    $scope.studentStats = response.data.stats;
                    $scope.studentUpcomingExams = response.data.upcomingExams;
                    $scope.recentResults = response.data.recentResults;
                    $scope.scoreDistribution = response.data.scoreDistribution;
                } else {
                    $scope.studentStats = {};
                    $scope.studentUpcomingExams = [];
                    $scope.recentResults = [];
                    $scope.scoreDistribution = [];
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

    // $scope.refreshLogs = function () {
    //     if ($scope.user.role === 1) {
    //         $scope.systemLogs.unshift({
    //             time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
    //             type: 'info',
    //             user: $scope.user.name,
    //             action: 'Manual Refresh',
    //             details: 'Logs refreshed manually'
    //         });
    //     }
    // };

    // $scope.clearCache = function () {
    //     $http.post(window.baseUrl + '/API/system/clear-cache')
    //         .then(function (response) {
    //             showNotification('Cache cleared successfully', 'success');
    //         })
    //         .catch(function (error) {
    //             showNotification('Failed to clear cache', 'error');
    //         });
    // };

    // $scope.runBackup = function () {
    //     $http.post(window.baseUrl + '/API/system/backup')
    //         .then(function (response) {
    //             showNotification('Backup started successfully', 'success');
    //         })
    //         .catch(function (error) {
    //             showNotification('Failed to start backup', 'error');
    //         });
    // };

    // $scope.checkUpdates = function () {
    //     $http.get(window.baseUrl + '/API/system/check-updates')
    //         .then(function (response) {
    //             if (response.data.updates) {
    //                 showNotification('System is up to date', 'success');
    //             } else {
    //                 showNotification('Updates available', 'info');
    //             }
    //         })
    //         .catch(function (error) {
    //             showNotification('Failed to check updates', 'error');
    //         });
    // };
}]);