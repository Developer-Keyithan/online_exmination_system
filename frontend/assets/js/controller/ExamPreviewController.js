app.controller('ExamPreviewController', [
    "$scope", "$http", "$compile", "$timeout", "window",
    function ($scope, $http, $compile, $timeout, window) {
        $scope.location = $scope.location || {};
        $scope.location.exam = window.getIdFromUrl();
        $scope.dropdownOpen = false;
        $scope.questionsDisplayMode = 'all';

        // Initialize arrays to prevent undefined errors
        $scope.allQuestions = [];
        $scope.sections = [];
        $scope.displayQuestions = [];

        // Initialize function
        $scope.init = async function () {
            await $scope.loadExamData();
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
                        allow_navigation: true,
                        published_at: null
                    };

                    $scope.sections = sections || [];
                    $scope.allQuestions = questions || [];

                    console.log($scope.examData);

                    $scope.processQuestions();
                    $scope.calculateTotals();

                    $scope.currentStep = 1;

                    $scope.questionsDisplayMode = 'all';
                    $scope.activeSection = $scope.sections.length > 0 ? $scope.sections[0] : null;
                    $scope.questionsPerPage = 10;
                    $scope.currentQuestionPage = 0;

                    $scope.updateQuestionsDisplay();

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
                    });
                }

                question.sectionNames = sectionNames.join(', ');
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

        // Question edit
        $scope.editQuestion = function (questionId) {
            Toast.popover({
                type: 'apiContent',
                title: 'Edit Question',
                titleColor: '#0099ff',
                apiConfig: {
                    endpoint: 'section_editor',
                    method: 'GET'
                },
                background: '#0003',
                position: 'center',
                confirm: {
                    text: 'Save changes',
                    background: '#0099ff',
                    onConfirm: function () {
                        console.log('Editing Question')
                    }
                },
                cancel: {
                    text: 'Cancel',
                    background: '#ff0000',
                    onCancel: function () {
                        Toast.popover({ type: 'close' })
                    }
                }
            })
        }

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
            if ($scope.currentStep < 4) {
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
            $scope.step4Completed = $scope.examData && $scope.examData.status === 'published';
        };

        // Validation functions
        $scope.isBasicInfoComplete = function () {
            return $scope.examData &&
                $scope.examData.title &&
                $scope.examData.code &&
                ($scope.examData.duration > 0) &&
                ($scope.totalQuestions > 0);
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

        // Publish exam (dummy function)
        $scope.publishExam = function () {
            if (!$scope.isReadyToPublish()) {
                alert('Please complete all requirements before publishing.');
                return;
            }

            if (confirm('Are you sure you want to publish this exam? It will be available to candidates.')) {
                // Simulate API call
                $timeout(function () {
                    $scope.examData.status = 'published';
                    $scope.examData.published_at = new Date().toISOString();
                    $scope.step4Completed = true;
                    alert('Exam published successfully!');
                }, 500);
            }
        };

        // Unpublish exam (dummy function)
        $scope.unpublishExam = function () {
            if (confirm('Are you sure you want to unpublish this exam? Candidates will no longer have access.')) {
                // Simulate API call
                $timeout(function () {
                    $scope.examData.status = 'draft';
                    $scope.step4Completed = false;
                    alert('Exam unpublished successfully!');
                }, 500);
            }
        };

        // Initialize on load
        $scope.init();
    }]);