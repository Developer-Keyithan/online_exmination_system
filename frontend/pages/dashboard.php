<?php $this->extend('frontend'); ?>
<?php $this->controller('DashboardController'); ?>
<?php
if ((int) user_role() === 1 || (int) user_role() === 7) {
    header('Location: ' . BASE_URL . '/profile');
    exit;
}
?>


<?php $this->start('content'); ?>
<div ng-controller="DashboardController" ng-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-100">Dashboard</h1>
        <p class="text-gray-400">Welcome back, <span class="text-gray-200 font-medium">{{user.name}}!</span> Here's your
            overview.</p>
        <!-- <p class="text-sm text-gray-500">User ID: {{user.id}} | Role: {{getRoleName(user.role)}}</p> -->
    </div>

    <!-- Loading State -->
    <div ng-if="loading" class="flex items-center justify-center min-h-[400px]">
        <div class="text-center">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <p class="text-gray-300">Loading dashboard data...</p>
        </div>
    </div>

    <!-- Role-Based Dashboard Sections -->
    <div ng-cloak ng-if="!loading">
        <!-- TECH SUPPORT/DEVELOPER DASHBOARD (Role 1) -->
        <div ng-if="user.role === 1">
            <!-- System Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-cyan-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-cyan-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-database text-cyan-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Total Users</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.totalUsers || 0}}</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Active Today</span>
                            <span class="text-green-400">{{stats.activeUsers || 0}}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-green-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-server text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">System Status</p>
                            <h3 class="text-2xl font-bold text-gray-100"
                                ng-class="systemStatus.online ? 'text-green-400' : 'text-red-400'">
                                {{systemStatus.online ? 'Online' : 'Offline'}}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Uptime</span>
                            <span class="text-yellow-400">{{systemStatus.uptime || '99.9%'}}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-purple-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-bug text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Errors (24h)</p>
                            <h3 class="text-2xl font-bold text-gray-100"
                                ng-class="stats.errors > 0 ? 'text-red-400' : 'text-green-400'">
                                {{stats.errors || 0}}
                            </h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Resolved</span>
                            <span class="text-cyan-400">{{stats.resolvedErrors || 0}}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-yellow-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-code text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">API Calls</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.apiCalls || '0'}}</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Success Rate</span>
                            <span class="text-green-400">{{stats.apiSuccessRate || '100%'}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Logs -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] mb-8">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-xl font-bold text-gray-100">Recent System Logs</h2>
                    <button ng-click="refreshLogs()" class="text-cyan-400 hover:text-cyan-300">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-[#fff2]">
                                <th class="text-left py-3 px-4 text-gray-400">Time</th>
                                <th class="text-left py-3 px-4 text-gray-400">Type</th>
                                <th class="text-left py-3 px-4 text-gray-400">User</th>
                                <th class="text-left py-3 px-4 text-gray-400">Action</th>
                                <th class="text-left py-3 px-4 text-gray-400">Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr ng-repeat="log in systemLogs" class="border-b border-[#fff2]/30 hover:bg-[#0007]">
                                <td class="py-3 px-4 text-gray-300 text-sm">{{log.time}}</td>
                                <td class="py-3 px-4">
                                    <span class="text-xs px-2 py-1 rounded-full" ng-class="{
                                            'bg-green-500/20 text-green-300': log.type === 'info',
                                            'bg-yellow-500/20 text-yellow-300': log.type === 'warning',
                                            'bg-red-500/20 text-red-300': log.type === 'error'
                                          }">
                                        {{log.type}}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-gray-300">{{log.user || 'System'}}</td>
                                <td class="py-3 px-4 text-gray-300">{{log.action}}</td>
                                <td class="py-3 px-4 text-gray-300 text-sm">{{log.details}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Quick Actions for Tech -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">System Tools</h2>
                    <div class="space-y-3">
                        <button ng-click="clearCache()"
                            class="w-full flex items-center justify-between p-4 rounded-lg bg-[#0007] hover:bg-[#0009] border border-[#fff2] hover:border-cyan-500/50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-broom text-cyan-400 text-xl mr-3"></i>
                                <span class="text-gray-100">Clear Cache</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>

                        <button ng-click="runBackup()"
                            class="w-full flex items-center justify-between p-4 rounded-lg bg-[#0007] hover:bg-[#0009] border border-[#fff2] hover:border-green-500/50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-save text-green-400 text-xl mr-3"></i>
                                <span class="text-gray-100">Backup Database</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>

                        <button ng-click="checkUpdates()"
                            class="w-full flex items-center justify-between p-4 rounded-lg bg-[#0007] hover:bg-[#0009] border border-[#fff2] hover:border-yellow-500/50 transition-colors">
                            <div class="flex items-center">
                                <i class="fas fa-sync text-yellow-400 text-xl mr-3"></i>
                                <span class="text-gray-100">Check for Updates</span>
                            </div>
                            <i class="fas fa-chevron-right text-gray-400"></i>
                        </button>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">Database Status</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Connection</span>
                            <span class="text-green-400">✓ Connected</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Size</span>
                            <span class="text-gray-300">{{dbStats.size || 'Calculating...'}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Tables</span>
                            <span class="text-gray-300">{{dbStats.tables || 0}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Last Backup</span>
                            <span class="text-gray-300">{{dbStats.lastBackup || 'Never'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- SUPER ADMIN & ADMIN DASHBOARD (Roles 2, 3) -->
        <div ng-if="[2, 3].includes(user.role)">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-cyan-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-cyan-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-users text-cyan-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Total Users</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.totalUsers || 0}}</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex flex-row justify-around items-center gap-4 text-xs text-gray-400">
                            <div class="flex justify-between w-full">
                                <span>Students:</span>
                                <span>{{stats.students || 0}}</span>
                            </div>
                            <div class="flex justify-between w-full">
                                <span>Lecturers:</span>
                                <span>{{stats.lecturers || 0}}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-green-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-check text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Active Exams</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.activeExams || 0}}</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Today</span>
                            <span class="text-yellow-400">{{stats.todayExams || 0}}</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-purple-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-chart-line text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Avg. Score</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.avgScore || 0}}%</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">Pass Rate</span>
                            <span class="text-green-400">{{stats.passRate || 0}}%</span>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-[#0005] p-6 rounded-xl border border-[#fff2] hover:border-yellow-500/50 transition-colors">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center mr-4">
                            <i class="fa-solid fa-question text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Questions</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{stats.totalQuestions || 0}}</h3>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-400">This Week</span>
                            <span class="text-cyan-400">+{{stats.newQuestions || 0}}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Users & Exams -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Recent Users -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-100">Recent Users</h2>
                        <a href="<?php echo BASE_URL ?>/users" class="text-cyan-400 hover:text-cyan-300 text-sm">
                            View All
                        </a>
                    </div>
                    <div class="space-y-4">
                        <div ng-repeat="user in recentUsers"
                            class="flex items-center justify-between p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-500 to-purple-500 flex items-center justify-center text-white font-bold">
                                {{user.name.charAt(0)}}
                            </div>
                            <div class="max-w-[calc(100%-9rem)] overflow-hidden md:flex-1 md:max-w-auto">
                                <h3 class="font-medium text-gray-100">{{user.name}}</h3>
                                <p id="user-email" class="text-sm text-gray-400 overflow-auto">{{user.email}}</p>
                            </div>
                            <div class="text-right">
                                <span class="text-xs px-2 py-1 rounded-full bg-gray-700 text-gray-300">
                                    {{getRoleName(user.role)}}
                                </span>
                                <p class="text-xs text-gray-400 mt-1">{{user.created_at | date:'MMM d, y'}}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Exams -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-xl font-bold text-gray-100">Upcoming Exams</h2>
                        <a href="<?php echo BASE_URL ?>/exam/all" class="text-cyan-400 hover:text-cyan-300 text-sm">
                            View All
                        </a>
                    </div>
                    <div ng-if="upcomingExams && upcomingExams.length > 0" class="space-y-4">
                        <div ng-repeat="exam in upcomingExams"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100 capitalize">{{exam.title}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
                                    {{exam.date | date: 'MMM d, y'}}
                                </span>
                            </div>
                            <div class="flex justify-between items-center">
                                <p class="text-gray-400">Exam code: <span class="uppercase">{{exam.code}}</span></p>
                                <p class="text-sm text-gray-400">Duration: {{exam.duration}} mins</p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty state -->
                    <div ng-if="!upcomingExams || upcomingExams.length === 0"
                        class="p-6 text-center rounded-lg bg-[#0007] text-gray-400">
                        <i class="fas fa-calendar-times text-3xl mb-3 text-gray-500"></i>
                        <p class="text-sm">No upcoming exams scheduled</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- LECTURER DASHBOARD (Role 5) -->
        <div ng-if="user.role === 5">
            <!-- Lecturer Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-blue-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-list text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Active exams</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.activeExams || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-file-alt text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Exams</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.exams || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-user-check text-purple-400 text-xl"></i>

                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Enrolled Students</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.enrolledStudents || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-question text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Questions</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.questions || 0}}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Exams & Pending Reviews -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Upcoming Exams -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100" ng-class="{'mb-6' : myUpcomingExams.length > 0}">My
                        Upcoming Exams</h2>

                    <!-- Upcoming exams list -->
                    <div class="max-h-96 overflow-hidden" ng-if="myUpcomingExams.length > 0">
                        <div class="space-y-4 max-h-96 overflow-y-auto">
                            <div ng-repeat="exam in myUpcomingExams"
                                class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">

                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-100 capitalize">{{exam.title}}</h3>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300 capitalize">
                                        {{exam.date | fromNow}}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-400 mb-3">Code: <span class="uppercase">{{exam.code}}</span>
                                </p>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">{{exam.students}} student{{exam.students > 1 ? 's' :
                                        ''}} enrolled</span>
                                    <a href="<?php echo BASE_URL ?>/exam/edit/{{exam.id}}"
                                        class="text-cyan-400 hover:text-cyan-300">Edit</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- No upcoming exams state -->
                    <div ng-if="!myUpcomingExams.length"
                        class="flex flex-col items-center justify-center py-10 text-center h-[calc(100%_-_1.75rem)]">

                        <div class="w-14 h-14 rounded-full bg-cyan-500/20 flex items-center justify-center mb-4">
                            <i class="fas fa-calendar-times text-cyan-400 text-2xl"></i>
                        </div>

                        <p class="text-gray-300 font-medium">No Upcoming Exams</p>
                        <p class="text-sm text-gray-500 mt-1">
                            You haven’t scheduled any exams yet
                        </p>
                    </div>
                </div>

                <!-- Recently Attempted Exams -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100" ng-class="{'mb-6' : recentAttempts.length > 0}">Recently
                        Attempted Exams by Students</h2>

                    <!-- Recently attempted list -->
                    <div class="space-y-4 max-h-96 overflow-y-auto" ng-if="recentAttempts.length > 0">
                        <div ng-repeat="attempt in recentAttempts"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">

                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100 capitalize">{{attempt.student_name}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-green-500/20 text-green-300">
                                    Attempted
                                </span>
                            </div>

                            <p class="text-sm text-gray-400 mb-3 capitalize">{{attempt.exam_title}}</p>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">
                                    Attempted on: <span class="capitalize">{{attempt.attempted_date | fromNow}}</span>
                                </span>
                                <a href="<?php echo BASE_URL ?>/result/review/{{attempt.attempt_id}}/{{attempt.exam_id}}/{{attempt.student_id}}"
                                    class="text-cyan-400 hover:text-cyan-300">
                                    View Result
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- No recently attempted students -->
                    <div ng-if="!recentAttempts.length"
                        class="flex flex-col items-center justify-center py-10 text-center h-[calc(100%_-_1.75rem)]">

                        <div class="w-14 h-14 rounded-full bg-green-500/20 flex items-center justify-center mb-4">
                            <i class="fas fa-user-clock text-green-400 text-2xl"></i>
                        </div>

                        <p class="text-gray-300 font-medium">No Recent Attempts</p>
                        <p class="text-sm text-gray-500 mt-1">
                            No students have attempted exams recently
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- STUDENT DASHBOARD (Role 6) -->
        <div ng-if="user.role === 6">
            <!-- Student Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-blue-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-book-open text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Pass Rate</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{studentStats.passRate || 0}}%</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-check text-green-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Exams Taken</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{studentStats.examsTaken || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-purple-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-trophy text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Average Score</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{studentStats.avgScore || 0}}%</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-clock text-yellow-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">Upcoming Exams</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{studentStats.upcomingExams || 0}}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Upcoming Exams & Recent Results -->
            <div class="flex flex-wrap lg:flex-row items-start gap-6 mb-8">
                <!-- Upcoming Exams -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] flex-1 min-w-[350px]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">My Upcoming Exams</h2>

                    <div class="max-h-96 overflow-hidden">

                        <!-- Upcoming exams list -->
                        <div class="space-y-4 max-h-96 overflow-y-auto overflow-x-hidden"
                            ng-if="studentUpcomingExams.length > 0">

                            <div ng-repeat="exam in studentUpcomingExams"
                                class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">

                                <div class="flex justify-between items-start mb-2">
                                    <h3 class="font-medium text-gray-100 capitalize">{{ exam.title }}</h3>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300 first-letter-uppercase">
                                        {{ exam.date | fromNowDate }}
                                    </span>
                                </div>

                                <p class="text-sm text-gray-400 mb-3">{{ exam.duration | number }} mins</p>

                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-400">Starts: {{ exam.start_time | fromNowTime }}</span>
                                    <a href="<?php echo BASE_URL ?>/exam/attempt/{{ exam.id }}/register"
                                        class="text-cyan-400 hover:text-cyan-300">
                                        Start
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- No upcoming exams state -->
                        <div ng-if="studentUpcomingExams.length === 0"
                            class="flex flex-col items-center justify-center text-center py-12 text-gray-400">

                            <svg class="w-12 h-12 mb-3 text-gray-500" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>

                            <p class="text-sm font-medium text-gray-300">
                                No Upcoming Exams
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                You’re all caught up 🎉
                            </p>
                        </div>

                    </div>
                </div>


                <!-- Recent Results -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] flex-1 min-w-[350px]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">Recent Results</h2>

                    <!-- Results List -->
                    <div class="space-y-4 max-h-96 overflow-y-auto overflow-x-hidden" ng-if="recentResults.length > 0">
                        <div ng-repeat="result in recentResults"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">

                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100 capitalize">{{ result.exam_title }}</h3>
                                <span class="text-xl font-bold"
                                    ng-class="result.passed ? 'text-green-400' : 'text-red-400'">
                                    {{ result.score }}%
                                </span>
                            </div>

                            <p class="text-sm text-gray-400 mb-3">{{ result.code }}</p>

                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400 first-letter-uppercase">{{ result.date | fromNow }}</span>
                                <a href="<?php echo BASE_URL ?>/result/review/{{ result.attempt_id }}/{{ result.exam_id }}/{{ user.id }}"
                                    class="text-cyan-400 hover:text-cyan-300">Review</a>
                            </div>
                        </div>
                    </div>

                    <!-- No Results State -->
                    <div ng-if="recentResults.length === 0"
                        class="flex flex-col items-center justify-center text-center py-12 text-gray-400">
                        <svg class="w-12 h-12 mb-3 text-gray-500" fill="none" stroke="currentColor" stroke-width="1.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12h6m2 0a2 2 0 100-4H7a2 2 0 100 4m0 0v4a2 2 0 002 2h6a2 2 0 002-2v-4" />
                        </svg>
                        <p class="text-sm font-medium text-gray-300">No Results</p>
                        <p class="text-xs text-gray-500 mt-1">You haven’t completed any exams yet.</p>
                    </div>

                </div>


                <!-- Performance Overview -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2] flex-1 min-w-[350px]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">Performance Overview</h2>
                    <div>
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Score Distribution</h3>
                        <div class="space-y-3">
                            <div ng-repeat="score in scoreDistribution" class="flex items-center justify-between">
                                <span class="text-gray-300">{{score.range}}</span>
                                <div class="flex-1 mx-4">
                                    <div class="w-full bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full bg-cyan-500"
                                            ng-style="{'width': score.percentage + '%'}"></div>
                                    </div>
                                </div>
                                <span class="text-gray-300">{{score.count}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>