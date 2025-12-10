<?php $this->extend('frontend'); ?>
<?php $this->controller('ExamPreviewController'); ?>
<?php $this->start('content'); ?>

<div class="bg-[#0003] p-6 rounded-lg mb-16" ng-cloak>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Exam Review & Preview</h1>
            <p class="text-gray-400">Review exam before publishing</p>
        </div>
        <div class="flex items-center space-x-4 mt-4 md:mt-0">
            <!-- Status Badge -->
            <div class="px-4 py-2 rounded-lg border"
                ng-class="examData.status === 'published' ? 'border-green-500 bg-green-900/20 text-green-400' : 
                    examData.status === 'scheduled' ? 'border-blue-500 bg-blue-900/20 text-blue-400' : 
                    examData.status === 'canceled' ? 'border-red-500 bg-red-900/20 text-red-400' : 'border-yellow-500 bg-yellow-900/20 text-yellow-400'">
                <span class="text-sm font-medium capitalize">{{examData.status || 'draft'}}</span>
            </div>

            <!-- Back Button -->
            <!-- <a href="{{location.exam}}"
                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors duration-200 flex items-center space-x-2">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Exams</span>
            </a> -->
        </div>
    </div>

    <!-- 5-Step Navigation -->
    <div class="mb-8">
        <div class="flex items-center justify-center">
            <div class="flex items-center w-full max-w-4xl mx-auto">
                <!-- Step 1: Basic Info -->
                <div class="flex-1 flex flex-wrap gap-4 items-center justify-center cursor-pointer"
                    ng-click="currentStep = 1; updateStepCompletion()">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full border-2 transition-colors duration-200"
                        ng-class="currentStep === 1 ? 'bg-cyan-600 border-cyan-600 text-white' : 
                                   step1Completed ? 'bg-green-500 border-green-500 text-white' : 
                                   'border-gray-500 text-gray-500'">
                        <i class="fas" ng-class="step1Completed ? 'fa-check' : 'fa-info-circle'"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium"
                            ng-class="currentStep === 1 ? 'text-cyan-400' : 'text-gray-400'">
                            Basic Info
                        </div>
                    </div>
                </div>

                <!-- Connector Line -->
                <div class="hidden flex-1 h-1" ng-class="step1Completed ? 'bg-green-500' : 'bg-gray-600'"></div>

                <!-- Step 2: Questions -->
                <div class="flex-1 flex flex-wrap gap-4 items-center justify-center cursor-pointer"
                    ng-click="currentStep = 2; updateStepCompletion()">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full border-2 transition-colors duration-200"
                        ng-class="currentStep === 2 ? 'bg-cyan-600 border-cyan-600 text-white' : 
                                   step2Completed ? 'bg-green-500 border-green-500 text-white' : 
                                   'border-gray-500 text-gray-500'">
                        <i class="fas" ng-class="step2Completed ? 'fa-check' : 'fa-question-circle'"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium"
                            ng-class="currentStep === 2 ? 'text-cyan-400' : 'text-gray-400'">
                            Questions
                        </div>
                    </div>
                </div>

                <!-- Connector Line -->
                <div class="hidden flex-1 h-1" ng-class="step2Completed ? 'bg-green-500' : 'bg-gray-600'"></div>

                <!-- Step 3: Settings -->
                <div class="flex-1 flex flex-wrap gap-4 items-center justify-center cursor-pointer"
                    ng-click="currentStep = 3; updateStepCompletion()">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full border-2 transition-colors duration-200"
                        ng-class="currentStep === 3 ? 'bg-cyan-600 border-cyan-600 text-white' : 
                                   step3Completed ? 'bg-green-500 border-green-500 text-white' : 
                                   'border-gray-500 text-gray-500'">
                        <i class="fas" ng-class="step3Completed ? 'fa-check' : 'fa-cog'"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium"
                            ng-class="currentStep === 3 ? 'text-cyan-400' : 'text-gray-400'">
                            Settings
                        </div>
                    </div>
                </div>

                <!-- Connector Line -->
                <div class="hidden flex-1 h-1" ng-class="step3Completed ? 'bg-green-500' : 'bg-gray-600'"></div>

                <!-- Step 4: Exact Preview  -->
                <div class="flex-1 flex flex-wrap gap-4 items-center justify-center cursor-pointer"
                    ng-click="currentStep = 4; updateStepCompletion()">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full border-2 transition-colors duration-200"
                        ng-class="currentStep === 4 ? 'bg-cyan-600 border-cyan-600 text-white' : 
                                   step4Completed ? 'bg-green-500 border-green-500 text-white' : 
                                   'border-gray-500 text-gray-500'">
                        <i class="fas fa-solid" ng-class="step4Completed ? 'fa-check' : 'fa-eye'"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium"
                            ng-class="currentStep === 4 ? 'text-cyan-400' : 'text-gray-400'">
                            Preview
                        </div>
                    </div>
                </div>

                <!-- Connector Line -->
                <div class="hidden flex-1 h-1" ng-class="step4Completed ? 'bg-green-500' : 'bg-gray-600'"></div>

                <!-- Step 5: Publish -->
                <div class="flex-1 flex flex-wrap gap-4 items-center justify-center cursor-pointer"
                    ng-click="currentStep = 5; updateStepCompletion()">
                    <div class="flex items-center justify-center w-11 h-11 rounded-full border-2 transition-colors duration-200"
                        ng-class="currentStep === 5 ? 'bg-cyan-600 border-cyan-600 text-white' : 
                                   step5Completed ? 'bg-green-500 border-green-500 text-white' : 
                                   'border-gray-500 text-gray-500'">
                        <i class="fas" ng-class="step5Completed ? 'fa-check' : 'fa-paper-plane'"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-sm font-medium"
                            ng-class="currentStep === 5 ? 'text-cyan-400' : 'text-gray-400'">
                            Publish
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Basic Exam Information -->
    <div ng-show="currentStep === 1" class="max-w-4xl mx-auto">
        <div class="md:bg-[#0004] rounded-lg md:p-6 md:border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Exam Basic Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Exam Title -->
                <div class="md:col-span-2">
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-lg font-medium text-cyan-400">Exam Title</h3>
                            <span class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded">Required</span>
                        </div>
                        <p class="text-gray-100 text-lg">{{examData.title || 'No title provided'}}</p>
                    </div>
                </div>

                <!-- Exam Code -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Exam Code</h3>
                        <p class="text-gray-100">{{examData.code || 'No code provided'}}</p>
                    </div>
                </div>

                <!-- Duration -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Duration</h3>
                        <p class="text-gray-100">{{examData.duration || 0}} minutes</p>
                    </div>
                </div>

                <!-- Total Marks -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Total Marks</h3>
                        <p class="text-gray-100">{{examData.total_marks || 0}} marks</p>
                    </div>
                </div>

                <!-- Passing Marks -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Passing Marks</h3>
                        <p class="text-gray-100">{{examData.passing_marks || 0}} marks</p>
                    </div>
                </div>

                <!-- Total Questions -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Total Questions</h3>
                        <p class="text-gray-100">{{totalQuestions || 0}} questions</p>
                    </div>
                </div>

                <!-- Status -->
                <div>
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Status</h3>
                        <p class="text-gray-100 capitalize">{{examData.status || 'draft'}}</p>
                    </div>
                </div>

                <!-- Instructions -->
                <div class="md:col-span-2">
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-cyan-400 mb-2">Exam Instructions</h3>
                        <div class="prose max-w-none">
                            <p class="text-gray-300 whitespace-pre-line">{{examData.instructions || 'No instructions
                                provided.'}}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Validation Status -->
            <div class="mt-6 pt-6 border-t border-gray-600">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-medium text-gray-100">Basic Information Status</h3>
                        <p class="text-gray-400 text-sm">Check if all required fields are filled</p>
                    </div>
                    <div ng-if="isBasicInfoComplete()" class="flex items-center space-x-2 text-green-400">
                        <i class="fas fa-check-circle"></i>
                        <span>Complete</span>
                    </div>
                    <div ng-if="!isBasicInfoComplete()" class="flex items-center space-x-2 text-yellow-400">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span>Incomplete</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                            ng-class="examData.title ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas fa-xs text-white" ng-class="examData.title ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <span class="text-sm" ng-class="examData.title ? 'text-green-400' : 'text-red-400'">
                            Exam title provided
                        </span>
                    </div>

                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                            ng-class="examData.code ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas fa-xs text-white" ng-class="examData.code ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <span class="text-sm" ng-class="examData.code ? 'text-green-400' : 'text-red-400'">
                            Exam code provided
                        </span>
                    </div>

                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                            ng-class="examData.duration > 0 ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas fa-xs text-white"
                                ng-class="examData.duration > 0 ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <span class="text-sm" ng-class="examData.duration > 0 ? 'text-green-400' : 'text-red-400'">
                            Duration specified
                        </span>
                    </div>

                    <div class="flex items-center">
                        <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                            ng-class="totalQuestions > 0 ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas fa-xs text-white" ng-class="totalQuestions > 0 ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <span class="text-sm" ng-class="totalQuestions > 0 ? 'text-green-400' : 'text-red-400'">
                            Questions added ({{totalQuestions}})
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 2: Questions Review -->
    <div ng-show="currentStep === 2" class="max-w-7xl mx-auto">
        <div class="md:bg-[#0004] rounded-lg md:p-6 md:border border-gray-600">
            <div class="flex flex-wrap justify-between items-center mb-6 gap-4">
                <h2 class="text-xl font-semibold text-gray-100">Questions Review</h2>
                <!-- Display Controls -->
                <div class="flex flex-wrap items-center justify-center gap-4">
                    <div class="flex items-center justify-center gap-2" ng-click="dropdownOpen = !dropdownOpen">
                        <!-- Display selected value -->
                        <label class="text-sm font-medium text-gray-300">Questions Per Page: </label>
                        <div class="relative">
                            <div
                                class="bg-[#0004] border w-full border-gray-600 text-gray-300 rounded-lg px-4 py-2 text-sm cursor-pointer flex items-center justify-between gap-2">
                                <span>{{ questionsPerPage }}</span>
                                <i class="fas fa-chevron-down text-gray-400 text-xs"></i>
                            </div>

                            <!-- Dropdown menu -->
                            <div ng-show="dropdownOpen"
                                class="absolute mt-1 w-full bg-[#0003] border border-gray-600 backdrop-blur rounded-lg shadow-lg z-50 overflow-hidden p-1">

                                <div ng-repeat="num in [10,20,50]" ng-click="selectPerPage($event, num)"
                                    class="px-3 py-2 cursor-pointer hover:bg-[#0f03] text-gray-300 text-sm transition-colors duration-300 rounded">
                                    {{ num }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <button type="button" ng-click=" questionsDisplayMode = 'all'; updateQuestionsDisplay()"
                        class="bg-green-600 hover:bg-green-700 w-full md:w-auto text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                        <i class="fa-solid fa-eye"></i>
                        <span>Show All Questions</span>
                    </button>
                </div>
            </div>

            <!-- Questions Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                    <div class="text-2xl font-bold text-cyan-400 text-center">{{totalQuestions}}</div>
                    <div class="text-sm text-cyan-300 text-center">Total Questions</div>
                </div>
                <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                    <div class="text-2xl font-bold text-green-400 text-center">{{totalMarks}}</div>
                    <div class="text-sm text-green-300 text-center">Total Marks</div>
                </div>
                <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                    <div class="text-2xl font-bold text-purple-400 text-center">{{sections.length}}</div>
                    <div class="text-sm text-purple-300 text-center">Sections</div>
                </div>
                <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                    <div class="text-2xl font-bold text-yellow-400 text-center">{{getQuestionsWithImages()}}</div>
                    <div class="text-sm text-yellow-300 text-center">With Images</div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column - Sections/Page Navigation -->
                <div class="lg:col-span-2">
                    <!-- Sections List -->
                    <div class="md:bg-[#0004] rounded-lg md:p-4 md:border border-gray-600 mb-4">
                        <h3 class="text-lg font-medium text-gray-100 mb-3">Sections ({{sections.length}})</h3>
                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            <div ng-repeat="section in originalSections" ng-click="selectSection(section)"
                                class="p-3 rounded-lg border cursor-pointer transition-colors duration-200" ng-class="(questionsDisplayMode === 'sections' && activeSection && activeSection.id === section.id) ? 
                                           'bg-cyan-900/20 border-cyan-600' : 
                                           'bg-[#0004] border-gray-600 hover:bg-[#000a]'">
                                <div class="flex justify-between items-center">
                                    <h4 class="font-medium text-gray-100">{{section.title}}</h4>
                                    <span class="text-xs px-2 py-1 bg-gray-700 text-gray-300 rounded">
                                        {{section.questions.length || 0}} Q
                                    </span>
                                </div>
                                <p class="text-xs text-gray-400 mt-1 truncate">{{section.title}}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Page Navigation -->
                    <div
                        class="md:bg-[#0004] rounded-lg pb-4 md:p-4 md:border border-gray-600 relative before:content-[''] md:before:content-none before:absolute before:bottom-0 before:left-0 before:w-full before:h-[1px] before:bg-gradient-to-l before:from-transparent before:via-slate-400 before:to-transparent">
                        <h3 class="text-lg font-medium text-gray-100 mb-3">Page Navigation</h3>
                        <div class="flex flex-wrap gap-2">
                            <button ng-repeat="page in questionPages track by $index"
                                ng-click="goToQuestionPage($index)"
                                class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-200"
                                ng-class="currentQuestionPage === $index ? 
                                             'bg-cyan-600 text-white' : 
                                             'bg-gray-700 text-gray-300 hover:bg-gray-600'">
                                {{$index + 1}}
                            </button>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-600 text-center">
                            <span class="text-sm text-gray-400">
                                Page {{currentQuestionPage + 1}} of {{questionPages.length}}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Questions Display -->
                <div class="lg:col-span-2">
                    <!-- Current Section/Page Header -->
                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600 mb-4">
                        <div class="flex justify-between items-center">
                            <div>
                                <h3 class="text-lg font-medium text-gray-100">
                                    <span ng-if="questionsDisplayMode === 'sections' && activeSection">
                                        {{activeSection.title}} - Questions
                                    </span>
                                    <span ng-if="questionsDisplayMode === 'all'">
                                        All Questions
                                    </span>
                                </h3>
                                <p class="text-gray-400 text-sm">
                                    Showing {{getCurrentPageQuestions().length}} of {{displayQuestions.length}}
                                    questions
                                </p>
                            </div>
                            <div class="text-right">
                                <span
                                    class="text-sm px-3 py-1 bg-cyan-900/30 text-cyan-300 rounded-full border border-cyan-600">
                                    Page {{currentQuestionPage + 1}}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="space-y-4">
                        <div ng-if="activeSection && activeSection.description"
                            class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                            <p class="text-white">{{activeSection.description || ''}}</p><br>
                            <p class="text-white">{{activeSection.second_description || ''}}</p>
                        </div>
                        <div ng-repeat="question in getCurrentPageQuestions() track by $index"
                            class="md:bg-[#0004] rounded-lg pt-6 pb-2 md:p-6 md:border border-gray-600 relative before:content-[''] before:absolute before:bottom-0 before:left-0 before:w-full before:h-[1px] before:bg-gradient-to-r before:from-transparent before:via-white/80 before:to-transparent">
                            <div ng-show="editingQuestionId !== question.id">
                                <!-- Question Header -->
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-block px-3 py-1 bg-cyan-900/30 text-cyan-300 text-sm font-medium rounded-full border border-cyan-600">
                                            Q{{(currentQuestionPage * questionsPerPage) + $index + 1}}
                                        </span>
                                        <span
                                            class="inline-block md:px-3 py-1 md:bg-gray-900/30 text-gray-300 text-sm font-medium rounded-full md:border border-gray-600">
                                            {{question.marks || 1}} mark{{question.marks !== 1 ? 's' : ''}}
                                        </span>
                                        <span ng-if="question.sectionNames"
                                            class="hidden md:inline-block md:px-3 py-1 md:bg-purple-900/30 text-purple-300 text-sm font-medium rounded-2xl md:border border-purple-600">
                                            {{question.sectionNames}}
                                        </span>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span
                                            class="inline-block md:px-3 py-1 md:bg-cyan-900/30 text-cyan-300 text-sm font-medium rounded-full md:border border-cyan-600">ID:
                                            {{question.id}}</span>
                                        <span class="md:hidden">|</span>
                                        <button type="button" ng-click="editQuestion(question.id)"
                                            class="md:px-3 py-1 md:bg-green-900/30 text-green-300 text-sm font-medium rounded-full md:border border-green-600 transition-colors duration-200 flex items-center justify-center space-x-2">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                                <div ng-if="question.sectionNames" class="inline-block md:hidden w-full">
                                    <div
                                        class="px-3 py-1 mb-2 bg-purple-900/30 text-purple-300 text-sm font-medium rounded-2xl border border-purple-600">
                                        {{question.sectionNames}}
                                    </div>
                                </div>

                                <!-- Question Text -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-100 mb-2">Question:</h4>
                                    <div class="bg-[#0004] rounded-lg p-4 border border-gray-600">
                                        <p class="text-gray-300">{{question.question}}</p>
                                        <div ng-if="question.image" class="mt-4">
                                            <img ng-src="{{question.image}}" alt="Question image"
                                                class="rounded-lg max-w-full h-auto border border-gray-600">
                                        </div>
                                    </div>
                                </div>

                                <!-- Options with Correct Answer -->
                                <div class="mb-6">
                                    <h4 class="text-lg font-medium text-gray-100 mb-2">Options:</h4>
                                    <div class="gap-2 md:gap-4 grid" ng-class="{
                                            'grid-cols-1': question.grid == 1,
                                            'grid-cols-1 md:grid-cols-2': question.grid == 2,
                                            'grid-cols-1 md:grid-cols-4': question.grid == 4
                                        }">
                                        <div ng-repeat="option in question.options track by $index"
                                            class="p-4 border-2 rounded-lg" ng-class="option.op.toLowerCase() === question.correctAnswer.toLowerCase() ? 
                                                   'border-green-500 bg-green-900/10' : 
                                                   'border-gray-600 bg-[#0004]'">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 flex items-center justify-center rounded-full border mr-4"
                                                    ng-class="option.op.toLowerCase() === question.correctAnswer.toLowerCase() ? 
                                                          'bg-green-600 border-green-700 text-white' : 
                                                          'bg-gray-700 border-gray-600 text-gray-300'">
                                                    {{ $index | letterIndex: 'A' }}
                                                </div>
                                                <div class="flex-1 text-gray-200">{{option.text}}</div>
                                                <div ng-if="option.image" class="ml-4">
                                                    <img ng-src="{{option.image}}" alt="Option image"
                                                        class="w-16 h-16 rounded border border-gray-600">
                                                </div>
                                                <div ng-if="option.op.toLowerCase() === question.correctAnswer.toLowerCase()"
                                                    class="ml-4">
                                                    <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Question Metadata -->
                                <!-- <div class="pt-4 border-t border-gray-600">
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <span class="text-gray-400">Type:</span>
                                            <span class="ml-2 text-gray-300">{{question.type || 'MCQ'}}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Difficulty:</span>
                                            <span class="ml-2 px-2 py-1 rounded-full text-xs" ng-class="{
                                                'bg-green-900/50 text-green-300': question.difficulty === 'easy',
                                                'bg-yellow-900/50 text-yellow-300': question.difficulty === 'medium',
                                                'bg-red-900/50 text-red-300': question.difficulty === 'hard'
                                              }">
                                                {{question.difficulty || 'medium'}}
                                            </span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Sections:</span>
                                            <span class="ml-2 text-gray-300">{{question.sectionIds.length || 0}}</span>
                                        </div>
                                        <div>
                                            <span class="text-gray-400">Has Image:</span>
                                            <span class="ml-2"
                                                ng-class="question.image ? 'text-green-400' : 'text-gray-400'">
                                                {{question.image ? 'Yes' : 'No'}}
                                            </span>
                                        </div>
                                    </div>
                                </div> -->
                            </div>

                            <div id="question_editor_modal" class="xl:col-span-2"
                                ng-show="editingQuestionId === question.id">
                                <form id="questionForm{{currentQuestion.id}}" onsubmit="return false"
                                    enctype="multipart/form-data">
                                    <!-- Exam ID hidden -->
                                    <input type="hidden" name="exam_id" ng-value="location.exam">
                                    <div class="flex flex-wrap md:flex-row">
                                        <!-- Question Text -->
                                        <div class="form-group w-full">
                                            <label class="form-label">Question Text <span
                                                    class="text-red-700">*</span></label>
                                            <textarea ng-model="currentQuestion.question" required rows="5"
                                                class="form-input" name="question"
                                                placeholder="Enter your question here..."></textarea>
                                        </div>

                                        <!-- Question Image -->
                                        <!-- <div class="w-full md:w-1/3 md:pl-2">
                                            <div class="form-group">
                                                <label class="form-label">Question Image (Optional)</label>
                                                <div
                                                    class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center">
                                                    <div ng-if="!currentQuestion.image">
                                                        <i
                                                            class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                                        <p class="text-gray-400 mb-2">Drag & drop an image or click
                                                            to
                                                            browse
                                                        </p>
                                                        <input type="file" id="questionImage" accept="image/*"
                                                            class="hidden" name="questionImage"
                                                            ng-file-select="onQuestionImageSelect($files)">
                                                        <label for="questionImage"
                                                            class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg cursor-pointer transition-colors duration-200">
                                                            Browse Files
                                                        </label>
                                                    </div>
                                                    <div ng-if="currentQuestion.image" class="relative inline-block">
                                                        <img ng-src="{{currentQuestion.image}}" alt="Question Image"
                                                            class="max-w-full max-h-64 rounded-lg">
                                                        <button type="button" ng-click="currentQuestion.image = null"
                                                            class="absolute top-2 right-2 bg-red-600 hover:bg-red-700 text-white p-1 rounded-full">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> -->
                                    </div>

                                    <!-- Multiple Choice Options -->
                                    <div class="space-y-4">
                                        <div class="flex flex-wrap items-center md:justify-between">
                                            <label class="form-label">Options <span
                                                    class="text-red-700">*</span></label>
                                            <div class="hidden md:flex flext-wrap items-center space-x-3">
                                                <p class="text-gray-300 font-semibold mb-1">Option Layout:</p>
                                                <label class="cursor-pointer">
                                                    <input type="radio" class="hidden" ng-model="currentQuestion.grid"
                                                        ng-value="1" name="grid">
                                                    <div class="px-4 py-1 rounded-lg border"
                                                        ng-class="currentQuestion.grid === 1 ? 'bg-purple-600/30 text-white border-purple-700' : 'bg-gray-700/30 text-gray-300 border-gray-500'">
                                                        1
                                                    </div>
                                                </label>

                                                <label class="cursor-pointer">
                                                    <input type="radio" class="hidden" ng-model="currentQuestion.grid"
                                                        ng-value="2" name="grid">
                                                    <div class="px-4 py-1 rounded-lg border"
                                                        ng-class="currentQuestion.grid === 2 ? 'bg-purple-600/30 text-white border-purple-700' : 'bg-gray-700/30 text-gray-300 border-gray-500'">
                                                        2
                                                    </div>
                                                </label>

                                                <label class="hidden lg:inline-block cursor-pointer">
                                                    <input type="radio" class="hidden" ng-model="currentQuestion.grid"
                                                        ng-value="4" name="grid">
                                                    <div class="px-4 py-1 rounded-lg border"
                                                        ng-class="currentQuestion.grid === 4 ? 'bg-purple-600/30 text-white border-purple-700' : 'bg-gray-700/30 text-gray-300 border-gray-500'">
                                                        4
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        <div ng-class="{
                                            'grid grid-cols-1 gap-4': currentQuestion.grid == 1,
                                            'grid grid-cols-1 md:grid-cols-2 gap-4': currentQuestion.grid == 2,
                                            'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4': currentQuestion.grid == 4
                                        }">
                                            <div ng-repeat="option in currentQuestion.options track by $index"
                                                class="flex items-center space-x-3">
                                                <label for="option{{$index}}" class="flex space-x-3">
                                                    <input type="radio" id="option{{$index}}" name="answer"
                                                        ng-model="currentQuestion.correctAnswer" ng-value="option.op"
                                                        class="text-cyan-500 cursor-pointer">
                                                    <p>{{ option.op }}&#x29;</p>
                                                </label>

                                                <div class="flex-1 flex items-center gap-3">
                                                    <input type="text" ng-model="option.text"
                                                        class="w-full rounded-lg px-4 py-3 border border-gray-600 text-gray-100 placeholder-gray-500 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed"
                                                        ng-class="option.op.toLowerCase() === currentQuestion.correctAnswer.toLowerCase() ? 'bg-green-900/30' : 'bg-[#0004]'"
                                                        name="{{option.op}}" placeholder="Option {{ option.op }} text">
                                                    <!-- <div id="{{ option.op }}ImgContainer" ng-if="option.image"
                                                        class="relative inline-block">
                                                        <img ng-src="{{option.image}}"
                                                            class="max-w-32 max-h-32 rounded">
                                                        <button type="button" ng-click="removeOptionImage(option)"
                                                            class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full text-xs">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </div> -->
                                                </div>

                                                <!-- <div class="flex space-x-2">
                                                    <button type="button" ng-click="uploadOptionImage(option)"
                                                        class="text-purple-400 hover:text-purple-300">
                                                        <i class="fas fa-image"></i>
                                                    </button>

                                                    <button type="button"
                                                        ng-click="removeOption(currentQuestion, $index)"
                                                        ng-disabled="currentQuestion.options.length <= 2"
                                                        class="text-red-400 hover:text-red-300 disabled:opacity-50">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </div> -->
                                            </div>
                                        </div>


                                        <!-- <div class="flex space-x-3">
                                            <button type="button" ng-click="addOption(currentQuestion)"
                                                class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                                                <i class="fas fa-plus"></i>
                                                <span>Add Text Option</span>
                                            </button>
                                        </div> -->
                                    </div>
                                    <!-- Question Metadata -->
                                    <div
                                        class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-600 mt-2">
                                        <!-- Marks -->
                                        <div class="form-group">
                                            <label class="form-label">Marks <span class="text-red-700">*</span></label>
                                            <input type="number" ng-model="currentQuestion.marks" required min="0.5"
                                                name="marks" step="0.5" class="form-input" placeholder="Marks">
                                        </div>
                                    </div>
                                </form>

                                <div class="flex space-x-3 mt-4">
                                    <button type="button" ng-click="saveQuestion()"
                                        class="px-4 py-2 bg-green-700 text-white rounded">
                                        Save Changes
                                    </button>

                                    <button type="button" ng-click="cancelEdit()"
                                        class="px-4 py-2 bg-red-700 text-white rounded">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination Controls -->
                    <div class="flex justify-between items-center mt-6 pt-6 border-t border-gray-600">
                        <button ng-click="previousQuestionPage()" ng-disabled="currentQuestionPage === 0"
                            class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg font-medium flex items-center space-x-2 hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50">
                            <i class="fas fa-arrow-left"></i>
                            <span>Previous</span>
                        </button>

                        <span class="text-gray-400 text-sm">
                            Page {{currentQuestionPage + 1}} of {{questionPages.length}}
                        </span>

                        <button ng-click="nextQuestionPage()"
                            ng-disabled="currentQuestionPage === questionPages.length - 1"
                            class="px-4 py-2 border border-gray-600 text-gray-300 rounded-lg font-medium flex items-center space-x-2 hover:bg-gray-700 transition-colors duration-200 disabled:opacity-50">
                            <span>Next</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 3: Exam Settings -->
    <div ng-show="currentStep === 3" class="max-w-4xl mx-auto">
        <div class="md:bg-[#0004] rounded-lg md:p-6 md:border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Exam Settings & Configuration</h2>

            <div class="space-y-6">
                <!-- Schedule Settings -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <h3 class="text-lg font-medium text-cyan-400 mb-4">Schedule Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-400 block mb-1">Schedule Type</span>
                            <span class="text-gray-100 font-medium capitalize">{{examData.schedule_type ||
                                'Anytime'}}</span>
                        </div>
                        <div ng-if="examData.schedule_type === 'scheduled'">
                            <span class="text-gray-400 block mb-1">Start Time</span>
                            <span class="text-gray-100 font-medium">{{examData.start_time | formatDateTime: 'MMM DD,
                                YYYY - hh:mm A'}}</span>
                        </div>
                        <div ng-if="examData.schedule_type === 'scheduled'">
                            <span class="text-gray-400 block mb-1">End Time</span>
                            <span class="text-gray-100 font-medium">{{examData.end_time | formatDateTime: 'MMM DD, YYYY
                                - hh:mm A'}}</span>
                        </div>
                    </div>
                </div>

                <!-- Display & Behavior Settings -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <h3 class="text-lg font-medium text-cyan-400 mb-4">Display & Behavior</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Shuffle Questions</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.shuffle_questions ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.shuffle_questions ? 'Enabled' : 'Disabled'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Shuffle Options</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.shuffle_options ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.shuffle_options ? 'Enabled' : 'Disabled'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Show Results</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.show_results_immediately ? 'bg-green-900/50 text-green-300' : 'bg-blue-900/50 text-blue-300'">
                                {{examData.show_results_immediately ? 'Immediately' : 'After Exam'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Allow Navigation</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.allow_navigation ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.allow_navigation ? 'Yes' : 'No'}}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Security Settings -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <h3 class="text-lg font-medium text-cyan-400 mb-4">Security Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Enable Proctoring</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.enable_proctoring ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.enable_proctoring ? 'Enabled' : 'Disabled'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Force Full Screen</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.full_screen_mode ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.full_screen_mode ? 'Yes' : 'No'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Disable Copy/Paste</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.disable_copy_paste ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.disable_copy_paste ? 'Yes' : 'No'}}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Disable Right Click</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.disable_right_click ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.disable_right_click ? 'Yes' : 'No'}}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Retake & Attempt Settings -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <h3 class="text-lg font-medium text-cyan-400 mb-4">Retake & Attempt Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-400">Allow Retake</span>
                            <span class="px-3 py-1 rounded-full text-sm font-medium"
                                ng-class="examData.allow_retake ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">
                                {{examData.allow_retake ? 'Yes' : 'No'}}
                            </span>
                        </div>
                        <div ng-if="examData.allow_retake" class="flex items-center justify-between">
                            <span class="text-gray-400">Max Attempts</span>
                            <span class="text-gray-100 font-medium">{{examData.max_attempts || 'Unlimited'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 4: Exam Preview -->
    <div ng-show="currentStep === 4" class="max-w-4xl mx-auto lg:col-span-2">
        <!-- Check Settings -->
        <div class="flex flex-wrap w-full md:w-auto items-center justify-center md:justify-end mb-10 gap-4">
            <!-- Display Mode Toggle -->
            <!-- <button ng-click="togglePreviewDisplayMode()"
                    ng-class="{'bg-gradient-to-r from-cyan-900 to-teal-800 text-cyan-100 border-cyan-500 hover:from-cyan-800 hover:to-teal-700 hover:bg-cyan-700': previewDisplayMode === 'sections', 'bg-gradient-to-r from-gray-800 to-gray-900 border-gray-600 text-gray-200 hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white hover:bg-gray-700': previewDisplayMode === 'all'}"
                    class="flex items-center justify-center w-full md:w-auto gap-2 px-4 py-2 font-medium rounded-lg border transition-all duration-300">
                    <i class="fas" ng-class="previewDisplayMode === 'sections' ? 'fa-layer-group' : 'fa-list'"></i>
                    <span>{{previewDisplayMode === 'sections' ? 'Section View' : 'All Questions'}}</span>
                </button> -->

            <!-- Shuffle Questions -->
            <button ng-click="examData.shuffle_questions = !examData.shuffle_questions; shufflePreviewQuestions()"
                ng-class="{'bg-gradient-to-r from-cyan-900 to-teal-800 text-cyan-100 border-cyan-500 hover:from-cyan-800 hover:to-teal-700 hover:bg-cyan-700': examData.shuffle_questions, 'bg-gradient-to-r from-gray-800 to-gray-900 border-gray-600 text-gray-200 hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white hover:bg-gray-700': !examData.shuffle_questions}"
                class="flex items-center justify-center w-full md:w-auto gap-2 px-4 py-2 font-medium rounded-lg border transition-all duration-300">
                <i class="fas" ng-class="examData.shuffle_questions ? 'fa-check-circle' : 'fa-random'"></i>
                <span>Shuffle Questions</span>
            </button>

            <!-- Shuffle Options -->
            <button ng-click="examData.shuffle_options = !examData.shuffle_options; shufflePreviewOptions()"
                ng-class="{'bg-gradient-to-r from-cyan-900 to-teal-800 text-cyan-100 border-cyan-500 hover:from-cyan-800 hover:to-teal-700 hover:bg-cyan-700': examData.shuffle_options, 'bg-gradient-to-r from-gray-800 to-gray-900 border-gray-600 text-gray-200 hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white hover:bg-gray-700': !examData.shuffle_options}"
                class="flex items-center justify-center w-full md:w-auto gap-2 px-4 py-2 font-medium rounded-lg border transition-all duration-300">
                <i class="fas" ng-class="examData.shuffle_options ? 'fa-check-circle' : 'fa-random'"></i>
                <span>Shuffle Options</span>
            </button>
        </div>

        <!-- Current Section/Page Header -->
        <div
            class="bg-gradient-to-r from-black/50 to-black/30 backdrop-blur rounded-xl p-6 border border-gray-700 mb-6 shadow-lg">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center space-y-4 md:space-y-0">
                <div class="space-y-2">
                    <h3 class="text-lg font-medium text-gray-100">
                        <span ng-if="previewDisplayMode === 'sections' && activePreviewSection">
                            {{activePreviewSection.title}} - Preview
                        </span>
                        <span ng-if="previewDisplayMode === 'all'">
                            All Questions Preview
                        </span>
                    </h3>
                    <p class="text-gray-300 text-sm">
                        Showing <span
                            class="text-cyan-300 font-medium">{{getCurrentPreviewPageQuestions().length}}</span> of
                        <span class="text-white font-medium">{{previewDisplayQuestions.length}}</span> questions
                    </p>
                </div>
                <div class="text-right">
                    <span
                        class="px-4 py-1 bg-gradient-to-r from-cyan-900/40 to-teal-900/30 text-cyan-100 rounded-full border border-cyan-500/50 shadow-inner text-sm font-medium">
                        Page {{currentPreviewQuestionPage + 1}}
                    </span>
                </div>
            </div>
        </div>

        <!-- Section Navigation (for section mode) -->
        <div ng-if="previewDisplayMode === 'sections'" class="mb-6">
            <div class="flex flex-wrap gap-2">
                <div ng-repeat="section in reviewSections track by section.id" ng-click="selectPreviewSection(section)"
                    class="px-3 py-2 rounded-lg border cursor-pointer transition-colors duration-200 text-sm" ng-class="activePreviewSection && activePreviewSection.id === section.id ? 
                           'bg-cyan-900/20 border-cyan-600 text-cyan-300' : 
                           'bg-gray-800/30 border-gray-600 text-gray-300 hover:bg-gray-700/30'">
                    <span class="font-medium">{{section.title}}</span>
                    <span class="ml-2 text-xs bg-gray-700/50 px-2 py-1 rounded">
                        {{section.questions.length || 0}} Q
                    </span>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex flex-row justify-center items-center gap-4 mb-8 pb-8 border-b border-gray-700">
            <button ng-click="previousPreviewQuestionPage()" ng-disabled="currentPreviewQuestionPage === 0"
                class=" flex flex-row items-center justify-center px-6 py-2 bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-600 text-gray-200 rounded-lg font-medium hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-arrow-left text-cyan-300"></i>
                <span class="hidden md:inline-block">Previous Page</span>
            </button>

            <div class="">
                <div class="flex items-center justify-center gap-4">
                    <div class="hidden sm:block h-2 w-24 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-cyan-500 to-teal-500"
                            ng-style="{'width': ((currentPreviewQuestionPage + 1) / previewQuestionPages.length) * 100 + '%'}">
                        </div>
                    </div>
                    <span class="text-gray-300 font-medium">
                        <span class="text-cyan-200">{{currentPreviewQuestionPage + 1}}</span> /
                        <span class="text-white">{{previewQuestionPages.length}}</span>
                    </span>
                </div>
            </div>

            <button ng-click="nextPreviewQuestionPage()"
                ng-disabled="currentPreviewQuestionPage === previewQuestionPages.length - 1"
                class=" flex flex-row items-center justify-center px-6 py-2 bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-600 text-gray-200 rounded-lg font-medium  hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="hidden md:inline-block">Next Page</span>
                <i class="fas fa-arrow-right text-cyan-300"></i>
            </button>
        </div>

        <!-- Questions List -->
        <div>
            <!-- All Questions Mode -->
            <div ng-if="previewDisplayMode === 'all'">
                <div class="mb-5" ng-repeat="section in currentPreviewPageSections track by section.id"
                    ng-class="section.description ? 'p-2 md:p-6 bg-gradient-to-t from-cyan-900/10 to-teal-900/10 rounded-lg md:rounded-2xl md:border border-cyan-500/30' : ''">
                    <!-- Section Header -->
                    <div ng-if="section.description" class="p-2 md:p-0 md:mt-0 mb-6">
                        <p class="text-gray-300" ng-if="section.description">
                            {{section.description}}
                        </p>
                        <br>
                        <p class="text-gray-300" ng-if="section.second_description">
                            {{section.second_description}}
                        </p>
                    </div>

                    <div ng-repeat="question in section.questions track by question.id"
                        ng-if="!question.isSectionHeader"
                        ng-class="section.questions.length > 1 ? 'mb-5 last:pb-4 last:border-0 md:last:border' : 'last:pb-4'"
                        class="relative md:bg-[#0004] backdrop-blur md:rounded-lg p-4 border-b md:border border-gray-700 md:pb-8 last:mb-0">

                        <!-- Question Number and Text -->
                        <div
                            class="flex flex-row gap-2 mb-6 relative before:absolute before:inset-x-0 before:bottom-0 before:h-px before:bg-gradient-to-r before:from-transparent before:via-gray-600 before:to-transparent before:content-[''] [&>span]:relative">
                            <div class="flex items-center mb-4">
                                <div
                                    class="w-8 h-8 flex items-center justify-center bg-gradient-to-br from-cyan-900/50 to-teal-900/30 rounded-full border border-cyan-500/30">
                                    <span class="text-cyan-200 font-bold">{{question._globalNumber}}</span>
                                </div>
                            </div>
                            <p class="text-gray-200 text-lg leading-relaxed">
                                {{question.question}}
                            </p>
                        </div>

                        <!-- Options -->
                        <div class="md:mb-6">
                            <div class="gap-2" ng-class="{
                                'grid grid-cols-1 gap-3': question.grid == 1,
                                'grid grid-cols-1 md:grid-cols-2 gap-4': question.grid == 2,
                                'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4': question.grid == 4
                            }">
                                <div ng-repeat="option in question.options track by $index" class="group relative"
                                    ng-click="selectPreviewOption(question.id, $index)">
                                    <div class="flex items-center py-2 px-4 border-cyan/100 hover:border-cyan-500/30 rounded-lg transition-all duration-200 cursor-pointer"
                                        ng-class="{
                                           'border-2 border-cyan-400 bg-cyan-300/10': question.selectedOption === $index,
                                           'hover:border-cyan-500/30': question.selectedOption !== $index
                                        }">
                                        <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-600 group-hover:border-cyan-500/50 transition-all duration-200 mr-4"
                                            ng-class="{
                                        'border-cyan-400': question.selectedOption === $index,
                                        'border-gray-600 group-hover:border-cyan-500/50': question.selectedOption !== $index
                                     }">
                                            <span
                                                class="text-gray-300 group-hover:text-cyan-200 font-bold transition-colors duration-200"
                                                ng-class="{
                                            'text-cyan-300': question.selectedOption === $index,
                                            'text-gray-300 group-hover:text-cyan-200': question.selectedOption !== $index
                                          }">
                                                {{$index | letterIndex: 'A'}}
                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-gray-100 group-hover:text-cyan-300 transition-colors duration-200"
                                                ng-class="{
                                        'text-cyan-300': question.selectedOption === $index,
                                        'text-gray-100 group-hover:text-cyan-300': question.selectedOption !== $index
                                       }">
                                                {{option.text}}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sections Mode -->
            <div ng-if="previewDisplayMode === 'sections' && activePreviewSection">
                <!-- Section Header -->
                <div
                    class="bg-gradient-to-r from-cyan-900/20 to-teal-900/10 backdrop-blur rounded-lg p-4 border border-cyan-500/30 mb-6">
                    <h3 class="text-xl font-bold text-cyan-200">{{activePreviewSection.title}}</h3>
                    <p class="text-gray-300 text-sm mt-1" ng-if="activePreviewSection.description">
                        {{activePreviewSection.description}}
                    </p>
                    <p class="text-gray-300 text-sm mt-1" ng-if="activePreviewSection.second_description">
                        {{activePreviewSection.second_description}}
                    </p>
                </div>

                <!-- Questions in this section -->
                <div ng-repeat="question in getCurrentPreviewPageQuestions() track by question.id"
                    ng-if="!question.isSectionHeader"
                    class="relative bg-gradient-to-tl from-black/50 to-black/30 backdrop-blur rounded-lg p-6 border border-gray-700 mb-10 pb-8 last:mb-0 last:pb-0">

                    <!-- Question Number and Text -->
                    <div
                        class="flex flex-row gap-2 mb-6 relative before:absolute before:inset-x-0 before:bottom-0 before:h-px before:bg-gradient-to-r before:from-transparent before:via-gray-600 before:to-transparent before:content-[''] [&>span]:relative">
                        <div class="flex items-center mb-4">
                            <div
                                class="w-8 h-8 flex items-center justify-center bg-gradient-to-br from-cyan-900/50 to-teal-900/30 rounded-full border border-cyan-500/30">
                                <span class="text-cyan-200 font-bold">{{(currentPreviewQuestionPage *
                                    previewQuestionsPerPage) + $index + 1}}</span>
                            </div>
                        </div>
                        <p class="text-gray-200 text-lg leading-relaxed">
                            {{question.question}}
                        </p>
                    </div>

                    <!-- Options -->
                    <div class="mb-6">
                        <div class="gap-2" ng-class="{
                        'grid grid-cols-1 gap-3': question.grid == 1,
                        'grid grid-cols-1 md:grid-cols-2 gap-4': question.grid == 2,
                        'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4': question.grid == 4
                    }">
                            <div ng-repeat="option in question.options track by $index" class="group relative"
                                ng-click="selectPreviewOption(question.id, $index)">
                                <div class="flex items-center py-2 px-4 border-cyan/100 hover:border-cyan-500/30 rounded-lg transition-all duration-200 cursor-pointer"
                                    ng-class="{
                                    'border-2 border-cyan-400 bg-cyan-300/10': question.selectedOption === $index,
                                    'hover:border-cyan-500/30': question.selectedOption !== $index
                                 }">
                                    <div class="w-8 h-8 flex items-center justify-center rounded-full bg-gradient-to-br from-gray-800 to-gray-900 border border-gray-600 group-hover:border-cyan-500/50 transition-all duration-200 mr-4"
                                        ng-class="{
                                        'border-cyan-400': question.selectedOption === $index,
                                        'border-gray-600 group-hover:border-cyan-500/50': question.selectedOption !== $index
                                     }">
                                        <span
                                            class="text-gray-300 group-hover:text-cyan-200 font-bold transition-colors duration-200"
                                            ng-class="{
                                            'text-cyan-300': question.selectedOption === $index,
                                            'text-gray-300 group-hover:text-cyan-200': question.selectedOption !== $index
                                          }">
                                            {{$index | letterIndex: 'A'}}
                                        </span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-gray-100 group-hover:text-cyan-300 transition-colors duration-200"
                                            ng-class="{
                                        'text-cyan-300': question.selectedOption === $index,
                                        'text-gray-100 group-hover:text-cyan-300': question.selectedOption !== $index
                                       }">
                                            {{option.text}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination Controls -->
        <div class="flex flex-row justify-center items-center gap-4 mb-8 pb-8 border-b border-gray-700">
            <button ng-click="previousPreviewQuestionPage()" ng-disabled="currentPreviewQuestionPage === 0"
                class=" flex flex-row items-center justify-center px-6 py-2 bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-600 text-gray-200 rounded-lg font-medium hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fas fa-arrow-left text-cyan-300"></i>
                <span class="hidden md:inline-block">Previous Page</span>
            </button>

            <div class="">
                <div class="flex items-center justify-center gap-4">
                    <div class="hidden sm:block h-2 w-24 bg-gray-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-cyan-500 to-teal-500"
                            ng-style="{'width': ((currentPreviewQuestionPage + 1) / previewQuestionPages.length) * 100 + '%'}">
                        </div>
                    </div>
                    <span class="text-gray-300 font-medium">
                        <span class="text-cyan-200">{{currentPreviewQuestionPage + 1}}</span> /
                        <span class="text-white">{{previewQuestionPages.length}}</span>
                    </span>
                </div>
            </div>

            <button ng-click="nextPreviewQuestionPage()"
                ng-disabled="currentPreviewQuestionPage === previewQuestionPages.length - 1"
                class=" flex flex-row items-center justify-center px-6 py-2 bg-gradient-to-r from-gray-800 to-gray-900 border border-gray-600 text-gray-200 rounded-lg font-medium  hover:from-gray-700 hover:to-gray-800 hover:border-cyan-500/50 hover:text-white transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="hidden md:inline-block">Next Page</span>
                <i class="fas fa-arrow-right text-cyan-300"></i>
            </button>
        </div>
    </div>

    <!-- Step 5: Publish Exam -->
    <div ng-show="currentStep === 5" class="max-w-4xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Publish Exam</h2>

            <!-- Publish Status -->
            <div class="bg-[#0004] rounded-lg p-6 border" ng-class="{
                   'border-green-500': examData.status === 'published',
                   'border-yellow-500': examData.status === 'draft',
                   'border-red-500': examData.status === 'canceled',
                   'border-blue-500': examData.status === 'scheduled'
                }">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="text-lg font-medium text-gray-100">Current Status</h3>
                        <p class="text-gray-400">Exam publication status</p>
                    </div>
                    <div class="px-4 py-2 rounded-lg capitalize"
                        ng-class="examData.status === 'published' ? 'bg-green-900/20 text-green-400' : 
                            examData.status === 'scheduled' ? 'bg-blue-900/20 text-blue-400' : 
                            examData.status === 'canceled' ? 'bg-red-900/20 text-red-400' : 'bg-yellow-900/20 text-yellow-400'">
                        <span class="font-medium">{{examData.status || 'draft'}}</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div ng-if="examData.status === 'published'">
                        <div class="flex items-center text-green-400 mb-2">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span class="font-medium">This exam is already published</span>
                        </div>
                        <p class="text-gray-300">Published on: {{examData.published_at | date:'fullDate'}}</p>
                    </div>

                    <div ng-if="examData.status === 'draft'">
                        <div class="flex items-center text-yellow-400 mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>
                            <span class="font-medium">This exam is in draft mode</span>
                        </div>
                        <p class="text-gray-300">Ready to be published. Review all details before publishing.</p>
                    </div>

                    <div ng-if="examData.status === 'cancelled'">
                        <div class="flex items-center text-red-400 mb-2">
                            <i class="fas fa-times-circle mr-2"></i>
                            <span class="font-medium">This exam has been cancelled</span>
                        </div>
                        <p class="text-gray-300">Candidates can no longer access this exam.</p>
                    </div>

                    <div ng-if="examData.status === 'scheduled'">
                        <div class="flex items-center text-blue-400 mb-2">
                            <i class="fas fa-clock mr-2"></i>
                            <span class="font-medium">This exam is scheduled</span>
                        </div>
                        <p class="text-gray-300">
                            Starts on:
                            {{ examData.start_time | date:'fullDate' }}
                            at {{ examData.start_time | date:'shortTime' }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- Publish Checklist -->
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mt-6">
                <h3 class="text-lg font-medium text-cyan-400 mb-4">Publishing Checklist</h3>

                <div class="space-y-3">
                    <!-- Step 1: Basic Info -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full mr-4 flex items-center justify-center"
                            ng-class="isBasicInfoComplete() ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas text-white" ng-class="isBasicInfoComplete() ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-100">Step 1: Basic Information</h4>
                            <p class="text-sm text-gray-400">Exam title, code, duration, marks, and instructions</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm" ng-class="isBasicInfoComplete() ? 'text-green-400' : 'text-red-400'">
                                {{isBasicInfoComplete() ? 'Complete' : 'Incomplete'}}
                            </span>
                        </div>
                    </div>

                    <!-- Step 2: Questions -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full mr-4 flex items-center justify-center"
                            ng-class="totalQuestions > 0 ? 'bg-green-500' : 'bg-red-500'">
                            <i class="fas text-white" ng-class="totalQuestions > 0 ? 'fa-check' : 'fa-times'"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-100">Step 2: Questions</h4>
                            <p class="text-sm text-gray-400">{{totalQuestions}} questions added ({{totalMarks}} total
                                marks)</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm" ng-class="totalQuestions > 0 ? 'text-green-400' : 'text-red-400'">
                                {{totalQuestions > 0 ? 'Complete' : 'No questions'}}
                            </span>
                        </div>
                    </div>

                    <!-- Step 3: Settings -->
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full mr-4 flex items-center justify-center"
                            ng-class="areSettingsValid() ? 'bg-green-500' : 'bg-yellow-500'">
                            <i class="fas text-white" ng-class="areSettingsValid() ? 'fa-check' : 'fa-exclamation'"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-medium text-gray-100">Step 3: Settings</h4>
                            <p class="text-sm text-gray-400">Exam configuration and security settings</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm" ng-class="areSettingsValid() ? 'text-green-400' : 'text-yellow-400'">
                                {{areSettingsValid() ? 'Configured' : 'Review needed'}}
                            </span>
                        </div>
                    </div>

                    <!-- Overall Status -->
                    <div class="mt-6 pt-6 border-t border-gray-600">
                        <div class="flex justify-between items-center">
                            <div>
                                <h4 class="font-medium text-gray-100">Ready to Publish?</h4>
                                <p class="text-sm text-gray-400">All requirements must be met to publish</p>
                            </div>
                            <div>
                                <span class="px-4 py-2 rounded-lg font-medium"
                                    ng-class="isReadyToPublish() ? 'text-green-600' : 'text-gray-600'">
                                    <i class="fas" ng-class="isReadyToPublish() ? 'fa-check' : 'fa-times'"></i>
                                    {{isReadyToPublish() ? 'Ready to Publish' : 'Not Ready'}}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publish Actions -->
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mt-6">
                <h3 class="text-lg font-medium text-cyan-400 mb-4">Publish Actions</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Publish Button -->
                    <button ng-click="publishExam()"
                        ng-disabled="!isReadyToPublish() || examData.status === 'published' || examData.status === 'scheduled'"
                        class="bg-green-600 hover:bg-green-700 text-white rounded-lg p-4 flex flex-col items-center justify-center transition-colors duration-200 disabled:opacity-50">
                        <i class="fas fa-paper-plane text-2xl mb-2"></i>
                        <span class="font-medium">Publish Exam</span>
                        <span class="text-sm mt-1">Make exam available to candidates</span>
                    </button>

                    <!-- Unpublish Button -->
                    <button ng-click="unpublishExam()"
                        ng-disabled="!(examData.status === 'published' || examData.status === 'scheduled')"
                        class="bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg p-4 flex flex-col items-center justify-center transition-colors duration-200 disabled:opacity-50">
                        <i class="fas fa-undo text-2xl mb-2"></i>
                        <span class="font-medium">Unpublish Exam</span>
                        <span class="text-sm mt-1">Return to draft for changes</span>
                    </button>
                </div>

                <!-- Entry Link -->
                <div class="mt-6 pt-6 border-t border-gray-600">
                    <h4 class="font-medium text-gray-100 mb-2">Entry Link</h4>
                    <div class="flex items-center">
                        <input type="text" value="<?php echo BASE_URL . '/attempt/'; ?>{{location.exam}}" readonly
                            class="flex-1 bg-[#0008] border border-gray-600 text-gray-300 rounded-l-lg px-4 py-2">
                        <button ng-click="copyToClipboard($event.target.previousElementSibling)"
                            class="bg-cyan-600 hover:bg-cyan-700 text-white px-4 py-2 rounded-r-lg">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Share this link with candidates for exam attempt</p>
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mt-6">
                <h3 class="text-lg font-medium text-cyan-400 mb-4">Exam Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-cyan-400">{{totalQuestions}}</div>
                        <div class="text-sm text-cyan-300">Total Questions</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-400">{{totalMarks}}</div>
                        <div class="text-sm text-green-300">Total Marks</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-400">{{sections.length}}</div>
                        <div class="text-sm text-purple-300">Sections</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-400">{{examData.duration || 0}}</div>
                        <div class="text-sm text-yellow-300">Minutes</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex justify-center gap-4 mt-8 pt-6 border-t border-gray-600">
        <button ng-click="previousStep()" ng-show="currentStep > 1"
            class="px-6 py-2 border border-gray-600 text-gray-300 rounded-lg font-medium flex items-center space-x-2 hover:bg-gray-700 transition-colors duration-200">
            <i class="fas fa-arrow-left"></i>
            <span>Previous</span>
        </button>

        <!-- <div class="flex-1"></div> -->

        <button ng-click="nextStep()" ng-show="currentStep < 5"
            class="px-6 py-2 bg-cyan-600 text-white rounded-lg font-medium flex items-center space-x-2 hover:bg-cyan-700 transition-colors duration-200">
            <span>Next</span>
            <i class="fas fa-arrow-right"></i>
        </button>
    </div>
</div>

<?php $this->end(); ?>