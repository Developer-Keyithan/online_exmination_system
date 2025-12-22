<?php $this->extend('frontend'); ?>
<?php $this->controller('ExamController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16">
    <!-- Header Section -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Exams Management</h1>
        <p class="text-gray-400">Manage and monitor all examinations</p>
    </div>

    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading Exams...</h3>
            <p class="text-gray-400">Please wait while we fetch your data.</p>
        </div>
    </div>

    <!-- Filters -->
    <div ng-cloak ng-if="!loading && exams.length > 0" class="mb-6 flex flex-wrap gap-3">
        <button ng-click="setFilter('all')"
            ng-class="{'bg-cyan-600 text-white': currentFilter === 'all', 'bg-[#0005] text-gray-300': currentFilter !== 'all'}"
            class="px-4 py-2 rounded-lg transition-colors">
            All Exams
        </button>
        <button ng-click="setFilter('active')"
            ng-class="{'bg-green-600 text-white': currentFilter === 'active', 'bg-[#0005] text-gray-300': currentFilter !== 'active'}"
            class="px-4 py-2 rounded-lg transition-colors">
            On live
        </button>
        <button ng-click="setFilter('upcoming')"
            ng-class="{'bg-blue-600 text-white': currentFilter === 'upcoming', 'bg-[#0005] text-gray-300': currentFilter !== 'upcoming'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Upcoming
        </button>
        <button ng-click="setFilter('completed')"
            ng-class="{'bg-teal-600 text-white': currentFilter === 'completed', 'bg-[#0005] text-gray-300': currentFilter !== 'completed'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Completed
        </button>
        <button ng-click="setFilter('draft')"
            ng-class="{'bg-yellow-600 text-white': currentFilter === 'draft', 'bg-[#0005] text-gray-300': currentFilter !== 'draft'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Draft
        </button>
        <button ng-click="setFilter('canceled')"
            ng-class="{'bg-red-600 text-white': currentFilter === 'canceled', 'bg-[#0005] text-gray-300': currentFilter !== 'canceled'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Canceled
        </button>
    </div>

    <!-- Exams Grid -->
    <div ng-cloak ng-if="!loading && filteredExams.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        <div ng-repeat="exam in filteredExams"
            class="bg-[#0003] rounded-xl shadow-md border border-[#fff2] hover:shadow-lg transition-shadow">
            <!-- Exam Header -->
            <div class="p-4 border-b border-[#fff2]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-200 capitalize">{{exam.title}}</h3>
                        <p class="text-sm text-gray-400 mt-1 uppercase">{{exam.code}}</p>
                    </div>
                    <div class="relative">
                        <button ng-click="toggleExamMenu(exam.id)"
                            class="text-gray-400 p-1 rounded-lg hover:bg-[#fff3] transition-colors">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div ng-if="activeExamMenu === exam.id && (exam.status !== 'draft' || theLoggedUser.role === '1' || theLoggedUser.role === 1 || exam.created_by ===  theLoggedUser.user)"
                            class="absolute right-0 top-8 bg-[#0003] backdrop-blur rounded-lg shadow-lg border border-[#fff2] p-2 z-10 min-w-[200px]">
                            <a href="<?php echo BASE_URL . '/exam/edit/' ?>{{exam.id}}"
                                ng-if="(theLoggedUser.role === '1' || theLoggedUser.role === 1 || exam.created_by ===  theLoggedUser.user) && exam.status !== 'active' && exam.status !== 'completed'"
                                class="w-full text-left px-4 py-2 text-sm text-cyan-500 hover:bg-[#12aac815] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-edit text-cyan-500"></i>
                                <span>Edit Exam</span>
                            </a>
                            <a href="<?php echo BASE_URL . '/exam/preview/' ?>{{exam.id}}" ng-if="exam.status !== 'draft'"
                                class="w-full text-left px-4 py-2 text-sm text-blue-500 hover:bg-[#00f3] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-eye text-blue-500"></i>
                                <span>View Details</span>
                            </a>
                            <!-- <a href="<?php // echo BASE_URL . '/exam/results/' 
                                            ?>{{exam.id}}"
                                class="w-full text-left px-4 py-2 text-sm text-green-500 hover:bg-[#0f03] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-chart-bar text-green-500"></i>
                                <span>View Results</span>
                            </a> -->
                            <button
                                ng-if="(theLoggedUser.role === '1' || theLoggedUser.role === 1 || exam.created_by ===  theLoggedUser.user) && exam.status !== 'active' && exam.status !== 'completed'"
                                ng-click="deleteExam(exam)"
                                class="w-full text-left px-4 py-2 text-sm text-red-500 hover:bg-[#f001] transition-colors duration-300 hover:text-red-200 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-trash"></i>
                                <span>Delete Exam</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Status Badge -->
                    <div>
                        <span ng-switch="exam.status" class="inline-flex items-center rounded-full text-sm">
                            <span ng-switch-when="active"
                                class=" px-2 py-1 rounded-full bg-green-500/20 text-green-300 border border-green-500">
                                <i class="fas fa-play-circle mr-1"></i> Active
                            </span>
                            <span ng-switch-when="upcoming"
                                class=" px-2 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500">
                                <i class="fas fa-clock mr-1"></i> Upcoming
                            </span>
                            <span ng-switch-when="completed"
                                class=" px-2 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500">
                                <i class="fas fa-check-circle mr-1"></i> Completed
                            </span>
                            <span ng-switch-when="draft"
                                class=" px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500">
                                <i class="fas fa-edit mr-1"></i> Draft
                            </span>
                        </span>
                    </div>

                    <!-- Duration & Questions -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-[#0005] p-3 rounded-lg">
                            <p class="text-xs text-gray-400">Duration</p>
                            <p class="text-gray-200 font-medium">{{exam.duration}} minutes</p>
                        </div>
                        <div class="bg-[#0005] p-3 rounded-lg">
                            <p class="text-xs text-gray-400">Questions</p>
                            <p class="text-gray-200 font-medium">{{exam.total_questions || 0}} questions</p>
                        </div>
                    </div>

                    <!-- Scheduled Exam -->
                    <div class="bg-[#0005] p-3 rounded-lg" ng-if="exam.schedule_type === 'scheduled'">
                        <p class="text-xs text-gray-400 mb-1">Schedule Details</p>
                        <div class="flex items-center text-gray-200">
                            <i class="fas fa-calendar-alt mr-2 text-cyan-400"></i>
                            <span>{{exam.start_time | formatDateTime: 'MMM DD, YYYY'}}</span>
                            <i class="fas fa-clock mx-2 text-cyan-400"></i>
                            <span>{{exam.start_time | formatDateTime: 'hh:mm a'}}</span>
                        </div>
                    </div>

                    <!-- Anytime Exam -->
                    <div class="bg-[#0005] p-3 rounded-lg" ng-if="exam.schedule_type === 'anytime'">
                        <p class="text-xs text-gray-400 mb-1">Anytime Exam</p>
                        <div class="flex items-center text-gray-200">
                            <i class="fas fa-infinity mr-2 text-green-400"></i>
                            <span>This exam can be taken anytime</span>
                        </div>
                    </div>

                    <!-- Not fully ready / setup -->
                    <div class="bg-[#0005] p-3 rounded-lg"
                        ng-if="exam.schedule_type !== 'scheduled' && exam.schedule_type !== 'anytime'">
                        <p class="text-xs text-gray-400 mb-1">Incomplete Setup</p>
                        <div class="flex items-center text-gray-200">
                            <i class="fas fa-exclamation-circle mr-2 text-yellow-400"></i>
                            <span>This exam is not fully ready</span>
                        </div>
                    </div>

                    <!-- Participants -->
                    <div class="bg-[#0005] p-3 rounded-lg">
                        <p class="text-xs text-gray-400 mb-1">Participants</p>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center text-gray-200">
                                <i class="fas fa-users mr-2 text-cyan-400"></i>
                                <span>{{exam.participants_count || 0}} enrolled</span>
                            </div>
                            <div class="text-green-400 text-sm" ng-if="exam.completed_count">
                                <i class="fas fa-check-circle mr-1"></i>{{exam.completed_count}} completed
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Exams State -->
    <div ng-cloak ng-if="!loading && filteredExams.length === 0 && exams.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No exams created yet</h3>
            <p class="text-gray-400 mb-6">Create your first exam to start assessing participants.</p>
            <button ng-click="createExam()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-plus"></i>
                <span>Create Exam</span>
            </button>
        </div>
    </div>

    <!-- No Results State -->
    <div ng-cloak ng-if="!loading && exams.length > 0 && filteredExams.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No matching exams found</h3>
            <p class="text-gray-400 mb-6">Try adjusting your search or filter criteria.</p>
            <button ng-click="clearFilters()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-redo"></i>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>

    <!-- Error State -->
    <div ng-cloak ng-if="error" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Failed to load exams</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <button ng-click="loadExams()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-redo"></i>
                <span>Try Again</span>
            </button>
        </div>
    </div>
</div>
<?php $this->end(); ?>