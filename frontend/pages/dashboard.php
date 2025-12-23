<?php $this->extend('frontend'); ?>
<?php $this->controller('DashboardController'); ?>

<?php $this->start('content'); ?>
<div ng-controller="DashboardController" ng-init="init()">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-100">Dashboard</h1>
        <p class="text-gray-400">Welcome back, {{user.name}}! Here's your overview.</p>
        <p class="text-sm text-gray-500">User ID: {{user.id}} | Role: {{getRoleName(user.role)}}</p>
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
                            <i class="fas fa-question-circle text-yellow-400 text-xl"></i>
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
                            class="flex items-center p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div
                                class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-500 to-purple-500 flex items-center justify-center text-white font-bold mr-4">
                                {{user.name.charAt(0)}}
                            </div>
                            <div class="flex-1">
                                <h3 class="font-medium text-gray-100">{{user.name}}</h3>
                                <p class="text-sm text-gray-400">{{user.email}}</p>
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
                                <h3 class="font-medium text-gray-100">{{exam.title}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
                                    {{exam.date}}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">Duration: {{exam.duration}} mins</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">{{exam.students}} students</span>
                                <a href="<?php echo BASE_URL ?>/exams/edit/{{exam.id}}"
                                    class="text-cyan-400 hover:text-cyan-300">Manage</a>
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
                            <i class="fas fa-book-open text-blue-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Courses</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.courses || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-green-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-list text-green-400 text-xl"></i>
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
                            <i class="fas fa-users text-purple-400 text-xl"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-400">My Students</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{lecturerStats.students || 0}}</h3>
                        </div>
                    </div>
                </div>

                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <div class="flex items-center">
                        <div class="w-12 h-12 rounded-lg bg-yellow-500/20 flex items-center justify-center mr-4">
                            <i class="fas fa-question-circle text-yellow-400 text-xl"></i>
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
                    <h2 class="text-xl font-bold text-gray-100 mb-6">My Upcoming Exams</h2>
                    <div class="space-y-4">
                        <div ng-repeat="exam in myUpcomingExams"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100">{{exam.title}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
                                    {{exam.date}}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">{{exam.course}}</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">{{exam.students}} students enrolled</span>
                                <a href="<?php echo BASE_URL ?>/exams/edit/{{exam.id}}"
                                    class="text-cyan-400 hover:text-cyan-300">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Reviews -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">Pending Reviews</h2>
                    <div class="space-y-4">
                        <div ng-repeat="review in pendingReviews"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100">{{review.student_name}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-300">
                                    Pending
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">{{review.exam_title}}</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Submitted: {{review.submitted_date}}</span>
                                <a href="<?php echo BASE_URL ?>/results/review/{{review.attempt_id}}/{{review.exam_id}}/{{review.student_id}}"
                                    class="text-cyan-400 hover:text-cyan-300">Review</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions for Lecturer -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <a href="<?php echo BASE_URL ?>/exams/create"
                        class="flex flex-col items-center justify-center p-4 rounded-lg bg-cyan-600/20 hover:bg-cyan-600/30 border border-cyan-500/30 transition-colors">
                        <i class="fas fa-plus-circle text-cyan-400 text-2xl mb-2"></i>
                        <span class="text-gray-100 text-sm">Create Exam</span>
                    </a>
                    <a href="<?php echo BASE_URL ?>/questions/create"
                        class="flex flex-col items-center justify-center p-4 rounded-lg bg-green-600/20 hover:bg-green-600/30 border border-green-500/30 transition-colors">
                        <i class="fas fa-question-circle text-green-400 text-2xl mb-2"></i>
                        <span class="text-gray-100 text-sm">Add Question</span>
                    </a>
                    <a href="<?php echo BASE_URL ?>/my-courses"
                        class="flex flex-col items-center justify-center p-4 rounded-lg bg-purple-600/20 hover:bg-purple-600/30 border border-purple-500/30 transition-colors">
                        <i class="fas fa-chalkboard-teacher text-purple-400 text-2xl mb-2"></i>
                        <span class="text-gray-100 text-sm">My Courses</span>
                    </a>
                    <a href="<?php echo BASE_URL ?>/my-results"
                        class="flex flex-col items-center justify-center p-4 rounded-lg bg-yellow-600/20 hover:bg-yellow-600/30 border border-yellow-500/30 transition-colors">
                        <i class="fas fa-chart-bar text-yellow-400 text-2xl mb-2"></i>
                        <span class="text-gray-100 text-sm">Results</span>
                    </a>
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
                            <p class="text-sm text-gray-400">My Courses</p>
                            <h3 class="text-2xl font-bold text-gray-100">{{studentStats.courses || 0}}</h3>
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
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Upcoming Exams -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">My Upcoming Exams</h2>
                    <div class="space-y-4">
                        <div ng-repeat="exam in studentUpcomingExams"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100">{{exam.title}}</h3>
                                <span class="text-xs px-2 py-1 rounded-full bg-cyan-500/20 text-cyan-300">
                                    {{exam.date}}
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">{{exam.course}} • {{exam.duration}} mins</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Starts: {{exam.start_time}}</span>
                                <a href="<?php echo BASE_URL ?>/exams/register/{{exam.hash}}"
                                    class="text-cyan-400 hover:text-cyan-300">Start</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Results -->
                <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                    <h2 class="text-xl font-bold text-gray-100 mb-6">Recent Results</h2>
                    <div class="space-y-4">
                        <div ng-repeat="result in recentResults"
                            class="p-4 rounded-lg bg-[#0007] hover:bg-[#0009] transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="font-medium text-gray-100">{{result.exam_title}}</h3>
                                <span class="text-xl font-bold"
                                    ng-class="result.passed ? 'text-green-400' : 'text-red-400'">
                                    {{result.score}}%
                                </span>
                            </div>
                            <p class="text-sm text-gray-400 mb-3">{{result.course}}</p>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">{{result.date}}</span>
                                <a href="<?php echo BASE_URL ?>/results/review/{{result.attempt_id}}/{{result.exam_id}}/{{user.id}}"
                                    class="text-cyan-400 hover:text-cyan-300">Review</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Overview -->
            <div class="bg-[#0005] p-6 rounded-xl border border-[#fff2]">
                <h2 class="text-xl font-bold text-gray-100 mb-6">Performance Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <div>
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Subject Performance</h3>
                        <div class="space-y-3">
                            <div ng-repeat="subject in subjectPerformance" class="flex items-center justify-between">
                                <span class="text-gray-300">{{subject.name}}</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-24 bg-gray-700 rounded-full h-2">
                                        <div class="h-2 rounded-full"
                                            ng-class="subject.score >= 70 ? 'bg-green-500' : subject.score >= 50 ? 'bg-yellow-500' : 'bg-red-500'"
                                            ng-style="{'width': subject.score + '%'}"></div>
                                    </div>
                                    <span class="text-gray-300 w-10 text-right">{{subject.score}}%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>