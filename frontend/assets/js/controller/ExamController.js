app.controller('ExamController', [
    "$scope", "$http", "$timeout", 
    function ($scope, $http, $timeout) {
        
        // Initialize exam data
        $scope.examData = {
            title: '',
            code: '',
            duration: 120,
            total_marks: 100,
            passing_marks: 40,
            instructions: '',
            status: 'draft',
            sections: [],
            start_time: '',
            end_time: '',
            shuffle_questions: false,
            shuffle_options: false,
            show_results_immediately: false,
            allow_retake: false,
            max_attempts: 1,
            enable_proctoring: false,
            full_screen_mode: false,
            disable_copy_paste: false
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
        
        // Section management
        $scope.showAssignModal = false;
        $scope.assignSectionIndex = null;
        $scope.showSectionModal = false;
        $scope.editingSectionIndex = null;
        $scope.currentSection = {};

        // Initialize controller
        $scope.init = function() {
            $scope.startNewQuestion();
            // Add a default section
            $scope.addNewSection();
        };

        // Navigation functions
        $scope.nextStep = function() {
            if ($scope.validateCurrentStep()) {
                $scope.steps[$scope.currentStep - 1].completed = true;
                $scope.steps[$scope.currentStep - 1].active = false;
                $scope.currentStep++;
                $scope.steps[$scope.currentStep - 1].active = true;
            }
        };

        $scope.previousStep = function() {
            $scope.steps[$scope.currentStep - 1].active = false;
            $scope.currentStep--;
            $scope.steps[$scope.currentStep - 1].active = true;
        };

        // Step validation
        $scope.validateCurrentStep = function() {
            switch($scope.currentStep) {
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
                            msg: 'Please create and save at least one question'
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
                    return true;

                case 3:
                    return true;

                default:
                    return true;
            }
        };

        // Question management
        $scope.startNewQuestion = function() {
            // Check if current question has unsaved changes
            if ($scope.currentQuestion && $scope.currentQuestion.text && !$scope.currentQuestion.isSaved) {
                if (!confirm('You have unsaved changes. Do you want to save before creating a new question?')) {
                    return;
                }
                $scope.saveCurrentQuestion();
            }

            $scope.currentQuestion = {
                type: 'multiple_choice',
                text: '',
                image: null,
                options: [
                    { text: '', order: 1 },
                    { text: '', order: 2 },
                    { text: '', order: 3 },
                    { text: '', order: 4 }
                ],
                correct_answer: null,
                model_answer: '',
                difficulty: 'medium',
                marks: 1,
                negative_marks: 0,
                explanation: '',
                isSaved: false,
                assignedSections: []
            };
            $scope.currentQuestionIndex = null;
        };

        $scope.saveCurrentQuestion = function() {
            if (!$scope.currentQuestion.text) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Please enter question text'
                });
                return;
            }

            // Validate multiple choice questions
            if ($scope.currentQuestion.type === 'multiple_choice') {
                const validOptions = $scope.currentQuestion.options.filter(opt => opt.text || opt.image);
                if (validOptions.length < 2) {
                    Toast.fire({
                        type: 'error',
                        title: 'Validation Error!',
                        msg: 'Multiple choice questions must have at least 2 options'
                    });
                    return;
                }
                if ($scope.currentQuestion.correct_answer === null) {
                    Toast.fire({
                        type: 'error',
                        title: 'Validation Error!',
                        msg: 'Please select a correct answer'
                    });
                    return;
                }
            }

            // Validate other question types
            if (['true_false', 'fill_blank'].includes($scope.currentQuestion.type) && !$scope.currentQuestion.correct_answer) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Please provide a correct answer'
                });
                return;
            }

            if ($scope.currentQuestionIndex === null) {
                // New question
                $scope.currentQuestion.isSaved = true;
                $scope.currentQuestion.id = 'q' + Date.now();
                $scope.currentQuestion.createdAt = new Date();
                $scope.savedQuestions.push(angular.copy($scope.currentQuestion));
                $scope.currentQuestionIndex = $scope.savedQuestions.length - 1;
                
                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: 'Question saved successfully'
                });
            } else {
                // Update existing question
                $scope.currentQuestion.isSaved = true;
                $scope.currentQuestion.updatedAt = new Date();
                $scope.savedQuestions[$scope.currentQuestionIndex] = angular.copy($scope.currentQuestion);
                
                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: 'Question updated successfully'
                });
            }
        };

        $scope.loadQuestionForEditing = function(index) {
            // Check if current question has unsaved changes
            if ($scope.currentQuestion && $scope.currentQuestion.text && !$scope.currentQuestion.isSaved) {
                if (!confirm('You have unsaved changes. Do you want to save before switching questions?')) {
                    return;
                }
                $scope.saveCurrentQuestion();
            }

            $scope.currentQuestion = angular.copy($scope.savedQuestions[index]);
            $scope.currentQuestionIndex = index;
        };

        $scope.deleteCurrentQuestion = function() {
            if ($scope.currentQuestionIndex === null) return;
            
            if (confirm('Are you sure you want to delete this question?')) {
                // Remove section assignments
                const question = $scope.savedQuestions[$scope.currentQuestionIndex];
                if (question.assignedSections && question.assignedSections.length > 0) {
                    question.assignedSections.forEach(sectionIndex => {
                        $scope.examData.sections[sectionIndex].assignedQuestions = 
                            ($scope.examData.sections[sectionIndex].assignedQuestions || 0) - 1;
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
        };

        $scope.previousQuestion = function() {
            if ($scope.currentQuestionIndex > 0) {
                $scope.loadQuestionForEditing($scope.currentQuestionIndex - 1);
            }
        };

        $scope.nextQuestion = function() {
            if ($scope.currentQuestionIndex < $scope.savedQuestions.length - 1) {
                $scope.loadQuestionForEditing($scope.currentQuestionIndex + 1);
            }
        };

        // Section management
        $scope.addNewSection = function() {
            $scope.currentSection = {
                title: 'Section ' + ($scope.examData.sections.length + 1),
                description: '',
                order: $scope.examData.sections.length + 1,
                question_count: 10,
                marks_per_question: 1,
                negative_marking: 0,
                question_type: 'multiple_choice',
                time_limit: null,
                assignedQuestions: 0
            };
            $scope.editingSectionIndex = null;
            $scope.showSectionModal = true;
        };

        $scope.editSection = function(index) {
            $scope.currentSection = angular.copy($scope.examData.sections[index]);
            $scope.editingSectionIndex = index;
            $scope.showSectionModal = true;
        };

        $scope.saveSection = function() {
            if (!$scope.currentSection.title || !$scope.currentSection.order) {
                Toast.fire({
                    type: 'error',
                    title: 'Validation Error!',
                    msg: 'Please fill all required fields'
                });
                return;
            }

            if ($scope.editingSectionIndex === null) {
                // New section
                $scope.examData.sections.push(angular.copy($scope.currentSection));
                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: 'Section created successfully'
                });
            } else {
                // Update existing section
                $scope.examData.sections[$scope.editingSectionIndex] = angular.copy($scope.currentSection);
                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: 'Section updated successfully'
                });
            }

            $scope.showSectionModal = false;
            $scope.updateSectionQuestionCounts();
        };

        $scope.removeSection = function(index) {
            if ($scope.examData.sections.length > 1) {
                if (confirm('Are you sure you want to delete this section? Assigned questions will be unassigned.')) {
                    // Remove section assignments from questions
                    $scope.savedQuestions.forEach(question => {
                        if (question.assignedSections) {
                            const sectionIndexInArray = question.assignedSections.indexOf(index);
                            if (sectionIndexInArray > -1) {
                                question.assignedSections.splice(sectionIndexInArray, 1);
                            }
                            // Update indices for sections after the removed one
                            question.assignedSections = question.assignedSections.map(secIndex => {
                                return secIndex > index ? secIndex - 1 : secIndex;
                            });
                        }
                    });
                    
                    $scope.examData.sections.splice(index, 1);
                    $scope.updateSectionQuestionCounts();
                    
                    Toast.fire({
                        type: 'success',
                        title: 'Success!',
                        msg: 'Section deleted successfully'
                    });
                }
            } else {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'Cannot delete the last section'
                });
            }
        };

        $scope.updateSectionQuestionCounts = function() {
            $scope.examData.sections.forEach((section, index) => {
                section.assignedQuestions = $scope.savedQuestions.filter(q => 
                    q.assignedSections && q.assignedSections.includes(index)
                ).length;
            });
        };

        // Question assignment to sections
        $scope.assignToSection = function() {
            if (!$scope.currentQuestion.isSaved) {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'Please save the question before assigning to a section'
                });
                return;
            }
            
            if ($scope.examData.sections.length === 0) {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'Please create at least one section first'
                });
                return;
            }
            
            $scope.showAssignModal = true;
            $scope.assignSectionIndex = null;
        };

        $scope.confirmAssignToSection = function() {
            const sectionIndex = $scope.assignSectionIndex;
            const questionIndex = $scope.currentQuestionIndex;
            const question = $scope.savedQuestions[questionIndex];
            const section = $scope.examData.sections[sectionIndex];

            // Check if section has reached its question limit
            if (section.assignedQuestions >= section.question_count) {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: 'This section has reached its question limit (' + section.question_count + ' questions)'
                });
                return;
            }

            if (!question.assignedSections) {
                question.assignedSections = [];
            }

            if (!question.assignedSections.includes(sectionIndex)) {
                question.assignedSections.push(sectionIndex);
                $scope.savedQuestions[questionIndex] = angular.copy(question);
                $scope.updateSectionQuestionCounts();

                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: 'Question assigned to section successfully'
                });
            } else {
                Toast.fire({
                    type: 'info',
                    title: 'Info',
                    msg: 'Question is already assigned to this section'
                });
            }

            $scope.showAssignModal = false;
        };

        $scope.getAssignedSectionNames = function(question) {
            if (!question.assignedSections || question.assignedSections.length === 0) {
                return 'None';
            }
            return question.assignedSections.map(index => {
                return $scope.examData.sections[index]?.title || 'Section ' + (index + 1);
            }).join(', ');
        };

        // Options management
        $scope.addOption = function(question) {
            if (!question.options) {
                question.options = [];
            }
            question.options.push({ text: '', order: question.options.length + 1 });
        };

        $scope.addImageOption = function(question) {
            if (!question.options) {
                question.options = [];
            }
            question.options.push({ image: null, order: question.options.length + 1 });
        };

        $scope.removeOption = function(question, optionIndex) {
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

        $scope.reorderOptions = function(question) {
            question.options.forEach((option, index) => {
                option.order = index + 1;
            });
        };

        $scope.onQuestionTypeChange = function() {
            // Reset options when question type changes
            if ($scope.currentQuestion.type === 'multiple_choice') {
                if (!$scope.currentQuestion.options || $scope.currentQuestion.options.length === 0) {
                    $scope.currentQuestion.options = [
                        { text: '', order: 1 },
                        { text: '', order: 2 },
                        { text: '', order: 3 },
                        { text: '', order: 4 }
                    ];
                }
                $scope.currentQuestion.correct_answer = null;
            } else {
                $scope.currentQuestion.options = [];
                $scope.currentQuestion.correct_answer = null;
            }
        };

        // Image handling
        $scope.onQuestionImageSelect = function(files) {
            if (files && files.length) {
                const file = files[0];
                if (file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $scope.$apply(function() {
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

        $scope.uploadOptionImage = function(option) {
            // Create a file input element
            const fileInput = document.createElement('input');
            fileInput.type = 'file';
            fileInput.accept = 'image/*';
            fileInput.onchange = function(e) {
                const file = e.target.files[0];
                if (file && file.type.match('image.*')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $scope.$apply(function() {
                            option.image = e.target.result;
                            option.text = ''; // Clear text if image is set
                        });
                    };
                    reader.readAsDataURL(file);
                }
            };
            fileInput.click();
        };

        $scope.removeOptionImage = function(option) {
            option.image = null;
        };

        // Calculations and summaries
        $scope.getTotalMarks = function() {
            return $scope.savedQuestions.reduce((total, question) => {
                return total + (question.marks || 0);
            }, 0);
        };

        $scope.getQuestionTypesSummary = function() {
            const typeCounts = {};
            $scope.savedQuestions.forEach(question => {
                typeCounts[question.type] = (typeCounts[question.type] || 0) + 1;
            });
            return Object.keys(typeCounts).map(type => {
                return `${typeCounts[type]} ${type.replace('_', ' ')}`;
            }).join(', ');
        };

        // Create exam
        $scope.createExam = function() {
            $scope.creatingExam = true;

            // Prepare data for API
            const submitData = {
                ...$scope.examData,
                questions: $scope.savedQuestions,
                total_questions: $scope.savedQuestions.length,
                total_marks: $scope.getTotalMarks()
            };

            $http({
                url: 'API/exams',
                method: 'POST',
                data: submitData
            }).then(
                function(response) {
                    $scope.creatingExam = false;
                    
                    if (response.data && response.data.success) {
                        Toast.fire({
                            type: 'success',
                            title: 'Success!',
                            msg: 'Exam created successfully'
                        });

                        // Redirect to exam management after 2 seconds
                        $timeout(() => {
                            window.location.href = 'exam_management';
                        }, 2000);
                    } else {
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: response.data.message || 'Failed to create exam'
                        });
                    }
                },
                function(error) {
                    $scope.creatingExam = false;
                    const errorMsg = error.data?.message || 'Failed to create exam';
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: errorMsg
                    });
                    console.error('API Error:', error);
                }
            );
        };

        // Initialize the controller
        $scope.init();
    }
]);