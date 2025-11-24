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
        <a href="exam_management"
            class="bg-gray-600 hover:bg-gray-700 mt-4 md:mt-0 w-fit text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Exams</span>
        </a>
    </div>

    <!-- Progress Steps -->
    <div class="mb-8">
        <div class="flex items-center justify-between max-w-4xl mx-auto">
            <div class="flex items-center" ng-repeat="step in steps" ng-class="{'flex-1': !$last}">
                <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 transition-colors duration-200"
                    ng-class="step.active ? 'bg-cyan-600 border-cyan-600 text-white' : 
                              step.completed ? 'bg-green-500 border-green-500 text-white' : 
                              'border-gray-500 text-gray-500'">
                    <i class="fas" ng-class="step.completed ? 'fa-check' : step.icon"></i>
                </div>
                <div class="ml-3" ng-if="!$last">
                    <div class="text-sm font-medium" ng-class="step.active ? 'text-cyan-400' : 'text-gray-400'">
                        {{step.title}}
                    </div>
                </div>
            </div>
            <div class="flex-1 h-1 bg-gray-600 mx-4" ng-if="!$last"></div>
        </div>
    </div>

    <!-- Exam Basic Information Section -->
    <div ng-show="currentStep === 1" class="max-w-4xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <h2 class="text-xl font-semibold text-gray-100 mb-6">Exam Basic Information</h2>

            <form name="basicInfoForm" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Exam Title -->
                    <div class="form-group md:col-span-2">
                        <label for="examTitle" class="form-label">Exam Title <span class="text-red-700">*</span></label>
                        <input type="text" id="examTitle" ng-model="examData.title" required class="form-input"
                            placeholder="Enter exam title">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examTitle.$error.required">
                            Exam title is required
                        </div>
                    </div>

                    <!-- Exam Code -->
                    <div class="form-group">
                        <label for="examCode" class="form-label">Exam Code <span class="text-red-700">*</span></label>
                        <input type="text" id="examCode" ng-model="examData.code" required class="form-input"
                            placeholder="e.g., MATH-2024-FINAL">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examCode.$error.required">
                            Exam code is required
                        </div>
                    </div>

                    <!-- Exam Type -->
                    <!-- <div class="form-group">
                        <label for="examType" class="form-label">Exam Type <span class="text-red-700">*</span></label>
                        <select id="examType" ng-model="examData.type" required class="form-input">
                            <option value="">Select Exam Type</option>
                            <option value="multiple_choice">Multiple Choice</option>
                            <option value="descriptive">Descriptive</option>
                            <option value="mixed">Mixed</option>
                            <option value="practical">Practical</option>
                        </select>
                        <div class="error-message" ng-show="basicInfoForm.$submitted && basicInfoForm.examType.$error.required">
                            Exam type is required
                        </div>
                    </div> -->

                    <!-- Category -->
                    <!-- <div class="form-group">
                        <label for="examCategory" class="form-label">Category <span class="text-red-700">*</span></label>
                        <select id="examCategory" ng-model="examData.category_id" required class="form-input">
                            <option value="">Select Category</option>
                            <option ng-repeat="category in categories" value="{{category.id}}">
                                {{category.name}}
                            </option>
                        </select>
                        <div class="error-message" ng-show="basicInfoForm.$submitted && basicInfoForm.examCategory.$error.required">
                            Category is required
                        </div>
                    </div> -->

                    <!-- Duration -->
                    <div class="form-group">
                        <label for="examDuration" class="form-label">Duration (minutes) <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="examDuration" ng-model="examData.duration" required min="1"
                            class="form-input" placeholder="e.g., 120">
                        <div class="error-message"
                            ng-show="basicInfoForm.$submitted && basicInfoForm.examDuration.$error.required">
                            Duration is required
                        </div>
                    </div>

                    <!-- Total Marks -->
                    <div class="form-group">
                        <label for="totalMarks" class="form-label">Total Marks <span
                                class="text-red-700">*</span></label>
                        <input type="number" id="totalMarks" ng-model="examData.total_marks" required min="1"
                            class="form-input" placeholder="e.g., 100">
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
                            max="{{examData.total_marks}}" class="form-input" placeholder="e.g., 40">
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
                            placeholder="Enter exam instructions for candidates..."></textarea>
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label class="form-label">Status</label>

                        <div class="flex space-x-4">

                            <!-- Draft -->
                            <label>
                                <input type="radio" name="examStatus" value="draft" ng-model="formData.status"
                                    class="hidden">

                                <div ng-class="formData.status=='draft' 
                                    ? 'px-4 py-2 bg-blue-600 text-white rounded cursor-pointer' 
                                    : 'px-4 py-2 bg-transparent border rounded cursor-pointer'">
                                    Draft
                                </div>
                            </label>

                            <!-- Published -->
                            <label>
                                <input type="radio" name="examStatus" value="published" ng-model="formData.status"
                                    class="hidden">

                                <div ng-class="formData.status=='published' 
                                    ? 'px-4 py-2 bg-blue-600 text-white rounded cursor-pointer' 
                                    : 'px-4 py-2 bg-transparent border rounded cursor-pointer'">
                                    Published
                                </div>
                            </label>

                            <!-- Archived -->
                            <label>
                                <input type="radio" name="examStatus" value="scheduled" ng-model="formData.status"
                                    class="hidden">

                                <div ng-class="formData.status=='scheduled' 
                                    ? 'px-4 py-2 bg-blue-600 text-white rounded cursor-pointer' 
                                    : 'px-4 py-2 bg-transparent border rounded cursor-pointer'">
                                    Archived
                                </div>
                            </label>

                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    <!-- Exam Sections Section -->
    <div ng-show="currentStep === 2" class="max-w-6xl mx-auto">
        <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-gray-100">Exam Sections</h2>
                <button type="button" ng-click="addNewSection()"
                    class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-4 rounded-lg transition-colors duration-200 flex items-center space-x-2">
                    <i class="fas fa-plus"></i>
                    <span>Add Section</span>
                </button>
            </div>

            <!-- Sections List -->
            <div class="space-y-4">
                <div ng-repeat="section in examData.sections track by $index"
                    class="border border-gray-600 rounded-lg p-4 bg-[#0006]">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg font-medium text-gray-100">Section {{$index + 1}}</h3>
                        <button type="button" ng-click="removeSection($index)"
                            class="text-red-400 hover:text-red-300 transition-colors"
                            ng-disabled="examData.sections.length <= 1">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Section Title -->
                        <div class="form-group md:col-span-2">
                            <label class="form-label">Section Title <span class="text-red-700">*</span></label>
                            <input type="text" ng-model="section.title" required class="form-input"
                                placeholder="e.g., Mathematics, Physics, etc.">
                        </div>

                        <!-- Section Order -->
                        <div class="form-group">
                            <label class="form-label">Order <span class="text-red-700">*</span></label>
                            <input type="number" ng-model="section.order" required min="1" class="form-input"
                                placeholder="Sequence">
                        </div>

                        <!-- Description -->
                        <div class="form-group md:col-span-3">
                            <label class="form-label">Description</label>
                            <textarea ng-model="section.description" rows="2" class="form-input"
                                placeholder="Section description..."></textarea>
                        </div>

                        <!-- Questions Count -->
                        <div class="form-group">
                            <label class="form-label">Number of Questions <span class="text-red-700">*</span></label>
                            <input type="number" ng-model="section.question_count" required min="1" class="form-input"
                                placeholder="e.g., 10">
                        </div>

                        <!-- Marks per Question -->
                        <div class="form-group">
                            <label class="form-label">Marks per Question <span class="text-red-700">*</span></label>
                            <input type="number" ng-model="section.marks_per_question" required min="0.5" step="0.5"
                                class="form-input" placeholder="e.g., 1">
                        </div>

                        <!-- Negative Marking -->
                        <div class="form-group">
                            <label class="form-label">Negative Marking</label>
                            <input type="number" ng-model="section.negative_marking" min="0" step="0.25"
                                class="form-input" placeholder="e.g., 0.25">
                        </div>

                        <!-- Question Type -->
                        <div class="form-group">
                            <label class="form-label">Question Type <span class="text-red-700">*</span></label>
                            <select ng-model="section.question_type" required class="form-input">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="descriptive">Descriptive</option>
                                <option value="fill_blank">Fill in the Blank</option>
                            </select>
                        </div>

                        <!-- Time Limit (Optional) -->
                        <div class="form-group">
                            <label class="form-label">Time Limit (minutes)</label>
                            <input type="number" ng-model="section.time_limit" min="1" class="form-input"
                                placeholder="Optional">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Summary -->
            <div class="mt-6 p-4 bg-[#0006] rounded-lg border border-gray-600">
                <h4 class="text-lg font-medium text-gray-100 mb-3">Section Summary</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-400">Total Sections:</span>
                        <span class="text-cyan-400 ml-2">{{examData.sections.length}}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Total Questions:</span>
                        <span class="text-cyan-400 ml-2">{{getTotalQuestions()}}</span>
                    </div>
                    <div>
                        <span class="text-gray-400">Total Section Marks:</span>
                        <span class="text-cyan-400 ml-2">{{getTotalSectionMarks()}}</span>
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
                        <div><strong class="text-gray-400">Type:</strong> <span
                                class="text-gray-100">{{examData.type}}</span></div>
                        <div><strong class="text-gray-400">Duration:</strong> <span
                                class="text-gray-100">{{examData.duration}} minutes</span></div>
                        <div><strong class="text-gray-400">Total Marks:</strong> <span
                                class="text-gray-100">{{examData.total_marks}}</span></div>
                        <div><strong class="text-gray-400">Passing Marks:</strong> <span
                                class="text-gray-100">{{examData.passing_marks}}</span></div>
                    </div>
                </div>

                <!-- Sections Summary -->
                <div class="bg-[#0006] rounded-lg p-4">
                    <h3 class="text-lg font-medium text-cyan-400 mb-3">Sections ({{examData.sections.length}})</h3>
                    <div ng-repeat="section in examData.sections"
                        class="mb-3 last:mb-0 p-3 border border-gray-600 rounded">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-medium text-gray-100">{{section.title}}</h4>
                                <p class="text-sm text-gray-400">{{section.question_count}} questions ×
                                    {{section.marks_per_question}} marks = {{section.question_count <span
                                        class="text-red-700">*</span> section.marks_per_question}} marks</p>
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

    <!-- Navigation Buttons -->
    <div class="flex justify-between mt-8 pt-6 border-t border-gray-600">
        <button type="button" ng-click="previousStep()" ng-show="currentStep > 1"
            class="bg-gray-600 hover:bg-gray-700 text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-arrow-left"></i>
            <span>Previous</span>
        </button>

        <div class="flex-1"></div>

        <button type="button" ng-click="nextStep()" ng-show="currentStep < 4"
            class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center space-x-2">
            <span>Next</span>
            <i class="fas fa-arrow-right"></i>
        </button>

        <button type="button" ng-click="createExam()" ng-show="currentStep === 4" ng-disabled="creatingExam"
            class="bg-green-600 hover:bg-green-700 text-white py-2 px-6 rounded-lg transition-colors duration-200 flex items-center space-x-2 disabled:opacity-50">
            <i class="fas fa-save" ng-class="{'fa-spin animate-spin': creatingExam}"></i>
            <span>{{creatingExam ? 'Creating Exam...' : 'Create Exam'}}</span>
        </button>
    </div>
</div>

<?php $this->end(); ?>