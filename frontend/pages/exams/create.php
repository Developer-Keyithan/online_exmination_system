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
                        <input type="number" id="totalQuestions" ng-model="examData.total_questions" required min="1"
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
            <div class="flex flex-wrap justify-between gap-2 items-center mb-6">
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
                </div>
            </div>

            <!-- Two Column Layout -->
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <!-- Left Column: Questions List & Navigation -->
                <div class="xl:col-span-1">
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Questions ({{savedQuestions.length}})</h3>

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
                                <button ng-if="savedQuestions.length > 8 && showMoreQuestions"
                                    ng-click="toggleMoreQuestions()"
                                    class="w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition-all duration-300">
                                    -{{ savedQuestions.length - 8 }}
                                </button>

                                <!-- 4) MORE BUTTON – ONLY IF NOT EXPANDED -->
                                <button ng-if="savedQuestions.length > 8 && !showMoreQuestions"
                                    ng-click="toggleMoreQuestions()"
                                    class="w-10 h-10 rounded-lg bg-purple-600 hover:bg-purple-700 text-white font-bold transition-all duration-300">
                                    +{{ savedQuestions.length - 8 }}
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
                                        <p class="text-sm text-gray-300 truncate">{{question.question || 'No question
                                            text'}}</p>
                                        <div class="flex justify-between items-center mt-2 text-xs text-gray-400">
                                            <span>{{question.marks || 0}} marks</span>
                                        </div>
                                        <div class="mt-1"
                                            ng-if="question.assignedSections && question.assignedSections.length > 0">
                                            <span class="text-xs text-cyan-400">
                                                Assigned Section{{question.assignedSections.length > 1 ? 's' : ''}}:
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
                            <div ng-if="savedQuestions.length === 0" class="text-center py-8">
                                <i class="fas fa-question-circle text-gray-500 text-3xl mb-2"></i>
                                <p class="text-gray-400">No questions created yet</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sections Panel -->
                    <div class="bg-[#0006] rounded-lg p-4 border border-gray-600 mt-4">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-100">Sections ({{savedSections.length}})</h3>
                        </div>

                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            <div ng-repeat="section in savedSections track by $index"
                                class="p-3 border border-gray-600 rounded-lg bg-[#0008]">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-medium text-gray-100">{{section.title || 'Untitled Section'}}</h4>
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
                                    <div>Questions: {{section.assignedQuestions || 0}}/{{section.question_count}}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Empty State for Sections -->
                        <div ng-if="savedSections.length === 0" class="text-center py-4">
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
                                <span ng-if="currentQuestionIndex !== null">Edit Question {{currentQuestionIndex +
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
                                    ng-disabled="!currentQuestion.isSaved || savedSections.length === 0"
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
                                                    <p class="text-gray-400 mb-2">Drag & drop an image or click to
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
                                    ng-disabled="currentQuestionIndex === null || currentQuestionIndex >= savedQuestions.length - 1"
                                    class="bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto py-2 px-4 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                                    <span>Next</span>
                                    <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Empty State for Question Editor -->
                        <div ng-if="!currentQuestion" class="text-center py-12">
                            <i class="fas fa-question-circle text-gray-500 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-100 mb-2">No Question Selected</h3>
                            <p class="text-gray-400 mb-6">Select a question from the list or create a new one to start
                                editing.</p>
                            <button type="button" ng-click="startNewQuestion()"
                                class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 rounded-lg transition-colors duration-200">
                                {{savedQuestions.length > 0 ? 'Create New Question' : 'Add Question'}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule & Settings Section -->
    <div ng-show="currentStep === 3" class="max-w-4xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Schedule & Settings</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Schedule Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Schedule</h3>
                </div>

                <!-- Start Date & Time -->
                <div class="form-group">
                    <label for="startDateTime" class="form-label">Start Date & Time</label>
                    <input type="datetime-local" id="startDateTime" ng-model="examData.start_time" class="form-input">
                </div>

                <!-- End Date & Time -->
                <div class="form-group">
                    <label for="endDateTime" class="form-label">End Date & Time</label>
                    <input type="datetime-local" id="endDateTime" ng-model="examData.end_time" class="form-input">
                </div>

                <!-- Exam Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Exam Settings</h3>
                </div>

                <!-- Shuffle Questions -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.shuffle_questions"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Shuffle Questions</span>
                    </label>
                </div>

                <!-- Shuffle Options -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.shuffle_options"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Shuffle Options</span>
                    </label>
                </div>

                <!-- Show Results Immediately -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.show_results_immediately"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Show Results Immediately</span>
                    </label>
                </div>

                <!-- Allow Retake -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.allow_retake"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Allow Retake</span>
                    </label>
                </div>

                <!-- Max Attempts -->
                <div class="form-group" ng-if="examData.allow_retake">
                    <label for="maxAttempts" class="form-label">Maximum Attempts</label>
                    <input type="number" id="maxAttempts" ng-model="examData.max_attempts" min="1" class="form-input"
                        placeholder="e.g., 3">
                </div>

                <!-- Security Settings -->
                <div class="md:col-span-2">
                    <h3 class="text-lg font-medium text-gray-100 mb-4">Security Settings</h3>
                </div>

                <!-- Enable Proctoring -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.enable_proctoring"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Enable Proctoring</span>
                    </label>
                </div>

                <!-- Full Screen Mode -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.full_screen_mode"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Force Full Screen Mode</span>
                    </label>
                </div>

                <!-- Disable Copy Paste -->
                <div class="form-group">
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="checkbox" ng-model="examData.disable_copy_paste"
                            class="rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                        <span class="text-gray-300">Disable Copy/Paste</span>
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Review & Create Section -->
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
                            <span class="text-cyan-400 font-medium">{{savedQuestions.length}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Total Marks:</span>
                            <span class="text-cyan-400 font-medium">{{getTotalMarks()}}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-300">Question Types:</span>
                            <span class="text-cyan-400 font-medium">{{getQuestionTypesSummary()}}</span>
                        </div>
                    </div>
                </div>

                <!-- Sections Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Sections ({{savedSections.length}})</h3>
                    <div ng-repeat="section in savedSections" class="mb-3 last:mb-0 p-3 border border-gray-600 rounded">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-100">{{section.title}}</h4>
                                <p class="text-sm text-gray-400">
                                    {{section.assignedQuestions || 0}}/{{section.question_count}} questions assigned
                                </p>
                                <p class="text-sm text-gray-400">
                                    {{section.marks_per_question}} marks per question | {{section.question_type}}
                                </p>
                            </div>
                            <span class="text-cyan-400 text-sm">Order: {{section.order}}</span>
                        </div>
                    </div>
                </div>

                <!-- Settings Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Settings</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div><strong class="text-gray-400">Shuffle Questions:</strong> <span
                                class="text-gray-100">{{examData.shuffle_questions ? 'Yes' : 'No'}}</span></div>
                        <div><strong class="text-gray-400">Allow Retake:</strong> <span
                                class="text-gray-100">{{examData.allow_retake ? 'Yes' : 'No'}}</span></div>
                        <div><strong class="text-gray-400">Show Results:</strong> <span
                                class="text-gray-100">{{examData.show_results_immediately ? 'Immediately' :
                                'Later'}}</span></div>
                        <div><strong class="text-gray-400">Proctoring:</strong> <span
                                class="text-gray-100">{{examData.enable_proctoring ? 'Enabled' : 'Disabled'}}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section Editor Modal -->
    <div ng-show="showSectionModal" id="sectionModal"
        ng-click="closeModalFromOutside($event, 'sectionModal', 'showSectionModal', ['showSecondDescription'])"
        class="fixed inset-0 bg-black bg-opacity-75 backdrop-blur flex items-center justify-center z-[99999999] p-4 transition-all duration-300">
        <div
            class="relative bg-[#fff1] rounded-lg p-6 border border-gray-600 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <i ng-click="showSectionModal = false; showSecondDescription = false"
                class="fas fa-close absolute top-3 right-3 hover:rotate-90 hover:text-red-400 transition-all duration-300 cursor-pointer"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-4">
                {{editingSectionIndex === null ? 'Create New Section' : 'Edit Section'}}
            </h3>

            <div class="space-y-4">
                <form id="section_form" onsubmit="return false" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="hidden" ng-model="examID" name="exam_id" value="{{examID}}">
                    <input type="hidden" ng-model="currentSection.id" name="section_id" value="{{currentSection.id}}">

                    <!-- Section Title -->
                    <div class="form-group">
                        <label class="form-label">Section Title <span class="text-red-700">*</span></label>
                        <input type="text" ng-model="currentSection.title" required class="form-input"
                            name="section_title" placeholder="e.g., Mathematics, Physics, etc.">
                    </div>

                    <!-- Questions Count -->
                    <div class="form-group">
                        <label class="form-label">Number of Questions <span class="text-red-700">*</span></label>
                        <input type="number" ng-model="currentSection.question_count" required min="1"
                            name="section_question_count" class="form-input" placeholder="e.g., 10">
                    </div>

                    <!-- Description -->
                    <div class="form-group md:col-span-2">
                        <label class="form-label">Description</label>
                        <textarea ng-model="currentSection.description" rows="3" class="form-input"
                            name="section_description" placeholder="Section description..."></textarea>
                    </div>

                    <button type="button" ng-click="addSecondDescription()" ng-show="!showSecondDescription"
                        class="group bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 w-auto flex items-center justify-center gap-2 rounded-lg transition-colors duration-200 disabled:opacity-50">
                        <i class="fas fa-plus group-hover:rotate-180 transition-transform duration-300"></i>
                        Add 2nd Description
                    </button>

                    <!-- 2nd Description -->
                    <div class="form-group md:col-span-2" ng-show="showSecondDescription">
                        <label class="form-label">2nd Description</label>
                        <textarea ng-model="currentSection.secondDescription" rows="3" class="form-input"
                            name="section_second_description" placeholder="Section 2nd description..."></textarea>
                    </div>
                </form>

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-600">
                    <button type="button" ng-click="showSectionModal = false; showSecondDescription = false"
                        class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-4 rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="button" ng-click="saveSection()"
                        ng-disabled="!currentSection.title || !currentSection.question_count"
                        class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 disabled:opacity-50">
                        {{editingSectionIndex === null ? 'Create Section' : 'Update Section'}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation Buttons -->
    <div class="flex flex-wrap md:flex-row justify-between gap-2 mt-8 pt-6 border-t border-gray-600">
        <button type="button" ng-click="previousStep()" ng-show="currentStep > 1"
            class="bg-gray-600 hover:bg-gray-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Previous</span>
        </button>

        <div class="flex-1"></div>

        <button type="button" id="basicInfoSubmit" ng-click="saveBasicInfo()"
            ng-show="currentStep === 1 && !location.exam" ng-disabled="basicInfoForm.$invalid"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-800 w-full md:w-auto disabled:cursor-not-allowed text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Save Basic Info</span>
        </button>

        <button type="button" id="basicInfoSubmit" ng-click="saveBasicInfo()"
            ng-show="currentStep === 1 && location.exam"
            class="bg-green-600 hover:bg-green-700 disabled:bg-green-800 w-full md:w-auto disabled:cursor-not-allowed text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <i class="fas fa-save"></i>
            <span>Update Basic Info</span>
        </button>

        <button type="button" ng-click="nextStep()" ng-show="currentStep < totalSteps"
            class="bg-cyan-600 hover:bg-cyan-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2">
            <span>Next</span>
            <i class="fas fa-arrow-right"></i>
        </button>

        <button type="button" ng-click="createExam()" ng-show="currentStep === totalSteps"
            ng-disabled="creatingExam || savedQuestions.length === 0"
            class="bg-green-600 hover:bg-green-700 text-white w-full md:w-auto py-2 px-6 rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
            <i class="fas fa-save" ng-class="{'fa-spin animate-spin': creatingExam}"></i>
            <span>{{creatingExam ? 'Creating Exam...' : 'Create Exam'}}</span>
        </button>
    </div>
</div>

<?php $this->end(); ?>