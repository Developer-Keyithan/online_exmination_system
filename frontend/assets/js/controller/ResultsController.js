app.controller('ResultsController', ['$scope', '$timeout', '$http', function ($scope, $timeout, $http) {
    // Initial state
    $scope.loading = true;
    $scope.error = null;
    $scope.results = [];
    $scope.filteredResults = [];
    $scope.currentFilter = 'all';
    $scope.selectedStudent = null;
    $scope.selectedExam = '';
    $scope.timeFilter = 'all';
    $scope.currentPage = 1;
    $scope.pageSize = 10;
    $scope.totalPages = 1;
    $scope.dropdownOpen = false;
    $scope.examList = [];
    $scope.timeFilterLabel = 'All Time';
    $scope.selectedExamTitle = 'All Exams';
    $scope.stats = {
        totalAttempts: 0,
        averageScore: 0,
        passRate: 0,
        activeStudents: 0,
        averageTime: '00:00',
        highestScore: 0
    };


    // Initialize controller
    $scope.init = function () {
        $scope.loadResults();
    };

    $scope.setTimeFilter = function (value, label) {
        $scope.timeFilter = value;
        $scope.timeFilterLabel = label;
        $scope.timeDropdownOpen = false;
        $scope.loadResults();
    };

    $scope.setExam = function (id, title) {
        $scope.selectedExam = id;
        $scope.selectedExamTitle = title;
        $scope.examDropdownOpen = false;
        $scope.loadResults();
    };

    // Load results from server
    $scope.loadResults = function () {
        $scope.loading = true;
        $scope.error = null;
        // Prepare API parameters
        const params = {
            student_id: $scope.selectedStudent ? +$scope.selectedStudent : 'all',
            exam_id: $scope.selectedExam || 'all',
            time_filter: $scope.timeFilter || 'all',
            filter: $scope.currentFilter
        };

        $http.get(window.baseUrl + '/API/results/admin', { params })
            .then(function (response) {
                if (response.data.status === 'success') {
                    $scope.examList = response.data.exams.map(exam => ({
                        id: exam.id,
                        title: exam.title
                    }));
                    $scope.results = response.data.results;
                    $scope.calculateStats();
                    $scope.applyFilters();
                    $scope.calculatePagination();
                    $scope.loading = false;
                } else {
                    Toast.fire({
                        type: 'error',
                        title: 'Loading Error',
                        msg: 'Failed to load results. Please try again.'
                    })
                }
            }).catch(function (err) {
                $scope.error = 'Failed to load results. Please try again.';
                $scope.loading = false;
                console.error('Error loading results:', err);
            });
    };

    // Calculate statistics
    $scope.calculateStats = function () {
        if ($scope.results.length === 0) {
            $scope.stats = {
                totalAttempts: 0,
                averageScore: 0,
                passRate: 0,
                activeStudents: 0,
                averageTime: '00:00',
                highestScore: 0
            };
            return;
        }

        const totalScore = $scope.results.reduce((sum, r) => sum + r.percentage, 0);
        const passedResults = $scope.results.filter(r => r.percentage >= r.passing_percentage);

        // Calculate average time in minutes
        const totalMinutes = $scope.results.reduce((sum, r) => sum + (r.time_taken / 60), 0);

        const avgMinutes = Math.round(totalMinutes / $scope.results.length);
        const avgSeconds = Math.round((avgMinutes) * 60);

        // Get unique students
        const uniqueStudents = [...new Set($scope.results.map(r => r.student_id))];

        // Find highest score
        const highestScore = Math.max(...$scope.results.map(r => r.percentage));

        $scope.stats = {
            totalAttempts: $scope.results.length,
            averageScore: (totalScore / $scope.results.length).toFixed(2),
            passRate: Math.round((passedResults.length / $scope.results.length) * 100),
            activeStudents: uniqueStudents.length,
            averageTime: avgSeconds,
            highestScore: highestScore
        };
    };

    // Apply filters to results
    $scope.applyFilters = function () {
        let filtered = [...$scope.results];

        switch ($scope.currentFilter) {
            case 'passed':
                filtered = filtered.filter(r => r.percentage >= r.passing_percentage);
                break;
            case 'failed':
                filtered = filtered.filter(r => r.percentage < r.passing_percentage);
                break;
            case 'recent':
                const weekAgo = new Date();
                weekAgo.setDate(weekAgo.getDate() - 7);
                filtered = filtered.filter(r => new Date(r.completed_date) > weekAgo);
                break;
            case 'top':
                filtered.sort((a, b) => b.percentage - a.percentage);
                filtered = filtered.slice(0, Math.ceil(filtered.length * 0.2));
                break;
        }

        $scope.filteredResults = filtered;
    };

    // Set filter
    $scope.setFilter = function (filter) {
        $scope.currentFilter = filter;
        $scope.currentPage = 1;
        $scope.applyFilters();
        $scope.calculatePagination();
    };

    // Clear all filters
    $scope.clearFilters = function () {
        $scope.selectedStudent = null;
        $scope.selectedExam = '';
        $scope.timeFilter = 'all';
        $scope.currentFilter = 'all';
        $scope.currentPage = 1;
        $scope.loadResults();
    };

    // Select student
    $scope.selectStudent = function (value) {
        if (!value) {
            $scope.selectedStudent = { id: 'all', name: 'All Students' };
        } else {
            try {
                $scope.selectedStudent = JSON.parse(value);
                $scope.selectedStudent.id = +$scope.selectedStudent.id;
            } catch (e) {
                console.error('Failed to parse student JSON:', value);
                $scope.selectedStudent = { id: 'all', name: 'All Students' };
            }
        }

        $scope.currentPage = 1;
        $scope.loadResults();
    };


    // Pagination functions
    $scope.calculatePagination = function () {
        $scope.totalPages = Math.ceil($scope.filteredResults.length / $scope.pageSize);
        if ($scope.currentPage > $scope.totalPages) {
            $scope.currentPage = 1;
        }
    };

    $scope.nextPage = function () {
        if ($scope.currentPage < $scope.totalPages) {
            $scope.currentPage++;
        }
    };

    $scope.prevPage = function () {
        if ($scope.currentPage > 1) {
            $scope.currentPage--;
        }
    };

    // Get paginated results
    $scope.getPaginatedResults = function () {
        const start = ($scope.currentPage - 1) * $scope.pageSize;
        const end = start + $scope.pageSize;
        return $scope.filteredResults.slice(start, end);
    };

    // Export functions
    $scope.exportResult = function (resultId) {
        const result = $scope.results.find(r => r.id === resultId);
        if (result) {
            alert(`Exporting result for ${result.student_name} - ${result.exam_title}`);
            // In real implementation, trigger file download
        }
    };

    $scope.exportAllResults = function () {
        alert(`Exporting all ${$scope.results.length} results as CSV`);
        // In real implementation, trigger bulk export
    };

    // Send reports
    $scope.sendStudentReport = function (studentId, resultId) {
        const result = $scope.results.find(r => r.id === resultId && r.student_id === studentId);
        if (result) {
            alert(`Sending report to ${result.student_name} for ${result.exam_title}`);
            // In real implementation, send email with report
        }
    };

    $scope.sendReports = function () {
        alert(`Sending reports to all ${$scope.results.length} students`);
        // In real implementation, batch send reports
    };

    // Performance evaluation helpers
    $scope.getPerformanceColor = function (score) {
        if (score >= 80) return 'text-green-400';
        if (score >= 60) return 'text-yellow-400';
        return 'text-red-400';
    };

    $scope.getPerformanceIcon = function (score) {
        if (score >= 80) return 'fa-arrow-up';
        if (score >= 60) return 'fa-minus';
        return 'fa-arrow-down';
    };

    $scope.getPerformanceLabel = function (score) {
        if (score >= 80) return 'Excellent';
        if (score >= 60) return 'Good';
        return 'Needs Improvement';
    };

    // Format date/time filter (if not in parent scope)
    $scope.formatDateTime = function (dateString, format) {
        if (!dateString) return 'N/A';

        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;

        if (format === 'MMM DD, YYYY') {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            });
        } else if (format === 'MMM DD') {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
        } else if (format === 'hh:mm A') {
            return date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        } else if (format === 'MMM DD, YYYY hh:mm A') {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric',
                year: 'numeric'
            }) + ' ' + date.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
        }
        return dateString;
    };

    // Close dropdown when clicking outside
    $scope.$watch('dropdownOpen', function (newVal) {
        if (newVal) {
            $timeout(function () {
                angular.element(document).on('click', function (event) {
                    if (!event.target.closest('.relative')) {
                        $scope.$apply(function () {
                            $scope.dropdownOpen = false;
                        });
                    }
                });
            });
        }
    });

    // Initialize on load
    $scope.init();
}]);