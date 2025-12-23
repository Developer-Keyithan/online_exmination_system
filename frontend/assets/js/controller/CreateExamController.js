app.controller('CreateExamController', [
    "$scope", "$http", "$compile", "$timeout", "window", "assignToSectionModalController", "unassignFromSectionModalController", "sectionEditorModalController",
    function ($scope, $http, $compile, $timeout, window, assignToSectionModalController, unassignFromSectionModalController, sectionEditorModalController) {

        // Initialize exam data
        $scope.examData = {
            title: '',
            id: '',
            code: '',
            duration: 120,
            total_marks: 100,
            passing_marks: 40,
            instructions: '',
            status: 'draft',
            totalQuestions: 20,
            sections: [],
            start_time: '',
            shuffle_questions: false,
            shuffle_options: false,
            show_results_immediately: false,
            allow_retake: false,
            max_attempts: 1,
            enable_proctoring: false,
            full_screen_mode: false,
            disable_copy_paste: false,
            disable_right_click: false,
            schedule_type: 'scheduled',
            isSettingsDone: false
        };

        // Steps configuration
        $scope.steps = [
            { number: 1, title: 'Basic Info', icon: 'fa-info-circle', active: true, completed: false },
            { number: 2, title: 'Questions', icon: 'fa-question-circle', active: false, completed: false },
            { number: 3, title: 'Settings', icon: 'fa-cog', active: false, completed: false },
            { number: 4, title: 'Review', icon: 'fa-check-circle', active: false, completed: false }
        ];

        $scope.currentStep = 1;
        $scope.totalSteps = $scope.steps.length;
        $scope.creatingExam = false;

        // Question management
        $scope.savedQuestions = [];
        $scope.currentQuestion = null;
        $scope.currentQuestionIndex = null;
        $scope.showMoreQuestions = false;

        // Section management
        $scope.showAssignModal = false;
        $scope.assignSectionIndex = null;
        $scope.showSectionModal = false;
        $scope.editingSectionIndex = null;
        $scope.savedSections = [];
        $scope.currentSection = {};
        $scope.location = {};
        $scope.showSecondDescription = false;
        $scope.showRemoveSectionModal = false;
        $scope.showUnasignSectionModal = false;
        $scope.isAllQuestionsAreCreated = false;
        $scope.isAllQuestionsAreSaved = false;
        $scope.isAllSectionsAreCompleted = false;
        $scope.isAllQuestionsAndSectionsAreCompleted = false;
        // $scope.location.exam = getParameterByName('exam');
        $scope.location.exam = getIdFromUrl();

        // Initialize controller
        $scope.init = function () {
            $scope.handleExamCreation();
        };

        $scope.handleExamCreation = async function () {
            const exam = $scope.location.exam
            if (exam && exam !== undefined) {
                // $scope.creatingExam = false;
                await $http({
                    url: window.baseUrl + '/API/exam/' + exam,
                    method: 'GET'
                }).then(async function (response) {
                    if (response.data.status === 'success') {
                        if (response.data.exam) {
                            $scope.examData.code = await response.data.exam.code;
                            $scope.examData.duration = await response.data.exam.duration;
                            $scope.examData.id = await response.data.exam.id;
                            $scope.examData.instructions = await response.data.exam.instructions;
                            $scope.examData.passing_marks = await response.data.exam.passing_marks;
                            $scope.examData.status = await response.data.exam.status;
                            $scope.examData.title = await response.data.exam.title;
                            $scope.examData.total_marks = await response.data.exam.total_marks;
                            $scope.examData.total_questions = await response.data.exam.total_questions;
                            $scope.examID = await response.data.exam.id;

                            $scope.currentStep = 2;
                            $scope.steps[0].completed = true;
                            $scope.steps[0].active = false;
                            $scope.steps[1].active = true;

                            if (response.data.questions) {
                                $scope.savedQuestions = await response.data.questions;
                            }

                            if (response.data.sections) {
                                $scope.savedSections = await response.data.sections;
                                $scope.examData.sections = await response.data.sections;
                            }

                            if (response.data.exam_settings) {
                                const settings = await response.data.exam_settings;
                                $scope.examData.setting_id = settings.id;
                                $scope.examData.schedule_type = settings.schedule_type;
                                $scope.examData.start_time = settings.start_time ? new Date(settings.start_time) : null;
                                $scope.examData.original_start_time = settings.start_time ? new Date(settings.start_time) : null;
                                $scope.examData.shuffle_questions = settings.shuffle_questions;
                                $scope.examData.shuffle_options = settings.shuffle_options;
                                $scope.examData.show_results_immediately = settings.immediate_results;
                                $scope.examData.allow_retake = settings.retake;
                                $scope.examData.max_attempts = settings.max_attempts ? settings.max_attempts : 1;
                                $scope.examData.enable_proctoring = settings.enable_proctoring;
                                $scope.examData.full_screen_mode = settings.full_screen_mode;
                                $scope.examData.disable_copy_paste = settings.disable_copy_paste;
                                $scope.examData.disable_right_click = settings.disable_right_click;
                                $scope.examData.isSettingsDone = settings.isDone;
                            }

                            await $scope.updateBaseDatas();
                            if ($scope.isAllQuestionsAndSectionsAreCompleted) {
                                $scope.examData = $scope.examData
                                $scope.nextStep();
                            }

                            if ($scope.examData.isSettingsDone) {
                                $scope.examData = $scope.examData
                                $scope.nextStep();
                            }
                        }
                    } else if (getPath() === '/exam/edit/' + getIdFromUrl()) {
                        window.location.href = window.baseUrl + '/exam/create'
                    }
                }, function (error) {
                    const errorMsg = error.data?.message || 'Failed to fetch exam data';
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: errorMsg
                    });
                    console.error('API Error:', error);
                });
            }

        }

        $scope.toggleMoreQuestions = function () {
            $scope.showMoreQuestions = !$scope.showMoreQuestions;
        };

        $scope.closePopover = function () {
            Toast.popover({ type: 'close' })
        }

        // Navigation functions
        $scope.nextStep = function () {
            if ($scope.validateCurrentStep()) {
                $scope.steps[$scope.currentStep - 1].completed = true;
                $scope.steps[$scope.currentStep - 1].active = false;
                $scope.currentStep++;
                $scope.steps[$scope.currentStep - 1].active = true;
            }
        };

        $scope.previousStep = function () {
            $scope.steps[$scope.currentStep - 1].active = false;
            $scope.currentStep--;
            $scope.steps[$scope.currentStep - 1].active = true;
        };

        // Step validation
        $scope.validateCurrentStep = function () {
            switch ($scope.currentStep) {
                case 1:
                    $scope.basicInfoForm.$submitted = true;
                    if ($scope.basicInfoForm.$invalid) {
                        Toast.fire({
                            type: 'error',
                            title: 'Validation Error!',
                            msg: 'Please fill all required fields in Basic Information'
                        });
                        return false;
                    }
                    return true;

                case 2:

                    if ($scope.savedQuestions.length === 0) {
                        Toast.fire({
                            type: 'error',
                            title: 'Validation Error!',
                            msg: 'Please create and save all question'
                        });
                        return false;
                    }

                    // Check if all questions are saved
                    const unsavedQuestions = $scope.savedQuestions.filter(q => !q.isSaved);
                    if (unsavedQuestions.length > 0) {
                        Toast.fire({
                            type: 'error',
                            title: 'Validation Error!',
                            msg: 'Please save all questions before proceeding'
                        });
                        return false;
                    }

                    const totalCreatedQuestions = $scope.savedQuestions.length;
                    const totalQuestionsRequired = $scope.examData.total_questions;
                    if (totalCreatedQuestions < totalQuestionsRequired) {
                        const remaining = totalQuestionsRequired - totalCreatedQuestions;
                        Toast.fire({
                            type: 'error',
                            title: 'Validation Error!',
                            msg: `Please create and save all questions. ${remaining} question${remaining > 1 ? 's' : ''} remaining.`
                        });
                        return false;
                    }

                    for (let counter = 0; counter < $scope.savedSections.length; counter++) {
                        const section = $scope.savedSections[counter];
                        const assignedCount = section.assignedQuestions

                        if (section.question_count !== assignedCount) {
                            Toast.fire({
                                type: 'error',
                                title: 'Validation Error!',
                                msg: `Section "${section.title}" has ${assignedCount === 0 ? 'no questions assigned,' : `${assignedCount} assigned questions`} , but expected ${section.question_count}.`
                            });
                            return false;
                        }
                    }

                    $scope.updateBaseDatas();
                    return true;

                case 3:
                    if ($scope.examData.isSettingsDone) {
                        return true;
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: 'Validation Error!',
                            msg: 'Please save your exam settings changes.'
                        })
                        return false;
                    }

                default:
                    return true;
            }
        };

        $scope.saveBasicInfo = async function () {
            const formData = $('#basicInfoForm').serialize();

            if ($scope.isPastStartTime()) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Since the exam has already started, you cannot change the exam basic information.'
                })
                return;
            }

            if ($scope.isExamComplete()) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'The exam has already completed. You cannot change the basic information now.'
                })
                return;
            }

            $http({
                url: window.baseUrl + '/API/exams/basic_info/' + ($scope.location.exam ? $scope.location.exam : 'save'),
                method: 'POST',
                data: formData
            }).then(function (response) {
                if (response.data.status === 'success') {
                    Toast.fire({
                        type: 'success',
                        title: 'Success!',
                        msg: 'Exam basic info ' + ($scope.location.exam ? 'updated' : 'saved') + ' successfully'
                    });
                    setTimeout(() => {
                        window.location.href = window.baseUrl + '/exam/edit/' + response.data.exam.id;
                    }, 500);
                } else {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: response.data.message || 'Failed to  ' + ($scope.location.exam ? 'update' : 'save') + '  exam basic info'
                    });
                    $scope.validateCurrentStep()
                }
            }, function (error) {
                const errorMsg = error.data?.message || 'Failed to  ' + ($scope.location.exam ? 'update' : 'save') + ' exam basic info';
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: errorMsg
                });
                $scope.validateCurrentStep()
                console.error('API Error:', error);
            })
        }

        $scope.startNewQuestion = function () {

            const totalCreatedQuestions = $scope.savedQuestions.length;
            const totalQuestionsRequired = $scope.examData.total_questions;
            const totalSavedQuestions = $scope.savedQuestions.filter(q => q.isSaved).length;

            if (totalCreatedQuestions >= totalQuestionsRequired) {
                Toast.popover({
                    type: 'confirm',
                    title: 'All Questions Created!',
                    titleColor: '#65deff',
                    content: `
                        <i class="fa-solid fa-circle-info" style="font-size: 3rem; color: #0e7490"></i><br><br>
                        <strong>You have created all questions.</strong><br><br>
                        <ul style="text-align:left; margin-left: 1rem;">
                            <li>Total questions required: ${totalQuestionsRequired}</li>
                            <li>Total questions saved: ${totalSavedQuestions}</li>
                            <li>⚠️ If you have any unsaved questions, please save them before moving to the next part.</li>
                            <li>After saving, you can proceed to the next section of the exam setup.</li>
                        </ul>
                        <p style="margin-top:0.5rem; color:#555;">Click "Save & Continue" to save all unsaved questions and move forward, or "Cancel" to stay here.</p>
                    `,
                    contentColor: '#fff',
                    // backgroundColor: '#fff3',
                    // closeButtonColor: '#dc2626',
                    options: {
                        confirm: {
                            text: 'Save & Continue',
                            background: '#0e7490',
                            onConfirm: async function () {
                                const response = await $scope.saveUnsavedQuestions();
                                if (response) {
                                    await $scope.updateBaseDatas();
                                    setTimeout(() => {
                                        $scope.nextStep();
                                    }, 500);
                                } else {
                                    $scope.$apply();
                                }
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            background: '#dc2626',
                            onCancel: function () {
                                $scope.closePopover();
                            }
                        }
                    }
                });
                return;
            }

            // Check if current question has unsaved changes
            if ($scope.currentQuestion && $scope.currentQuestion.question && !$scope.currentQuestion.isSaved) {

                Toast.popover({
                    type: 'confirm',
                    title: 'Unsaved Changes',
                    titleColor: '#fb0',
                    content: '<i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i><br><br>You have unsaved changes. Do you want to save before creating a new question?',
                    contentColor: '#fb0',
                    backgroundColor: '#fff3',
                    closeButtonColor: '#dc2626',
                    options: {
                        confirm: {
                            text: 'Yes, Save',
                            background: '#0e7490',
                            onConfirm: async function () {
                                const response = await $scope.saveCurrentQuestion();
                                if (response) {
                                    if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                                        $scope.currentQuestion = null;
                                    } else {
                                        $scope.currentQuestion = {
                                            question: '',
                                            image: null,
                                            options: [
                                                { text: '', image: '', order: 1, op: 'A' },
                                                { text: '', image: '', order: 2, op: 'B' },
                                                { text: '', image: '', order: 3, op: 'C' },
                                                { text: '', image: '', order: 4, op: 'D' }
                                            ],
                                            answer: null,
                                            marks: 1,
                                            isSaved: false,
                                            assignedSections: [],
                                            grid: 1
                                        };
                                        $scope.currentQuestionIndex = null;
                                    }
                                    $scope.currentQuestionIndex = null;
                                }
                            }
                        },
                        cancel: {
                            text: 'No, Cancel',
                            background: '#dc2626',
                            onCancel: function () {
                                $scope.storeUnsavedQuestions();
                                if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                                    $scope.currentQuestion = null;
                                } else {
                                    $scope.currentQuestion = {
                                        question: '',
                                        image: null,
                                        options: [
                                            { text: '', image: '', order: 1, op: 'A' },
                                            { text: '', image: '', order: 2, op: 'B' },
                                            { text: '', image: '', order: 3, op: 'C' },
                                            { text: '', image: '', order: 4, op: 'D' }
                                        ],
                                        answer: null,
                                        marks: 1,
                                        isSaved: false,
                                        assignedSections: [],
                                        grid: 1
                                    };
                                    $scope.currentQuestionIndex = null;
                                }
                                $scope.currentQuestionIndex = null;
                                $scope.$apply();
                                $scope.closePopover();
                            }
                        }
                    }
                })
                return;
            }

            $scope.currentQuestion = {
                question: '',
                image: null,
                options: [
                    { text: '', image: '', order: 1, op: 'A' },
                    { text: '', image: '', order: 2, op: 'B' },
                    { text: '', image: '', order: 3, op: 'C' },
                    { text: '', image: '', order: 4, op: 'D' }
                ],
                correct_answer: null,
                model_answer: '',
                marks: 1,
                isSaved: false,
                assignedSections: [],
                grid: 1
            };
            $scope.currentQuestionIndex = null;

        };

        $scope.saveCurrentQuestion = async function () {
            if ($scope.isPastStartTime()) {
                
                return;
            }

            if ($scope.isExamComplete()) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'The exam has already completed. You cannot save the question now.'
                })
                return;
            }

            const formId = 'questionForm' + ($scope.currentQuestion.id || 'New');
            const formElement = document.getElementById(formId);
            if (!formElement) return;

            // Validation
            if (!$scope.currentQuestion.question) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please enter question text' });
                return false;
            }

            const validOptions = $scope.currentQuestion.options.filter(opt => opt.text || opt.image);
            if (validOptions.length < 4) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'All options are required' });
                return false;
            }

            if ($scope.currentQuestion.answer === null || $scope.currentQuestion.answer === undefined) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please select a correct answer' });
                return false;
            }

            // Use FormData instead of serialize
            const formData = new FormData(formElement);

            // Add option images
            $scope.currentQuestion.options.forEach(option => {
                if (option.file) {
                    formData.append(option.op + 'img', option.file); // use name attribute
                }
            });

            // Add main question image if exists
            if ($scope.currentQuestion.imageFile) {
                formData.append('questionImage', $scope.currentQuestion.imageFile);
            }

            // API URL
            const apiUrl = $scope.currentQuestion.id ? window.baseUrl + '/API/questions/edit_question/' + $scope.currentQuestion.id : window.baseUrl + '/API/questions/add_question';
            try {
                const response = await $http.post(apiUrl, formData, {
                    transformRequest: angular.identity,
                    headers: { 'Content-Type': undefined } // important for file upload
                });

                if (response.data.status === 'success') {
                    Toast.fire({ type: 'success', title: 'Success!', msg: response.data.msg || 'Question ' + ($scope.currentQuestion.id ? 'updated' : 'saved') + ' successfully' });

                    const updated = response.data.question;

                    // Update currentQuestion
                    $scope.currentQuestion = updated;

                    // Handle new question ID
                    if (updated.id) {
                        $scope.currentQuestion.id = updated.id;
                        $scope.currentQuestion.isSaved = true;
                    }

                    // If editing existing → replace in savedQuestions
                    if ($scope.currentQuestionIndex !== null && $scope.currentQuestionIndex !== undefined) {
                        $scope.savedQuestions[$scope.currentQuestionIndex] = angular.copy(updated);
                        $scope.savedQuestions[$scope.currentQuestionIndex].updatedAt = new Date();
                    } else {
                        updated.createdAt = new Date();
                        $scope.savedQuestions.push(updated);
                        $scope.currentQuestionIndex = $scope.savedQuestions.length - 1;
                    }

                    if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                        $scope.currentQuestion = null
                    }

                    $scope.updateBaseDatas();
                    $scope.updateSectionQuestionCounts();
                    $scope.$apply();
                    return true;
                } else {
                    Toast.fire({ type: 'error', title: 'Error!', msg: response.data.msg });
                    return false;
                }
            } catch (error) {
                Toast.fire({ type: 'error', title: 'Error!', msg: 'Something went wrong' });
                console.error(error);
                return false;
            };
        };

        $scope.storeUnsavedQuestions = function () {
            if (!$scope.currentQuestion.question) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please enter question text' });
                return;
            }

            $scope.savedQuestions = $scope.savedQuestions || [];

            // Check if question already exists
            const existingIndex = $scope.savedQuestions.findIndex(q => q.question === $scope.currentQuestion.question);

            if (existingIndex === -1) {
                if ($scope.currentQuestion.tempId) {
                    const currentQuestionIndex = $scope.savedQuestions.findIndex(q => q.tempId === $scope.currentQuestion.tempId);
                    const existingQuestion = $scope.savedQuestions[currentQuestionIndex];
                    existingQuestion.question = $scope.currentQuestion.question || $scope.currentQuestion.text;

                    // Update only changed options
                    $scope.currentQuestion.options.forEach(currOpt => {
                        const existingOpt = existingQuestion.options.find(opt => opt.op === currOpt.op);
                        if (existingOpt) {
                            if (existingOpt.text !== currOpt.text) existingOpt.text = currOpt.text;
                            if (existingOpt.image !== currOpt.image) existingOpt.image = currOpt.image;
                        } else {
                            existingQuestion.options.push(currOpt);
                        }
                    });

                    // Update other properties
                    existingQuestion.answer = $scope.currentQuestion.answer;
                    existingQuestion.marks = $scope.currentQuestion.marks;
                    existingQuestion.grid = $scope.currentQuestion.grid;
                    existingQuestion.assignedSections = angular.copy($scope.currentQuestion.assignedSections);
                    $scope.updateBaseDatas();
                    if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                        $scope.currentQuestion = null;
                    }
                } else {
                    // Question not found → push new
                    $scope.currentQuestion.tempId = Math.floor(100000 + Math.random() * 900000).toString();
                    $scope.savedQuestions.push(angular.copy($scope.currentQuestion));
                    $scope.updateBaseDatas();
                    if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                        $scope.currentQuestion = null;
                    }
                }
            } else {
                setTimeout(() => {
                    Toast.popover({
                        type: 'confirm',
                        title: 'This Question Already Exists',
                        titleColor: '#65deff',
                        content: `
                            <i class="fa-solid fa-circle-info" style="font-size: 3rem; color: #0e7490"></i><br><br>
                            <p>This question already exists. Do you want to update it with new changes?</p>
                        `,
                        contentColor: '#fff',
                        // backgroundColor: '#fff3',
                        // closeButtonColor: '#dc2626',
                        options: {
                            confirm: {
                                text: 'Update',
                                background: '#0e7490',
                                onConfirm: function () {
                                    const existingQuestion = $scope.savedQuestions[existingIndex];

                                    // Update only changed options
                                    $scope.currentQuestion.options.forEach(currOpt => {
                                        const existingOpt = existingQuestion.options.find(opt => opt.op === currOpt.op);
                                        if (existingOpt) {
                                            if (existingOpt.text !== currOpt.text) existingOpt.text = currOpt.text;
                                            if (existingOpt.image !== currOpt.image) existingOpt.image = currOpt.image;
                                        } else {
                                            existingQuestion.options.push(currOpt);
                                        }
                                    });

                                    // Update other properties
                                    existingQuestion.answer = $scope.currentQuestion.answer;
                                    existingQuestion.marks = $scope.currentQuestion.marks;
                                    existingQuestion.assignedSections = angular.copy($scope.currentQuestion.assignedSections);
                                    $scope.updateBaseDatas();
                                    if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                                        $scope.currentQuestion = null;
                                    }

                                    $scope.currentQuestion = $scope.toLoadQuestion;
                                    $scope.currentQuestionIndex = $scope.toLoadQuestionIndex;
                                    $scope.$apply();
                                }
                            },
                            cancel: {
                                text: 'Cancel',
                                background: '#dc2626',
                                onCancel: function () {
                                    $scope.closePopover();
                                    $scope.currentQuestion = $scope.toLoadQuestion;
                                    $scope.currentQuestionIndex = $scope.toLoadQuestionIndex;
                                    $scope.$apply();
                                }
                            }
                        }
                    });
                }, 500);
            }
        };

        $scope.loadQuestionForEditing = function (index) {
            // Check if current question has unsaved changes
            if ($scope.currentQuestion && $scope.currentQuestion.question && !$scope.currentQuestion.isSaved) {
                Toast.popover({
                    type: 'confirm',
                    title: 'Unsaved Changes',
                    titleColor: '#fb0',
                    content: '<i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i><br><br>You have unsaved changes. Do you want to save before switching questions?',
                    contentColor: '#fb0',
                    backgroundColor: '#fff3',
                    closeButtonColor: '#dc2626',
                    options: {
                        confirm: {
                            text: 'Yes, Save!',
                            background: '#0e7490',
                            onConfirm: async function () {
                                const response = await $scope.saveCurrentQuestion();
                                if (response) {
                                    $scope.currentQuestion = angular.copy($scope.savedQuestions[index]);
                                    $scope.currentQuestionIndex = index;
                                    $scope.$apply();
                                }
                            }
                        },
                        cancel: {
                            text: 'No, Cancel',
                            background: '#dc2626',
                            onCancel: async function () {
                                // Load the new question without saving
                                await $scope.storeUnsavedQuestions();
                                $scope.closePopover()
                                $scope.toLoadQuestion = angular.copy($scope.savedQuestions[index]);
                                $scope.toLoadQuestionIndex = index;
                                $scope.$apply();
                            }
                        }
                    }
                })
                return;
            }

            $scope.currentQuestion = angular.copy($scope.savedQuestions[index]);
            $scope.currentQuestionIndex = index;
        };

        $scope.deleteCurrentQuestion = function () {
            if ($scope.currentQuestionIndex === null) return;

            Toast.popover({
                type: 'confirm',
                title: 'Delete Question',
                titleColor: '#fb0',
                content: '<i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i><br><br>Are you sure you want to delete this question? This action cannot be undone.',
                contentColor: '#fb0',
                backgroundColor: '#fff3',
                closeButtonColor: '#dc2626',
                options: {
                    confirm: {
                        text: 'Yes, Delete!',
                        background: '#dc2626',
                        onConfirm: async function () {
                            $http({
                                url: window.baseUrl + '/API/questions/delete_question/' + $scope.currentQuestion.id,
                                method: 'DELETE'
                            }).then(function (response) {
                                if (response.data.status === 'success') {
                                    const question = $scope.savedQuestions[$scope.currentQuestionIndex];
                                    if (question.assignedSections && question.assignedSections.length > 0) {
                                        question.assignedSections.forEach(sectionIndex => {
                                            $scope.savedSections[sectionIndex].assignedQuestions =
                                                ($scope.savedSections[sectionIndex].assignedQuestions || 0) - 1;
                                        });
                                    }

                                    $scope.savedQuestions.splice($scope.currentQuestionIndex, 1);
                                    $scope.startNewQuestion();
                                    Toast.fire({
                                        type: 'success',
                                        title: 'Success!',
                                        msg: 'Question deleted successfully'
                                    });
                                }
                            })
                        }
                    },
                    cancel: {
                        text: 'No, Cancel',
                        background: '#0e7490',
                        onCancel: function () {
                            $scope.closePopover();
                        }
                    }
                }
            })
        };

        $scope.removeCurrentQuestionFromExam = function () {

            if (!$scope.currentQuestion) {
                Toast.fire({ type: 'error', title: 'Error!', msg: 'No question selected!' });
                return;
            }

            if (!$scope.currentQuestion.isSaved || !$scope.currentQuestion.id) {
                Toast.popover({
                    type: 'confirm',
                    title: 'Remove Unsaved Question',
                    titleColor: '#fb0',
                    content: `
                        <i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i>
                        <br><br>

                        This question is not saved yet. If you continue, all unsaved changes will be lost.
                        <br><br>

                        What would you like to do?
                        <br><br>

                        <b>Save & Remove:</b> Save this question to the database and remove it from this exam.
                        <br>
                        <b>Remove Only:</b> Remove this question from this exam without saving.
                        <br>
                        <b>Cancel:</b> Go back without making any changes.
                    `,
                    contentColor: '#fff',
                    backgroundColor: '#fff3',
                    // closeButtonColor: '#dc2626',
                    // position: 'top-center',
                    options: {
                        confirm: {
                            text: 'Save & Remove',
                            background: '#0e7490',
                            onConfirm: async function () {
                                const response = await $scope.saveCurrentQuestion();
                                if (response) {
                                    const questionId = $scope.currentQuestion.id;
                                    const examId = $scope.location.exam;
                                    try {
                                        const response = await $http.patch(
                                            window.baseUrl + '/API/questions/remove/' + questionId + '?exam_id=' + examId,
                                        );

                                        if (response.data.status === 'success') {

                                            // Remove from savedQuestions
                                            const index = $scope.savedQuestions.findIndex(q => q.id === questionId);
                                            if (index !== -1) {
                                                $scope.savedQuestions.splice(index, 1);
                                            }

                                            // Reset current question
                                            $scope.currentQuestion = null;
                                            $scope.currentQuestionIndex = null;
                                            $scope.updateBaseDatas();
                                            $scope.$apply();

                                            Toast.fire({ type: 'success', title: 'Success', msg: 'Question removed from exam' });

                                        } else {
                                            Toast.fire({ type: 'error', title: 'Error', msg: response.data.msg });
                                        }

                                    } catch (error) {
                                        console.error(error);
                                        Toast.fire({ type: 'error', title: 'Error', msg: 'Something went wrong!' });
                                    }
                                }
                            }
                        },
                        buttons: [{
                            text: 'Remove Only',
                            background: '#0e7490',
                            position: 'middle',
                            onClick: function () {
                                $scope.savedQuestions = $scope.savedQuestions.filter(q => q.tempId !== $scope.currentQuestion.tempId);
                                $scope.currentQuestion = null;
                                $scope.currentQuestionIndex = null;
                                $scope.updateBaseDatas();
                                $scope.$apply();
                                Toast.popover({ type: 'close' })
                            }
                        }],
                        cancel: {
                            text: 'Cancel',
                            background: '#dc2626',
                            onCancel: function () {
                                $scope.closePopover()
                            }
                        }
                    }
                });
                return;
            }

            const questionId = $scope.currentQuestion.id;
            const examId = $scope.location.exam;

            Toast.popover({
                type: 'confirm',
                title: 'Remove Question',
                titleColor: '#fb0',
                content: '<i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i><br><br>Are you sure you want to remove this question from the exam?',
                contentColor: '#fb0',
                backgroundColor: '#fff3',
                closeButtonColor: '#dc2626',
                // position: 'top-center',
                options: {
                    confirm: {
                        text: 'Yes, Remove',
                        background: '#dc2626',
                        onConfirm: async function () {
                            try {
                                const response = await $http.patch(
                                    window.baseUrl + '/API/questions/remove/' + questionId + '?exam_id=' + examId,
                                );

                                if (response.data.status === 'success') {

                                    // Remove from savedQuestions
                                    const index = $scope.savedQuestions.findIndex(q => q.id === questionId);
                                    if (index !== -1) {
                                        $scope.savedQuestions.splice(index, 1);
                                    }

                                    // Reset current question
                                    $scope.currentQuestion = null;
                                    $scope.currentQuestionIndex = null;
                                    $scope.updateBaseDatas();
                                    $scope.$apply();

                                    Toast.fire({ type: 'success', title: 'Success', msg: 'Question removed from exam' });

                                } else {
                                    Toast.fire({ type: 'error', title: 'Error', msg: response.data.msg });
                                }

                            } catch (error) {
                                console.error(error);
                                Toast.fire({ type: 'error', title: 'Error', msg: 'Something went wrong!' });
                            }
                        }
                    },
                    cancel: {
                        text: 'No, Cancel',
                        background: '#0e7490',
                        onCancel: function () {
                            $scope.closePopover()
                        }
                    }
                }
            });
        };

        $scope.previousQuestion = function () {
            if ($scope.currentQuestionIndex > 0) {
                $scope.loadQuestionForEditing($scope.currentQuestionIndex - 1);
            }
        };

        $scope.nextQuestion = function () {
            if ($scope.currentQuestionIndex < $scope.savedQuestions.length - 1) {
                $scope.loadQuestionForEditing($scope.currentQuestionIndex + 1);
            }
        };

        // Section management
        $scope.addNewSection = function () {
            if (!$scope.savedSections) {
                $scope.savedSections = [];
            }
            $scope.currentSection = {
                title: 'Section ' + ($scope.savedSections.length + 1),
                description: '',
                secondDescription: '',
                question_count: 2,
                assignedQuestions: 0,
                id: '',
                examID: $scope.examID
            };
            $scope.editingSectionIndex = null;
            $scope.showSectionModal = true;
            $scope.openSectionEditor();
        };

        $scope.editSection = function (index) {
            $scope.currentSection = angular.copy($scope.savedSections[index]);
            $scope.editingSectionIndex = index;
            $scope.showSectionModal = true;
            if ($scope.currentSection.secondDescription) {
                $scope.showSecondDescription = true;
            }
            $scope.openSectionEditor();
        };

        $scope.addSecondDescription = function () {
            $scope.showSecondDescription = true;
        };

        $scope.openSectionEditor = function () {
            if (!$scope.assigningModalCtrl) {
                $scope.sectionEditorModalCtrl = sectionEditorModalController($scope);
            }
            Toast.popover({
                type: 'apiContent',
                title: 'Assigning question "' + ($scope.currentQuestionIndex + 1) + '" to a section',
                apiConfig: {
                    endpoint: 'section_editor',
                    method: 'GET'
                },
                backgroundColor: '#0009',
                position: 'center',
                size: 'xl'
            }).then(popoverInstance => {
                $timeout(() => {
                    const modal = document.getElementById('section_editor_modal');
                    if (modal) {
                        $compile(modal)($scope);
                        $(modal).find('.select2').select2()
                        $scope.$apply();
                    } else {
                        console.error('#section_editor_modal not found');
                    }
                }, 150);
            });
        }

        $scope.removeSection = function (sectionID) {
            // if ($scope.savedSections.length <= 1) {
            //     Toast.fire({
            //         type: 'error',
            //         title: 'Error!',
            //         msg: 'Cannot delete the last section'
            //     });
            //     return;
            // }

            Toast.popover({
                type: 'confirm',
                title: 'Remove Section',
                titleColor: '#fb0',
                content: '<i class="fa-regular fa-circle-question" style="font-size: 4rem; color: #fb0"></i><br><br>Are you sure you want to delete this section? Assigned questions will be unassigned.',
                contentColor: '#fb0',
                backgroundColor: '#fff3',
                closeButtonColor: '#dc2626',
                options: {
                    confirm: {
                        text: 'Yes, Remove this section!',
                        background: '#dc2626',
                        onConfirm: function () {
                            $http({
                                url: window.baseUrl + '/API/sections/delete/' + sectionID,
                                method: 'DELETE'
                            }).then(function (response) {
                                if (response.data.status === 'success') {

                                    // Remove section from assignedSections in questions
                                    $scope.savedQuestions.forEach(question => {
                                        if (Array.isArray(question.assignedSections)) {
                                            question.assignedSections = question.assignedSections.filter(id => id !== sectionID);
                                        }
                                    });

                                    // Remove section from savedSections
                                    $scope.savedSections = $scope.savedSections.filter(s => s.id !== sectionID);

                                    $scope.updateSectionQuestionCounts();
                                    $scope.updateBaseDatas();
                                    Toast.fire({
                                        type: 'success',
                                        title: 'Success!',
                                        msg: 'Section deleted successfully'
                                    });
                                }
                            });
                        }
                    },
                    cancel: {
                        text: 'No, Cancel',
                        background: '#0e7490',
                        onCancel: function () {
                            $scope.closePopover();
                        }
                    }
                }
            });
            $scope.updateBaseDatas();
        };

        $scope.updateSectionQuestionCounts = function () {
            $scope.savedSections.forEach(section => {
                section.assignedQuestions = $scope.savedQuestions.filter(q =>
                    q.assignedSections && q.assignedSections.includes(section.id)
                ).length;
            });
        };

        $scope.updateBaseDatas = () => {
            $scope.isAllQuestionsAreSaved = false;
            $scope.isAllQuestionsAreCreated = false;
            $scope.isAllSectionsAreCompleted = false;
            $scope.isAllQuestionsAndSectionsAreCompleted = false;

            const neededQuestionsCount = $scope.examData.total_questions;
            const createdQuestionsCount = $scope.savedQuestions.length;
            const savedQuestionsCount = $scope.savedQuestions.filter(q => q.isSaved).length;
            const unsavedQuestionsCount = $scope.savedQuestions.filter(q => !q.isSaved).length;

            if (unsavedQuestionsCount === 0 && savedQuestionsCount === createdQuestionsCount) {
                $scope.isAllQuestionsAreSaved = true;
            }

            if (neededQuestionsCount === createdQuestionsCount) {
                $scope.isAllQuestionsAreCreated = true;
            }

            let allSectionsCompleted = true;
            for (let counter = 0; counter < $scope.savedSections.length; counter++) {
                const section = $scope.savedSections[counter];

                if (section.assignedQuestions !== section.question_count) {
                    allSectionsCompleted = false;
                    break;
                }
            }
            $scope.isAllSectionsAreCompleted = allSectionsCompleted;

            if ($scope.isAllQuestionsAreCreated && $scope.isAllQuestionsAreSaved && $scope.isAllSectionsAreCompleted) {
                $scope.isAllQuestionsAndSectionsAreCompleted = true;
            }

            $scope.savedQuestionsCount = savedQuestionsCount;
            $scope.unsavedQuestionsCount = unsavedQuestionsCount;
            $scope.createdQuestionsCount = createdQuestionsCount;
            $scope.neededQuestionsCount = neededQuestionsCount;
            $scope.totalSectionsCount = $scope.savedSections.length;
            $scope.completedSectionsCount = $scope.savedSections.filter(s => s.assignedQuestions === s.question_count).length;
        };

        // Open Assign Modal
        $scope.assignToSection = function () {
            if (!$scope.currentQuestion.isSaved) {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'Please save the question before assigning to a section'
                });
                return;
            }

            if ($scope.savedSections.length === 0) {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'Please create at least one section first'
                });
                return;
            }

            if (!$scope.assigningModalCtrl) {
                $scope.assigningModalCtrl = assignToSectionModalController($scope);
            }
            $scope.assigningModalCtrl.init($scope.currentQuestion.id)
            // $scope.showAssignModal = true;
            Toast.popover({
                type: 'apiContent',
                title: 'Assigning question "' + ($scope.currentQuestionIndex + 1) + '" to a section',
                apiConfig: {
                    endpoint: 'assign_to_section',
                    method: 'GET'
                },
                backgroundColor: '#0009',
                position: 'center'
            }).then(popoverInstance => {
                $timeout(() => {
                    const modal = document.getElementById('assign_to_section_modal');
                    if (modal) {
                        $compile(modal)($scope);
                        $(modal).find('.select2').select2()
                        $scope.$apply();
                    } else {
                        console.error('#assign_to_section_modal not found');
                    }
                }, 150);
            });
        };

        // Open Unssign Modal
        $scope.openUnassignSectionModal = function (questionID) {

            if (!$scope.unassigningModalCtrl) {
                $scope.unassigningModalCtrl = unassignFromSectionModalController($scope);
            }
            $scope.unassigningModalCtrl.init(questionID)

            // $scope.showUnasignSectionModal = true;
            Toast.popover({
                type: 'apiContent',
                title: 'Unassigning question "' + ($scope.currentQuestionIndex + 1) + '" from a section',
                apiConfig: {
                    endpoint: 'unassign_section',
                    method: 'GET'
                },
                // backgroundColor: '#fff1',
                position: 'center'
            }).then(popoverInstance => {
                $timeout(() => {
                    const modal = document.getElementById('remove_section_modal');
                    if (modal) {
                        $compile(modal)($scope);
                        $(modal).find('.select2').select2()
                        $scope.$apply();
                    } else {
                        console.error('#remove_section_modal not found');
                    }
                }, 150);
            });
        };

        $scope.getAssignedSectionNames = function (question) {
            if (!question.assignedSections || question.assignedSections.length === 0) {
                return 'None';
            }

            return question.assignedSections
                .map(sectionId => {
                    const section = $scope.savedSections.find(s => s.id == sectionId);
                    return section ? section.title : 'Unknown';
                })
                .join(', ');
        };

        $scope.saveUnsavedQuestions = async function () {
            if ($scope.currentQuestion) {
                const currentQuestionIndex = $scope.savedQuestions.findIndex(q => q.tempId === $scope.currentQuestion.tempId);
                const existingQuestion = $scope.savedQuestions[currentQuestionIndex];
                existingQuestion.question = $scope.currentQuestion.question || $scope.currentQuestion.text;

                // Update only changed options
                $scope.currentQuestion.options.forEach(currOpt => {
                    const existingOpt = existingQuestion.options.find(opt => opt.op === currOpt.op);
                    if (existingOpt) {
                        if (existingOpt.text !== currOpt.text) existingOpt.text = currOpt.text;
                        if (existingOpt.image !== currOpt.image) existingOpt.image = currOpt.image;
                    } else {
                        existingQuestion.options.push(currOpt);
                    }
                });

                // Update other properties
                existingQuestion.answer = $scope.currentQuestion.answer;
                existingQuestion.marks = $scope.currentQuestion.marks;
                existingQuestion.assignedSections = angular.copy($scope.currentQuestion.assignedSections);
                $scope.updateBaseDatas();
                if ($scope.savedQuestions.length === $scope.examData.total_questions) {
                    $scope.currentQuestion = null;
                }
            }

            const unsavedQuestions = $scope.savedQuestions.filter(q => !q.isSaved);

            for (let index = 0; index < unsavedQuestions.length; index++) {
                const q = unsavedQuestions[index];
                const originalIndex = $scope.savedQuestions.findIndex(oq => oq.id === q.id);

                if (!q.question) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please enter the text for question ' + (originalIndex + 1) });
                    return false;
                }

                const validOptions = q.options.filter(opt => opt.text || opt.image);
                if (validOptions.length < 4) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'All options are required for question ' + (originalIndex + 1) });
                    return false;
                }

                if (q.answer === null || q.answer === undefined) {
                    Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please select a correct answer for question ' + (originalIndex + 1) });
                    return false;
                }


                const formData = new FormData();
                formData.append('exam_id', $scope.location.exam);
                formData.append('question', q.question);
                formData.append('answer', q.answer);
                formData.append('marks', q.marks);
                q.options.forEach(opt => {
                    formData.append(opt.op, opt.text || '');
                    formData.append('img' + opt.op, opt.image || '');
                });

                try {
                    const response = await $http({
                        method: 'POST',
                        url: window.baseUrl + '/API/questions/add_question',
                        data: formData,
                        headers: { 'Content-Type': undefined }
                    });

                    const savedQuestionIndex = $scope.savedQuestions.findIndex(i => i.tempId === q.tempId);

                    if (response.data.status === 'success') {
                        const savedQuestion = response.data.question;
                        if (savedQuestionIndex !== -1) {
                            angular.copy(savedQuestion, $scope.savedQuestions[savedQuestionIndex]);
                            $scope.savedQuestions[savedQuestionIndex].isSaved = true;
                        }
                    } else {
                        if (savedQuestionIndex !== -1) {
                            $scope.savedQuestions[savedQuestionIndex].isSaved = false;
                        }
                    }

                } catch (error) {
                    Toast.fire({ type: 'error', title: 'Error!', msg: 'Network or server error' });
                    return false;
                }
            }

            const isAllSaved = $scope.savedQuestions.every(q => q.isSaved);
            if (isAllSaved) {
                Toast.fire({ type: 'success', title: 'Success!', msg: 'All questions saved successfully' });
                return true;
            } else {
                Toast.fire({ type: 'error', title: 'Error!', msg: 'Failed to save all questions' });
                return false;
            }
        };

        $scope.addOption = function (question) {
            if (!question.options) {
                question.options = [];
            }
            // Get the next letter based on current length
            let nextChar = String.fromCharCode(65 + question.options.length); // 65 = 'A'

            question.options.push({
                text: '',
                order: question.options.length + 1,
                op: nextChar
            });
        };

        $scope.removeOption = function (question, optionIndex) {
            if (question.options.length > 2) {
                question.options.splice(optionIndex, 1);
                // Update correct answer if it was the removed option
                if (question.correct_answer === optionIndex) {
                    question.correct_answer = null;
                } else if (question.correct_answer > optionIndex) {
                    question.correct_answer--;
                }
                $scope.reorderOptions(question);
            }
        };

        $scope.reorderOptions = function (question) {
            question.options.forEach((option, index) => {
                option.order = index + 1;
            });
        };

        $scope.onQuestionTypeChange = function () {
            // Reset options when question type changes
            if (!$scope.currentQuestion.options || $scope.currentQuestion.options.length === 0) {
                $scope.currentQuestion.options = [
                    { text: '', image: '', order: 1, op: 'A' },
                    { text: '', image: '', order: 2, op: 'B' },
                    { text: '', image: '', order: 3, op: 'C' },
                    { text: '', image: '', order: 4, op: 'D' }
                ];
            }
            $scope.currentQuestion.correct_answer = null;
        };

        // Image handling
        $scope.onQuestionImageSelect = function (files) {
            if (files && files.length) {
                const file = files[0];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $scope.$apply(function () {
                            $scope.currentQuestion.image = e.target.result;
                        });
                    };
                    reader.readAsDataURL(file);
                } else {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: 'Please select a valid image file'
                    });
                }
            }
        };

        $scope.uploadOptionImage = function (option) {
            // Create a file input element
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.name = option.op + 'img'
            fileInput.onchange = function (e) {
                const file = e.target.files[0];
                if (file && file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        $scope.$apply(function () {
                            option.file = file;                        // ✅ actual file for API
                            option.image = URL.createObjectURL(file);  // preview
                            option.text = '';
                        });
                    };
                    reader.readAsDataURL(file);
                }
            };
            fileInput.click();
        };

        $scope.removeOptionImage = function (option) {
            option.image = null;
        };

        // Save exam settings
        $scope.saveExamSettings = async function () {
            if ($scope.isPastStartTime() && $scope.settings) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Since the exam has already started, you cannot save the exam settings.'
                })
                return false;
            }

            if ($scope.isExamComplete() && $scope.settings) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Since the exam has already completed, you cannot save the exam settings.'
                })
                return false;
            }

            if ($scope.examData.schedule_type === 'scheduled' && !$scope.examData.start_time) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Please select a start time.'
                });
                return false;
            }

            if (
                $scope.examData.schedule_type === 'scheduled' && $scope.examData.start_time && new Date($scope.examData.start_time) < new Date()) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Start time cannot be earlier than the current time.'
                });
                return false;
            }

            if ($scope.examData.allow_retake && !$scope.examData.max_attempts) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Please enter the number of times the exam can be retaken.'
                });
                return false;
            }

            if ($scope.examData.allow_retake && $scope.examData.max_attempts < 1) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'The number of times the exam can be retaken must be greater than 0.'
                });
                return false;
            }

            const formData = $('#exam_settings_form').serialize();
            const restEndpoint = $scope.examData.setting_id ? '/' + $scope.location.exam : '';
            const response = await $http({
                url: `${window.baseUrl}/API/exams/settings${restEndpoint}`,
                method: 'POST',
                data: formData
            })

            if (response.data.status === 'success') {
                Toast.fire({ type: 'success', title: 'Success!', msg: `Exam settings ${$scope.examData.setting_id ? 'updated' : 'save'} successfully.` });

                const settings = response.data.exam_settings;
                $scope.examData.setting_id = settings.id;
                $scope.examData.schedule_type = settings.schedule_type;
                $scope.examData.start_time = settings.start_time ? new Date(settings.start_time) : null;
                $scope.examData.shuffle_questions = settings.shuffle_questions;
                $scope.examData.shuffle_options = settings.shuffle_options;
                $scope.examData.show_results_immediately = settings.immediate_results;
                $scope.examData.allow_retake = settings.retake;
                $scope.examData.max_attempts = settings.max_attempts;
                $scope.examData.enable_proctoring = settings.enable_proctoring;
                $scope.examData.full_screen_mode = settings.full_screen_mode;
                $scope.examData.disable_copy_paste = settings.disable_copy_paste;
                $scope.examData.isSettingsDone = settings.isDone;
                return true;
            }

            Toast.fire({ type: 'error', title: 'Error!', msg: `Failed to  ${$scope.examData.setting_id ? 'update' : 'save'}  exam settings.` });
            return false;
        };

        // Calculations and summaries
        $scope.getTotalMarks = function () {
            return $scope.savedQuestions.reduce((total, question) => {
                return total + (question.marks || 0);
            }, 0);
        };

        $scope.getQuestionTypesSummary = function () {
            const typeCounts = {};
            $scope.savedQuestions.forEach(question => {
                typeCounts[question.type] = (typeCounts[question.type] || 0) + 1;
            });
            return Object.keys(typeCounts).map(type => {
                return `${typeCounts[type]} ${type.replace('_', ' ')}`;
            }).join(', ');
        };

        // Close when clicking outside modal
        $scope.closeModalFromOutside = function (event, id, variable, relatedValiales = []) {
            if (event.target.id === id) {
                $scope[variable] = false;
                for (let i = 0; i < relatedValiales.length; i++) {
                    $scope[relatedValiales[i]] = false;
                }
            }
        };

        // Check is start time passed
        $scope.isPastStartTime = function () {
            if (!$scope.examData.schedule_type) {
                return false;
            }
            if ($scope.examData.schedule_type === 'anytime') {
                return true;
            }
            const startTime = $scope.examData.original_start_time;
            const now = new Date();
            const examStart = new Date(startTime);
            return now >= examStart;
        }

        // Check is exam complete
        $scope.isExamComplete = () => {
            if (!$scope.examData.schedule_type) {
                return false;
            }
            if ($scope.examData.schedule_type === 'anytime') {
                return false;
            }

            const now = new Date();
            const examStart = new Date($scope.examData.original_start_time);
            const examDurationMinutes = parseInt($scope.examData.duration);

            // Calculate exam end time
            const examEnd = new Date(examStart.getTime() + examDurationMinutes * 60000);

            // Return true if current time is after exam end
            return now >= examEnd;
        }

        // Initialize the controller
        $scope.init();

    }
]);