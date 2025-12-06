<div id="question_editor_modal" class="xl:col-span-2">
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
                <button type="button" ng-click="assignToSection()" title="Assign this question to a section"
                    ng-disabled="!currentQuestion.isSaved || totalSectionsCount === 0"
                    class="bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 w-full md:w-auto rounded-lg transition-colors duration-200 flex items-center justify-center space-x-2 disabled:opacity-50">
                    <i class="fas fa-layer-group"></i>
                    <span>Assign to Section</span>
                </button>
                <button type="button" ng-click="removeCurrentQuestionFromExam()"
                    title="Remove this question from this exam" ng-disabled="currentQuestionIndex === null"
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
                        <label class="form-label">Question Text <span class="text-red-700">*</span></label>
                        <textarea ng-model="currentQuestion.question" required rows="5" class="form-input"
                            name="question" placeholder="Enter your question here..."></textarea>
                    </div>

                    <!-- Question Image -->
                    <div class="w-full md:w-1/3 md:pl-2">
                        <div class="form-group">
                            <label class="form-label">Question Image (Optional)</label>
                            <div class="border-2 border-dashed border-gray-600 rounded-lg p-4 text-center">
                                <div ng-if="!currentQuestion.image">
                                    <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                    <p class="text-gray-400 mb-2">Drag & drop an image or click
                                        to
                                        browse
                                    </p>
                                    <input type="file" id="questionImage" accept="image/*" class="hidden"
                                        name="questionImage" ng-file-select="onQuestionImageSelect($files)">
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
                            <input type="radio" id="option{{$index}}" name="answer" ng-model="currentQuestion.answer"
                                ng-value="option.op" class="text-cyan-500 cursor-pointer">
                            <p>{{ option.op }}&#x29;</p>
                        </label>

                        <div class="flex-1">
                            <input type="text" ng-model="option.text" class="form-input mb-2" name="{{option.op}}"
                                placeholder="Option {{ option.op }} text">

                            <div id="{{ option.op }}ImgContainer" ng-if="option.image" class="relative inline-block">
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
                        <input type="number" ng-model="currentQuestion.marks" required min="0.5" name="marks" step="0.5"
                            class="form-input" placeholder="Marks">
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
            <button type="button" ng-click="unsavedQuestionsCount > 0 ? saveUnsavedQuestions() : nextStep()"
                class="bg-cyan-600 hover:bg-cyan-700 text-white py-2 px-6 mt-6 rounded-lg transition-colors duration-200">
                {{ unsavedQuestionsCount > 0 ? 'Save unsaved questions & Move next part' : 'Move next
                part' }}
            </button>
        </div>
    </div>
</div>