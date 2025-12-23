<?php $this->extend('frontend'); ?>
<?php $this->controller('MyExamController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16">
    <!-- Header Section -->
    <div ng-cloak class="mb-6 flex flex-col md:flex-row md:items-start md:justify-between gap-4">
        <div>
            <div
                ng-show="(theLoggedUser.user === '5' || theLoggedUser.user === 5 || selectedUser.role_id === '5' || selectedUser.role_id === 5) && isUserSelect">
                <h1 class="text-2xl font-bold">
                    Your Created Exams
                </h1>
                <p class="text-gray-400">
                    Create, manage, and monitor all of your examinations in one place.
                </p>
            </div>

            <div
                ng-show="(theLoggedUser.user === '6' || theLoggedUser.user === 6 || selectedUser.role_id === '6' || selectedUser.role_id === 6) && isUserSelect">
                <h1 class="text-2xl font-bold">
                    Your Exams
                </h1>
                <p class="text-gray-400">
                    View and attempt only the examinations you are registered for.
                </p>
            </div>

            <div
                ng-show="(theLoggedUser.user === '7' || theLoggedUser.user === 7 || selectedUser.role_id === '7' || selectedUser.role_id === 7) && isUserSelect">
                <h1 class="text-2xl font-bold">
                    Child's Exams
                </h1>
                <p class="text-gray-400">
                    View your child’s assigned examinations and monitor their progress and results.
                </p>
            </div>
        </div>


        <?php if (user_id() == 1):
            $stmt = db()->prepare("SELECT id, name FROM users WHERE user_group = 6");
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stmt = db()->prepare("SELECT id, name FROM users WHERE user_group = 5");
            $stmt->execute();
            $lecturers = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                <!-- Select a student -->
                <div
                    class="bg-[#0d1117] rounded-lg border border-[#fff2] text-gray-300 p-2 relative w-full md:min-w-[250px]">

                    <!-- Default label -->
                    <div class="cursor-pointer w-full flex items-center justify-between"
                        ng-click="studentDropdownOpen = !studentDropdownOpen; lecturerDropdownOpen = false;">
                        <p class="px-2">{{ selectedStudent.id ? selectedStudent.name : 'Select a student' }}</p>
                        <i class="fa-solid fa-chevron-down transition-all duration-300"
                            ng-class="{'rotate-180': studentDropdownOpen}"></i>
                    </div>

                    <!-- Dropdown list -->
                    <div ng-show="studentDropdownOpen"
                        class="overflow-hidden mt-1 absolute top-full right-0 bg-[#0d1117] border border-[#fff2] rounded-lg w-full z-50">

                        <div class="p-2">
                            <!-- Search input -->
                            <input type="text" ng-model="studentSearch" placeholder="Search students..."
                                class="w-full p-2 rounded bg-[#0d1117] border border-[#fff2] text-gray-300 focus:outline-none" />
                        </div>

                        <div class="max-h-60 overflow-y-auto p-2">

                            <!-- Individual students -->
                            <?php foreach ($students as $student): ?>
                                <?php if (!isset($student['id']))
                                    continue; ?>
                                <div class="rounded-lg px-4 py-2 text-gray-300 hover:bg-cyan-500/30 cursor-pointer transition-all duration-300"
                                    ng-class="{'bg-cyan-500/70 hover:bg-cyan-500/60 text-white': selectedStudent && selectedStudent.id === <?= $student['id'] ?>}"
                                    ng-show="'<?= strtolower(addslashes($student['name'])) ?>'.indexOf((studentSearch || '').toLowerCase()) !== -1"
                                    ng-click="selectStudent(<?= $student['id'] ?>, '<?= addslashes($student['name']) ?>'); studentDropdownOpen = false; selectedUser = selectedStudent; loadExams()">
                                    <?= htmlspecialchars($student['name']) ?>
                                </div>
                            <?php endforeach; ?>

                        </div>
                    </div>
                </div>

                <!-- Select a lecturer -->
                <div
                    class="bg-[#0d1117] rounded-lg border border-[#fff2] text-gray-300 p-2 relative w-full md:min-w-[250px]">

                    <!-- Default label -->
                    <div class="cursor-pointer w-full flex items-center justify-between"
                        ng-click="lecturerDropdownOpen = !lecturerDropdownOpen; studentDropdownOpen = false;">
                        <p class="px-2">{{ selectedLecturer.id ? selectedLecturer.name : 'Select a lecturer' }}</p>
                        <i class="fa-solid fa-chevron-down transition-all duration-300"
                            ng-class="{'rotate-180': lecturerDropdownOpen}"></i>
                    </div>

                    <!-- Dropdown list -->
                    <div ng-show="lecturerDropdownOpen"
                        class="overflow-hidden mt-1 absolute top-full right-0 bg-[#0d1117] border border-[#fff2] rounded-lg w-full z-50">

                        <div class="p-2">
                            <!-- Search input -->
                            <input type="text" ng-model="lecturerSearch" placeholder="Search Lecturers..."
                                class="w-full p-2 rounded bg-[#0d1117] border border-[#fff2] text-gray-300 focus:outline-none" />
                        </div>

                        <div class="max-h-60 overflow-y-auto p-2">

                            <!-- Individual students -->
                            <?php foreach ($lecturers as $lecturer): ?>
                                <?php if (!isset($lecturer['id']))
                                    continue; ?>
                                <div class="rounded-lg px-4 py-2 text-gray-300 hover:bg-cyan-500/30 cursor-pointer transition-all duration-300"
                                    ng-class="{'bg-cyan-500/70 hover:bg-cyan-500/60 text-white': selectedLecturer && selectedLecturer.id === <?= $lecturer['id'] ?>}"
                                    ng-show="'<?= strtolower(addslashes($lecturer['name'])) ?>'.indexOf((lecturerSearch || '').toLowerCase()) !== -1"
                                    ng-click="selectLecturer(<?= $lecturer['id'] ?>, '<?= addslashes($lecturer['name']) ?>'); lecturerDropdownOpen = false; selectedUser = selectedLecturer; loadExams()">
                                    <?= htmlspecialchars($lecturer['name']) ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Loading State -->
    <div ng-if="loading && isUserSelect" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading...</h3>
            <p class="text-gray-400">Please wait while we fetch your data.</p>
        </div>
    </div>

    <!-- For Lecturers: Show Create Button -->
    <div ng-cloak
        ng-if="!loading && (theLoggedUser.role == '5' || theLoggedUser.role == 5 || selectedUser.role_id == '5' || selectedUser.role_id == 5)"
        class="mb-6 flex flex-wrap gap-3">
        <div class="flex flex-wrap justify-between items-center gap-4 w-full">
            <div class="flex flex-wrap gap-3">
                <button ng-click="setFilter('all')"
                    ng-class="{'bg-cyan-600 text-white': currentFilter === 'all', 'bg-[#0005] text-gray-300': currentFilter !== 'all'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    All Exams
                </button>

                <button ng-click="setFilter('published')"
                    ng-class="{'bg-green-600 text-white': currentFilter === 'published', 'bg-[#0005] text-gray-300': currentFilter !== 'published'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Published
                </button>

                <button ng-click="setFilter('scheduled')"
                    ng-class="{'bg-blue-600 text-white': currentFilter === 'scheduled', 'bg-[#0005] text-gray-300': currentFilter !== 'scheduled'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Scheduled
                </button>

                <button ng-click="setFilter('live')"
                    ng-class="{'bg-emerald-600 text-white': currentFilter === 'live', 'bg-[#0005] text-gray-300': currentFilter !== 'live'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    On Live
                </button>

                <button ng-click="setFilter('ended')"
                    ng-class="{'bg-purple-600 text-white': currentFilter === 'ended', 'bg-[#0005] text-gray-300': currentFilter !== 'ended'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Ended
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
            <button ng-click="createExam()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-2 rounded-lg inline-flex items-center justify-center space-x-2 transition-colors w-full md:w-auto">
                <i class="fas fa-plus"></i>
                <span>Create New Exam</span>
            </button>
        </div>
    </div>

    <!-- For Students: Show Attempt Filters -->
    <div ng-cloak
        ng-if="!loading && (theLoggedUser.role == '6' || theLoggedUser.role == 6 || selectedUser.role_id == '6' || selectedUser.role_id == 6 || theLoggedUser.role == '7' || theLoggedUser.role == 7 || selectedUser.role_id == '7' || selectedUser.role_id == 7)"
        class="mb-6 flex flex-wrap gap-3">
        <button ng-click="setFilter('all')"
            ng-class="{'bg-cyan-600 text-white': currentFilter === 'all', 'bg-[#0005] text-gray-300': currentFilter !== 'all'}"
            class="px-4 py-2 rounded-lg transition-colors">
            All Exams
        </button>
        <button ng-click="setFilter('available')"
            ng-class="{'bg-green-600 text-white': currentFilter === 'available', 'bg-[#0005] text-gray-300': currentFilter !== 'available'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Available
        </button>
        <button ng-click="setFilter('in_progress')"
            ng-class="{'bg-yellow-600 text-white': currentFilter === 'in_progress', 'bg-[#0005] text-gray-300': currentFilter !== 'in_progress'}"
            class="px-4 py-2 rounded-lg transition-colors">
            In Progress
        </button>
        <button ng-click="setFilter('completed')"
            ng-class="{'bg-teal-600 text-white': currentFilter === 'completed', 'bg-[#0005] text-gray-300': currentFilter !== 'completed'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Completed
        </button>
        <button ng-click="setFilter('upcoming')"
            ng-class="{'bg-blue-600 text-white': currentFilter === 'upcoming', 'bg-[#0005] text-gray-300': currentFilter !== 'upcoming'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Upcoming
        </button>
        <button ng-click="setFilter('expired')"
            ng-class="{'bg-gray-600 text-white': currentFilter === 'expired', 'bg-[#0005] text-gray-300': currentFilter !== 'expired'}"
            class="px-4 py-2 rounded-lg transition-colors">
            Expired
        </button>
    </div>

    <!-- Exams Grid -->
    <div ng-cloak ng-if="!loading && filteredExams.length > 0"
        class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">

        <!-- For Lecturers: Created Exams View -->
        <div ng-repeat="exam in filteredExams"
            ng-show="theLoggedUser.role == '5' || theLoggedUser.role == 5 || selectedUser.role_id == '5' || selectedUser.role_id == 5"
            class="bg-[#0003] rounded-xl shadow-md border border-[#fff2] hover:shadow-lg transition-shadow hover:border-cyan-500/50 hover:scale-[1.02] transition-all duration-300">
            <!-- Exam Header -->
            <div class="p-4 border-b border-[#fff2] grid grid-rows-subgrid grid-start">
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
                        <div ng-if="activeExamMenu === exam.id"
                            class="absolute right-0 top-8 bg-[#0003] backdrop-blur rounded-lg shadow-lg border border-[#fff2] p-2 z-10 min-w-[200px]">
                            <a href="<?php echo BASE_URL . '/exam/edit/' ?>{{exam.id}}" ng-if="(theLoggedUser.role === '1' || theLoggedUser.role === 1 || exam.created_by ===  theLoggedUser.user) && exam.status !== 'live' && exam.status !== 'ended'"
                                class="w-full text-left px-4 py-2 text-sm text-cyan-500 hover:bg-[#12aac815] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-edit text-cyan-500"></i>
                                <span>Edit Exam</span>
                            </a>
                            <a href="<?php echo BASE_URL . '/exam/preview/' ?>{{exam.id}}" ng-if="exam.status !== 'draft'"
                                class="w-full text-left px-4 py-2 text-sm text-blue-500 hover:bg-[#00f3] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-eye text-blue-500"></i>
                                <span>Preview Exam</span>
                            </a>
                            <!-- <a href="<?php // echo BASE_URL . '/exam/results/' ?>{{exam.id}}"
                                class="w-full text-left px-4 py-2 text-sm text-green-500 hover:bg-[#0f03] transition-colors duration-300 flex items-center space-x-2 rounded-md">
                                <i class="fas fa-chart-bar text-green-500"></i>
                                <span>View Results</span>
                            </a> -->
                            <button
                                ng-if="(theLoggedUser.role === '1' || theLoggedUser.role === 1 || exam.created_by ===  theLoggedUser.user) && exam.status !== 'live' && exam.status !== 'ended'"
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
                            <span ng-switch-when="published"
                                class="px-2 py-1 rounded-full bg-green-500/20 text-green-300 border border-green-500">
                                <i class="fas fa-check-circle mr-1"></i> Published
                            </span>
                            <span ng-switch-when="scheduled"
                                class="px-2 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500">
                                <i class="fas fa-clock mr-1"></i> Scheduled
                            </span>
                            <span ng-switch-when="draft"
                                class="px-2 py-1 rounded-full bg-yellow-500/20 text-yellow-300 border border-yellow-500">
                                <i class="fas fa-edit mr-1"></i> Draft
                            </span>
                            <span ng-switch-when="canceled"
                                class="px-2 py-1 rounded-full bg-red-500/20 text-red-300 border border-red-500">
                                <i class="fas fa-ban mr-1"></i> Canceled
                            </span>
                            <span ng-switch-when="live"
                                class="px-2 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500 animate-pulse">
                                <i class="fas fa-broadcast-tower mr-1"></i> On Live
                            </span>
                            <span ng-switch-when="ended"
                                class="px-2 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500">
                                <i class="fas fa-flag-checkered mr-1"></i> Ended
                            </span>
                        </span>
                    </div>

                    <!-- Basic Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-clock text-cyan-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Duration</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.duration}} minutes</p>
                        </div>
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-question-circle text-purple-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Questions</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.total_questions || 25}} questions</p>
                        </div>
                    </div>

                    <!-- Marks Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-star text-yellow-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Total Marks</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.total_marks || 100}} marks</p>
                        </div>
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-flag text-green-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Passing</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.passing_marks || 40}} marks</p>
                        </div>
                    </div>

                    <!-- Schedule Info -->
                    <div ng-if="exam.schedule_type === 'scheduled'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-calendar-alt text-blue-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Schedule</p>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center text-gray-200 text-sm">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                <span>Starts: {{exam.start_time | formatDateTime: 'MMM DD, YYYY'}}</span>
                            </div>
                            <div class="flex items-center text-gray-200 text-sm">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                <span>Time: {{exam.start_time | formatDateTime: 'hh:mm a'}}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anytime Exam -->
                    <div ng-if="exam.schedule_type === 'anytime'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-infinity text-green-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Schedule Type</p>
                        </div>
                        <div class="flex items-center text-gray-200">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            <span class="text-sm">Anytime Exam</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Candidates can take anytime</p>
                    </div>

                    <!-- Participants Stats -->
                    <div ng-if="exam.status !== 'draft' && exam.status !== 'cancelled'" class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center justify-between mb-1">
                            <div class="flex items-center">
                                <i class="fas fa-users text-cyan-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Participants</p>
                            </div>
                            <span class="text-xs text-gray-400">{{exam.participants_count || 0}} enrolled</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 mb-2">
                            <div class="bg-green-500 h-2 rounded-full"
                                ng-style="{'width': ((exam.completed_count || 0) / (exam.participants_count || 1) * 100) + '%'}">
                            </div>
                        </div>
                        <div class="flex justify-between text-xs">
                            <span class="text-green-400">
                                <i class="fas fa-check-circle mr-1"></i>{{exam.completed_count || 0}} completed
                            </span>
                            <span class="text-yellow-400">
                                <i class="fas fa-clock mr-1"></i>{{(exam.participants_count || 0) -
                                (exam.completed_count || 0)}} pending
                            </span>
                        </div>
                    </div>

                    <!-- Exam Settings -->
                    <div ng-if="exam.status !== 'draft' && exam.status !== 'cancelled'" class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-cog text-purple-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Settings</p>
                        </div>
                        <div class="grid grid-cols-2 gap-1 text-xs">
                            <span class="text-gray-300" ng-if="exam.shuffle_questions">
                                <i class="fas fa-random text-green-400 mr-1"></i>Shuffle Q
                            </span>
                            <span class="text-gray-300" ng-if="exam.shuffle_options">
                                <i class="fas fa-random text-green-400 mr-1"></i>Shuffle Options
                            </span>
                            <span class="text-gray-300" ng-if="exam.full_screen_mode">
                                <i class="fas fa-expand text-blue-400 mr-1"></i>Full Screen
                            </span>
                            <span class="text-gray-300" ng-if="exam.allow_retake">
                                <i class="fas fa-redo text-yellow-400 mr-1"></i>Retake Allowed
                            </span>
                        </div>
                    </div>

                    <!-- Draft / Canceled -->
                    <div ng-if="exam.status === 'draft' || exam.status === 'canceled'"
                        class="bg-[#0005] p-3 rounded-lg min-h-[80px] flex items-center justify-center text-gray-300">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-yellow-400 mr-2" ng-if="exam.status === 'draft'"></i>
                            <i class="fas fa-ban text-red-400 mr-2" ng-if="exam.status === 'canceled'"></i>
                            <p class="text-xs">
                                {{ exam.status === 'draft' ? 'This exam is Draft' : 'This exam is Canceled' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- For Students: Attempt Exams View -->
        <div ng-repeat="exam in filteredExams"
            ng-show="theLoggedUser.role == '6' || theLoggedUser.role == 6 || selectedUser.role_id == '6' || selectedUser.role_id == 6 || theLoggedUser.role == '7' || theLoggedUser.role == 7 || selectedUser.role_id == '7' || selectedUser.role_id == 7"
            class="bg-[#0003] rounded-xl shadow-md border border-[#fff2] hover:shadow-lg transition-shadow hover:border-green-500/50 hover:scale-[1.02] transition-all duration-300">
            <!-- Exam Header -->
            <div class="p-4 border-b border-[#fff2]">
                <div class="flex justify-between items-start">
                    <div>
                        <h3 class="font-semibold text-lg text-gray-200 capitalize">{{exam.title}}</h3>
                        <p class="text-sm text-gray-400 mt-1 uppercase">{{exam.code}}</p>
                        <p class="text-xs text-gray-500 mt-1">Instructor: {{exam.instructor_name || 'Dr. John Smith'}}
                        </p>
                    </div>
                    <div class="relative">
                        <!-- Student exam status badge in header -->
                        <span ng-if="exam.attempt_status"
                            class="inline-flex items-center rounded-full text-xs px-2 py-1" ng-class="{
                                'bg-green-500/20 text-green-300 border border-green-500': exam.attempt_status === 'completed',
                                'bg-yellow-500/20 text-yellow-300 border border-yellow-500': exam.attempt_status === 'in_progress',
                                'bg-blue-500/20 text-blue-300 border border-blue-500': exam.attempt_status === 'available',
                                'bg-purple-500/20 text-purple-300 border border-purple-500': exam.attempt_status === 'upcoming',
                                'bg-gray-500/20 text-gray-300 border border-gray-500': exam.attempt_status === 'expired'
                            }">
                            <i class="fas mr-1 text-xs" ng-class="{
                                   'fa-check-circle': exam.attempt_status === 'completed',
                                   'fa-spinner fa-spin': exam.attempt_status === 'in_progress',
                                   'fa-play-circle': exam.attempt_status === 'available',
                                   'fa-clock': exam.attempt_status === 'upcoming',
                                   'fa-ban': exam.attempt_status === 'expired'
                               }"></i>
                            {{exam.attempt_status === 'completed' ? 'Completed' :
                            exam.attempt_status === 'in_progress' ? 'In Progress' :
                            exam.attempt_status === 'available' ? 'Available' :
                            exam.attempt_status === 'upcoming' ? 'Upcoming' :
                            'Expired'}}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Exam Details -->
            <div class="p-4">
                <div class="space-y-3">
                    <!-- Schedule Info -->
                    <div ng-if="exam.schedule_type === 'scheduled'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-calendar-alt text-blue-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Exam Schedule</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center text-gray-200 text-sm">
                                    <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                    <span>Date: {{exam.start_time | formatDateTime: 'MMM DD, YYYY'}}</span>
                                </div>
                                <span class="text-xs px-2 py-1 rounded bg-blue-900/30 text-blue-300">
                                    {{getDaysRemaining(exam.start_time)}}
                                </span>
                            </div>
                            <div class="flex items-center text-gray-200 text-sm">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                <span>Time: {{exam.start_time | formatDateTime: 'hh:mm a'}}</span>
                            </div>
                            <div class="flex items-center text-gray-200 text-sm">
                                <span class="w-2 h-2 bg-blue-400 rounded-full mr-2"></span>
                                <span>Duration: {{exam.duration}} minutes</span>
                            </div>
                        </div>
                    </div>

                    <!-- Anytime Exam -->
                    <div ng-if="exam.schedule_type === 'anytime'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-infinity text-green-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Exam Type</p>
                        </div>
                        <div class="flex items-center text-gray-200 mb-1">
                            <span class="w-2 h-2 bg-green-400 rounded-full mr-2"></span>
                            <span class="text-sm">Anytime Exam</span>
                        </div>
                        <p class="text-xs text-gray-400">Take this exam whenever you're ready</p>
                        <div class="mt-2 text-xs text-yellow-400">
                            <i class="fas fa-exclamation-circle mr-1"></i>Must be completed before: {{exam.end_time |
                            formatDateTime: 'MMM DD'}}
                        </div>
                    </div>

                    <!-- Exam Info -->
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-question-circle text-purple-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Questions</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.total_questions || 25}}</p>
                            <p class="text-xs text-gray-400 mt-1">Multiple choice</p>
                        </div>
                        <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                            <div class="flex items-center mb-1">
                                <i class="fas fa-star text-yellow-400 text-xs mr-2"></i>
                                <p class="text-xs text-gray-400">Total Marks</p>
                            </div>
                            <p class="text-gray-200 font-medium">{{exam.total_marks || 100}}</p>
                            <p class="text-xs text-gray-400 mt-1">{{exam.passing_marks || 40}} to pass</p>
                        </div>
                    </div>

                    <!-- Attempt Info -->
                    <div ng-if="exam.attempt_status === 'completed'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-trophy text-yellow-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Your Result</p>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <div class="text-2xl font-bold"
                                    ng-class="exam.percentage >= exam.passing_percentage ? 'text-green-400' : 'text-red-400'">
                                    {{exam.percentage || 78}}%
                                </div>
                                <div class="text-xs text-gray-400">Score Percentage</div>
                            </div>
                            <div class="text-right">
                                <div class="text-gray-200 font-medium">{{exam.your_score || 78}}/{{exam.total_marks ||
                                    100}}</div>
                                <div class="text-xs" ng-class="exam.is_passed ? 'text-green-400' : 'text-red-400'">
                                    {{exam.is_passed ? '✓ Passed' : '✗ Failed'}}
                                </div>
                            </div>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full"
                                ng-style="{'width': (exam.percentage || 78) + '%'}"></div>
                        </div>
                        <div class="flex justify-between text-xs mt-1">
                            <span class="text-gray-400">0%</span>
                            <span class="text-gray-400">Passing: {{exam.passing_percentage || 40}}%</span>
                            <span class="text-gray-400">100%</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-400">
                            <i class="fas fa-calendar-check mr-1"></i>
                            Completed on: {{exam.last_attempt_date | formatDateTime: 'MMM DD, YYYY'}}
                        </div>
                    </div>

                    <!-- Attempt Status -->
                    <div ng-if="exam.attempt_status !== 'completed'"
                        class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-info-circle text-cyan-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Status</p>
                        </div>
                        <div class="space-y-2">
                            <div class="flex items-center">
                                <div class="w-3 h-3 rounded-full mr-2" ng-class="{
                                        'bg-green-500 animate-pulse': exam.attempt_status === 'available',
                                        'bg-yellow-500': exam.attempt_status === 'in_progress',
                                        'bg-blue-500': exam.attempt_status === 'upcoming',
                                        'bg-gray-500': exam.attempt_status === 'expired'
                                    }"></div>
                                <span class="text-gray-200 text-sm">
                                    {{exam.attempt_status === 'available' ? 'Ready to start' :
                                    exam.attempt_status === 'in_progress' ? 'Continue where you left off' :
                                    exam.attempt_status === 'upcoming' ? 'Will be available soon' :
                                    'Attempt window has closed'}}
                                </span>
                            </div>

                            <div ng-if="exam.attempt_status === 'in_progress'" class="text-xs text-yellow-400">
                                <i class="fas fa-clock mr-1"></i>
                                Time remaining: {{exam.time_remaining || '45:30'}}
                            </div>

                            <div ng-if="exam.attempt_status === 'upcoming'" class="text-xs text-blue-400">
                                <i class="fas fa-hourglass-start mr-1"></i>
                                Starts in: {{getTimeUntilStart(exam.start_time)}}
                            </div>

                            <div ng-if="exam.attempts_remaining" class="text-xs text-green-400">
                                <i class="fas fa-redo mr-1"></i>
                                {{exam.attempts_remaining}} attempt{{exam.attempts_remaining > 1 ? 's' : ''}} remaining
                            </div>
                        </div>
                    </div>

                    <!-- Exam Instructions -->
                    <!-- <div class="bg-[#0005] p-3 rounded-lg hover:bg-[#0007] transition-colors">
                        <div class="flex items-center mb-1">
                            <i class="fas fa-clipboard-list text-purple-400 text-xs mr-2"></i>
                            <p class="text-xs text-gray-400">Instructions</p>
                        </div>
                        <ul class="text-xs text-gray-300 space-y-1 pl-2">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-400 text-xs mr-2 mt-0.5"></i>
                                <span>Read questions carefully before answering</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-400 text-xs mr-2 mt-0.5"></i>
                                <span>No external resources allowed</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-400 text-xs mr-2 mt-0.5"></i>
                                <span>Submit before time runs out</span>
                            </li>
                        </ul>
                    </div> -->
                </div>
            </div>

            <!-- Student Exam Actions -->
            <div class="p-4 flex items-end space-x-2 border-t border-[#fff2]">
                <a ng-if="exam.attempt_status === 'available'" href="<?php echo BASE_URL ?>/exam/attempt/{{exam.id}}/register"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1 hover:scale-105 transition-transform">
                    <i class="fas fa-play mr-1"></i>
                    <span>Start Exam</span>
                </a>

                <a ng-if="exam.attempt_status === 'in_progress'" href="<?php echo BASE_URL ?>/exam/attempt/{{exam.id}}/register"
                    class="flex-1 bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1 hover:scale-105 transition-transform">
                    <i class="fas fa-redo mr-1"></i>
                    <span>Continue</span>
                </a>

                <a ng-if="exam.attempt_status === 'completed'"
                    href="<?php echo BASE_URL ?>/result/review/{{exam.attempt_id}}/{{exam.id}}/{{selectedUser.id}}"
                    class="flex-1 bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1 hover:scale-105 transition-transform">
                    <i class="fas fa-chart-bar mr-1"></i>
                    <span>View Results</span>
                </a>

                <!-- <a ng-if="exam.attempt_status === 'upcoming' || exam.attempt_status === 'expired'"
                    href="<?php // echo BASE_URL ?>/exam/preview/{{exam.id}}"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1 hover:scale-105 transition-transform">
                    <i class="fas fa-eye mr-1"></i>
                    <span>View Details</span>
                </a> -->

                <!-- <button ng-if="exam.attempt_status === 'completed' && exam.allow_retake"
                    class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2 px-3 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1 hover:scale-105 transition-transform">
                    <i class="fas fa-redo mr-1"></i>
                    <span>Retake</span>
                </button> -->
            </div>
        </div>
    </div>

    <!-- No Exams State - Lecturer -->
    <div ng-cloak
        ng-if="!loading && filteredExams.length === 0 && exams.length === 0 && (theLoggedUser.role == '2' || theLoggedUser.role == 2) && isUserSelect"
        class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-clipboard-list text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No exams created yet</h3>
            <p class="text-gray-400 mb-6">Create your first exam to start assessing participants.</p>
            <button ng-click="createExam()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors hover:scale-105 transition-transform">
                <i class="fas fa-plus"></i>
                <span>Create Exam</span>
            </button>
        </div>
    </div>

    <!-- No Exams State - Student -->
    <div ng-cloak
        ng-if="!loading && filteredExams.length === 0 && exams.length === 0 && (theLoggedUser.role == '6' || theLoggedUser.role == 6 || theLoggedUser.role == '1' || theLoggedUser.role == 1) && isUserSelect"
        class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-user-clock text-yellow-400 text-6xl mb-4"></i>

            <h3 class="text-lg font-medium text-gray-100 mb-2">
                No Exams Found
            </h3>

            <p class="text-gray-400 mb-6">
                You have not registered for or attended any exams yet.
            </p>

            <button ng-click="requestExams()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-all hover:scale-105">
                <i class="fas fa-paper-plane"></i>
                <span>Request Exam Access</span>
            </button>
        </div>
    </div>

    <!-- No Results State -->
    <div ng-cloak ng-if="!loading && exams.length > 0 && filteredExams.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No matching exams found</h3>
            <p class="text-gray-400 mb-6">Try adjusting your filter criteria or check back later.</p>
            <button ng-click="clearFilters()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors hover:scale-105 transition-transform">
                <i class="fas fa-redo"></i>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>

    <!-- Error State -->
    <div ng-cloak ng-if="error && isUserSelect" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Failed to load exams</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <button ng-click="loadExams()"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                    <i class="fas fa-redo"></i>
                    <span>Try Again</span>
                </button>
            </div>
        </div>
    </div>

    <!-- User not selected state -->
    <div ng-cloak
        ng-if="!isUserSelect && (theLoggedUser.user === '1' || theLoggedUser.user === 1 || theLoggedUser.user === '2' || theLoggedUser.user === 2 || theLoggedUser.user === '3' || theLoggedUser.user === 3 || theLoggedUser.user === '4' || theLoggedUser.user === 4)"
        class="text-center py-12">

        <div class="max-w-md mx-auto">
            <i class="fas fa-user-slash text-cyan-400 text-6xl mb-4"></i>

            <h3 class="text-lg font-medium text-gray-100 mb-2">
                Select a User Type
            </h3>

            <p class="text-gray-400 mb-6">
                Please select a Student or Lecturer to continue.
            </p>
        </div>
    </div>

</div>
<?php $this->end(); ?>