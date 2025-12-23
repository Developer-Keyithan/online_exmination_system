<?php $this->extend('frontend'); ?>
<?php $this->controller('MyResultsController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16">
    <!-- Header Section -->
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">My Results</h1>
            <p class="text-gray-400">Track your exam performance and progress</p>
        </div>

        <?php if (user_id() == 1):
            $stmt = db()->prepare("SELECT id, name FROM users WHERE user_group = 6");
            $stmt->execute();
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <span class="bg-[#0d1117] rounded-lg border border-[#fff2] text-gray-300 p-2 relative md:min-w-[250px]">
                <input type="hidden" ng-model="theLoggedUser.id" ng-change="loadResults()">

                <!-- Default label -->
                <div class="cursor-pointer w-full flex items-center justify-between"
                    ng-click="dropdownOpen = !dropdownOpen">
                    <p class="px-2">{{ theLoggedUser.name || 'Select a Student' }}</p>
                    <i class="fa-solid fa-chevron-down transition-all duration-300"
                        ng-class="{'rotate-180': dropdownOpen}"></i>
                </div>

                <!-- Dropdown list -->
                <div ng-show="dropdownOpen"
                    class="overflow-hidden mt-1 absolute top-full right-0 bg-[#0d1117] border border-[#fff2] rounded-lg w-full z-50">

                    <div class="p-2">
                        <!-- Search input -->
                        <input type="text" ng-model="studentSearch" placeholder="Search students..."
                            class="w-full p-2 rounded bg-[#0d1117] border border-[#fff2] text-gray-300 focus:outline-none" />
                    </div>

                    <div class="max-h-60 overflow-y-auto p-2">

                        <!-- All Students option -->
                        <div class="rounded-lg px-4 py-2 text-gray-300 hover:bg-cyan-500/30 cursor-pointer transition-all duration-300"
                            ng-class="{'bg-cyan-500/70 hover:bg-cyan-500/60 text-white': !selectedStudent.id}"
                            ng-click="selectedStudent = {id:0,name:'All Students'}; dropdownOpen = false; loadResults()">
                            All Students
                        </div>

                        <!-- Individual students -->
                        <?php foreach ($students as $student): ?>
                            <?php if (!isset($student['id']))
                                continue; ?>
                            <div class="rounded-lg px-4 py-2 text-gray-300 hover:bg-cyan-500/30 cursor-pointer transition-all duration-300"
                                ng-class="{'bg-cyan-500/70 hover:bg-cyan-500/60 text-white': selectedStudent && selectedStudent.id === <?= $student['id'] ?>}"
                                ng-show="'<?= strtolower(addslashes($student['name'])) ?>'.indexOf((studentSearch || '').toLowerCase()) !== -1"
                                ng-click="theLoggedUser = {id: <?php echo $student['id'] ?>, name: '<?php echo $student['name'] ?>'}; dropdownOpen = false; loadResults()">
                                <?= htmlspecialchars($student['name']) ?>
                            </div>
                        <?php endforeach; ?>

                    </div>
                </div>
            </span>
        <?php endif; ?>

    </div>

    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading Results...</h3>
            <p class="text-gray-400">Please wait while we fetch your data.</p>
        </div>
    </div>

    <!-- Overall Stats Section -->
    <div ng-cloak ng-if="!loading && results.length > 0" class="mb-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <!-- Total Attempts -->
            <div class="bg-[#0005] p-4 rounded-lg hover:bg-[#0007] transition-colors border border-[#fff2]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-cyan-500/20 flex items-center justify-center mr-3">
                        <i class="fas fa-clipboard-list text-cyan-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Total Exams</p>
                        <h3 class="text-xl font-bold text-gray-100">{{stats.totalExams || 0}}</h3>
                    </div>
                </div>
            </div>

            <!-- Average Score -->
            <div class="bg-[#0005] p-4 rounded-lg hover:bg-[#0007] transition-colors border border-[#fff2]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-green-500/20 flex items-center justify-center mr-3">
                        <i class="fas fa-chart-line text-green-400"></i>
                    </div>
                    <div class="w-[calc(100%-2.5rem)]">
                        <p class="text-sm text-gray-400">Average Score</p>
                        <div class="flex flex-row gap-4 items-center w-full">
                            <h3 class="text-xl font-bold text-gray-100">{{stats.averageScore || 0}}%</h3>
                            <div class="w-full bg-gray-700 rounded-full h-2 mt-2 overflow-hidden">
                                <div class="bg-green-500 h-2 rounded-full"
                                    ng-style="{'width': (stats.averageScore || 0) + '%'}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pass Rate -->
            <div class="bg-[#0005] p-4 rounded-lg hover:bg-[#0007] transition-colors border border-[#fff2]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-teal-500/20 flex items-center justify-center mr-3">
                        <i class="fas fa-trophy text-teal-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Pass Rate</p>
                        <div class="flex flex-row items-center gap-4">
                            <h3 class="text-xl font-bold text-gray-100">{{stats.passRate || 0}}%</h3>
                            <div class="text-xs" ng-class="getPerformanceColor(stats.passRate)">
                                <i class="fas" ng-class="getPerformanceIcon(stats.passRate)"></i>
                                {{ getPerformanceLabel(stats.passRate) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Highest Score -->
            <div class="bg-[#0005] p-4 rounded-lg hover:bg-[#0007] transition-colors border border-[#fff2]">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center mr-3">
                        <i class="fas fa-star text-purple-400"></i>
                    </div>
                    <div>
                        <p class="text-sm text-gray-400">Highest Score</p>
                        <h3 class="text-xl font-bold text-gray-100">{{stats.highestScore || 0}}%</h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Trend Chart (Placeholder) -->
        <!-- <div class="bg-[#0005] p-4 rounded-lg mb-6 border border-[#fff2]">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="text-lg font-medium text-gray-100">Performance Trend</h3>
                    <p class="text-sm text-gray-400">Your scores over the last 6 months</p>
                </div>
                <select class="bg-[#0007] text-gray-300 text-sm rounded-lg px-3 py-1 border border-[#fff2]">
                    <option>Last 6 Months</option>
                    <option>Last Year</option>
                    <option>All Time</option>
                </select>
            </div>
            <div
                class="h-64 flex items-center justify-center text-gray-400 border border-dashed border-gray-700 rounded-lg">
                <div class="text-center">
                    <i class="fas fa-chart-bar text-3xl mb-2"></i>
                    <p>Performance chart will appear here</p>
                </div>
            </div>
        </div> -->
    </div>

    <!-- Filters Section -->
    <div ng-cloak ng-if="!loading && results.length > 0" class="mb-6">
        <div class="flex flex-wrap justify-between items-center gap-4">
            <div class="flex flex-wrap gap-3">
                <button ng-click="setFilter('all')"
                    ng-class="{'bg-cyan-600 text-white': currentFilter === 'all', 'bg-[#0005] text-gray-300': currentFilter !== 'all'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    All Results
                </button>
                <button ng-click="setFilter('passed')"
                    ng-class="{'bg-green-600 text-white': currentFilter === 'passed', 'bg-[#0005] text-gray-300': currentFilter !== 'passed'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Passed
                </button>
                <button ng-click="setFilter('failed')"
                    ng-class="{'bg-red-600 text-white': currentFilter === 'failed', 'bg-[#0005] text-gray-300': currentFilter !== 'failed'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Failed
                </button>
                <button ng-click="setFilter('recent')"
                    ng-class="{'bg-blue-600 text-white': currentFilter === 'recent', 'bg-[#0005] text-gray-300': currentFilter !== 'recent'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Recent
                </button>
                <button ng-click="setFilter('top')"
                    ng-class="{'bg-yellow-600 text-white': currentFilter === 'top', 'bg-[#0005] text-gray-300': currentFilter !== 'top'}"
                    class="px-4 py-2 rounded-lg transition-colors">
                    Top Scores
                </button>
            </div>
        </div>
    </div>

    <!-- Results Grid -->
    <div ng-cloak ng-if="!loading && filteredResults.length > 0" class="space-y-4">
        <!-- For Students: Individual Results View -->
        <div ng-repeat="result in filteredResults"
            class="bg-[#0003] rounded-xl shadow-md border border-[#fff2] hover:shadow-lg transition-shadow hover:border-cyan-500/50">
            <div class="p-6">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <!-- Exam Info -->
                    <div class="">
                        <div class="flex items-start space-x-4">
                            <div class="flex-shrink-0">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center text-2xl font-bold"
                                    ng-class="result.percentage >= result.passing_percentage ? 'bg-green-500/20 text-green-400' : 'bg-red-500/20 text-red-400'">
                                    {{result.percentage}}%
                                </div>
                            </div>
                            <div>
                                <h3 class="font-semibold text-lg text-gray-200 capitalize mb-1">
                                    {{result.exam_title}}
                                </h3>
                                <p class="text-sm text-gray-400 mb-2">Code: <span
                                        class="uppercase">{{result.exam_code}}</span></p>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs px-2 py-1 rounded-full"
                                        ng-class="result.percentage >= result.passing_percentage ? 'bg-green-500/20 text-green-300 border border-green-500' : 'bg-red-500/20 text-red-300 border border-red-500'">
                                        <i class="fas"
                                            ng-class="result.percentage >= result.passing_percentage ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                        {{result.percentage >= result.passing_percentage ? 'Passed' : 'Failed'}}
                                    </span>
                                    <span
                                        class="text-xs px-2 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500">
                                        <i class="fas fa-calendar-alt"></i>
                                        {{result.completed_date | formatDateTime: 'MMM DD, YYYY'}}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Score Details -->
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">Score</p>
                        <p class="text-xl font-bold text-gray-100">{{result.score}}/{{result.total_marks}}</p>
                    </div>
                    <div class="text-center">
                        <p class="text-xs text-gray-400 mb-1">Time Taken</p>
                        <p class="text-xl font-bold text-gray-100">{{result.time_taken | formatTime}}</p>
                        <p class="text-xs"
                            ng-class="result.time_taken_percentage <= 80 ? 'text-green-400' : 'text-yellow-400'">
                            {{result.time_taken_percentage}}% of duration
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="">
                        <div class="flex flex-col space-y-3">
                            <a href="<?php echo BASE_URL ?>/result/review/{{result.id}}/{{result.exam_id}}/{{theLoggedUser.id}}"
                                class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-eye"></i>
                                <span>Review Answers</span>
                            </a>
                            <!-- <button ng-if="result.allow_retake"
                                class="bg-yellow-600 hover:bg-yellow-700 text-white py-2 px-4 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-2">
                                <i class="fas fa-redo"></i>
                                <span>Retake Exam</span>
                            </button> -->
                        </div>
                    </div>
                </div>

                <!-- Progress Bar and Additional Info -->
                <div class="mt-6">
                    <div class="flex justify-between text-sm mb-2">
                        <span class="text-gray-400">Performance</span>
                        <span class="text-gray-300">{{result.percentage || 'N/A'}}% (Passing:
                            {{result.passing_percentage
                            || 'N/A'}}%)</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-3 mb-4">
                        <div class="h-3 rounded-full"
                            ng-class="result.percentage >= result.passing_percentage ? 'bg-green-500' : 'bg-red-500'"
                            ng-style="{'width': (result.percentage || 78) + '%'}"></div>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <div class="text-center">
                            <p class="text-gray-400">Correct</p>
                            <p class="text-green-400 font-medium">{{result.correct_answers ||
                                0}}/{{result.total_questions || 0}}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-400">Incorrect</p>
                            <p class="text-red-400 font-medium">{{result.incorrect_answers ||
                                0}}/{{result.total_questions || 0}}</p>
                        </div>
                        <div class="text-center">
                            <p class="text-gray-400">Skipped</p>
                            <p class="text-yellow-400 font-medium">{{result.skipped_questions ||
                                0}}/{{result.total_questions || 0}}</p>
                        </div>
                        <!-- <div class="text-center">
                            <p class="text-gray-400">Accuracy</p>
                            <p class="text-cyan-400 font-medium">{{ result.accuracy ? result.accuracy + '%' : 'N/A' }}
                            </p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- No Results State -->
    <div ng-cloak ng-if="!loading && filteredResults.length === 0 && results.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-clipboard-check text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No exam results yet</h3>
            <p class="text-gray-400 mb-6">Complete your assigned exams to see results here.</p>
        </div>
    </div>

    <!-- No Filtered Results State -->
    <div ng-cloak ng-if="!loading && results.length > 0 && filteredResults.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-search text-gray-300 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">No matching results found</h3>
            <p class="text-gray-400 mb-6">Try adjusting your filter criteria or check back later.</p>
            <button ng-click="clearFilters()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors hover:scale-105 transition-transform">
                <i class="fas fa-redo"></i>
                <span>Clear Filters</span>
            </button>
        </div>
    </div>

    <!-- Error State -->
    <div ng-cloak ng-if="error" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Failed to load results</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <button ng-click="loadResults()"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                    <i class="fas fa-redo"></i>
                    <span>Try Again</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>