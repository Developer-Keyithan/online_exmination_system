app.controller('ExamPreviewController', [
    "$scope", "$http", "$compile", "$timeout", "window",
    function ($scope, $http, $compile, $timeout, window,) {
        $scope.location = $scope.location || {};
        $scope.location.exam = window.getIdFromUrl();
        $scope.dropdownOpen = false;
        $scope.questionsDisplayMode = 'all';
        $scope.editingQuestionId = null;
        $scope.organaizedSections = [];
        $scope.previewDisplayMode = 'all'; // 'all' or 'sections'
        $scope.activePreviewSection = null;
        $scope.previewQuestionsPerPage = 10;
        $scope.currentPreviewQuestionPage = 0;
        $scope.reviewSections = [];
        $scope.originalAllPreviewSections = null;
        $scope.originalSectionQuestions = {};
        $scope.cachedPreviewSections = [];
        $scope.cachedPage = null;
        $scope.currentQuestion = null;
        $scope.currentQuestionIndex = null;

        // Initialize arrays to prevent undefined errors
        $scope.allQuestions = [];
        $scope.sections = [];
        $scope.originalSections = [];
        $scope.displayQuestions = [];
        $scope.previewDisplayQuestions = [];
        $scope.previewDisplaySections = [];
        $scope.currentPreviewPageSections = [];
        $scope.cachedPreviewSections = [];


        // Initialize function
        $scope.init = async function () {
            await $scope.loadExamData();
            $scope.isExamFullySetup();
            $scope.updateStepCompletion();
        };

        $scope.loadExamData = async () => {
            try {
                const response = await $http({
                    url: `${window.baseUrl}/API/exam/data/${$scope.location.exam}`,
                    method: 'GET'
                });

                if (response.data.status === 'success') {
                    const examInfo = response.data.exam_info;
                    const examSettings = response.data.settings;
                    const sections = response.data.sections;
                    const questions = response.data.questions;

                    $scope.examData = {
                        title: examInfo.title,
                        code: examInfo.code,
                        duration: examInfo.duration,
                        total_marks: examInfo.total_marks,
                        passing_marks: examInfo.passing_marks,
                        instructions: formatInstructions(examInfo.instructions),
                        status: examInfo.status,
                        published_at: examInfo.published_at ? new Date(examInfo.published_at).toISOString() : null,
                        schedule_type: examSettings.schedule_type,
                        start_time: examSettings.start_time,
                        end_time: (function () {
                            var start = new Date(examSettings.start_time);
                            return new Date(start.getTime() + parseInt(examInfo.duration) * 60000).toISOString();
                        })(),
                        shuffle_questions: examSettings.shuffle_questions,
                        shuffle_options: examSettings.shuffle_options,
                        show_results_immediately: examSettings.show_results_immediately,
                        enable_proctoring: examSettings.enable_proctoring,
                        full_screen_mode: examSettings.full_screen_mode,
                        disable_copy_paste: examSettings.disable_copy_paste,
                        disable_right_click: examSettings.disable_right_click,
                        allow_retake: examSettings.allow_retake,
                        max_attempts: examSettings.max_attempts,
                        allow_navigation: true
                    };

                    $scope.sections = sections || [];
                    $scope.originalSections = angular.copy(sections || []);
                    $scope.allQuestions = questions || [];

                    $scope.processQuestions();
                    $scope.calculateTotals();

                    $scope.currentStep = 1;

                    $scope.questionsDisplayMode = 'all';
                    $scope.activeSection = $scope.sections.length > 0 ? $scope.sections[0] : null;
                    $scope.questionsPerPage = 10;
                    $scope.currentQuestionPage = 0;

                    $scope.updateQuestionsDisplay();
                    // $scope.getSectionaizedQuestions();

                    $scope.$applyAsync();
                }
            } catch (err) {
                console.error(err);
            }
        };

        const formatInstructions = (instructions) => {
            if (!instructions) return '';
            return instructions.replace(/(\s|^)(\d+\.|[a-z]\.|\([a-z]\))\s+/gi, '\n$2 ');
        };

        // Process questions and nest them in sections
        $scope.processQuestions = function () {
            // Reset section questions
            if (!$scope.sections) return;

            $scope.sections.forEach(function (section) {
                section.questions = [];
            });

            // If no questions, return
            if (!$scope.allQuestions || $scope.allQuestions.length === 0) return;

            // Assign questions to sections
            $scope.allQuestions.forEach(function (question) {
                // Get section names for display
                var sectionNames = [];
                var hasSectionIds = false;

                if (question.sectionIds && question.sectionIds.length > 0) {
                    hasSectionIds = true;
                    question.sectionIds.forEach(function (sectionId) {
                        var section = $scope.sections.find(function (s) {
                            return s.id === sectionId;
                        });

                        if (section) {
                            sectionNames.push(section.title);

                            // Add question to section
                            section.questions = section.questions || [];
                            if (!section.questions.find(q => q.id === question.id)) {
                                section.questions.push(question);
                            }
                        }

                        var originalSection = $scope.originalSections.find(function (s) {
                            return s.id === sectionId;
                        });

                        if (originalSection) {

                            // Add question to section
                            originalSection.questions = originalSection.questions || [];
                            if (!originalSection.questions.find(q => q.id === question.id)) {
                                originalSection.questions.push(question);
                            }
                        }
                    });
                }

                question.sectionNames = sectionNames.join(', ');
                $scope.organaizedSections = $scope.sections;
            });
        };

        // Calculate totals
        $scope.calculateTotals = function () {
            if (!$scope.allQuestions || $scope.allQuestions.length === 0) {
                $scope.totalQuestions = 0;
                $scope.totalMarks = 0;
                return;
            }

            $scope.totalQuestions = $scope.allQuestions.length;
            $scope.totalMarks = $scope.allQuestions.reduce(function (total, q) {
                return total + (parseInt(q.marks) || 1);
            }, 0);
        };

        // Get count of questions with images
        $scope.getQuestionsWithImages = function () {
            if (!$scope.allQuestions) return 0;
            return $scope.allQuestions.filter(function (q) {
                return q && q.image;
            }).length;
        };

        $scope.selectPerPage = function (event, num) {
            event.stopPropagation();
            $scope.questionsPerPage = num;
            $scope.dropdownOpen = false;
            $scope.updateQuestionsDisplay();
        };

        // Update questions display
        $scope.updateQuestionsDisplay = function () {
            if (!$scope.allQuestions) {
                $scope.displayQuestions = [];
                return;
            }

            if ($scope.questionsDisplayMode === 'sections' && $scope.activeSection) {
                // Show only questions from active section
                $scope.displayQuestions = ($scope.activeSection.questions || []);
            } else {
                // Show all questions
                $scope.displayQuestions = $scope.allQuestions;
            }

            // Update pagination
            $scope.updateQuestionPagination();
        };

        // Update question pagination
        $scope.updateQuestionPagination = function () {
            if (!$scope.displayQuestions) {
                $scope.questionPages = [];
                return;
            }

            var totalPages = Math.ceil($scope.displayQuestions.length / $scope.questionsPerPage);
            $scope.questionPages = new Array(totalPages).fill().map(function (_, i) {
                return i;
            });

            // Ensure current page is valid
            if ($scope.currentQuestionPage >= totalPages) {
                $scope.currentQuestionPage = Math.max(0, totalPages - 1);
            }
        };

        // Get questions for current page
        $scope.getCurrentPageQuestions = function () {
            if (!$scope.displayQuestions || $scope.displayQuestions.length === 0) {
                return [];
            }

            var start = $scope.currentQuestionPage * $scope.questionsPerPage;
            var end = start + parseInt($scope.questionsPerPage);
            return $scope.displayQuestions.slice(start, end);
        };

        // Section selection
        $scope.selectSection = function (section) {
            $scope.questionsDisplayMode = 'sections';
            $scope.activeSection = section;
            $scope.currentQuestionPage = 0;
            $scope.updateQuestionsDisplay();
        };

        $scope.editQuestion = function (id) {
            if (isPastStartTime($scope.examData.start_time)) {
                Toast.fire({
                    type: 'error',
                    title: 'Exam has started',
                    msg: 'You cannot edit questions after the exam has started'
                })
                return;
            }

            if (isExamComplete()) {
                Toast.fire({
                    type: 'error',
                    title: 'Exam completed',
                    msg: 'You cannot edit questions after the exam is completed'
                });
                return;
            }

            $scope.editingQuestionId = id;
            let question = $scope.allQuestions.find(q => q.id === id);
            question.correctAnswer = question.correctAnswer.toUpperCase();
            $scope.currentQuestion = question;
        };

        $scope.saveQuestion = async function () {
            if (!$scope.currentQuestion.question) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please enter question text' });
                return false;
            }

            const validOptions = $scope.currentQuestion.options.filter(opt => opt.text || opt.image);
            if (validOptions.length < 4) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'All options are required' });
                return false;
            }

            if ($scope.currentQuestion.correctAnswer === null || $scope.currentQuestion.correctAnswer === undefined) {
                Toast.fire({ type: 'error', title: 'Validation Error!', msg: 'Please select a correct answer' });
                return false;
            }

            // API URI
            const apiUrl = window.baseUrl + '/API/questions/edit_question/' + $scope.currentQuestion.id;
            const formData = $(`#questionForm${$scope.currentQuestion.id}`).serialize();
            try {
                const response = await $http({
                    url: apiUrl,
                    data: formData,
                    method: 'POST',
                });

                if (response.data.status === 'success') {
                    Toast.popover({ type: 'close' })
                    Toast.fire({ type: 'success', title: 'Success!', msg: response.data.msg || 'Question updated successfully' });

                    let updated = response.data.question;
                    updated.correctAnswer = updated.answer;

                    // Update currentQuestion
                    $scope.currentQuestion = updated;
                    $scope.currentQuestionIndex = $scope.allQuestions.findIndex(q => q.id === updated.id);
                    $scope.allQuestions[$scope.currentQuestionIndex] = angular.copy(updated);
                    $scope.currentQuestion = null
                    $scope.editingQuestionId = null;
                    $scope.$applyAsync();
                } else {
                    Toast.fire({ type: 'error', title: 'Error!', msg: response.data.msg });
                }
            } catch (error) {
                Toast.fire({ type: 'error', title: 'Error!', msg: 'Something went wrong. Failed to update the question.' });
                console.error(error);
            };
        };

        $scope.cancelEdit = function () {
            $scope.editingQuestionId = null;
        };

        // Transform questions into reviewSections structure
        $scope.getSectionaizedQuestions = function () {
            if (!$scope.sections) return;

            // Reset questions in each section
            $scope.sections.forEach(section => section.questions = []);

            if (!$scope.allQuestions || $scope.allQuestions.length === 0) {
                $scope.reviewSections = $scope.sections;
                $scope.updatePreviewQuestionsDisplay();
                return;
            }

            let reviewSectionsMap = {};
            let reviewSectionsOrder = [];

            // Reset original data for shuffle
            $scope.originalAllPreviewSections = null;
            $scope.originalSectionQuestions = {};

            $scope.allQuestions.forEach(question => {
                let sectionNames = [];
                let assigned = false;

                // Assign to existing sections if sectionIds exist and match
                if (question.sectionIds && question.sectionIds.length > 0) {
                    for (let counter = 0; counter < question.sectionIds.length; counter++) {
                        let section = $scope.sections.find(s => s.id === question.sectionIds[counter]);
                        if (section) {
                            section.questions = section.questions || [];
                            section.questions.push(question);
                            sectionNames.push(section.title);
                            assigned = true;

                            if (!reviewSectionsMap[section.id]) {
                                reviewSectionsMap[section.id] = section;
                                reviewSectionsOrder.push(section);
                            }
                            break;
                        }
                    }
                }

                // If not assigned → create new unique section
                if (!assigned) {
                    let newSectionId = Math.floor(100000 + Math.random() * 900000);
                    let newSection = {
                        id: newSectionId,
                        exam_id: +$scope.location.exam,
                        title: `Section ${newSectionId}`,
                        description: '',
                        questions: [question],
                        order: $scope.sections.length + 1,
                        second_description: ''
                    };

                    $scope.sections.push(newSection);
                    sectionNames.push(newSection.title);

                    reviewSectionsMap[newSection.id] = newSection;
                    reviewSectionsOrder.push(newSection);
                }

                question.sectionNames = sectionNames.join(', ');
            });

            $scope.reviewSections = reviewSectionsOrder;

            // Sort questions inside each section by original question order
            $scope.reviewSections.forEach(section => {
                if (section.questions) {
                    section.questions = section.questions
                        .slice()
                        .sort((a, b) => (a.order || 0) - (b.order || 0));
                }
            });

            $scope.updatePreviewQuestionsDisplay();
        };

        // Update preview display based on current mode
        $scope.updatePreviewQuestionsDisplay = function () {
            if ($scope.previewDisplayMode === 'sections' && $scope.activePreviewSection) {
                // Single section mode
                $scope.previewDisplayQuestions = $scope.activePreviewSection.questions || [];
                $scope.previewDisplaySections = [$scope.activePreviewSection];
            } else {
                // All sections mode
                $scope.previewDisplayQuestions = [];
                $scope.previewDisplaySections = angular.copy($scope.reviewSections);

                $scope.previewDisplaySections.forEach(section => {
                    if (section.questions && section.questions.length > 0) {
                        $scope.previewDisplayQuestions.push(...section.questions);
                    }
                });
            }

            // Store original order for shuffle restoration
            if (!$scope.originalAllPreviewSections) {
                $scope.originalAllPreviewSections = angular.copy($scope.previewDisplaySections);
            }

            // Initialize original section questions
            $scope.previewDisplaySections.forEach(section => {
                if (!section.id || $scope.originalSectionQuestions[section.id]) return;
                $scope.originalSectionQuestions[section.id] = angular.copy(section.questions);
            });

            // Apply shuffle if needed
            $scope.applyShuffleToPreview();

            $scope.updatePreviewQuestionPagination();
            $scope.getCurrentPreviewPageSections();
        };

        // Apply shuffle logic to preview display
        $scope.applyShuffleToPreview = function () {
            if (!$scope.examData.shuffle_questions) {
                // Restore original order
                if ($scope.originalAllPreviewSections) {
                    $scope.previewDisplaySections = angular.copy($scope.originalAllPreviewSections);
                }
            } else {
                // Apply shuffle based on mode
                if ($scope.previewDisplayMode === 'sections' && $scope.activePreviewSection) {
                    // Shuffle questions within active section
                    let questions = $scope.activePreviewSection.questions;
                    for (let i = questions.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [questions[i], questions[j]] = [questions[j], questions[i]];
                    }
                } else {
                    // Shuffle sections (but keep questions within sections in order)
                    let sections = $scope.previewDisplaySections;
                    for (let i = sections.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [sections[i], sections[j]] = [sections[j], sections[i]];
                    }
                }
            }

            // Shuffle options if needed
            if ($scope.examData.shuffle_options) {
                $scope.previewDisplaySections.forEach(section => {
                    section.questions.forEach(question => {
                        if (!question.options || !question.options.length) return;
                        // Shuffle options
                        for (let i = question.options.length - 1; i > 0; i--) {
                            const j = Math.floor(Math.random() * (i + 1));
                            [question.options[i], question.options[j]] = [question.options[j], question.options[i]];
                        }
                    });
                });
            }


            // Update global numbering
            $scope.updateGlobalNumbering();
        };

        // Update global question numbering
        $scope.updateGlobalNumbering = function () {
            let globalCounter = 1;

            $scope.previewDisplaySections.forEach(section => {
                section.questions.forEach(question => {
                    question._globalNumber = globalCounter++;
                });
            });
        };

        $scope.getCurrentPreviewPageQuestions = function () {
            if (!$scope.previewDisplayQuestions || $scope.previewDisplayQuestions.length === 0) {
                return [];
            }

            var start = $scope.currentPreviewQuestionPage * $scope.previewQuestionsPerPage;
            var end = start + parseInt($scope.previewQuestionsPerPage);
            return $scope.previewDisplayQuestions.slice(start, end);
        };

        // Get sections for current preview page
        $scope.getCurrentPreviewPageSections = function () {
            if (!$scope.previewDisplaySections || $scope.previewDisplaySections.length === 0) {
                $scope.currentPreviewPageSections = [];
                return;
            }

            // Clear cache on page change
            $scope.cachedPreviewSections = null;
            $scope.cachedPage = null;

            let start = $scope.currentPreviewQuestionPage * $scope.previewQuestionsPerPage;
            let end = start + parseInt($scope.previewQuestionsPerPage);

            let resultSections = [];
            let questionCounter = 0;

            for (let sectionIndex = 0; sectionIndex < $scope.previewDisplaySections.length; sectionIndex++) {
                let section = $scope.previewDisplaySections[sectionIndex];
                let totalQuestions = section.questions.length;

                if (questionCounter + totalQuestions <= start) {
                    questionCounter += totalQuestions;
                    continue;
                }

                let sectionCopy = angular.copy(section);
                sectionCopy.questions = [];

                // Determine slice range for this section
                let sectionStart = Math.max(0, start - questionCounter);
                let sectionEnd = Math.min(totalQuestions, end - questionCounter);

                // Slice questions for this section
                let slicedQuestions = section.questions.slice(sectionStart, sectionEnd);
                sectionCopy.questions = slicedQuestions;

                // Only add section if it has questions
                if (slicedQuestions.length > 0) {
                    resultSections.push(sectionCopy);
                }

                questionCounter += totalQuestions;

                if (questionCounter >= end) {
                    break;
                }
            }

            $scope.currentPreviewPageSections = resultSections;
        };

        // Update preview question pagination
        $scope.updatePreviewQuestionPagination = function () {
            if (!$scope.previewDisplayQuestions || $scope.previewDisplayQuestions.length === 0) {
                $scope.previewQuestionPages = [];
                $scope.currentPreviewQuestionPage = 0;
                return;
            }

            var totalPages = Math.ceil($scope.previewDisplayQuestions.length / $scope.previewQuestionsPerPage);
            $scope.previewQuestionPages = new Array(totalPages).fill().map((_, i) => i);

            if ($scope.currentPreviewQuestionPage >= totalPages) {
                $scope.currentPreviewQuestionPage = Math.max(0, totalPages - 1);
            }
        };

        // Preview navigation functions
        $scope.previousPreviewQuestionPage = function () {
            if ($scope.currentPreviewQuestionPage > 0) {
                $scope.currentPreviewQuestionPage--;
                $scope.getCurrentPreviewPageSections();
            }
        };

        $scope.nextPreviewQuestionPage = function () {
            if ($scope.currentPreviewQuestionPage < $scope.previewQuestionPages.length - 1) {
                $scope.currentPreviewQuestionPage++;
                $scope.getCurrentPreviewPageSections();
            }
        };

        // Select option in preview mode
        $scope.selectPreviewOption = function (questionId, oIndex) {
            const question = $scope.previewDisplayQuestions.find(q => q.id === questionId);
            if (question) {
                question.selectedOption = oIndex;

                // Also update in the main question array for consistency
                const mainQuestion = $scope.allQuestions.find(q => q.id === questionId);
                if (mainQuestion) {
                    mainQuestion.selectedOption = oIndex;
                }
            }
        };

        // Toggle shuffle and update display
        $scope.shufflePreviewQuestions = function () {
            // Just update the display which will apply shuffle logic
            $scope.updatePreviewQuestionsDisplay();
        };

        // Select section in preview mode
        $scope.selectPreviewSection = function (section) {
            $scope.activePreviewSection = section;
            $scope.previewDisplayMode = 'sections';
            $scope.currentPreviewQuestionPage = 0;
            $scope.updatePreviewQuestionsDisplay();
        };

        // Toggle preview display mode
        $scope.togglePreviewDisplayMode = function () {
            $scope.previewDisplayMode = $scope.previewDisplayMode === 'all' ? 'sections' : 'all';
            if ($scope.previewDisplayMode === 'all') {
                $scope.activePreviewSection = null;
            }
            $scope.currentPreviewQuestionPage = 0;
            $scope.updatePreviewQuestionsDisplay();
        };

        // Shuffle options for preview
        $scope.shufflePreviewOptions = function () {
            if (!$scope.previewDisplayQuestions || !$scope.previewDisplayQuestions.length) return;

            // Store original order if not already stored
            if (!$scope.originalQuestionOptions) {
                $scope.originalQuestionOptions = {};
            }

            $scope.previewDisplayQuestions.forEach(question => {
                if (!question.options || !question.options.length) return;

                // Initialize original options storage for this question if needed
                if (!$scope.originalQuestionOptions[question.id]) {
                    $scope.originalQuestionOptions[question.id] = angular.copy(question.options);
                }

                if ($scope.examData.shuffle_options) {
                    // Store previous selection
                    const prevSelectedOption = question.selectedOption !== undefined
                        ? angular.copy(question.options[question.selectedOption])
                        : null;

                    // Shuffle options
                    const shuffledOptions = [...question.options];
                    for (let i = shuffledOptions.length - 1; i > 0; i--) {
                        const j = Math.floor(Math.random() * (i + 1));
                        [shuffledOptions[i], shuffledOptions[j]] = [shuffledOptions[j], shuffledOptions[i]];
                    }
                    question.options = shuffledOptions;

                    // Restore selection if exists
                    if (prevSelectedOption !== null) {
                        const newIndex = question.options.findIndex(o =>
                            o.text === prevSelectedOption.text &&
                            o.id === prevSelectedOption.id
                        );
                        question.selectedOption = newIndex !== -1 ? newIndex : undefined;
                    }
                } else {
                    // Restore original order
                    const originalOptions = $scope.originalQuestionOptions[question.id];
                    if (originalOptions) {
                        // Store current selection before restoring
                        const currentSelectedOption = question.selectedOption !== undefined
                            ? angular.copy(question.options[question.selectedOption])
                            : null;

                        // Restore original order
                        question.options = angular.copy(originalOptions);

                        // Restore selection if exists
                        if (currentSelectedOption !== null) {
                            const newIndex = question.options.findIndex(o =>
                                o.text === currentSelectedOption.text &&
                                o.id === currentSelectedOption.id
                            );
                            question.selectedOption = newIndex !== -1 ? newIndex : undefined;
                        }
                    }
                }
            });

            // Also update options in current page sections for consistency
            if ($scope.currentPreviewPageSections) {
                $scope.currentPreviewPageSections.forEach(section => {
                    if (section.questions && section.questions.length) {
                        section.questions.forEach(q => {
                            const updatedQuestion = $scope.previewDisplayQuestions.find(pq => pq.id === q.id);
                            if (updatedQuestion) {
                                q.options = angular.copy(updatedQuestion.options);
                                q.selectedOption = updatedQuestion.selectedOption;
                            }
                        });
                    }
                });
            }
        };

        // Initialize Step 4
        $scope.initializeStep4 = function () {
            // Generate reviewSections structure
            $scope.getSectionaizedQuestions();

            // Set default active section if in sections mode
            if ($scope.previewDisplayMode === 'sections' && $scope.reviewSections.length > 0) {
                $scope.activePreviewSection = $scope.reviewSections[0];
            }

            // Update display
            $scope.getCurrentPreviewPageSections();
            $scope.updatePreviewQuestionsDisplay();
            $scope.updatePreviewQuestionPagination();
        };

        // Question page navigation
        $scope.goToQuestionPage = function (pageIndex) {
            $scope.currentQuestionPage = pageIndex;
        };

        $scope.previousQuestionPage = function () {
            if ($scope.currentQuestionPage > 0) {
                $scope.currentQuestionPage--;
            }
        };

        $scope.nextQuestionPage = function () {
            if ($scope.currentQuestionPage < $scope.questionPages.length - 1) {
                $scope.currentQuestionPage++;
            }
        };

        // Step navigation
        $scope.nextStep = function () {
            if ($scope.currentStep < 5) {
                $scope.currentStep++;
                $scope.updateStepCompletion();
            }
        };

        $scope.previousStep = function () {
            if ($scope.currentStep > 1) {
                $scope.currentStep--;
            }
        };

        // Update step completion status
        $scope.updateStepCompletion = function () {
            $scope.step1Completed = $scope.isBasicInfoComplete();
            $scope.step2Completed = $scope.totalQuestions > 0;
            $scope.step3Completed = $scope.areSettingsValid();
            $scope.step4Completed = $scope.step1Completed && $scope.step2Completed && $scope.step3Completed;
            $scope.step5Completed = $scope.examData && ($scope.examData.status === 'published' || $scope.examData.status === 'scheduled');
            if ($scope.currentStep === 4) {
                $scope.initializeStep4();
            }
        };

        // Validation functions
        $scope.isBasicInfoComplete = function () {
            return $scope.examData &&
                $scope.examData.title &&
                $scope.examData.code &&
                $scope.examData.duration > 0 &&
                $scope.examData.total_marks > 0 &&
                $scope.examData.passing_marks > 0;
        };

        $scope.areSettingsValid = function () {
            if (!$scope.examData) return false;

            // Basic settings validation
            return $scope.examData.total_marks > 0 &&
                $scope.examData.passing_marks <= $scope.examData.total_marks;
        };

        $scope.isReadyToPublish = function () {
            return $scope.isBasicInfoComplete() &&
                $scope.totalQuestions > 0 &&
                $scope.areSettingsValid();
        };

        // Proceed to publish exam
        function proceedPublish() {
            $http.post(window.baseUrl + '/API/publish_exam/' + $scope.location.exam)
                .then((response) => {

                    if (response.data.status === 'success') {
                        Toast.fire({
                            type: 'success',
                            title: 'Exam Published!',
                            msg: 'Exam published successfully!'
                        });

                        $scope.examData.status = $scope.examData.schedule_type === 'scheduled' ? 'scheduled' : 'published';
                        // $scope.examData.published_at = new Date().toISOString();
                        $scope.examData.published_at = new Date(response.data.published_at).toISOString();
                        $scope.step5Completed = true;

                    } else {
                        Toast.fire({
                            type: 'error',
                            title: 'Failed!',
                            msg: response.data.msg || 'Something went wrong.'
                        });
                    }
                    $scope.$applyAsync();
                })
                .catch(() => {
                    Toast.fire({
                        type: 'error',
                        title: 'Server Error',
                        msg: 'API not response'
                    });
                });
        }

        // Publish exam
        $scope.publishExam = function () {

            if (!$scope.isReadyToPublish()) {
                Toast.fire({
                    type: 'error',
                    title: 'Cannot publish exam',
                    msg: 'Please complete all requirements before publishing.'
                });
                return;
            }

            if (isPastStartTime($scope.examData.start_time)) {
                Toast.fire({
                    type: 'error',
                    title: 'Cannot publish exam',
                    msg: 'Exam start time must be in the future.'
                });
                return;
            }

            // If exam was previously cancelled
            if ($scope.examData.status === 'canceled') {
                Toast.popover({
                    type: 'confirm',
                    title: 'Exam Previously Cancelled!',
                    titleColor: '#ca8a04',
                    content: `
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:3rem;color:#ca8a04"></i><br><br>
                        <p>This exam was cancelled earlier.<br>
                        Do you want to re-publish it?</p>
                    `,
                    contentColor: '#fff',
                    options: {
                        confirm: {
                            text: 'Yes, Re-publish it!',
                            background: '#0ea5e9',
                            onConfirm: function () {

                                // Ask schedule type
                                if ($scope.examData.schedule_type === 'scheduled') {

                                    // If scheduled → start time required
                                    if (!$scope.examData.start_time) {
                                        Toast.fire({
                                            type: 'error',
                                            title: 'Start Time Required',
                                            msg: 'Since the exam is scheduled, please set the start time.'
                                        });
                                        Toast.popover({ type: 'close' });
                                        return;
                                    }
                                }

                                proceedPublish(); // call main function
                                Toast.popover({ type: 'close' });
                            }
                        },
                        cancel: {
                            text: 'Cancel',
                            background: '#dc2626',
                            onCancel: () => Toast.popover({ type: 'close' })
                        }
                    }
                });

                return;
            }

            Toast.popover({
                type: 'confirm',
                title: 'Publish Exam?',
                titleColor: '#65deff',
                content: `
                    <i class="fa-solid fa-circle-info" style="font-size: 3rem; color: #65deff"></i><br><br>
                    <p>Are you sure you want to publish this exam? It will be available to candidates.</p>
                `,
                contentColor: '#fff',
                options: {
                    confirm: {
                        text: 'Yes, publish it!',
                        background: '#0e7490',
                        onConfirm: async function () {
                            await proceedPublish()
                            Toast.popover({ type: 'close' });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        background: '#dc2626',
                        onCancel: function () {
                            Toast.popover({ type: 'close' });
                        }
                    }
                }
            });

        };

        // Check is start time passed
        function isPastStartTime(startTime) {
            if (($scope.examData.status === 'published' || $scope.examData.status === 'scheduled')) {
                if ($scope.examData.schedule_type === 'anytime') {
                    return true;
                }
                const now = new Date();
                const examStart = new Date(startTime);
                return now >= examStart;
            }
            return false;
        }

        // Check is exam complete
        function isExamComplete() {
            if (($scope.examData.status === 'published' || $scope.examData.status === 'scheduled')) {
                if ($scope.examData.schedule_type === 'anytime') {
                    return false;
                }

                const now = new Date();
                const examStart = new Date($scope.examData.start_time);
                const examDurationMinutes = parseInt($scope.examData.duration);

                // Calculate exam end time
                const examEnd = new Date(examStart.getTime() + examDurationMinutes * 60000);

                // Return true if current time is after exam end
                return now >= examEnd;
            }
            return false;
        }

        // Unpublish exam
        $scope.unpublishExam = function () {
            // if (isPastStartTime($scope.examData.start_time)) {
            //     Toast.fire({
            //         type: 'error',
            //         title: 'Cannot Unpublish',
            //         msg: 'Exam has already started. You can only SUDDEN CANCEL it.'
            //     });
            //     return;
            // }

            if (isExamComplete()) {
                Toast.fire({
                    type: 'error',
                    title: 'Cannot Unpublish',
                    msg: 'Exam has already completed.'
                });
                return;
            }

            const now = new Date();
            const examStart = new Date($scope.examData.start_time);

            // If exam already started
            if (isPastStartTime($scope.examData.start_time)) {

                Toast.popover({
                    type: 'confirm',
                    title: 'Cancel Exam?',
                    titleColor: '#ca8a04',
                    content: `
                        <i class="fa-solid fa-circle-xmark" style="font-size: 3rem; color: #dc2626"></i><br><br>
                        <p>This exam has already started.<br>
                        You cannot unpublish it now.<br>
                        Do you want to <b>Cancel</b> the exam?</p>
                    `,
                    contentColor: '#fff',
                    options: {
                        confirm: {
                            text: 'Yes, Cancel Now!',
                            background: '#dc2626',
                            onConfirm: async function () {
                                try {
                                    const response = await $http.post(
                                        window.baseUrl + '/API/cancel_exam/' + $scope.location.exam
                                    );

                                    if (response.data.status === 'success') {
                                        Toast.fire({
                                            type: 'warning',
                                            title: 'Exam Cancelled!',
                                            msg: 'The exam was suddenly stopped for all candidates.'
                                        });
                                        $scope.examData.status = 'canceled';
                                        $scope.$applyAsync();
                                    } else {
                                        Toast.fire({
                                            type: 'error',
                                            title: 'Failed!',
                                            msg: response.data.msg || 'Something went wrong.'
                                        });
                                    }
                                } catch (e) {
                                    Toast.fire({
                                        type: 'error',
                                        title: 'Server Error',
                                        msg: 'Check your API!'
                                    });
                                }

                                Toast.popover({ type: 'close' });
                            }
                        },
                        cancel: {
                            text: 'No, Keep Running',
                            background: '#0ea5e9',
                            onCancel: function () {
                                Toast.popover({ type: 'close' });
                            }
                        }
                    }
                });

                return;
            }

            // If exam NOT started
            Toast.popover({
                type: 'confirm',
                title: 'Unpublish Exam?',
                titleColor: '#ff6b6b',
                content: `
                    <i class="fa-solid fa-circle-exclamation" style="font-size: 3rem; color: #dc2626"></i><br><br>
                    <p>Are you sure you want to unpublish this exam? Candidates will no longer have access.</p>
                `,
                contentColor: '#fff',
                options: {
                    confirm: {
                        text: 'Yes, unpublish it!',
                        background: '#dc2626',
                        onConfirm: async function () {
                            try {
                                const response = await $http.post(
                                    window.baseUrl + '/API/unpublish_exam/' + $scope.location.exam
                                );

                                if (response.data.status === 'success') {
                                    Toast.fire({
                                        type: 'success',
                                        title: 'Exam Unpublished!',
                                        msg: 'Exam unpublished successfully!'
                                    });

                                    $scope.examData.status = 'draft';
                                    $scope.examData.published_at = null;
                                    $scope.step5Completed = false;
                                } else {
                                    Toast.fire({
                                        type: 'error',
                                        title: 'Failed!',
                                        msg: response.data.msg || 'Something went wrong.'
                                    });
                                }

                                $scope.$applyAsync()
                            } catch (e) {
                                Toast.fire({
                                    type: 'error',
                                    title: 'Server Error',
                                    msg: 'Check your API!'
                                });
                            }

                            Toast.popover({ type: 'close' });
                        }
                    },
                    cancel: {
                        text: 'Cancel',
                        background: '#0e7490',
                        onCancel: function () {
                            Toast.popover({ type: 'close' });
                        }
                    }
                }
            });
        };

        // Copy to clipboard
        $scope.copyToClipboard = function (inputElement) {
            try {
                if ($scope.examData.status === 'published' || $scope.examData.status === 'scheduled') {
                    inputElement.select();
                    inputElement.setSelectionRange(0, 99999);
                    document.execCommand("copy");
                    Toast.fire({ type: 'success', title: 'Success!', msg: 'Link copied to clipboard!' });
                } else {
                    Toast.fire({ type: 'error', title: 'Error', msg: 'Exam is not published.' });
                }
            } catch (e) {
                Toast.fire({ type: 'error', title: 'Error', msg: 'Failed to copy link.' });
                console.error(e);
            }
        };

        $scope.isExamFullySetup = function () {
            return $scope.isBasicInfoComplete() &&
                $scope.areQuestionsComplete() &&
                $scope.areSettingsComplete();
        };

        $scope.areQuestionsComplete = function () {
            // For preview, check if we have questions and sections
            if (!$scope.allQuestions || $scope.allQuestions.length === 0) return false;
            if (!$scope.originalSections || $scope.originalSections.length !== 0) return false;

            // Check if all questions are properly assigned to sections
            let allQuestionsAssigned = true;
            // $scope.allQuestions.forEach(function (question) {
            //     if (!question.sectionIds || question.sectionIds.length === 0) {
            //         allQuestionsAssigned = false;
            //     }
            // });

            $scope.originalSections.forEach(function (section) {
                if (!section.questions || section.questions.length === 0) {
                    allQuestionsAssigned = false;
                }

                if (section.questions.length !== section.questions_count) {
                    allQuestionsAssigned = false;
                }

                if (section.questions.length === section.questions_count) {
                    allQuestionsAssigned = true;
                }
            })

            return allQuestionsAssigned;
        };

        $scope.areSettingsComplete = function () {
            if (!$scope.examData || !$scope.examData.schedule_type) return false;

            // Check schedule type specific requirements
            if ($scope.examData.schedule_type === 'scheduled') {
                if (!$scope.examData.start_time) return false;

                // Check if start time is in the future for scheduled exams
                const now = new Date();
                const examStart = new Date($scope.examData.start_time);
                if (now >= examStart && $scope.examData.status !== 'published' && $scope.examData.status !== 'scheduled') {
                    return false; // Start time must be in future for unpublished exams
                }
            }

            // Check all required settings are defined (not undefined)
            const requiredSettings = [
                'shuffle_questions',
                'shuffle_options',
                'show_results_immediately',
                'allow_retake',
                'full_screen_mode',
                'disable_copy_paste',
                'disable_right_click'
            ];

            for (let setting of requiredSettings) {
                if ($scope.examData[setting] === undefined) {
                    return false;
                }
            }

            // If retake is allowed, check max attempts is set
            if ($scope.examData.allow_retake &&
                (!$scope.examData.max_attempts || $scope.examData.max_attempts < 1)) {
                return false;
            }

            return true;
        };

        $scope.getMissingRequirements = function () {
            var missing = [];

            if (!$scope.isBasicInfoComplete()) {
                missing.push("Complete basic exam information (title, code, duration, marks)");
            }

            if (!$scope.areQuestionsComplete()) {
                if (!$scope.allQuestions || $scope.allQuestions.length === 0) {
                    missing.push("Create at least one question");
                } else if (!$scope.originalSection || $scope.originalSection.length === 0) {
                    missing.push("Create at least one section");
                } else {
                    // Check for unassigned questions
                    const unassignedQuestions = $scope.allQuestions.filter(q =>
                        !q.sectionIds || q.sectionIds.length === 0
                    );
                    if (unassignedQuestions.length > 0) {
                        missing.push(`Assign ${unassignedQuestions.length} unassigned question${unassignedQuestions.length > 1 ? 's' : ''} to sections`);
                    }
                }
            }

            if (!$scope.areSettingsComplete()) {
                if (!$scope.examData.schedule_type) {
                    missing.push("Select schedule type (anytime or scheduled)");
                } else if ($scope.examData.schedule_type === 'scheduled') {
                    if (!$scope.examData.start_time) {
                        missing.push("Set start date and time for scheduled exam");
                    } else {
                        const now = new Date();
                        const examStart = new Date($scope.examData.start_time);
                        if (now >= examStart && $scope.examData.status !== 'published') {
                            missing.push("Start time must be in the future for scheduled exams");
                        }
                    }
                }

                // Check individual settings
                if ($scope.examData.allow_retake &&
                    (!$scope.examData.max_attempts || $scope.examData.max_attempts < 1)) {
                    missing.push("Set maximum attempts when retake is allowed");
                }
            }

            return missing;
        };

        // Initialize on load
        $scope.init();
    }
]);