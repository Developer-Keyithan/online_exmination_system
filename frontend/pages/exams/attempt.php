<?php $this->extend('frontend'); ?>
<?php $this->controller('ExamAttemptController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16" ng-cloak>
    <button id="fsBtn" ng-click="enterFullscreen()" class="hidden">
        Hidden Fullscreen Trigger
    </button>

    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading Exam...</h3>
            <p class="text-gray-400">Preparing your exam environment</p>
        </div>
    </div>

    <!-- Eligibility Modal -->
    <div ng-if="showEligibilityModal && !loading"
        class="fixed inset-0 bg-black/80 z-[9999999] flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-lg">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-400 mr-2"></i>
                    Exam Access Restricted
                </h3>
            </div>

            <div class="p-6">
                <!-- Error Icon based on error code -->
                <div class="text-center mb-6">
                    <div class="w-24 h-24 mx-auto mb-4 rounded-full border-4 flex items-center justify-center" ng-class="{
                            'bg-red-500/20 border-red-500/30': eligibilityError.code === 'MAX_ATTEMPTS_EXCEEDED' || eligibilityError.code === 'EXAM_ENDED' || eligibilityError.code === 'EXAM_CANCELED' || eligibilityError.code === 'EXAM_NOT_FOUND',
                            'bg-yellow-500/20 border-yellow-500/30': eligibilityError.code === 'EXAM_NOT_STARTED' || eligibilityError.code === 'EXAM_NOT_PUBLISHED',
                            'bg-blue-500/20 border-blue-500/30': eligibilityError.code === 'NOT_REGISTERED'
                        }">
                        <i class="fas fa-solid text-4xl" ng-class="{
                            'fa-ban text-red-400': eligibilityError.code === 'MAX_ATTEMPTS_EXCEEDED' || eligibilityError.code === 'EXAM_NOT_FOUND',
                            'fa-clock text-yellow-400': eligibilityError.code === 'EXAM_NOT_STARTED' || eligibilityError.code === 'EXAM_ENDED',
                            'fa-eye-slash text-yellow-400': eligibilityError.code === 'EXAM_NOT_PUBLISHED',
                            'fa-times-circle text-red-400': eligibilityError.code === 'EXAM_CANCELED',
                            'fa-solid fa-user-slash text-blue-400': eligibilityError.code === 'NOT_REGISTERED',
                            'fa-solid fa-exclamation-circle text-yellow-400': !eligibilityError.code
                        }"></i>
                    </div>

                    <h4 class="text-lg font-medium text-gray-100 mb-2">
                        {{eligibilityError.title || 'Access Denied'}}
                    </h4>
                    <p class="text-gray-400">{{eligibilityError.msg}}</p>
                </div>

                <!-- Error Details Card -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <h5 class="text-md font-medium text-gray-100 mb-3 flex items-center">
                        <i class="fas fa-info-circle text-cyan-400 mr-2"></i>
                        Error Details
                    </h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Error Code:</span>
                            <span class="font-mono px-2 py-1 rounded bg-gray-700 text-gray-300">
                                {{eligibilityError.code || 'UNKNOWN_ERROR'}}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Exam ID:</span>
                            <span class="text-cyan-400">{{ 'EX_' + ('0000' + examId).slice(-4) }}</span>
                        </div>
                        <div class="flex justify-between items-center" ng-if="eligibilityError.timestamp">
                            <span class="text-gray-400">Timestamp:</span>
                            <span class="text-gray-300">{{eligibilityError.timestamp | date:'medium'}}</span>
                        </div>
                    </div>

                    <!-- Specific guidance based on error code -->
                    <div ng-if="eligibilityError.code === 'EXAM_NOT_STARTED'"
                        class="mt-4 p-3 bg-blue-500/10 border border-blue-500/30 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-alt text-blue-400 text-xl mr-3"></i>
                            <div>
                                <div class="text-blue-300 font-medium">Exam Schedule</div>
                                <div class="text-blue-400 text-sm mt-1">
                                    Please wait until the scheduled start time to begin your exam.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="eligibilityError.code === 'MAX_ATTEMPTS_EXCEEDED'"
                        class="mt-4 p-3 bg-red-500/10 border border-red-500/30 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-chart-line text-red-400 text-xl mr-3"></i>
                            <div>
                                <div class="text-red-300 font-medium">Attempt Limit Reached</div>
                                <div class="text-red-400 text-sm mt-1">
                                    You have used all available attempts for this exam.
                                </div>
                            </div>
                        </div>
                    </div>

                    <div ng-if="eligibilityError.code === 'NOT_REGISTERED'"
                        class="mt-4 p-3 bg-blue-500/10 border border-blue-500/30 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-user-plus text-blue-400 text-xl mr-3"></i>
                            <div>
                                <div class="text-blue-300 font-medium">Registration Required</div>
                                <div class="text-blue-400 text-sm mt-1">
                                    Please register for this exam before attempting it.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3 p-4">
                    <a href="<?php echo BASE_URL; ?>/exam/my"
                        class="block w-full py-3 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors text-center flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                        <i class="fas fa-arrow-left"></i>
                        <span>Back to Exams List</span>
                    </a>

                    <button ng-click="retryEligibilityCheck()" ng-if="eligibilityError.code === 'EXAM_NOT_STARTED' || 
                                  eligibilityError.code === 'EXAM_ENDED' ||
                                  eligibilityError.code === 'EXAM_NOT_PUBLISHED'"
                        class="w-full py-3 rounded-lg border border-yellow-500 text-yellow-400 hover:bg-yellow-500/10 transition-colors hover:scale-105 transition-transform">
                        <i class="fas fa-redo mr-2"></i>
                        Check Again
                    </button>

                    <a ng-if="eligibilityError.code === 'NOT_REGISTERED'"
                        ng-href="<?php echo BASE_URL; ?>/exam/register/{{examId}}"
                        class="block w-full py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors text-center flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                        <i class="fas fa-user-plus"></i>
                        <span>Register Now</span>
                    </a>

                    <!-- <button ng-click="contactSupport()"
                        class="w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors hover:scale-105 transition-transform">
                        <i class="fas fa-headset mr-2"></i>
                        Contact Support
                    </button> -->
                </div>
            </div>
        </div>
    </div>

    <!-- Error State (for non-eligibility errors) -->
    <div ng-if="error && !loading && !showEligibilityModal" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Failed to Load Exam</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <div class="flex flex-wrap gap-3 justify-center">
                <a href="<?php echo BASE_URL; ?>/exam/my"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                    <i class="fas fa-arrow-left"></i>
                    <span>Back to Exams</span>
                </a>
                <button ng-click="retryLoading()"
                    class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                    <i class="fas fa-redo"></i>
                    <span>Retry</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Exam Container (Visible when eligible and loaded) -->
    <div ng-if="!loading && examData && isEligible && !showEligibilityModal">
        <!-- [Rest of the exam interface remains exactly the same as in your original code] -->
        <!-- Exam Header -->
        <div class="bg-[#0004] rounded-lg p-6 mb-6 border border-gray-600">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-100 capitalize">{{examData.title}}</h1>
                    <p class="text-gray-400"><span class="uppercase">{{examData.code}}</span> •
                        {{examData.total_questions}} Questions •
                        {{examData.total_marks}} Total marks</p>
                </div>

                <!-- Timer Section -->
                <div class="bg-[#0005] rounded-lg p-4 border border-gray-600 min-w-[280px]">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-clock text-cyan-400"></i>
                            <span class="text-gray-300 font-medium">Time Remaining</span>
                        </div>
                        <span class="text-sm px-2 py-1 rounded"
                            ng-class="timerWarning ? 'bg-red-500/20 text-red-300' : 'bg-green-500/20 text-green-300'">
                            {{timerWarning ? 'Hurry Up!' : 'On Time'}}
                        </span>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold mb-1" ng-class="timerWarning ? 'text-red-400' : 'text-cyan-400'">
                            {{timeRemainingFormatted}}
                        </div>
                        <div class="text-xs text-gray-400">
                            Exam Duration: {{examData.duration}} minutes
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions & Rules -->
            <div class="mt-6 p-4 bg-[#0005] rounded-lg border border-gray-600">
                <h3 class="text-lg font-medium text-gray-100 mb-3 flex items-center">
                    <i class="fas fa-clipboard-list text-cyan-400 mr-2"></i>
                    Exam Instructions
                </h3>
                <div class="text-gray-300 space-y-2 text-sm" ng-bind-html="examData.instructions | safeHtml"></div>

                <!-- Exam Rules -->
                <div class="mt-4 pt-4 border-t border-gray-600">
                    <h4 class="text-md font-medium text-gray-100 mb-2 flex items-center">
                        <i class="fas fa-gavel text-yellow-400 mr-2"></i>
                        Important Rules:
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            <span>Answer all questions before submitting</span>
                        </div>
                        <div class="flex items-center text-gray-300">
                            <i class="fas fa-check-circle text-green-400 mr-2"></i>
                            <span>Each question carries {{examData.marks_per_question || 4}} marks</span>
                        </div>
                        <div class="flex items-center text-gray-300" ng-if="examData.full_screen_mode">
                            <i class="fas fa-expand text-blue-400 mr-2"></i>
                            <span>Full screen mode is enabled</span>
                        </div>
                        <!-- <div class="flex items-center text-gray-300" ng-if="examData.negative_marking">
                            <i class="fas fa-minus-circle text-red-400 mr-2"></i>
                            <span>Negative marking: -{{examData.negative_mark || 1}} mark per wrong answer</span>
                        </div> -->
                        <div class="flex items-center text-gray-300" ng-if="examData.disable_copy_paste">
                            <i class="fas fa-ban text-red-400 mr-2"></i>
                            <span>Copy/paste is disabled</span>
                        </div>
                        <div class="flex items-center text-gray-300" ng-if="examData.show_results_immediately">
                            <i class="fas fa-chart-line text-green-400 mr-2"></i>
                            <span>Results will be shown immediately after submission</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats Bar -->
            <div ng-show="isExamStarted && !isExamEnded" class="mt-4 grid grid-cols-3 gap-3">
                <div class="bg-[#0005] p-3 rounded-lg text-center">
                    <div class="text-2xl font-bold text-cyan-400">{{answeredCount}}</div>
                    <div class="text-xs text-gray-400">Answered</div>
                </div>
                <div class="bg-[#0005] p-3 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-400">{{flaggedCount}}</div>
                    <div class="text-xs text-gray-400">Flagged</div>
                </div>
                <div class="bg-[#0005] p-3 rounded-lg text-center">
                    <div class="text-2xl font-bold text-gray-400">{{examData.total_questions - answeredCount}}</div>
                    <div class="text-xs text-gray-400">Remaining</div>
                </div>
            </div>
        </div>

        <!-- Main Exam Area -->
        <div ng-show="isExamStarted && !isExamEnded" class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Sidebar: Question Navigator -->
            <div class="lg:col-span-1">
                <div class="bg-[#0004] rounded-lg p-4 border border-gray-600 sticky top-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-list-ol text-cyan-400 mr-2"></i>
                        Question Navigator
                    </h3>

                    <!-- Progress Overview -->
                    <div class="mb-6">
                        <div class="flex justify-between text-sm text-gray-400 mb-1">
                            <span>Progress</span>
                            <span>{{answeredCount}}/{{examData.total_questions}}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-2 mb-2">
                            <div class="bg-green-500 h-2 rounded-full transition-all duration-300"
                                ng-style="{ width: ((answeredCount / examData.total_questions) * 100) + '%' }"></div>
                        </div>
                        <div class="flex justify-between text-xs text-gray-400">
                            <span>{{Math.round((answeredCount/examData.total_questions)*100)}}% complete</span>
                            <span>{{examData.total_questions - answeredCount}} left</span>
                        </div>
                    </div>

                    <!-- Question Grid -->
                    <div class="grid grid-cols-5 gap-2 mb-4 max-h-60 overflow-y-auto p-1">
                        <button ng-repeat="question in questions track by $index" ng-click="goToQuestion($index)"
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-200 hover:scale-110 hover:shadow-lg"
                            ng-class="{
                                    'bg-cyan-600 text-white border-cyan-500 shadow-lg scale-110': currentQuestionIndex === $index,
                                    'bg-green-600 text-white border-green-500': question.answer !== null && currentQuestionIndex !== $index,
                                    'bg-yellow-600 text-white border-yellow-500': question.flagged && currentQuestionIndex !== $index,
                                    'bg-gray-700 text-gray-300 border-gray-600 hover:bg-gray-600': question.answer === null && !question.flagged && currentQuestionIndex !== $index
                                }">
                            {{$index + 1}}
                        </button>
                    </div>

                    <!-- Legend -->
                    <div class="space-y-2 text-sm mb-6">
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded bg-cyan-600 mr-2"></div>
                            <span class="text-gray-300">Current Question</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded bg-green-600 mr-2"></div>
                            <span class="text-gray-300">Answered</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded bg-yellow-600 mr-2"></div>
                            <span class="text-gray-300">Flagged for Review</span>
                        </div>
                        <div class="flex items-center">
                            <div class="w-3 h-3 rounded bg-gray-700 mr-2"></div>
                            <span class="text-gray-300">Not Answered</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="space-y-2">
                        <button ng-click="flagCurrentQuestion()"
                            class="w-full py-2 px-4 rounded-lg border border-yellow-500 text-yellow-400 hover:bg-yellow-500/10 transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                            <i class="fas"
                                ng-class="questions[currentQuestionIndex].flagged ? 'fa-flag-checkered' : 'fa-flag'"></i>
                            <span>{{questions[currentQuestionIndex].flagged ? 'Unflag' : 'Flag'}} Question</span>
                        </button>
                        <button ng-click="clearAnswer()"
                            class="w-full py-2 px-4 rounded-lg border border-red-500 text-red-400 hover:bg-red-500/10 transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                            <i class="fas fa-eraser"></i>
                            <span>Clear Answer</span>
                        </button>
                        <button ng-click="saveAnswer()"
                            class="w-full py-2 px-4 rounded-lg border border-green-500 text-green-400 hover:bg-green-500/10 transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                            <i class="fas fa-save"></i>
                            <span>Save Answer</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Content Area: Question Display -->
            <div class="lg:col-span-3">
                <!-- Current Question -->
                <!-- Main Question Container -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mb-6">
                    <!-- Section Descriptions -->
                    <p ng-if="currentQuestion.sectionDescription" class="text-gray-300 mb-4"
                        ng-class="currentQuestion.sectionSecondDescription ? '' : 'border-b border-gray-600/70 pb-4'">
                        {{ currentQuestion.sectionDescription }}
                    </p>
                    <p ng-if="currentQuestion.sectionSecondDescription"
                        class="text-gray-300 mb-4 border-b border-gray-600/70 pb-4">
                        {{ currentQuestion.sectionSecondDescription }}
                    </p>

                    <!-- Question Header -->
                    <div class="flex justify-between items-start mb-6">
                        <!-- Left side: Question identification -->
                        <div>
                            <div class="flex items-center space-x-3 mb-2">
                                <!-- Question number with cyan accent for visual prominence -->
                                <span class="text-lg font-bold text-cyan-400">
                                    Question {{ currentQuestionIndex + 1 }}
                                </span>

                                <!-- Flagged indicator - only shown when question is marked for review -->
                                <span ng-if="questions[currentQuestionIndex].flagged"
                                    class="text-xs px-2 py-1 bg-yellow-500/20 text-yellow-300 rounded-full">
                                    <i class="fas fa-flag mr-1"></i>Flagged for Review
                                </span>
                            </div>
                        </div>

                        <!-- Right side: Question metrics -->
                        <div class="flex items-center space-x-4">
                            <!-- Marks display - shows point value of current question -->
                            <div class="text-sm">
                                <span class="text-gray-400">Marks:</span>
                                <span class="text-yellow-400 font-bold ml-1">
                                    {{ currentQuestion.marks }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Question Content Area -->
                    <div class="mb-8">
                        <!-- Question Text -->
                        <div class="text-gray-100 text-lg leading-relaxed mb-4"
                            ng-bind-html="currentQuestion.question | safeHtml">
                        </div>

                        <!-- Question Image Container -->
                        <div ng-if="currentQuestion.image" class="mb-6">
                            <div class="relative">
                                <!-- Question image with border and size constraints -->
                                <img ng-src="{{ currentQuestion.image }}" alt="Question Image"
                                    class="max-w-full max-h-96 rounded-lg border border-gray-600">

                                <!-- Image label overlay positioned top-right -->
                                <div class="absolute top-2 right-2 bg-black/70 text-white text-xs px-2 py-1 rounded">
                                    <i class="fas fa-image mr-1"></i>Question Image
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Answer Options Container -->
                    <div class="space-y-4 mb-8">
                        <!-- Individual Option -->
                        <div ng-repeat="option in currentQuestion.options track by $index" class="rounded-lg py-3 px-4 transition-all duration-200 cursor-pointer 
                            hover:border-cyan-500 hover:bg-cyan-500/5 hover:shadow-lg hover:scale-[1.01]" ng-class="{
                            'border-2 border-cyan-500 bg-cyan-500/10 shadow-lg scale-[1.01]': currentQuestion.answer === option.op,
                            'border-2 border-gray-600': currentQuestion.answer !== option.op
                        }" ng-click="selectAnswer(option.op)">

                            <!-- Option inner layout using flexbox -->
                            <div class="flex items-center">
                                <!-- Option letter indicator (A, B, C, D) -->
                                <div class="mr-4">
                                    <!-- Circular indicator that changes based on selection state -->
                                    <div class="w-10 h-10 rounded-full border-2 flex items-center justify-center 
                                transition-all duration-200" ng-class="currentQuestion.answer === option.op ? 
                                   'border-cyan-500 bg-cyan-500 shadow-lg' : 
                                   'border-gray-500 hover:border-cyan-400'">
                                        <span class="text-sm font-medium" ng-class="currentQuestion.answer === option.op ? 
                                        'text-white' : 'text-gray-300'">
                                            {{ ["A","B","C","D"][$index] }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Option content area -->
                                <div class="flex-1">
                                    <!-- Option text (supports HTML content) -->
                                    <div class="text-gray-100 text-lg" ng-bind-html="option.text | safeHtml">
                                    </div>

                                    <!-- Optional option-specific image -->
                                    <div ng-if="option.image" class="mt-3">
                                        <img ng-src="{{ option.image }}" alt="Option Image"
                                            class="max-w-48 max-h-48 rounded border border-gray-600">
                                    </div>
                                </div>

                                <!-- Selection confirmation indicator -->
                                <div class="ml-4" ng-if="currentQuestion.answer === option.op">
                                    <div
                                        class="w-10 h-10 rounded-full bg-cyan-500 flex items-center justify-center animate-pulse">
                                        <i class="fas fa-check text-white text-lg"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Controls -->
                    <div class="flex justify-between mt-8 pt-6 border-t border-gray-600">
                        <!-- Previous Question Button -->
                        <button ng-click="previousQuestion()" ng-disabled="currentQuestionIndex === 0" class="px-6 py-3 rounded-lg border border-gray-600 text-gray-300 
                       hover:bg-gray-700 transition-colors flex items-center space-x-2 
                       disabled:opacity-50 disabled:cursor-not-allowed hover:scale-105 transition-transform">
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous Question</span>
                        </button>

                        <!-- Action Buttons Container -->
                        <div class="flex space-x-3">
                            <!-- Save & Mark Button -->
                            <button ng-click="saveAndMark()" class="px-6 py-3 rounded-lg bg-yellow-600 hover:bg-yellow-700 
                           text-white transition-colors flex items-center space-x-2 
                           hover:scale-105 transition-transform">
                                <i class="fas fa-bookmark"></i>
                                <span>Save & Mark</span>
                            </button>

                            <!-- Save & Next Button -->
                            <button ng-click="saveAndNext()" class="px-6 py-3 rounded-lg bg-cyan-600 hover:bg-cyan-700 
                           text-white transition-colors flex items-center space-x-2 
                           hover:scale-105 transition-transform">
                                <span>Save & Next</span>
                                <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Exam Controls -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <div class="flex flex-col md:flex-row justify-between items-center gap-4">
                        <!-- Quick Stats -->
                        <div class="flex flex-wrap gap-6">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-green-400">{{answeredCount}}</div>
                                <div class="text-xs text-gray-400">Answered</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-yellow-400">{{flaggedCount}}</div>
                                <div class="text-xs text-gray-400">Flagged</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-red-400">{{examData.total_questions -
                                    answeredCount}}</div>
                                <div class="text-xs text-gray-400">Unanswered</div>
                            </div>
                            <div class="text-center">
                                <div class="text-2xl font-bold text-blue-400">
                                    {{Math.round((answeredCount/examData.total_questions)*100)}}%</div>
                                <div class="text-xs text-gray-400">Progress</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-3">
                            <button ng-click="reviewExam()"
                                class="px-6 py-3 rounded-lg border border-blue-500 text-blue-400 hover:bg-blue-500/10 transition-colors flex items-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-search"></i>
                                <span>Review Exam</span>
                            </button>

                            <button ng-click="saveAllAnswers()"
                                class="px-6 py-3 rounded-lg border border-green-500 text-green-400 hover:bg-green-500/10 transition-colors flex items-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-save"></i>
                                <span>Save All</span>
                            </button>

                            <button ng-click="showSubmitModal()"
                                class="px-6 py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors flex items-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-paper-plane"></i>
                                <span>Submit Exam</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Review Modal -->
    <div ng-if="showReviewModal" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div
            class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-6xl max-h-[90vh] overflow-hidden">
            <div class="p-6 border-b border-gray-600 flex justify-between items-center">
                <h3 class="text-xl font-bold text-gray-100">
                    <i class="fas fa-search text-blue-400 mr-2"></i>
                    Exam Review Dashboard
                </h3>
                <button ng-click="closeReviewModal()"
                    class="text-gray-400 hover:text-white hover:scale-110 transition-transform">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>

            <div class="p-6 overflow-y-auto max-h-[70vh]">
                <!-- Question Grid -->
                <div class="mb-8">
                    <h4 class="text-lg font-medium text-gray-100 mb-4">All Questions</h4>
                    <div class="grid grid-cols-8 gap-3">
                        <div ng-repeat="question in questions track by $index"
                            ng-click="goToQuestion($index); closeReviewModal()"
                            class="aspect-square rounded-lg flex flex-col items-center justify-center cursor-pointer transition-all duration-200 hover:scale-110 hover:shadow-lg"
                            ng-class="{
                                 'bg-cyan-600 text-white shadow-lg scale-110': currentQuestionIndex === $index,
                                 'bg-green-600 text-white': question.answer !== null,
                                 'bg-yellow-600 text-white': question.flagged,
                                 'bg-gray-700 text-gray-300 hover:bg-gray-600': question.answer === null && !question.flagged
                             }">
                            <span class="font-bold text-lg">{{$index + 1}}</span>
                            <span class="text-xs mt-1">{{question.marks}}m</span>
                        </div>
                    </div>
                </div>

                <!-- Stats and Actions -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Summary Card -->
                    <div class="bg-[#0005] rounded-lg p-6 border border-gray-600">
                        <h4 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-chart-bar text-cyan-400 mr-2"></i>
                            Exam Summary
                        </h4>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Total Questions:</span>
                                <span class="text-gray-100 font-bold">{{examData.total_questions}}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Answered:</span>
                                <span class="text-green-400 font-bold">{{answeredCount}}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Unanswered:</span>
                                <span class="text-red-400 font-bold">{{examData.total_questions - answeredCount}}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-400">Flagged:</span>
                                <span class="text-yellow-400 font-bold">{{flaggedCount}}</span>
                            </div>
                            <div class="pt-4 border-t border-gray-600">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-400">Time Remaining:</span>
                                    <span class="text-cyan-400 font-bold text-xl">{{timeRemainingFormatted}}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions Card -->
                    <div class="bg-[#0005] rounded-lg p-6 border border-gray-600">
                        <h4 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-bolt text-yellow-400 mr-2"></i>
                            Quick Actions
                        </h4>
                        <div class="space-y-3">
                            <button ng-click="goToFirstUnanswered()"
                                class="w-full py-3 px-4 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-search"></i>
                                <span>Go to First Unanswered</span>
                            </button>
                            <button ng-click="goToFlaggedQuestions()"
                                class="w-full py-3 px-4 rounded-lg bg-yellow-600 hover:bg-yellow-700 text-white transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-flag"></i>
                                <span>Go to Flagged Questions</span>
                            </button>
                            <button ng-click="saveAllAnswers()"
                                class="w-full py-3 px-4 rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                                <i class="fas fa-save"></i>
                                <span>Save All Answers</span>
                            </button>
                        </div>
                    </div>

                    <!-- Legend Card -->
                    <div class="bg-[#0005] rounded-lg p-6 border border-gray-600">
                        <h4 class="text-lg font-medium text-gray-100 mb-4 flex items-center">
                            <i class="fas fa-key text-purple-400 mr-2"></i>
                            Navigation Legend
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-cyan-600 mr-3 flex items-center justify-center">
                                    <span class="text-xs text-white">C</span>
                                </div>
                                <span class="text-gray-300">Current Question</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-green-600 mr-3 flex items-center justify-center">
                                    <span class="text-xs text-white">✓</span>
                                </div>
                                <span class="text-gray-300">Answered</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-yellow-600 mr-3 flex items-center justify-center">
                                    <span class="text-xs text-white">!</span>
                                </div>
                                <span class="text-gray-300">Flagged for Review</span>
                            </div>
                            <div class="flex items-center">
                                <div class="w-6 h-6 rounded bg-gray-700 mr-3 flex items-center justify-center">
                                    <span class="text-xs text-gray-300">?</span>
                                </div>
                                <span class="text-gray-300">Not Answered</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="p-6 border-t border-gray-600 flex justify-between items-center">
                <div class="text-sm text-gray-400">
                    <i class="fas fa-info-circle mr-2"></i>
                    Click on any question number to navigate directly
                </div>
                <div class="flex space-x-3">
                    <button ng-click="closeReviewModal()"
                        class="px-6 py-2 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors hover:scale-105 transition-transform">
                        Close Review
                    </button>
                    <button ng-click="closeReviewModal()"
                        class="px-6 py-2 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors hover:scale-105 transition-transform">
                        Continue Exam
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Confirmation Modal -->
    <div ng-if="showSubmitConfirmation" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-paper-plane text-red-400 mr-2"></i>
                    Submit Exam
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 border-4 border-red-500/30 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-400 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">Final Submission Confirmation</h4>
                    <p class="text-gray-400">Once submitted, you cannot change your answers.</p>
                </div>

                <!-- Submission Summary -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <h5 class="text-md font-medium text-gray-100 mb-3">Submission Summary:</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Answered Questions:</span>
                            <span class="text-green-400 font-bold">{{answeredCount}}/{{examData.total_questions}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Flagged Questions:</span>
                            <span class="text-yellow-400 font-bold">{{flaggedCount}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Time Remaining:</span>
                            <span class="text-cyan-400 font-bold">{{timeRemainingFormatted}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-400">Time Spent:</span>
                            <span class="text-blue-400 font-bold">{{formatTime((examData.duration * 60) -
                                timeRemaining)}}</span>
                        </div>
                    </div>

                    <!-- Warning for unanswered questions -->
                    <div ng-if="examData.total_questions - answeredCount > 0"
                        class="mt-4 p-3 bg-red-500/10 border border-red-500/30 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                            <div>
                                <div class="text-red-300 font-medium">
                                    {{examData.total_questions - answeredCount}} unanswered
                                    question{{examData.total_questions - answeredCount > 1 ? 's' : ''}}!
                                </div>
                                <div class="text-red-400 text-sm mt-1">
                                    You still have time to answer them.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button ng-click="submitExam()"
                        class="w-full py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors flex items-center justify-center space-x-2 hover:scale-105 transition-transform">
                        <i class="fas fa-paper-plane"></i>
                        <span class="font-bold">Yes, Submit Exam Now</span>
                    </button>

                    <button ng-click="cancelSubmit()"
                        class="w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors hover:scale-105 transition-transform">
                        No, Continue Exam
                    </button>

                    <button ng-click="saveAndClose()"
                        class="w-full py-3 rounded-lg border border-blue-500 text-blue-400 hover:bg-blue-500/10 transition-colors hover:scale-105 transition-transform">
                        <i class="fas fa-save mr-2"></i>
                        Save Progress and Exit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Time Expired Modal -->
    <div ng-if="timeExpired" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md animate-pulse">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-hourglass-end text-red-400 mr-2"></i>
                    Time's Up!
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-24 h-24 mx-auto mb-4 rounded-full bg-red-500/20 border-4 border-red-500 animate-pulse flex items-center justify-center">
                        <i class="fas fa-clock text-red-400 text-4xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">Exam Time Has Ended</h4>
                    <p class="text-gray-400">Your exam will be automatically submitted.</p>
                </div>

                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Questions Answered:</span>
                            <span class="text-green-400 font-bold">{{answeredCount}}/{{examData.total_questions}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Exam Duration:</span>
                            <span class="text-cyan-400 font-bold">{{examData.duration}} minutes</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Time Taken:</span>
                            <span class="text-yellow-400 font-bold">{{formatTime((examData.duration * 60) -
                                timeRemaining)}}</span>
                        </div>
                    </div>
                </div>

                <button ng-click="forceSubmit()"
                    class="w-full py-3 rounded-lg bg-red-600 hover:bg-red-700 text-white transition-colors flex items-center justify-center space-x-2 animate-pulse">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Exam Now</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div ng-if="showSuccessModal" class="fixed inset-0 bg-black/80 z-50 flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-check-circle text-green-400 mr-2"></i>
                    Exam Submitted Successfully!
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-24 h-24 mx-auto mb-4 rounded-full bg-green-500/20 border-4 border-green-500 flex items-center justify-center">
                        <i class="fas fa-check text-green-400 text-4xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">Congratulations!</h4>
                    <p class="text-gray-400">Your exam has been submitted successfully.</p>
                </div>

                <!-- Submission Details -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <h5 class="text-md font-medium text-gray-100 mb-3">Submission Details:</h5>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Submission Time:</span>
                            <span class="text-cyan-400">{{submissionTime | date:'medium'}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Time Taken:</span>
                            <span class="text-green-400 font-bold">{{timeTakenFormatted}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Questions Attempted:</span>
                            <span
                                class="text-yellow-400 font-bold">{{answeredCount}}/{{examData.total_questions}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Estimated Score:</span>
                            <span class="text-blue-400 font-bold">{{estimatedScore ||
                                0}}/{{examData.total_marks}}</span>
                        </div>
                    </div>
                </div>

                <!-- Next Steps -->
                <div class="space-y-3">
                    <a ng-if="examData.show_results_immediately"
                        ng-href="<?php echo BASE_URL; ?>/exam/results/student/{{examData.id}}"
                        class="block w-full py-3 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors text-center hover:scale-105 transition-transform">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Results Now
                    </a>

                    <a href="<?php echo BASE_URL; ?>/exams"
                        class="block w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors text-center hover:scale-105 transition-transform">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
                    </a>

                    <button ng-click="closeSuccessModal()"
                        class="block w-full py-3 rounded-lg border border-green-500 text-green-400 hover:bg-green-500/10 transition-colors hover:scale-105 transition-transform">
                        <i class="fas fa-home mr-2"></i>
                        Go to Dashboard
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>