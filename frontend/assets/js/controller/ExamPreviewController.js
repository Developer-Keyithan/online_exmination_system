app.controller('ExamPreviewController', [
    "$scope", "$http", "$compile", "$timeout", "window", "questionEditorModalController",
    function ($scope, $http, $compile, $timeout, window, questionEditorModalController) {
        $scope.location = $scope.location || {};
        $scope.location.exam = window.getIdFromUrl();
        $scope.dropdownOpen = false;
        $scope.questionsDisplayMode = 'all';
        $scope.editingQuestionId = null;
        $scope.organaizedSections = [];

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

        // Question edit
        // $scope.editQuestion = function (questionId) {
        //     if (!$scope.questionEditiorModalCtrl) {
        //         $scope.questionEditiorModalCtrl = questionEditorModalController($scope);
        //     }
        //     const question = $scope.allQuestions.find(q => q.id === questionId);
        //     $scope.questionEditiorModalCtrl.init(question);

        //     Toast.popover({
        //         type: 'apiContent',
        //         title: 'Edit Question',
        //         titleColor: '#0e7490',
        //         apiConfig: {
        //             endpoint: 'question_editor',
        //             method: 'GET'
        //         },
        //         background: '#0003',
        //         position: 'center',
        //         buttons: [
        //             {
        //                 text: 'Save changes',
        //                 background: '#0e7490',
        //                 onClick: function () {
        //                     $scope.questionEditiorModalCtrl.save();
        //                 }
        //             },
        //             {
        //                 text: 'Cancel',
        //                 background: '#dc2626',
        //                 onClick: function () {
        //                     Toast.popover({ type: 'close' })
        //                 }
        //             }
        //         ],
        //         size:'xxl'
        //     }).then(popoverInstance => {
        //         $timeout(() => {
        //             const modal = document.getElementById('question_editor_modal');
        //             if (modal) {
        //                 $compile(modal)($scope);
        //                 $(modal).find('.select2').select2()
        //                 $scope.$apply();
        //             } else {
        //                 console.error('#question_editor_modal not found');
        //             }
        //         }, 150);
        //     });
        // }

        $scope.editQuestion = function (id) {
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

        // Process questions and nest them in sections
        $scope.getSectionaizedQuestions = function () {
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
                let sectionNames = [];
                let hasSectionIds = false;

                if (question.sectionIds && question.sectionIds.length > 0) {
                    hasSectionIds = true;
                    question.sectionIds.forEach(function (sectionId) {
                        let section = $scope.sections.find(function (s) {
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

                if (!question.sectionIds || (question.sectionIds && question.sectionIds.length === 0)) {
                    hasSectionIds = false;
                    let section = {
                        description: '',
                        exam_id: 1,
                        id: 6,
                        order: $scope.organaizedSections.length + 1,
                        questions: [],
                        second_description: "",
                        title: ""
                    }

                    if (section) {
                        // Add question to section
                        section.questions = section.questions || [];
                        if (!section.questions.find(q => q.id === question.id)) {
                            section.questions.push(question);
                        }
                    }
                }

                question.sectionNames = sectionNames.join(', ');
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

        $scope.selectOption = function (questionId, oIndex) {
            const question = $scope.allQuestions.find(q => q.id === questionId);
            question.selectedOption = oIndex;
            let realIndex = $scope.allQuestions.findIndex(q => q.id === question.id);
            if (realIndex !== -1) {
                $scope.allQuestions[realIndex].selectedOption = oIndex;
            }
        };

        // Step navigation
        $scope.nextStep = function () {
            if ($scope.currentStep < 5) {
                $scope.currentStep++;
                $scope.updateStepCompletion();
                if ($scope.currentStep === 3) {
                    $scope.questionsDisplayMode = 'all';
                    $scope.getCurrentPageQuestions();
                    $scope.updateQuestionsDisplay();
                    $scope.getSectionaizedQuestions();
                }
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
            $scope.step5Completed = $scope.examData && $scope.examData.status === 'published';
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

            Toast.popover({
                type: 'confirm',
                title: 'Publish!',
                titleColor: '#65deff',
                content: `
                    <i class="fa-solid fa-circle-info" style="font-size: 3rem; color: #0e7490"></i><br><br>
                    <p>Are you sure you want to publish this exam? It will be available to candidates.</p>
                `,
                contentColor: '#fff',
                options: {
                    confirm: {
                        text: 'Yes, publish it!',
                        background: '#0e7490',

                        onConfirm: async function () {
                            try {
                                const response = await $http.post(
                                    window.baseUrl + '/API/publish_exam/' + $scope.location.exam
                                );

                                if (response.data.status === 'success') {

                                    Toast.fire({
                                        type: 'success',
                                        title: 'Exam Published!',
                                        msg: 'Exam published successfully!'
                                    });

                                    $scope.examData.status = 'published';
                                    $scope.examData.published_at = new Date().toISOString();
                                    $scope.step5Completed = true;

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
                                    msg: 'API not response'
                                });
                            }

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

        // Unpublish exam
        $scope.unpublishExam = function () {
            Toast.popover({
                type: 'confirm',
                title: 'Unpublish!',
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
                if ($scope.examData.status === 'published') {
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

        // Initialize on load
        $scope.init();
    }
]);