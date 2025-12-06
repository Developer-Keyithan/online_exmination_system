<?php $this->extend('frontend');
$this->controller('ExamController');
?>
<?php $this->start('content'); ?>

<div class="bg-[#0003] p-6 rounded-lg mb-16" ng-cloak>
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between md:items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold">Create New Exam</h1>
            <p class="text-gray-400">Set up a new examination with sections and questions</p>
        </div>
        <a href="exams"
            class="bg-gray-600 hover:bg-gray-700 mt-4 md:mt-0 w-fit text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Exams</span>
        </a>
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
            <!-- <div class="flex-1 h-1 bg-gray-600 mx-4" ng-if="!$last"></div> -->
        </div>
    </div>

    <!-- Exam Basic Information Section -->
    <div ng-show="currentStep === 1" class="max-w-4xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Exam Basic Information</h2>

            <form name="basicInfoForm" id="basicInfoForm" novalidate onsubmit="return false">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Exam Title -->
                    <div class="form-group md:col-span-2">
                        <label for="examTitle" class="form-label">Exam Title <span class="text-red-700">*</span></label>
                        <input type="text" id="examTitle" ng-model="examData.title" required class="form-input"
                            name="examTitle" placeholder="Enter exam title">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examTitle.$error.required">
                            Exam title is required
                        </div>
                    </div>

                    <!-- Exam Code -->
                    <div class="form-group md:col-span-2">
                        <label for="examCode" class="form-label">Exam Code <span class="text-red-700">*</span></label>
                        <input type="text" id="examCode" ng-model="examData.code" required class="form-input"
                            name="examCode" placeholder="e.g., MATH-2024-FINAL">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examCode.$error.required">
                            Exam code is required
                        </div>
                    </div>

                    <!-- Duration -->
                    <div class="form-group">
                        <label for="examDuration" class="form-label">Duration (minutes) <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="examDuration" ng-model="examData.duration" required min="1"
                            name="examDuration" class="form-input" placeholder="e.g., 120">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examDuration.$error.required">
                            Duration is required
                        </div>
                    </div>

                    <!-- Total number of questions -->
                    <div class="form-group">
                        <label for="totalQuestions" class="form-label">Total questions <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="totalQuestions" ng-model="neededQuestionsCount" required min="1"
                            name="totalQuestions" class="form-input" placeholder="e.g., 20">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.totalQuestions.$error.required">
                            Number of total question is required
                        </div>
                    </div>

                    <!-- Total Marks -->
                    <div class="form-group">
                        <label for="totalMarks" class="form-label">Total Marks <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="totalMarks" ng-model="examData.total_marks" required min="1"
                            name="totalMarks" class="form-input" placeholder="e.g., 100">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.totalMarks.$error.required">
                            Total marks is required
                        </div>
                    </div>

                    <!-- Passing Marks -->
                    <div class="form-group">
                        <label for="passingMarks" class="form-label">Passing Marks <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="passingMarks" ng-model="examData.passing_marks" required min="1"
                            name="passingMarks" max="{{examData.total_marks}}" class="form-input"
                            placeholder="e.g., 40">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.passingMarks.$error.required">
                            Passing marks is required
                        </div>
                        <div class="error-message" ng-show="basicInfoForm.passingMarks.$error.max">
                            Passing marks cannot exceed total marks
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="form-group md:col-span-2">
                        <label for="examInstructions" class="form-label">Exam Instructions</label>
                        <textarea id="examInstructions" ng-model="examData.instructions" rows="4" class="form-input"
                            name="examInstructions" placeholder="Enter exam instructions for candidates..."></textarea>
                    </div>

                    <!-- Status -->
                    <div class="form-group hidden">
                        <label class="form-label">Status</label>
                        <div class="flex space-x-4">
                            <!-- Draft -->
                            <label>
                                <input type="radio" name="examStatus" value="draft" ng-model="examData.status"
                                    class="hidden">
                                <div ng-class="examData.status=='draft' 
                                    ? 'px-4 py-2 bg-blue-600 text-white rounded cursor-pointer' 
                                    : 'px-4 py-2 bg-transparent border rounded cursor-pointer'">
                                    Draft
                                </div>
                            </label>

                            <!-- Published -->
                            <label>
                                <input type="radio" name="examStatus" value="published" ng-model="examData.status"
                                    class="hidden">
                                <div ng-class="examData.status=='published' 
                                    ? 'px-4 py-2 bg-blue-600 text-white rounded cursor-pointer' 
                                    : 'px-4 py-2 bg-transparent border rounded cursor-pointer'">
                                    Published
                                </div>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Questions & Sections Management -->
    <div ng-show="currentStep === 2" class="max-w-7xl mx-auto">
        <div class="md:bg-[#0004] rounded-lg p-0 md:p-6 md:border border-gray-600">
            <!-- Header with Progress Indicators -->
            <div class="mb-6">
                <div class="flex flex-wrap justify-between gap-2 items-center mb-4">
                    <h2 class="text-xl font-semibold text-gray-100">Questions & Sections Management</h2>
                    <div class="flex flex-wrap md:flex-row gap-2">
                        <button type="button" ng-click="addNewSection()"
                            class="bg-cyan-600 hover:bg-cyan-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-layer-group"></i>
                            <span>Add Section</span>
                        </button>
                        <button type="button" ng-click="startNewQuestion()"
                            class="bg-green-600 hover:bg-green-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                            <i class="fas fa-question-circle"></i>
                            <span>Create Question</span>
                        </button>
                        <!-- <button type="button" ng-click="importQuestion()" disabled
                            class="flex items-center justify-center space-x-2 bg-green-600 hover:bg-green-700 text-white w-full md:w-auto py-2 px-4 rounded-lg disabled:bg-opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                            <i class="fa-solid fa-download"></i>
                            <span>Import Question</span>
                        </button> -->
                    </div>
                </div>

                <!-- Progress Status Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
                    <!-- Questions Created Status -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-400 mb-1">Questions Created</div>
                                <div class="text-lg font-semibold text-white">
                                    {{createdQuestionsCount}} / {{neededQuestionsCount}}
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                ng-class="isAllQuestionsAreCreated ? 'bg-green-500' : 'bg-yellow-500'">
                                <i class="fas"
                                    ng-class="isAllQuestionsAreCreated ? 'fa-check text-white' : 'fa-exclamation text-white'"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs"
                            ng-class="isAllQuestionsAreCreated ? 'text-green-400' : 'text-yellow-400'">
                            <span ng-if="isAllQuestionsAreCreated">✓ All questions created</span>
                            <span ng-if="!isAllQuestionsAreCreated">
                                {{neededQuestionsCount - createdQuestionsCount}} more needed
                            </span>
                        </div>
                    </div>

                    <!-- Questions Saved Status -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-400 mb-1">Questions Saved</div>
                                <div class="text-lg font-semibold text-white">
                                    {{ savedQuestionsCount }} / {{ createdQuestionsCount }}
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                ng-class="isAllQuestionsAreSaved ? 'bg-green-500' : 'bg-red-500'">
                                <i class="fas"
                                    ng-class="isAllQuestionsAreSaved ? 'fa-check text-white' : 'fa-times text-white'"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs" ng-class="isAllQuestionsAreSaved ? 'text-green-400' : 'text-red-400'">
                            <span ng-if="isAllQuestionsAreSaved">✓ All questions saved</span>
                            <span ng-if="!isAllQuestionsAreSaved">
                                {{ createdQuestionsCount - savedQuestionsCount }} unsaved
                                question{{(createdQuestionsCount - getSavedCount()) > 1 ? 's' : ''}}
                            </span>
                        </div>
                    </div>

                    <!-- Sections Completed Status -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-400 mb-1">Sections Completed</div>
                                <div class="text-lg font-semibold text-white">
                                    <span ng-if="totalSectionsCount > 0">
                                        {{ completedSectionsCount }} / {{ totalSectionsCount }}
                                    </span>
                                    <span ng-if="totalSectionsCount === 0">0 / 0</span>
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                ng-class="isAllSectionsAreCompleted ? 'bg-green-500' : totalSectionsCount === 0 ? 'bg-gray-500' : 'bg-yellow-500'">
                                <i class="fas"
                                    ng-class="isAllSectionsAreCompleted ? 'fa-check text-white' : totalSectionsCount === 0 ? 'fa-minus text-white' : 'fa-exclamation text-white'"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs"
                            ng-class="isAllSectionsAreCompleted ? 'text-green-400' : totalSectionsCount === 0 ? 'text-gray-400' : 'text-yellow-400'">
                            <span ng-if="isAllSectionsAreCompleted">✓ All sections completed</span>
                            <span ng-if="!isAllSectionsAreCompleted && totalSectionsCount > 0">
                                {{ totalSectionsCount - completedSectionsCount }} incomplete
                            </span>
                            <span ng-if="totalSectionsCount === 0">No sections created</span>
                        </div>
                    </div>

                    <!-- Overall Status -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600"
                        ng-class="isAllQuestionsAndSectionsAreCompleted ? 'border-green-500' : 'border-gray-600'">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-sm text-gray-400 mb-1">Step 2 Ready</div>
                                <div class="text-lg font-semibold"
                                    ng-class="isAllQuestionsAndSectionsAreCompleted ? 'text-green-400' : 'text-white'">
                                    {{isAllQuestionsAndSectionsAreCompleted ? 'Ready to Proceed' : 'In Progress'}}
                                </div>
                            </div>
                            <div class="w-8 h-8 rounded-full flex items-center justify-center"
                                ng-class="isAllQuestionsAndSectionsAreCompleted ? 'bg-green-500' : 'bg-blue-500'">
                                <i class="fas"
                                    ng-class="isAllQuestionsAndSectionsAreCompleted ? 'fa-check text-white' : 'fa-solid fa-list-check  text-white'"></i>
                            </div>
                        </div>
                        <div class="mt-2 text-xs"
                            ng-class="isAllQuestionsAndSectionsAreCompleted ? 'text-green-400' : 'text-blue-400'">
                            <span ng-if="isAllQuestionsAndSectionsAreCompleted">
                                ✓ All requirements met for Step 2
                            </span>
                            <span ng-if="!isAllQuestionsAndSectionsAreCompleted">
                                Complete all requirements to proceed
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Requirements Checklist -->
                <div class="bg-[#0006] rounded-lg p-4 border border-gray-600 mb-6">
                    <h3 class="text-lg font-medium text-gray-100 mb-3">Requirements Checklist</h3>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                                ng-class="createdQuestionsCount >= neededQuestionsCount ? 'bg-green-500' : 'bg-gray-600'">
                                <i class="fas fa-xs text-white"
                                    ng-class="createdQuestionsCount >= neededQuestionsCount ? 'fa-check' : 'fa-times'"></i>
                            </div>
                            <span class="text-sm"
                                ng-class="createdQuestionsCount >= neededQuestionsCount ? 'text-green-400' : 'text-gray-300'">
                                Create {{neededQuestionsCount}} questions
                                <span class="text-gray-500">
                                    ({{createdQuestionsCount}}/{{neededQuestionsCount}})
                                </span>
                            </span>
                        </div>

                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                                ng-class="isAllQuestionsAreSaved ? 'bg-green-400' : 'bg-gray-600'">
                                <i class="fas fa-xs text-white"
                                    ng-class="isAllQuestionsAreSaved ? 'fa-check' : 'fa-times'"></i>
                            </div>
                            <span class="text-sm"
                                ng-class="isAllQuestionsAreSaved ? 'text-green-500' : 'text-gray-300'">
                                Save all questions
                                <span class="text-gray-500">
                                    ({{savedQuestionsCount}}/{{createdQuestionsCount}}
                                    saved)
                                </span>
                            </span>
                        </div>

                        <div class="flex items-center">
                            <div class="w-6 h-6 rounded-full mr-3 flex items-center justify-center"
                                ng-class="isAllSectionsAreCompleted ? 'bg-green-500' : 'bg-gray-600'">
                                <i class="fas fa-xs text-white"
                                    ng-class="isAllSectionsAreCompleted ? 'fa-check' : 'fa-times'"></i>
                            </div>
                            <span class="text-sm"
                                ng-class="isAllSectionsAreCompleted ? 'text-green-400' : 'text-gray-300'">
                                Complete all sections
                                <span class="text-gray-500" ng-if="totalSectionsCount > 0">
                                    {{ completedSectionsCount }}/{{ totalSectionsCount }} completed
                                </span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column: Questions List & Navigation -->
                <div class="xl:col-span-1">
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Questions ({{createdQuestionsCount}})
                        </h3>

                        <!-- Questions Navigation -->
                        <div class="mb-4">
                            <div class="flex flex-wrap gap-2">

                                <!-- 1) FIRST 8 QUESTIONS -->
                                <button ng-repeat="question in savedQuestions.slice(0,8) track by $index"
                                    ng-click="loadQuestionForEditing($index)"
                                    class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-300"
                                    ng-class="currentQuestionIndex === $index 
                                        ? 'bg-cyan-600 text-white' 
                                        : (question.isSaved ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-yellow-600 text-white hover:bg-yellow-700')">
                                    {{$index + 1}}
                                </button>

                                <!-- 3) EXTRA QUESTIONS – SHOW ONLY WHEN EXPANDED -->
                                <button ng-if="showMoreQuestions"
                                    ng-repeat="question in savedQuestions.slice(8) track by $index"
                                    ng-click="loadQuestionForEditing($index + 8)"
                                    class="w-10 h-10 rounded-lg flex items-center justify-center text-sm font-medium transition-all duration-300"
                                    ng-class="currentQuestionIndex === ($index + 8)
                                        ? 'bg-cyan-600 text-white' 
                                        : (question.isSaved ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-yellow-600 text-white hover:bg-yellow-700')">
                                    {{$index + 9}}
                                </button>

                                <!-- 2) IF SHOWING MORE → SHOW "HIDE MORE" BUTTON BEFORE ADD -->
                                <button ng-if="createdQuestionsCount > 8 && showMoreQuestions"
                                    ng-click="toggleMoreQuestions()"
                                    class="w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition-all duration-300">
                                    -{{ createdQuestionsCount - 8 }}
                                </button>

                                <!-- 4) MORE BUTTON – ONLY IF NOT EXPANDED -->
                                <button ng-if="createdQuestionsCount > 8 && !showMoreQuestions"
                                    ng-click="toggleMoreQuestions()"
                                    class="w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition-all duration-300">
                                    +{{ createdQuestionsCount - 8 }}
                                </button>

                                <!-- 5) ADD NEW QUESTION BUTTON (ALWAYS LAST) -->
                                <button ng-click="startNewQuestion()"
                                    class="w-10 h-10 rounded-lg bg-blue-600 hover:bg-blue-700 text-white">
                                    <i class="fas fa-plus"></i>
                                </button>

                            </div>
                        </div>

                        <!-- Questions List -->
                        <div class="space-y-2 max-h-96 overflow-y-auto">
                            <div ng-repeat="question in savedQuestions track by $index"
                                ng-click="loadQuestionForEditing($index)"
                                class="relative p-3 rounded-lg border cursor-pointer transition-colors duration-200"
                                ng-class="currentQuestionIndex === $index 
                                    ? 'bg-cyan-600 border-cyan-600' 
                                    : 'bg-[#0008] border-gray-600 hover:bg-[#000a]'">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm font-medium text-white">Q{{$index + 1}}</span>
                                            <span class="text-xs px-2 py-1 rounded-full"
                                                ng-class="question.isSaved ? 'bg-green-600 text-white' : 'bg-yellow-600 text-white'">
                                                {{question.isSaved ? 'Saved' : 'Unsaved'}}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-300 truncate">{{question.question || 'No
                                            question
                                            text'}}</p>
                                        <div class="flex justify-between items-center mt-2 text-xs text-gray-400">
                                            <span>{{question.marks || 0}} marks</span>
                                        </div>
                                        <div class="mt-1"
                                            ng-if="question.assignedSections && question.assignedSections.length > 0">
                                            <span class="text-xs text-cyan-400">
                                                Assigned Section{{question.assignedSections.length > 1 ? 's' :
                                                ''}}:
                                                {{getAssignedSectionNames(question)}}
                                            </span>
                                            <button title="Remove assign question to section"
                                                ng-click="openUnassignSectionModal(question.id)">
                                                <i
                                                    class="fa-solid fa-link-slash absolute top-3 right-3 text-red-400 text-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Empty State -->
                            <div ng-if="createdQuestionsCount === 0" class="text-center py-8">
                                <i class="fas fa-question-circle text-gray-500 text-3xl mb-2"></i>
                                <p class="text-gray-400">No questions created yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sections Panel -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600 mt-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-100">Sections ({{totalSectionsCount}})
                            </h3>
                        </div>

                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            <div ng-repeat="section in savedSections track by $index"
                                class="p-3 border border-gray-600 rounded-lg bg-[#0008]">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-gray-100">{{section.title || 'Untitled
                                        Section'}}</h4>
                                    <div class="flex space-x-1">
                                        <button type="button" ng-click="editSection($index)"
                                            class="text-blue-400 hover:text-blue-300 transition-colors text-sm">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" ng-click="removeSection(section.id)"
                                            class="text-red-400 hover:text-red-300 transition-colors text-sm">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-400 space-y-1">
                                    <div>Questions: {{section.assignedQuestions ||
                                        0}}/{{section.question_count}}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State for Sections -->
                        <div ng-if="totalSectionsCount === 0" class="text-center py-4">
                            <i class="fas fa-layer-group text-gray-500 text-2xl mb-2"></i>
                            <p class="text-gray-400 text-sm">No sections created</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Question Editor -->
                <div class="xl:col-span-2">
                    <div class="bg-[#0006] rounded-lg p-6 border border-gray-600">
                        <div class="flex flex-wrap gap-2 justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-100">
                                <span ng-if="currentQuestionIndex !== null">Edit Question {{currentQuestionIndex
                                    +
                                    1}}</span>
                                <span ng-if="currentQuestionIndex === null">Create New Question</span>
                                <span class="text-sm font-normal text-cyan-400 ml-2"
                                    ng-if="currentQuestion && !currentQuestion.isSaved">
                                    (Unsaved)
                                </span>
                            </h3>
                            <div class="flex flex-wrap md:flex-row gap-2">
                                <button type="button" ng-click="saveCurrentQuestion()"
                                    title="{{currentQuestion.isSaved ? 'Update' : 'Save'}} this question"
                                    ng-disabled="!currentQuestion.question"
                                    class="bg-green-600 hover:bg-green-700 text-white py-2 px-4 w-full md:w-auto rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <i class="fas fa-save"></i>
                                    <span>{{currentQuestion.isSaved ? 'Update' : 'Save'}}</span>
                                </button>
                                <button type="button" ng-click="assignToSection()"
                                    title="Assign this question to a section"
                                    ng-disabled="!currentQuestion.isSaved || totalSectionsCount === 0"
                                    class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 w-full md:w-auto rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <i class="fas fa-layer-group"></i>
                                    <span>Assign to Section</span>
                                </button>
                                <button type="button" ng-click="removeCurrentQuestionFromExam()"
                                    title="Remove this question from this exam"
                                    ng-disabled="currentQuestionIndex === null"
                                    class="bg-red-600 hover:bg-red-700 text-white py-2 px-4 w-full md:w-auto rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <i class="fas fa-trash"></i>
                                    <span>Remove</span>
                                </button>
                            </div>
                        </div>

                        <!-- Question Editor -->
                        <div class="space-y-4" ng-if="currentQuestion">
                            <form id="questionForm{{currentQuestion.id || 'New'}}" onsubmit="return false"
                                enctype="multipart/form-data">
                                <!-- Exam ID hidden -->
                                <input type="hidden" name="exam_id" ng-value="examID">
                                <div class="flex flex-wrap md:flex-row">
                                    <!-- Question Text -->
                                    <div class="form-group w-full md:w-2/3">
                                        <label class="form-label">Question Text <span
                                                class="text-red-700">*</span></label>
                                        <textarea ng-model="currentQuestion.question" required rows="5"
                                            class="form-input" name="question"
                                            placeholder="Enter your question here..."></textarea>
                                    </div>

                                    <!-- Question Image -->
                                    <div class="w-full md:w-1/3 md:pl-2">
                                        <div class="form-group">
                                            <label class="form-label">Question Image (Optional)</label>
                                            <div
                                                class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center">
                                                <div ng-if="!currentQuestion.image">
                                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
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
                                    </div>
                                </div>

                                <!-- Multiple Choice Options -->
                                <div class="space-y-4">
                                    <label class="form-label">Options <span class="text-red-700">*</span></label>

                                    <div ng-repeat="option in currentQuestion.options track by $index"
                                        class="flex items-center space-x-3">
                                        <label for="option{{$index}}" class="flex space-x-3">
                                            <input type="radio" id="option{{$index}}" name="answer"
                                                ng-model="currentQuestion.answer" ng-value="option.op"
                                                class="text-cyan-500 cursor-pointer">
                                            <p>{{ option.op }}&#x29;</p>
                                        </label>

                                        <div class="flex-1">
                                            <input type="text" ng-model="option.text" class="form-input mb-2"
                                                name="{{option.op}}" placeholder="Option {{ option.op }} text">

                                            <div id="{{ option.op }}ImgContainer" ng-if="option.image"
                                                class="relative inline-block">
                                                <img ng-src="{{option.image}}" class="max-w-32 max-h-32 rounded">
                                                <button type="button" ng-click="removeOptionImage(option)"
                                                    class="absolute top-1 right-1 bg-red-600 text-white p-1 rounded-full text-xs">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="flex space-x-2">
                                            <button type="button" ng-click="uploadOptionImage(option)"
                                                class="text-purple-400 hover:text-purple-300">
                                                <i class="fas fa-image"></i>
                                            </button>

                                            <!-- <button type="button" ng-click="removeOption(currentQuestion, $index)"
                                                ng-disabled="currentQuestion.options.length <= 2"
                                                class="text-red-400 hover:text-red-300 disabled:opacity-50">
                                                <i class="fas fa-times"></i>
                                            </button> -->
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
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-600">
                                    <!-- Marks -->
                                    <div class="form-group">
                                        <label class="form-label">Marks <span class="text-red-700">*</span></label>
                                        <input type="number" ng-model="currentQuestion.marks" required min="0.5"
                                            name="marks" step="0.5" class="form-input" placeholder="Marks">
                                    </div>
                                </div>
                            </form>

                            <!-- Navigation Buttons -->
                            <div class="flex flex-col md:flex-row gap-2 justify-between">
                                <button type="button" ng-click="previousQuestion()"
                                    ng-disabled="currentQuestionIndex === null || currentQuestionIndex === 0"
                                    class="bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <i class="fas fa-arrow-left"></i>
                                    <span>Previous</span>
                                </button>

                                <button type="button" ng-click="startNewQuestion()"
                                    class="bg-blue-600 hover:bg-blue-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
                                    <i class="fas fa-plus"></i>
                                    <span>New Question</span>
                                </button>

                                <button type="button" ng-click="nextQuestion()"
                                    ng-disabled="currentQuestionIndex === null || currentQuestionIndex >= createdQuestionsCount - 1"
                                    class="bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <span>Next</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Empty State for Question Editor -->
                        <div ng-if="!currentQuestion && !isAllQuestionsAreCreated" class="text-center py-12">
                            <i class="fas fa-question-circle text-gray-500 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-100 mb-2">No Question Selected</h3>
                            <p class="text-gray-400 mb-6">Select a question from the list or create a new one to
                                start
                                editing.</p>
                            <button type="button" ng-click="startNewQuestion()"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">
                                {{createdQuestionsCount > 0 ? 'Create New Question' : 'Add Question'}}
                            </button>
                        </div>

                        <!-- All Questions Created Indicator for Question Editor -->
                        <div ng-if="isAllQuestionsAreCreated && !currentQuestion" class="text-center py-12">
                            <i class="fas fa-question-circle text-gray-500 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-100 mb-2">All Questions Created</h3>
                            <p class="text-gray-400">Total questions required: {{ neededQuestionsCount }}</p>
                            <p class="text-gray-400">Total questions saved: {{ savedQuestionsCount }}</p>
                            <p ng-if="unsavedQuestionsCount > 0" class="text-gray-400">Total questions unsaved: {{
                                unsavedQuestionsCount }}</p>
                            <p ng-if="unsavedQuestionsCount > 0" class="text-gray-400">⚠️ Please save unsaved questions
                                before moving to the next part.</p>
                            <button type="button"
                                ng-click="unsavedQuestionsCount > 0 ? saveUnsavedQuestions() : nextStep()"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 mt-6 rounded-lg transition-colors duration-200">
                                {{ unsavedQuestionsCount > 0 ? 'Save unsaved questions & Move next part' : 'Move next
                                part' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule & Settings Section -->
    <div ng-show="currentStep === 3" class="max-w-4xl mx-auto">
        <form id="exam_settings_form" onsubmit="return false" class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <input type="hidden" name="exam_id" value="{{ examID }}">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Schedule & Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Schedule Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Schedule</h3>
                </div>

                <!-- Schedule Type Selection -->
                <div class="form-group md:col-span-2">
                    <label class="form-label mb-2">Schedule Type</label>
                    <div class="flex flex-wrap gap-4">
                        <!-- Any Time Option -->
                        <label class="flex-1 min-w-[200px] cursor-pointer">
                            <input type="radio" name="scheduleType" required ng-model="examData.schedule_type"
                                ng-change="examData.isSettingsDone = false" value="anytime" class="hidden">
                            <div class="h-full p-4 border-2 rounded-lg transition-all duration-200" ng-class="examData.schedule_type === 'anytime' ? 
                                     'border-cyan-500 bg-cyan-900/20' : 
                                     'border-gray-600 hover:border-gray-500 bg-[#0006]'">
                                <div class="flex items-center justify-center space-x-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                        ng-class="examData.schedule_type === 'anytime' ? 
                                             'border-cyan-500 bg-cyan-500' : 
                                             'border-gray-500'">
                                        <div ng-if="examData.schedule_type === 'anytime'"
                                            class="w-2 h-2 rounded-full bg-white"></div>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-100 flex flex-wrap items-center gap-x-2">Any
                                            Time Attempt<span class="text-gray-400 text-sm">(No start time
                                                restrictions)</span></h4>
                                        <!-- <p class="text-sm text-gray-400 mt-1">No start/end time restrictions</p> -->
                                    </div>
                                </div>
                            </div>
                        </label>

                        <!-- Scheduled Option -->
                        <label class="flex-1 min-w-[200px] cursor-pointer">
                            <input type="radio" name="scheduleType" required ng-model="examData.schedule_type"
                                ng-change="examData.isSettingsDone = false" value="scheduled" class="hidden">
                            <div class="h-full p-4 border-2 rounded-lg transition-all duration-200" ng-class="examData.schedule_type === 'scheduled' ? 
                                     'border-cyan-500 bg-cyan-900/20' : 
                                     'border-gray-600 hover:border-gray-500 bg-[#0006]'">
                                <div class="flex items-center justify-center space-x-3">
                                    <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
                                        ng-class="examData.schedule_type === 'scheduled' ? 
                                             'border-cyan-500 bg-cyan-500' : 
                                             'border-gray-500'">
                                        <div ng-if="examData.schedule_type === 'scheduled'"
                                            class="w-2 h-2 rounded-full bg-white"></div>
                                    </div>
                                    <div>
                                        <h4 class="font-medium text-gray-100 flex flex-wrap items-center gap-x-2">
                                            Scheduled Attempt<span class="text-gray-400 text-sm">(Set specific time
                                                window)</span></h4>
                                        <!-- <p class="text-sm text-gray-400 mt-1">Set specific time window</p> -->
                                    </div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Start Date & Time (Conditional) -->
                <div class="form-group" ng-if="examData.schedule_type === 'scheduled'">
                    <label for="startDateTime" class="form-label">Start Date & Time</label>
                    <input type="datetime-local" id="startDateTime" name="startDateTime" required
                        ng-model="examData.start_time" ng-change="examData.isSettingsDone = false" class="form-input">
                </div>

                <!-- End Date & Time (Conditional) -->
                <!-- <div class="form-group" ng-if="examData.schedule_type === 'scheduled'">
                    <label for="endDateTime" class="form-label">End Date & Time</label>
                    <input type="datetime-local" id="endDateTime" name="endDateTime" required ng-model="examData.end_time" ng-change="examData.isSettingsDone = false" class="form-input">
                </div> -->

                <!-- Exam Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Exam Settings</h3>
                </div>

                <!-- Shuffle Questions -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.shuffle_questions"
                            ng-change="examData.isSettingsDone = false" id="shuffleQuestions" name="shuffleQuestions"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Shuffle Questions</span>
                    </label>
                </div>

                <!-- Shuffle Options -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.shuffle_options"
                            ng-change="examData.isSettingsDone = false" id="shuffleOptions" name="shuffleOptions"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Shuffle Options</span>
                    </label>
                </div>

                <!-- Show Results Immediately -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.show_results_immediately"
                            ng-change="examData.isSettingsDone = false" id="showResultsImmediately"
                            name="showResultsImmediately" required
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Show Results Immediately</span>
                    </label>
                </div>

                <!-- Allow Retake -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.allow_retake"
                            ng-change="examData.isSettingsDone = false" id="allowRetake" name="allowRetake" required
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Allow Retake</span>
                    </label>
                </div>

                <!-- Max Attempts -->
                <div class="form-group" ng-if="examData.allow_retake">
                    <label for="maxAttempts" class="form-label">Maximum Attempts</label>
                    <input type="number" id="maxAttempts" name="maxAttempts" ng-model="examData.max_attempts"
                        ng-change="examData.isSettingsDone = false" min="1" class="form-input" placeholder="e.g., 3">
                </div>

                <!-- Security Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Security Settings</h3>
                </div>

                <!-- Enable Proctoring -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.enable_proctoring"
                            ng-change="examData.isSettingsDone = false" id="enableProctoring" name="enableProctoring"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Enable Proctoring</span>
                    </label>
                </div>

                <!-- Full Screen Mode -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.full_screen_mode"
                            ng-change="examData.isSettingsDone = false" id="fullScreenMode" name="fullScreenMode"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Force Full Screen Mode</span>
                    </label>
                </div>

                <!-- Disable Copy Paste -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.disable_copy_paste"
                            ng-change="examData.isSettingsDone = false" id="disableCopyPaste" name="disableCopyPaste"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Disable Copy/Paste</span>
                    </label>
                </div>

                <!-- Disable Right Click -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.disable_right_click"
                            ng-change="examData.isSettingsDone = false" id="disableRightClick" name="disableRightClick"
                            required class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Disable Right Click</span>
                    </label>
                </div>
            </div>
        </form>
    </div>

    <!-- Review Exam Config -->
    <div ng-show="currentStep === 4" class="max-w-4xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Review Exam Details</h2>

            <!-- Exam Summary -->
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><strong class="text-gray-400">Title:</strong> <span
                                class="text-gray-100">{{examData.title}}</span></div>
                        <div><strong class="text-gray-400">Code:</strong> <span
                                class="text-gray-100">{{examData.code}}</span></div>
                        <div><strong class="text-gray-400">Duration:</strong> <span
                                class="text-gray-100">{{examData.duration}} minutes</span></div>
                        <div><strong class="text-gray-400">Total Marks:</strong> <span
                                class="text-gray-100">{{examData.total_marks}}</span></div>
                        <div><strong class="text-gray-400">Passing Marks:</strong> <span
                                class="text-gray-100">{{examData.passing_marks}}</span></div>
                        <div><strong class="text-gray-400">Status:</strong> <span
                                class="text-gray-100">{{examData.status}}</span></div>
                    </div>
                </div>

                <!-- Questions Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Questions Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Total Questions Created:</span>
                            <span class="text-cyan-400 font-medium">{{createdQuestionsCount}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Total Marks:</span>
                            <span class="text-cyan-400 font-medium">{{getTotalMarks()}}</span>
                        </div>
                        <!-- <div class="flex justify-between items-center">
                            <span class="text-gray-300">Question Types:</span>
                            <span class="text-cyan-400 font-medium">{{getQuestionTypesSummary()}}</span>
                        </div> -->
                    </div>
                </div>

                <!-- Sections Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Sections ({{totalSectionsCount}})</h3>
                    <div ng-repeat="section in savedSections" class="mb-3 last:mb-0 p-3 border border-gray-600 rounded">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-100">{{section.title}}</h4>
                                <p class="text-sm text-gray-400">
                                    {{section.assignedQuestions || 0}}/{{section.question_count}} questions
                                    assigned
                                </p>
                                <!-- <p class="text-sm text-gray-400">
                                    {{section.marks_per_question}} marks per question |
                                    {{section.question_type}}
                                </p> -->
                            </div>
                            <!-- <span class="text-cyan-400 text-sm">Order: {{section.order}}</span> -->
                        </div>
                    </div>
                </div>

                <!-- Settings Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div>
                            <strong class="text-gray-400">Schedule Type:</strong>
                            <span class="text-gray-100 capitalize">{{examData.schedule_type}}</span>
                        </div>
                        <div ng-if="examData.schedule_type === 'scheduled'">
                            <strong class="text-gray-400">Start Date/Time:</strong>
                            <span class="text-gray-100">{{examData.start_time | formatDateTime:'DD MMM YYYY -
                                HH:mm'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Shuffle Questions:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.shuffle_questions ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.shuffle_questions ? 'Yes' : 'No'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Shuffle Options:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.shuffle_options ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.shuffle_options ? 'Yes' : 'No'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Allow Retake:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.allow_retake ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.allow_retake ? 'Yes' : 'No'}}</span>
                        </div>
                        <div ng-if="examData.allow_retake">
                            <strong class="text-gray-400">Retake Attempts:</strong>
                            <span class="text-gray-100">{{examData.max_attempts }}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Show Results:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full text-blue-300" ng-class="examData.show_results_immediately ? 'bg-green-900/50 text-green-300' : 'bg-blue-900/50 text-blue-300'">{{examData.show_results_immediately ? 'Immediately' :
                                'After Exam'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Proctoring:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.enable_proctoring ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.enable_proctoring ? 'Enabled' : 'Disabled'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Full Screen Mode:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.full_screen_mode ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.full_screen_mode ? 'Enabled' : 'Disabled'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Disable Copy/Paste:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.disable_copy_paste ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.disable_copy_paste ? 'Yes' : 'No'}}</span>
                        </div>
                        <div>
                            <strong class="text-gray-400">Disable Right Click:</strong>
                            <span class="font-medium text-sm py-1 px-4 rounded-full" ng-class="examData.disable_right_click ? 'bg-green-900/50 text-green-300' : 'bg-gray-700 text-gray-300'">{{examData.disable_right_click ? 'Yes' : 'No'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex flex-wrap md:flex-row justify-center gap-2 mt-8 pt-6 border-t border-gray-600">
        <button type="button" ng-click="previousStep()" ng-show="currentStep > 1"
            class="bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Previous</span>
        </button>

        <!-- <div class="flex-1"></div> -->

        <button type="button" id="basicInfoSave" ng-click="saveBasicInfo()"
            ng-show="currentStep === 1 && !location.exam" ng-disabled="basicInfoForm.$invalid"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-800 w-full md:w-auto disabled:cursor-not-allowed text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Save Basic Info</span>
        </button>

        <button type="button" id="basicInfoEdit" ng-click="saveBasicInfo()" ng-show="currentStep === 1 && location.exam"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-800 w-full md:w-auto disabled:cursor-not-allowed text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Update Basic Info</span>
        </button>

        <button type="button" id="examSettingsSave" ng-click="saveExamSettings()"
            ng-show="currentStep === 3 && location.exam"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-800 w-full md:w-auto disabled:cursor-not-allowed text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Save Exam Settings</span>
        </button>

        <button type="button" ng-click="nextStep()" ng-show="currentStep < totalSteps"
            class="bg-cyan-600 hover:bg-cyan-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <span>Next</span>
            <i class="fas fa-arrow-right"></i>
        </button>

        <!-- <button type="button" ng-click="createExam()" ng-show="currentStep === totalSteps"
            ng-disabled="creatingExam || createdQuestionsCount === 0"
            class="bg-green-600 hover:bg-green-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
            <i class="fas fa-save" ng-class="{'fa-spin animate-spin': creatingExam}"></i>
            <span>{{creatingExam ? 'Creating Exam...' : 'Create Exam'}}</span>
        </button> -->

        <a href="<?php echo BASE_URL . '/preview/'?>{{ location.exam }}" ng-show="currentStep === 4"
            class="bg-green-600 hover:bg-green-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
            <i class="fas fa-save" ng-class="{'fa-spin animate-spin': creatingExam}"></i>
            <span>Preview Exam</span>
        </a>
    </div>
</div>

<?php $this->end(); ?>