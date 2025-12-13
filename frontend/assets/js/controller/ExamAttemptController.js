app.controller('ExamAttemptController', [
    "$scope", "$http", "$compile", "$timeout", "window", "$sce", "$interval",
    function ($scope, $http, $compile, $timeout, window, $sce, $interval) {
        // Initialize scope variables
        $scope.examId = window.getIdFromUrl();
        $scope.loading = true;
        $scope.error = null;
        $scope.examData = null;
        $scope.questions = [];
        $scope.currentQuestionIndex = 0;
        $scope.currentQuestion = null;
        $scope.timeRemaining = null;
        $scope.timeRemainingFormatted = null;
        $scope.timerWarning = false;
        $scope.timeExpired = false;
        $scope.answeredCount = 0;
        $scope.flaggedCount = 0;
        $scope.showReviewModal = false;
        $scope.showSubmitConfirmation = false;
        $scope.showSuccessModal = false;
        $scope.submissionTime = null;
        $scope.timeTaken = 0;
        $scope.timeTakenFormatted = "00:00";
        $scope.examStartedAt = null;
        $scope.examEndTime = null;
        $scope.timerInterval = null;
        $scope.autoSaveInterval = null;
        $scope.isSubmitting = false;
        $scope.attemptId = null;
        $scope.estimatedScore = 0;
        $scope.showEligibilityModal = false;
        $scope.eligibilityError = null;
        $scope.currentDate = new Date();
        $scope.isAlreadyTaken = false;
        $scope.isProgress = false;
        $scope.isCompleted = false;
        $scope.isAbandoned = false;


        // Initialize exam
        $scope.init = function () {
            $scope.loading = true;
            $scope.showEligibilityModal = false;
            $scope.eligibilityError = null;

            // First check eligibility
            $scope.checkUserEligibility();
        };

        // Check the user(student) eligibility for the exam
        $scope.checkUserEligibility = function () {
            $scope.loading = true;
            $scope.showEligibilityModal = false;
            $scope.eligibilityError = null;

            try {
                $http.get(window.baseUrl + "/API/exam/eligibility/" + $scope.examId)
                    .then(function (response) {
                        $scope.loading = false;

                        if (response.data.status === 'success' && response.data.isEligible) {
                            $scope.isEligible = true;
                            $scope.loadExamMetaData();
                        } else {
                            // Set error information
                            $scope.eligibilityError = {
                                code: response.data.code || 'UNKNOWN_ERROR',
                                msg: response.data.msg || 'An unknown error occurred',
                                title: getErrorTitle(response.data.code),
                                timestamp: new Date()
                            };

                            $scope.showEligibilityModal = true;
                        }
                    })
                    .catch(function (error) {
                        $scope.loading = false;
                        $scope.eligibilityError = {
                            code: 'NETWORK_ERROR',
                            msg: 'Failed to connect to server. Please check your internet connection.',
                            title: 'Connection Error',
                            timestamp: new Date()
                        };
                        $scope.showEligibilityModal = true;
                    });
            } catch (error) {
                $scope.loading = false;
                $scope.eligibilityError = {
                    code: 'CLIENT_ERROR',
                    msg: 'An error occurred while checking eligibility.',
                    title: 'Client Error',
                    timestamp: new Date()
                };
                $scope.showEligibilityModal = true;
                console.error('Eligibility check error:', error);
            }
        };

        // Helper function to get error titles
        function getErrorTitle(errorCode) {
            const errorTitles = {
                'EXAM_NOT_FOUND': 'Exam Not Found',
                'EXAM_NOT_PUBLISHED': 'Exam Not Published',
                'EXAM_CANCELED': 'Exam Canceled',
                'EXAM_NOT_STARTED': 'Exam Not Started',
                'EXAM_ENDED': 'Exam Ended',
                'NOT_REGISTERED': 'Not Registered',
                'MAX_ATTEMPTS_EXCEEDED': 'Maximum Attempts Exceeded',
                'NETWORK_ERROR': 'Network Error',
                'CLIENT_ERROR': 'Client Error',
                'UNKNOWN_ERROR': 'Unknown Error'
            };

            return errorTitles[errorCode] || 'Access Denied';
        }

        // Method to retry eligibility check
        $scope.retryEligibilityCheck = function () {
            $scope.checkUserEligibility();
        };

        // Method to contact support
        $scope.contactSupport = function () {
            // Implement support contact logic
            window.location.href = window.baseUrl + '/support';
        };

        // Load exam data
        $scope.loadExamMetaData = async function () {
            try {
                // Load exam details
                const examResponse = await $http.get(
                    window.baseUrl + '/API/exam/attempt/meta_data/' + $scope.examId
                );

                if (examResponse.data.status === 'success') {
                    const data = examResponse.data.exam_info

                    $scope.examData = {
                        id: data.id,
                        title: data.title.replace(/ /g, "_"),
                        code: data.code.replace(/ /g, "_"),
                        duration: data.duration,
                        total_questions: data.total_questions,
                        total_marks: data.total_marks,
                        instructions: $sce.trustAsHtml(data.instructions || ''),
                        schedule_type: data.schedule_type,

                        start_time: data.start_time ? new Date(data.start_time) : null,
                        end_time: data.start_time ? new Date(new Date(data.start_time).getTime() + data.duration * 60 * 1000) : null,
                        started_at: (data.schedule_type === 'anytime' && data.started_at) ? new Date(data.started_at) : new Date(), // only for schedule type anytime

                        max_attempts: data.max_attempts > 0 ? data.max_attempts : 1,
                        allow_retake: data.allow_retake,

                        isAlreadyTaken: data.isAlredyTaken,
                        isProgress: data.isProgress,
                        isCompleted: data.isCompleted,
                        isAbandoned: data.isAbandoned,
                    };
                    const now = new Date();

                    // ANYTIME
                    if ($scope.examData.schedule_type === 'anytime') {
                        $scope.startExam();
                    }

                    // SCHEDULED
                    if ($scope.examData.schedule_type === 'scheduled') {

                        if (now < $scope.examData.start_time) {
                            $scope.isExamStarted = false;
                            $scope.isExamEnded = false;
                        } else if (now >= $scope.examData.start_time && now <= $scope.examData.end_time) {
                            $scope.startExam();

                        } else if (now > $scope.examData.end_time) {
                            $scope.isExamStarted = false;
                            $scope.isExamEnded = true;
                        }
                    }

                    $scope.loading = false;
                    $scope.$apply();
                } else {
                    throw new Error(examResponse.data.message || 'Failed to load exam data');
                }
            } catch (error) {
                console.error('Error loading exam:', error);
            }
        };

        // Start Exam
        $scope.startExam = () => {
            $scope.isExamStarted = true;
            $scope.isExamEnded = false;
            $scope.loadExamData();
        }

        // load other exam data
        $scope.loadExamData = async function () {
            try {
                // Load exam details
                const examResponse = await $http.get(
                    window.baseUrl + '/API/exam/attempt/' + $scope.examId
                );

                if (examResponse.data.status === 'success') {
                    const data = examResponse.data.rest_exam_info

                    $scope.examData =angular.extend($scope.examData || {}, {
                        passing_marks: data.passing_marks,
                        passing_persentage: (data.passing_marks / data.total_marks) * 100,
                        shuffle_questions: data.shuffle_questions,
                        shuffle_options: data.shuffle_options,
                        full_screen_mode: data.full_screen_mode,
                        disable_copy_paste: data.disable_copy_paste,
                        disable_right_click: data.disable_right_click,
                        show_results_immediately: data.show_results_immediately,
                    });


                    // Process questions
                    $scope.processQuestions(examResponse.data.questions, examResponse.data.sections);
                    // Initialize timer
                    $scope.timeRemaining = $scope.examData.duration * 60;
                    $scope.initializeTimer($scope.timeRemaining);

                    // Start auto-save
                    $scope.startAutoSave();
                    $scope.loading = false;
                    $scope.$apply();
                } else {
                    throw new Error(examResponse.data.message || 'Failed to load exam data');
                }
            } catch (error) {
                console.error('Error loading exam:', error);
            }
        };

        // Process questions
        $scope.processQuestions = function (questionsData, sectionsData) {
            let processedQuestions = questionsData.map((question, index) => {
                // Shuffle options if enabled
                let options = question.options || [];
                if ($scope.examData.shuffle_options) {
                    options = $scope.shuffleArray([...options]);
                }

                return {
                    id: question.id,
                    question: question.question || $sce.trustAsHtml(question.text || ''),
                    // image: question.image,
                    options: options.map(opt => ({
                        op: opt.op,
                        text: opt.text || $sce.trustAsHtml(opt.label || ''),
                        image: opt.image,
                        // explanation: opt.explanation
                    })),
                    marks: question.marks,
                    // difficulty: question.difficulty,
                    answer: question.user_answer || null,
                    flagged: question.flagged || false,
                    order: index + 1,
                    sectionIds: question.sectionIconsods || []
                };
            });


            // Create sections with questions
            $scope.getSectionaizedQuestions(processedQuestions, sectionsData);

            // Shuffle sections if enabled
            if ($scope.examData.shuffle_questions) {
                $scope.reviewSections = $scope.shuffleArray($scope.reviewSections);
            }

            $scope.reviewSections.forEach(section => {
                const questions = section.questions;
                questions.forEach((question) => {
                    question.sectionDescription = section.description;
                    question.sectionSecondDescription = section.secondDescription;
                    $scope.questions.push(question);
                })
            })

            $scope.currentQuestion = $scope.questions[0];
            $scope.updateCounts();
            $scope.calculateEstimatedScore();
        };

        // Shuffle array
        $scope.shuffleArray = function (array) {
            const shuffled = [...array];
            for (let i = shuffled.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
            }
            return shuffled;
        };

        // Transform questions into reviewSections structure
        $scope.getSectionaizedQuestions = function (questions, sections = []) {

            // Reset questions in each section
            sections.forEach(section => section.questions = []);

            if (!questions || questions.length === 0) {
                $scope.reviewSections = sections;
                return;
            }

            let reviewSectionsMap = {};
            let reviewSectionsOrder = [];

            questions.forEach(question => {
                let assigned = false;

                // Assign to existing sections if sectionIds exist and match
                if (question.sectionIds && question.sectionIds.length > 0) {
                    for (let counter = 0; counter < question.sectionIds.length; counter++) {
                        let section = sections.find(s => s.id === question.sectionIds[counter]);
                        if (section) {
                            section.questions = section.questions || [];
                            section.questions.push(question);
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
                        exam_id: +$scope.examId,
                        description: '',
                        questions: [question],
                        order: sections.length + 1,
                        second_description: ''
                    };

                    sections.push(newSection);

                    reviewSectionsMap[newSection.id] = newSection;
                    reviewSectionsOrder.push(newSection);
                }
            });

            $scope.reviewSections = reviewSectionsOrder;

            // Sort questions inside each section by original question order
            $scope.reviewSections.forEach(section => {
                if (section.questions) {
                    section.questions = section.questions.slice().sort((a, b) => (a.order || 0) - (b.order || 0));
                }
            });
        };

        // Initialize timer
        $scope.initializeTimer = function (timeRemainingSeconds) {
            $scope.timeRemaining = timeRemainingSeconds;
            $scope.updateTimerDisplay();

            $scope.examEndTime = new Date();
            $scope.examEndTime.setSeconds($scope.examEndTime.getSeconds() + $scope.timeRemaining);

            $scope.timerInterval = $interval(() => {
                $scope.timeRemaining--;
                $scope.updateTimerDisplay();

                // Check for warnings
                if ($scope.timeRemaining <= 300 && !$scope.timerWarning) { // 5 minutes
                    $scope.timerWarning = true;
                    // Play warning sound
                    try {
                        const audio = new Audio('https://assets.mixkit.co/sfx/preview/mixkit-alarm-digital-clock-beep-989.mp3');
                        audio.volume = 0.3;
                        audio.play();
                    } catch (e) { }
                }

                // Check if time expired
                if ($scope.timeRemaining <= 0) {
                    $scope.timeExpired = true;
                    $interval.cancel($scope.timerInterval);
                    $scope.forceSubmit();
                }
            }, 1000);
        };

        // Update timer display
        $scope.updateTimerDisplay = function () {
            let endTime;

            if ($scope.examData.schedule_type === 'scheduled') {
                const startTime = new Date($scope.examData.start_time);
                endTime = new Date(startTime.getTime() + $scope.examData.duration * 60 * 1000);
            } else {
                const startTime = new Date($scope.examData.started_at);
                endTime = new Date(startTime.getTime() + $scope.examData.duration * 60 * 1000);
            }

            const currentTime = new Date();

            // Time remaining in seconds
            $scope.timeRemaining = Math.max(0, Math.floor((endTime - currentTime) / 1000));

            const hours = Math.floor($scope.timeRemaining / 3600);
            const minutes = Math.floor(($scope.timeRemaining % 3600) / 60);
            const seconds = $scope.timeRemaining % 60;

            $scope.timeRemainingFormatted =
                hours.toString().padStart(2, '0') + ':' +
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');
        };

        // Calculate estimated score
        $scope.calculateEstimatedScore = function () {
            let score = 0;
            $scope.questions.forEach(q => {
                if (q.answer) {
                    const selectedOption = q.options.find(opt => opt.op === q.answer);
                    if (selectedOption?.correct) {
                        score += q.marks;
                    } else if ($scope.examData?.negative_marking) {
                        score -= ($scope.examData.negative_mark || 1);
                    }
                }
            });
            $scope.estimatedScore = Math.max(0, score);
        };

        // Start auto-save
        $scope.startAutoSave = function () {
            $scope.autoSaveInterval = $interval(() => {
                $scope.saveProgress();
            }, 30000); // Auto-save every 30 seconds
        };

        // Save progress
        $scope.saveProgress = async function () {
            if ($scope.isSubmitting || $scope.showSuccessModal) return;

            try {
                const answers = $scope.questions.map(q => ({
                    question_id: q.id,
                    answer: q.answer,
                    flagged: q.flagged
                }));

                await $http.post(
                    window.baseUrl + '/API/exam/save-progress/' + $scope.attemptId,
                    { answers: answers, time_remaining: $scope.timeRemaining }
                );

                console.log('Progress saved at', new Date().toLocaleTimeString());
            } catch (error) {
                console.error('Error saving progress:', error);
            }
        };

        // Update counts
        $scope.updateCounts = function () {
            $scope.answeredCount = $scope.questions.filter(q => q.answer !== null).length;
            $scope.flaggedCount = $scope.questions.filter(q => q.flagged).length;
            $scope.calculateEstimatedScore();
        };

        // Navigation
        $scope.goToQuestion = function (index) {
            if (index >= 0 && index < $scope.questions.length) {
                $scope.currentQuestionIndex = index;
                $scope.currentQuestion = $scope.questions[index];
            }
        };

        $scope.previousQuestion = function () {
            if ($scope.currentQuestionIndex > 0) {
                $scope.goToQuestion($scope.currentQuestionIndex - 1);
            }
        };

        $scope.nextQuestion = function () {
            if ($scope.currentQuestionIndex < $scope.questions.length - 1) {
                $scope.goToQuestion($scope.currentQuestionIndex + 1);
            }
        };

        // Question actions
        $scope.selectAnswer = function (answer) {
            const formData = new FormData();
            formData.append('answer', answer);

            let attempts = 0;
            const maxAttempts = 5;

            function sendAnswer() {
                $http({
                    url: window.baseUrl + '/API/exam/' + $scope.examId + '/attempt/' + $scope.attemptId + '/question/' + $scope.currentQuestion.id + '/answer',
                    method: 'POST',
                    data: formData,
                    headers: { 'Content-Type': undefined }
                }).then(function (response) {
                    const res = response.data;

                    if (res.status === 'success') {
                        $scope.currentQuestion.answer = res.answer;
                    } else {
                        $scope.currentQuestion.answer = null;
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: res.msg || 'Failed to save the answer, please select again.'
                        });
                    }

                    $scope.updateCounts();
                }).catch(function (error) {
                    attempts++;
                    if (attempts < maxAttempts) {
                        console.warn('Retrying to save answer, attempt', attempts);
                        sendAnswer(); // retry
                    } else {
                        console.error('Error saving answer after 5 attempts:', error);
                        $scope.currentQuestion.answer = null;
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: 'Failed to save the answer, please select the answer again.'
                        });
                        $scope.updateCounts();
                    }
                });
            }

            sendAnswer();
        };


        $scope.clearAnswer = function () {
            $scope.currentQuestion.answer = null;
            $scope.updateCounts();
            Toast.fire({
                type: 'warning',
                title: 'Answer Cleared',
                msg: 'Your answer has been cleared.',
                timer: 1500
            });
        };

        $scope.flagCurrentQuestion = function () {
            $scope.currentQuestion.flagged = !$scope.currentQuestion.flagged;
            $scope.updateCounts();
            Toast.fire({
                type: $scope.currentQuestion.flagged ? 'warning' : 'info',
                title: $scope.currentQuestion.flagged ? 'Question Flagged' : 'Question Unflagged',
                msg: $scope.currentQuestion.flagged ?
                    'Question has been flagged for review' :
                    'Question has been unflagged',
                timer: 1500
            });
        };

        $scope.saveAnswer = function () {
            $scope.saveProgress();
            Toast.fire({
                type: 'success',
                title: 'Answer Saved',
                msg: 'Your answer has been saved successfully.',
                timer: 1500
            });
        };

        $scope.saveAndMark = function () {
            $scope.currentQuestion.flagged = true;
            $scope.saveProgress();
            $scope.nextQuestion();
        };

        $scope.saveAndNext = function () {
            $scope.saveProgress();
            $scope.nextQuestion();
        };

        $scope.saveAllAnswers = function () {
            $scope.saveProgress();
            Toast.fire({
                type: 'success',
                title: 'All Answers Saved',
                msg: 'All your answers have been saved.',
                timer: 1500
            });
        };

        // Review functions
        $scope.reviewExam = function () {
            $scope.showReviewModal = true;
        };

        $scope.closeReviewModal = function () {
            $scope.showReviewModal = false;
        };

        $scope.goToFirstUnanswered = function () {
            const firstUnanswered = $scope.questions.findIndex(q => q.answer === null);
            if (firstUnanswered !== -1) {
                $scope.goToQuestion(firstUnanswered);
                $scope.closeReviewModal();
            } else {
                Toast.fire({
                    type: 'info',
                    title: 'All Questions Answered',
                    msg: 'You have answered all questions!',
                    timer: 2000
                });
            }
        };

        $scope.goToFlaggedQuestions = function () {
            const firstFlagged = $scope.questions.findIndex(q => q.flagged);
            if (firstFlagged !== -1) {
                $scope.goToQuestion(firstFlagged);
                $scope.closeReviewModal();
            } else {
                Toast.fire({
                    type: 'info',
                    title: 'No Flagged Questions',
                    msg: 'You have not flagged any questions.',
                    timer: 2000
                });
            }
        };

        // Submit exam
        $scope.showSubmitModal = function () {
            $scope.showSubmitConfirmation = true;
        };

        $scope.cancelSubmit = function () {
            $scope.showSubmitConfirmation = false;
        };

        $scope.saveAndClose = function () {
            $scope.saveProgress();
            Toast.fire({
                type: 'info',
                title: 'Progress Saved',
                msg: 'Your progress has been saved. You can resume later.',
                timer: 2000
            });
            $timeout(() => {
                window.location.href = window.baseUrl + '/exams';
            }, 2000);
        };

        $scope.submitExam = async function () {
            $scope.isSubmitting = true;
            $scope.showSubmitConfirmation = false;

            try {
                // Calculate time taken
                $scope.timeTaken = ($scope.examData.duration * 60) - $scope.timeRemaining;
                $scope.timeTakenFormatted = $scope.formatTime($scope.timeTaken);
                $scope.submissionTime = new Date();

                // Prepare final submission
                const answers = $scope.questions.map(q => ({
                    question_id: q.id,
                    answer: q.answer
                }));

                const response = await $http.post(
                    window.baseUrl + '/API/exam/submit/' + $scope.attemptId,
                    {
                        answers: answers,
                        time_taken: $scope.timeTaken
                    }
                );

                if (response.data.status === 'success') {
                    // Clear intervals
                    $interval.cancel($scope.timerInterval);
                    $interval.cancel($scope.autoSaveInterval);

                    // Show success modal
                    $scope.showSuccessModal = true;
                } else {
                    throw new Error(response.data.message || 'Submission failed');
                }

            } catch (error) {
                console.error('Error submitting exam:', error);
                Toast.fire({
                    type: 'error',
                    title: 'Submission Failed',
                    msg: 'Failed to submit exam. Please try again.'
                });
            } finally {
                $scope.isSubmitting = false;
            }
        };

        $scope.forceSubmit = function () {
            if (!$scope.isSubmitting) {
                $scope.submitExam();
            }
        };

        $scope.closeSuccessModal = function () {
            $scope.showSuccessModal = false;
            window.location.href = window.baseUrl + '/dashboard';
        };

        // Helper functions
        $scope.getQuestionType = function (question) {
            return 'Multiple Choice';
        };

        $scope.formatTime = function (seconds) {
            const hours = Math.floor(seconds / 3600);
            const minutes = Math.floor((seconds % 3600) / 60);
            const secs = seconds % 60;

            if (hours > 0) {
                return `${hours}h ${minutes}m ${secs}s`;
            } else if (minutes > 0) {
                return `${minutes}m ${secs}s`;
            } else {
                return `${secs}s`;
            }
        };

        // Safe HTML filter
        $scope.safeHtml = function (text) {
            return $sce.trustAsHtml(text);
        };

        $scope.enterFullscreen = function () {
            const elem = document.documentElement;
            if (elem.requestFullscreen) {
                elem.requestFullscreen();
            } else if (elem.webkitRequestFullscreen) {
                elem.webkitRequestFullscreen();
            } else if (elem.msRequestFullscreen) {
                elem.msRequestFullscreen();
            }
        };

        // Security features for demo
        $scope.setupSecurityFeatures = function () {
            // Prevent right click if disabled
            if ($scope.examData?.disable_right_click) {
                // document.addEventListener('contextmenu', function (e) {
                //     e.preventDefault();
                //     Toast.fire({
                //         type: 'warning',
                //         title: 'Action Restricted',
                //         msg: 'Right click is disabled during the exam.',
                //         timer: 2000
                //     });
                // });
            }

            // Prevent copy/paste if disabled
            if ($scope.examData?.disable_copy_paste) {
                document.addEventListener('copy', function (e) {
                    e.preventDefault();
                    Toast.fire({
                        type: 'warning',
                        title: 'Action Restricted',
                        msg: 'Copying is disabled during the exam.',
                        timer: 2000
                    });
                });

                document.addEventListener('paste', function (e) {
                    e.preventDefault();
                    Toast.fire({
                        type: 'warning',
                        title: 'Action Restricted',
                        msg: 'Pasting is disabled during the exam.',
                        timer: 2000
                    });
                });

                document.addEventListener('cut', function (e) {
                    e.preventDefault();
                    Toast.fire({
                        type: 'warning',
                        title: 'Action Restricted',
                        msg: 'Cutting is disabled during the exam.',
                        timer: 2000
                    });
                });
            }

            // Full screen mode
            if ($scope.examData?.full_screen_mode) {

                // Try to enter full screen
                $('#fsBtn').click();

                // Monitor full screen changes
                document.addEventListener('fullscreenchange', function () {
                    if (!document.fullscreenElement) {
                        Toast.fire({
                            type: 'warning',
                            title: 'Full Screen Required',
                            msg: 'Please return to full screen mode to continue the exam.'
                        });
                        // Re-enter full screen after delay
                        // $timeout($('#fsBtn').click(), 1000);
                    }
                });
            }

            // Detect tab switching
            let isTabActive = true;
            window.addEventListener('blur', function () {
                if ($scope.examData?.full_screen_mode && !$scope.showSuccessModal) {
                    isTabActive = false;
                    Toast.fire({
                        type: 'warning',
                        title: 'Stay Focused!',
                        msg: 'Please do not switch tabs during the exam.',
                        timer: 3000
                    });
                }
            });

            window.addEventListener('focus', function () {
                isTabActive = true;
            });
        };

        // Before unload warning
        window.addEventListener('beforeunload', function (e) {
            if (!$scope.isSubmitting && !$scope.showSuccessModal && $scope.answeredCount > 0) {
                e.preventDefault();
                e.returnValue = 'You have unsaved answers. Are you sure you want to leave?';
                return e.returnValue;
            }
        });

        // Initialize after data is loaded
        $timeout(() => {
            if ($scope.examData) {
                $scope.setupSecurityFeatures();
            }
        }, 1000);

        // Cleanup
        $scope.$on('$destroy', function () {
            if ($scope.timerInterval) $interval.cancel($scope.timerInterval);
            if ($scope.autoSaveInterval) $interval.cancel($scope.autoSaveInterval);

            // Remove event listeners
            document.removeEventListener('contextmenu', () => { });
            document.removeEventListener('copy', () => { });
            document.removeEventListener('paste', () => { });
            document.removeEventListener('cut', () => { });
            document.removeEventListener('fullscreenchange', () => { });
            window.removeEventListener('blur', () => { });
            window.removeEventListener('focus', () => { });
            window.removeEventListener('beforeunload', () => { });
        });

        $scope.init();
    }
]);