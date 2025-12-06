<?php $this->extend('frontend'); ?>
<?php $this->controller('ExamAttemptController'); setMinibar() ?>
<?php $this->start('content'); ?>

<div class="bg-[#0003] p-6 rounded-lg" ng-init="init()" ng-cloak>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">{{examData.title || 'Exam Preview'}}</h1>
            <p class="text-gray-400">Live exam preview - Code: {{examData.code}}</p>
        </div>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <!-- Real-time Status -->
            <div class="flex items-center space-x-2 px-4 py-2 rounded-lg border" 
                 ng-class="isOnline ? 'border-green-500 bg-green-900/20' : 'border-red-500 bg-red-900/20'">
                <div class="w-2 h-2 rounded-full animate-pulse" 
                     ng-class="isOnline ? 'bg-green-500' : 'bg-red-500'"></div>
                <span class="text-sm" ng-class="isOnline ? 'text-green-400' : 'text-red-400'">
                    {{isOnline ? 'Live' : 'Offline'}}
                </span>
            </div>
            
            <!-- Timer -->
            <div class="bg-cyan-900/30 px-4 py-2 rounded-lg border border-cyan-600">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock text-cyan-400"></i>
                    <span class="font-mono text-lg font-semibold text-cyan-300">{{formattedTime}}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8 flex items-center justify-center">
        <div class="flex items-center w-full max-w-4xl mx-auto md:mx-24">
            <div class="flex flex-1 items-center justify-center" ng-repeat="step in steps">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors duration-200"
                     ng-class="step.active ? 'bg-cyan-600 border-cyan-600 text-white' : 
                               step.completed ? 'bg-green-500 border-green-500 text-white' : 
                               'border-gray-500 text-gray-500'">
                    <i class="fas" ng-class="step.completed ? 'fa-check' : step.icon"></i>
                </div>
                <div class="ml-3 hidden md:block">
                    <div class="text-sm font-medium" ng-class="step.active ? 'text-cyan-400' : 'text-gray-400'">
                        {{step.title}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Questions Navigation -->
        <div class="lg:col-span-1">
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-100">Questions</h3>
                    <span class="text-sm text-cyan-400">{{currentQuestionIndex + 1}}/{{questions.length}}</span>
                </div>
                
                <!-- Questions Grid -->
                <div class="grid grid-cols-5 gap-2 mb-6">
                    <button ng-repeat="q in questions track by $index"
                            ng-click="goToQuestion($index)"
                            class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-200"
                            ng-class="{
                                'bg-cyan-600 text-white border-cyan-700': currentQuestionIndex === $index,
                                'bg-green-600 text-white border-green-700': q.answered,
                                'bg-yellow-600 text-white border-yellow-700': q.markedForReview,
                                'bg-gray-700 text-gray-300 border-gray-600': !q.answered && !q.markedForReview
                            }">
                        {{$index + 1}}
                    </button>
                </div>
                
                <!-- Progress Stats -->
                <div class="grid grid-cols-2 gap-3 mb-6">
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <div class="text-2xl font-bold text-green-400 text-center">{{getAnsweredCount()}}</div>
                        <div class="text-sm text-green-300 text-center">Answered</div>
                    </div>
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <div class="text-2xl font-bold text-cyan-400 text-center">{{getRemainingCount()}}</div>
                        <div class="text-sm text-cyan-300 text-center">Remaining</div>
                    </div>
                </div>
                
                <!-- Live Updates Panel -->
                <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="font-medium text-gray-100">Live Updates</h4>
                        <span class="text-xs px-2 py-1 bg-cyan-900/50 text-cyan-300 rounded-full">
                            {{realTimeUpdates.length}}
                        </span>
                    </div>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        <div ng-repeat="update in realTimeUpdates | orderBy:'timestamp':true | limitTo:5"
                             class="p-3 bg-[#0008] rounded-lg border border-gray-600">
                            <div class="flex justify-between items-start mb-1">
                                <span class="text-xs px-2 py-1 rounded-full"
                                      ng-class="{
                                          'bg-green-900/50 text-green-300': update.type === 'answer',
                                          'bg-blue-900/50 text-blue-300': update.type === 'system',
                                          'bg-yellow-900/50 text-yellow-300': update.type === 'warning'
                                      }">
                                    {{update.type | uppercase}}
                                </span>
                                <span class="text-xs text-gray-400">{{update.timestamp | date:'HH:mm:ss'}}</span>
                            </div>
                            <p class="text-sm text-gray-300">{{update.message}}</p>
                        </div>
                        <div ng-if="realTimeUpdates.length === 0" class="text-center py-4">
                            <i class="fas fa-bell text-gray-500 text-2xl mb-2"></i>
                            <p class="text-gray-400 text-sm">No updates yet</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Exam Information -->
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mt-4">
                <h3 class="text-lg font-medium text-gray-100 mb-4">Exam Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Exam Code:</span>
                        <span class="font-medium text-gray-100">{{examData.code}}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Duration:</span>
                        <span class="font-medium text-cyan-400">{{examData.duration}} minutes</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Marks:</span>
                        <span class="font-medium text-gray-100">{{examData.total_marks}}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Passing Marks:</span>
                        <span class="font-medium text-gray-100">{{examData.passing_marks}}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status:</span>
                        <span class="font-medium" ng-class="isOnline ? 'text-green-400' : 'text-red-400'">
                            {{isOnline ? 'Connected' : 'Disconnected'}}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Column - Question Display -->
        <div class="lg:col-span-2">
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 h-full">
                <!-- Question Header -->
                <div class="flex flex-wrap justify-between items-start mb-6 gap-4">
                    <div>
                        <span class="inline-block px-3 py-1 bg-cyan-900/30 text-cyan-300 text-sm font-medium rounded-full border border-cyan-600">
                            Question {{currentQuestionIndex + 1}} of {{questions.length}}
                        </span>
                        <span class="ml-2 inline-block px-3 py-1 bg-gray-700 text-gray-300 text-sm font-medium rounded-full">
                            {{currentQuestion.marks || 1}} point{{currentQuestion.marks !== 1 ? 's' : ''}}
                        </span>
                    </div>
                    <button ng-click="toggleMarkForReview()"
                            class="px-4 py-2 rounded-lg border flex items-center space-x-2 transition-colors duration-200"
                            ng-class="currentQuestion.markedForReview ? 
                                     'bg-yellow-900/20 border-yellow-600 text-yellow-300' : 
                                     'bg-gray-700 border-gray-600 text-gray-300 hover:bg-gray-600'">
                        <i class="fas" ng-class="currentQuestion.markedForReview ? 'fa-check-circle' : 'fa-flag'"></i>
                        <span>{{currentQuestion.markedForReview ? 'Marked' : 'Mark for Review'}}</span>
                    </button>
                </div>

                <!-- Question Text -->
                <div class="prose max-w-none mb-8">
                    <h3 class="text-xl font-semibold text-gray-100 mb-4">{{currentQuestion.question}}</h3>
                    <div ng-if="currentQuestion.image" class="my-6">
                        <img ng-src="{{currentQuestion.image}}" 
                             alt="Question image" 
                             class="rounded-lg max-w-full h-auto border border-gray-600">
                    </div>
                </div>

                <!-- Options -->
                <div class="space-y-3 mb-8">
                    <div ng-repeat="option in currentQuestion.options track by $index"
                         ng-click="selectOption($index)"
                         class="p-4 border-2 rounded-xl cursor-pointer transition-all duration-200"
                         ng-class="{
                           'border-cyan-500 bg-cyan-900/10': option.selected,
                           'border-gray-600 hover:border-gray-500 hover:bg-[#0006]': !option.selected
                         }">
                        <div class="flex items-center">
                            <div class="w-8 h-8 flex items-center justify-center rounded-full border mr-4"
                                 ng-class="option.selected ? 
                                          'bg-cyan-600 border-cyan-700 text-white' : 
                                          'bg-gray-700 border-gray-600 text-gray-300'">
                                {{$index | letterIndex}}
                            </div>
                            <div class="flex-1 text-gray-200">{{option.text}}</div>
                            <div ng-if="option.image" class="ml-4">
                                <img ng-src="{{option.image}}" alt="Option image" class="w-16 h-16 rounded border border-gray-600">
                            </div>
                            <div ng-if="option.selected" class="ml-4">
                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="flex flex-wrap justify-between items-center pt-6 border-t border-gray-600 gap-4">
                    <div class="flex space-x-3">
                        <button ng-click="clearAnswer()"
                                class="px-6 py-3 border border-red-600 text-red-300 rounded-lg font-medium hover:bg-red-900/20 transition-colors duration-200">
                            Clear Answer
                        </button>
                        <button ng-click="previousQuestion()"
                                ng-disabled="currentQuestionIndex === 0"
                                class="px-6 py-3 border border-gray-600 text-gray-300 rounded-lg font-medium flex items-center space-x-2 hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50">
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous</span>
                        </button>
                    </div>
                    
                    <button ng-click="nextQuestion()"
                            class="px-6 py-3 bg-cyan-600 text-white rounded-lg font-medium hover:bg-cyan-700 transition-colors duration-200 flex items-center space-x-2">
                        <span>{{currentQuestionIndex === questions.length - 1 ? 'Submit Exam' : 'Next'}}</span>
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Panel - Status & Controls -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
        <!-- Connection Status -->
        <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
            <h4 class="font-medium text-gray-100 mb-3">Connection Status</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Data Sync:</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium"
                          ng-class="dataSynced ? 'bg-green-900/50 text-green-300' : 'bg-yellow-900/50 text-yellow-300'">
                        {{dataSynced ? 'Synced' : 'Syncing...'}}
                    </span>
                </div>
                <button ng-click="manualSync()"
                        class="w-full mt-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-sync" ng-class="{'fa-spin animate-spin': !dataSynced}"></i>
                    <span>Sync Now</span>
                </button>
            </div>
        </div>

        <!-- Time Management -->
        <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
            <h4 class="font-medium text-gray-100 mb-3">Time Management</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Time Used:</span>
                    <span class="font-mono text-cyan-300">{{getTimeUsed()}}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Time Remaining:</span>
                    <span class="font-mono text-cyan-300">{{formattedTime}}</span>
                </div>
                <div class="w-full bg-gray-700 h-2 rounded-full overflow-hidden">
                    <div class="h-full bg-cyan-600 transition-all duration-300" 
                         ng-style="{'width': getTimePercentage() + '%'}"></div>
                </div>
            </div>
        </div>

        <!-- Exam Controls -->
        <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
            <h4 class="font-medium text-gray-100 mb-3">Exam Controls</h4>
            <div class="space-y-3">
                <button ng-click="saveProgress()"
                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-save"></i>
                    <span>Save Progress</span>
                </button>
                <button ng-click="pauseExam()"
                        class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-pause"></i>
                    <span>Pause Exam</span>
                </button>
                <button ng-click="showSubmitModal = true"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center space-x-2">
                    <i class="fas fa-paper-plane"></i>
                    <span>Submit Exam</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Connection Alert -->
    <div ng-if="!isOnline" 
         class="fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-3 animate-fade-in max-w-md">
        <i class="fas fa-exclamation-triangle"></i>
        <div>
            <p class="font-medium">You are offline</p>
            <p class="text-sm opacity-90">Answers will be saved locally and synced when reconnected</p>
        </div>
        <button ng-click="dismissAlert()" class="ml-4 text-white hover:text-gray-300">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Auto-save Indicator -->
    <div ng-if="autoSaving" 
         class="fixed bottom-4 left-4 bg-cyan-600 text-white px-4 py-2 rounded-lg shadow-lg flex items-center space-x-2">
        <div class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
        <span>Saving...</span>
    </div>

    <!-- Submit Exam Modal -->
    <div ng-if="showSubmitModal" class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 p-4">
        <div class="bg-[#0006] rounded-lg p-6 border border-gray-600 max-w-md w-full">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-semibold text-gray-100">Submit Exam</h3>
                <button ng-click="showSubmitModal = false" class="text-gray-400 hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="space-y-4 mb-6">
                <div class="bg-[#0008] p-4 rounded border border-gray-600">
                    <h4 class="font-medium text-gray-100 mb-2">Summary</h4>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Answered:</span>
                            <span class="text-green-400">{{getAnsweredCount()}} questions</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Remaining:</span>
                            <span class="text-cyan-400">{{getRemainingCount()}} questions</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Marked for Review:</span>
                            <span class="text-yellow-400">{{getMarkedCount()}} questions</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Time Used:</span>
                            <span class="text-gray-300">{{getTimeUsed()}}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="confirmSubmit" ng-model="confirmSubmit" class="rounded bg-[#0006] border-gray-600 text-cyan-500">
                    <label for="confirmSubmit" class="text-gray-300">I confirm I want to submit the exam</label>
                </div>
            </div>
            
            <div class="flex justify-end space-x-3">
                <button ng-click="showSubmitModal = false"
                        class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg hover:bg-gray-700">
                    Cancel
                </button>
                <button ng-click="submitExam()" ng-disabled="!confirmSubmit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 disabled:opacity-50">
                    Submit Exam
                </button>
            </div>
        </div>
    </div>
</div>

<?php $this->end(); ?>