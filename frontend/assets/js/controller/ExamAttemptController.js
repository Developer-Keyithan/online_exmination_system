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
        $scope.remainingCountdown = '00:00:00'
        $scope.timeRemainingFormatted = null;
        $scope.timerWarning = false;
        $scope.timeExpired = false;
        $scope.allowedRulesViolationsCount = 3;
        $scope.rulesViolatedCount = 0;
        $scope.violations = [];
        $scope.showViolationModal = false;
        $scope.answeredCount = 0;
        $scope.flaggedCount = 0;
        $scope.showReviewModal = false;
        $scope.showSubmitConfirmation = false;
        $scope.showSuccessModal = false;
        $scope.submissionTime = null;
        $scope.timeTaken = 0;
        $scope.timeTakenFormatted = "00:00:00";
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
        $scope.showExamStartModal = false;
        $scope.examStartWarning = false;
        $scope.isExamRunning = false;
        $scope.isCountdownInitialized = false;
        $scope.isTimerInitialized = false;
        $scope.selectedAnswers = null;


        // Initialize exam
        $scope.init = async function () {
            $scope.loading = true;
            $scope.showEligibilityModal = false;
            $scope.eligibilityError = null;
            // Preload warning audio
            $scope.startWarningAudio = await new Audio(window.baseUrl + '/frontend/assets/sounds/start_waring.mp3');
            $scope.endWarningAudio = await new Audio(window.baseUrl + '/frontend/assets/sounds/end_waring.mp3');
            $scope.startWarningAudio.volume = 0.3;
            $scope.endWarningAudio.volume = 0.3;

            // Play-pause once to give browser permission
            $scope.startWarningAudio.play().catch(() => { });
            $scope.endWarningAudio.play().catch(() => { });
            $scope.startWarningAudio.pause();
            $scope.endWarningAudio.pause();
            $scope.startWarningAudio.currentTime = 0;
            $scope.endWarningAudio.currentTime = 0;

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
                    .then(async function (response) {
                        $scope.loading = false;

                        if (response.data.status === 'success' && response.data.isEligible) {
                            $scope.isEligible = true;
                            await $scope.loadExamMetaData();
                            $scope.setupSecurityFeatures();
                        } else {
                            // Set error information
                            $scope.eligibilityError = {
                                code: response.data.code || 'UNKNOWN_ERROR',
                                msg: response.data.msg || 'An unknown error occurred',
                                title: getErrorTitle(response.data.code),
                                timestamp: new Date()
                            };

                            if (response.data.code === 'EXAM_NOT_STARTED') {
                                $scope.timeData = {
                                    start_time: response.data.start_time ? new Date(response.data.start_time) : null
                                };

                                $scope.remainingCountdown = Math.floor(
                                    ($scope.timeData.start_time - new Date()) / 1000
                                );
                                $scope.initializeExamCountdown(
                                    Math.max(0, $scope.remainingCountdown)
                                );
                            }

                            $scope.showEligibilityModal = true;
                        }
                    })
                    .catch(function (error) {
                        console.error(error);
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
                        title: data.title,
                        code: data.code.replace(/ /g, "_"),
                        duration: data.duration,
                        total_questions: data.total_questions,
                        total_marks: data.total_marks,
                        instructions: $sce.trustAsHtml(data.instructions || ''),
                        schedule_type: data.schedule_type,

                        start_time: data.start_time ? new Date(data.start_time) : null,
                        end_time: data.start_time ? new Date(new Date(data.start_time).getTime() + data.duration * 60 * 1000) : null,
                        started_at: (data.schedule_type === 'anytime' && data.started_at) ? new Date(data.started_at) : new Date(),

                        max_attempts: data.max_attempts > 0 ? data.max_attempts : 1,
                        allow_retake: data.allow_retake,
                        total_attempts: data.total_attempts,
                        disable_right_click: data.disable_right_click,

                        isAlreadyTaken: data.isAlredyTaken,
                        isProgress: data.isProgress,
                        isCompleted: data.isCompleted,
                        isAbandoned: data.isAbandoned,
                    };
                    const now = new Date();

                    if (data.allow_retake && data.max_attempts >= data.total_attempts) {
                        // ANYTIME
                        if ($scope.examData.schedule_type === 'anytime') {
                            $scope.showExamStartModal = true;
                        }

                        // SCHEDULED
                        if ($scope.examData.schedule_type === 'scheduled') {
                            if (now < $scope.examData.start_time) {
                                console.log('Exam not started yet');
                                $scope.isExamStarted = false;
                                $scope.isExamEnded = false;
                            } else if (now >= $scope.examData.start_time && now <= $scope.examData.end_time) {
                                console.log('Exam can be started now');
                                $scope.showExamStartModal = true;
                                
                            } else if (now > $scope.examData.end_time) {
                                console.log('Exam has ended');
                                $scope.isExamStarted = false;
                                $scope.isExamEnded = true;
                            }
                        }
                    }


                    console.log('Exam Modal: ', $scope.showExamStartModal, 'Loading: ', $scope.loading, 'showEligibilityModal: ', $scope.showEligibilityModal);

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
        $scope.startExam = async () => {
            $scope.isExamStarted = true;
            $scope.isExamEnded = false;
            $scope.showExamStartModal = false;
            await $scope.loadExamData();
            $scope.isExamRunning = true;
            $scope.setupSecurityFeatures();
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

                    $scope.examData = angular.extend($scope.examData || {}, {
                        passing_marks: data.passing_marks,
                        passing_persentage: (data.passing_marks / data.total_marks) * 100,
                        shuffle_questions: data.shuffle_questions,
                        shuffle_options: data.shuffle_options,
                        full_screen_mode: data.full_screen_mode,
                        disable_copy_paste: data.disable_copy_paste,
                        show_results_immediately: data.show_results_immediately,
                        attempt_id: data.attempt_id
                    });

                    $scope.attemptId = data.attempt_id;

                    $scope.selectedAnswers = examResponse.data.answers;


                    // Process questions
                    $scope.processQuestions(examResponse.data.questions, examResponse.data.sections);
                    // Initialize timer
                    // $scope.timeRemaining = $scope.examData.duration * 60;
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
                    $scope.initializeTimer($scope.timeRemaining);

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

                // find selected answer ONCE (safe)
                const selected = $scope.selectedAnswers.find(
                    ans => ans.question_id === question.id
                ) || {};

                if (selected) {
                    $scope.answeredCount++
                };
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
                    answer: selected.answer || null,
                    flagged: selected.flagged || false,
                    order: index + 1,
                    sectionIds: question.sectionIconsods || [],
                    grid: question.grid || 1,
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

        // Initialize exam countdown
        $scope.initializeExamCountdown = function (timeRemainingSeconds) {
            if (!$scope.isCountdownInitialized) {
                $scope.isCountdownInitialized = true;
                $scope.remainingCountdown = timeRemainingSeconds;
                $scope.updateTimerDisplay($scope.remainingCountdown);

                $scope.examStartTime = new Date();
                $scope.examStartTime.setSeconds($scope.examStartTime.getSeconds() + $scope.remainingCountdown);

                $scope.timerInterval = $interval(() => {
                    $scope.remainingCountdown--;
                    $scope.updateTimerDisplay($scope.remainingCountdown);

                    // Check for warnings
                    if ($scope.remainingCountdown <= 300 && !$scope.examStartWarning) { // 5 minutes
                        $scope.examStartWarning = true;
                        // Play warning sound
                        $scope.startWarningAudio.play().catch(() => { });
                    }

                    // Check if time expired
                    if ($scope.remainingCountdown < 0) {
                        $interval.cancel($scope.timerInterval);
                        setTimeout(() => {
                            $scope.retryEligibilityCheck();
                        }, 1000);
                    }
                }, 1000);
            }
        }

        // Initialize timer
        $scope.initializeTimer = function (timeRemainingSeconds) {
            if (!$scope.isTimerInitialized) {
                $scope.isTimerInitialized = true;
                $scope.timeRemaining = timeRemainingSeconds;
                $scope.updateTimerDisplay($scope.timeRemaining);

                $scope.examEndTime = new Date();
                $scope.examEndTime.setSeconds($scope.examEndTime.getSeconds() + $scope.timeRemaining);

                $scope.timerInterval = $interval(() => {
                    if (!$scope.timeExpired) $scope.timeRemaining--; // Decrease time remaining by 1 second
                    $scope.updateTimerDisplay($scope.timeRemaining);

                    // Check for warnings
                    if ($scope.timeRemaining === 600 && !$scope.timerWarning) { // 5 minutes
                        // $scope.timerWarning = true;
                        $scope.endWarningAudio.play().catch(() => { });
                        Toast.fire({
                            type: 'warning',
                            title: 'Exam Warning',
                            text: 'You have 10 minutes remaining!'
                        })
                    }

                    if ($scope.timeRemaining === 300 && !$scope.timerWarning) { // 5 minutes
                        // $scope.timerWarning = true;
                        $scope.endWarningAudio.play().catch(() => { });
                        Toast.fire({
                            type: 'warning',
                            title: 'Exam Warning',
                            text: 'You have 5 minutes remaining!'
                        })
                    }

                    if ($scope.timeRemaining === 60 && !$scope.timerWarning) { // 5 minutes
                        // $scope.timerWarning = true;
                        $scope.endWarningAudio.play().catch(() => { });
                        Toast.fire({
                            type: 'warning',
                            title: 'Exam Warning',
                            text: 'You have 5 minutes remaining!'
                        })
                    }

                    if ($scope.timeRemaining <= 60 && !$scope.timerWarning) { // 5 minutes
                        $scope.timerWarning = true;
                        // Play warning sound
                        $scope.endWarningAudio.play().catch(() => { });
                    }

                    // Check if time expired
                    if ($scope.timeRemaining <= 0 && !$scope.timeExpired) {
                        $scope.timeExpired = true;
                        $interval.cancel($scope.timerInterval);
                        $scope.removeAllSecurityFeatures();
                        $scope.submitExam();
                        // $timeout(() => {
                        //     $scope.showExpiredModal = false;
                        //     $scope.showSuccessModal = true;
                        // }, 3000);
                    }
                }, 1000);
            }
        };

        // Update timer display
        $scope.updateTimerDisplay = function (time) {
            const hours = Math.floor(time / 3600);
            const minutes = Math.floor((time % 3600) / 60);
            const seconds = time % 60;

            $scope.timeRemainingFormatted =
                hours.toString().padStart(2, '0') + ':' +
                minutes.toString().padStart(2, '0') + ':' +
                seconds.toString().padStart(2, '0');
        };

        // Update counts
        $scope.updateCounts = function () {
            $scope.answeredCount = $scope.questions.filter(q => q.answer !== null).length;
            $scope.flaggedCount = $scope.questions.filter(q => q.flagged).length;
            $scope.progressPercentage = $scope.examData.total_questions ? Math.min(100, Math.round(($scope.answeredCount / $scope.examData.total_questions) * 100)) : 0
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
        $scope.selectAnswer = function (answer, flagged = false) {
            const formData = new FormData();
            formData.append('answer', answer);
            formData.append('flagged', flagged);

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
                        $scope.currentQuestion.flagged = res.flagged;
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
            $scope.selectAnswer(null, $scope.currentQuestion.flagged);
            Toast.fire({
                type: 'warning',
                title: 'Answer Cleared',
                msg: 'Your answer has been cleared.',
                timer: 1500
            });
        };
        
        $scope.flagCurrentQuestion = function () {
            // Toggle local flagged state
            $scope.currentQuestion.flagged = !$scope.currentQuestion.flagged;

            const formData = new FormData();
            formData.append('answer', $scope.currentQuestion.answer);
            formData.append('flagged', $scope.currentQuestion.flagged);

            let attempts = 0;
            const maxAttempts = 5;

            function sendFlag() {
                $http({
                    url: window.baseUrl + '/API/exam/' + $scope.examId + '/attempt/' + $scope.attemptId + '/question/' + $scope.currentQuestion.id + '/answer',
                    method: 'POST',
                    data: formData,
                    headers: { 'Content-Type': undefined }
                }).then(function (response) {
                    const res = response.data;

                    if (res.status !== 'success') {
                        // Revert flagged if failed
                        $scope.currentQuestion.flagged = !$scope.currentQuestion.flagged;
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: res.msg || 'Failed to update flagged status. Try again.'
                        });
                    } else {
                        Toast.fire({
                            type: $scope.currentQuestion.flagged ? 'warning' : 'info',
                            title: $scope.currentQuestion.flagged ? 'Question Flagged' : 'Question Unflagged',
                            msg: $scope.currentQuestion.flagged ?
                                'Question has been flagged for review' :
                                'Question has been unflagged',
                            timer: 1500
                        });
                    }

                    $scope.updateCounts();
                }).catch(function (error) {
                    attempts++;
                    if (attempts < maxAttempts) {
                        console.warn('Retrying to flag question, attempt', attempts);
                        setTimeout(sendFlag, 500);
                    } else {
                        console.error('Error flagging question after 5 attempts:', error);
                        $scope.currentQuestion.flagged = !$scope.currentQuestion.flagged;
                        Toast.fire({
                            type: 'error',
                            title: 'Error!',
                            msg: 'Failed to update flagged status. Please try again.'
                        });
                        $scope.updateCounts();
                    }
                });
            }

            sendFlag();
        };


        $scope.saveAndMark = function () {
            $scope.currentQuestion.flagged = true;
            $scope.selectAnswer($scope.currentQuestion.answer, $scope.currentQuestion.flagged);
            $scope.nextQuestion();
        };

        $scope.saveAndNext = function () {
            $scope.selectAnswer($scope.currentQuestion.answer, $scope.currentQuestion.flagged);
            $scope.nextQuestion();
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
            $scope.selectAnswer($scope.currentQuestion.answer, $scope.currentQuestion.flagged);
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

        $scope.submitExam = async function (reason = 'completed') {
            $scope.isSubmitting = true;
            $scope.showSubmitConfirmation = false;
            try {
                // Calculate time taken
                $scope.timeTaken = ($scope.examData.duration * 60) - $scope.timeRemaining;
                $scope.timeTakenFormatted = $scope.formatTime($scope.timeTaken);
                $scope.submissionTime = new Date();
                let totalSeconds = $scope.timeRemaining;

                let hours = Math.floor(totalSeconds / 3600);
                let minutes = Math.floor((totalSeconds % 3600) / 60);
                let seconds = totalSeconds % 60;

                const formData = new FormData();
                formData.append('time_remaining', (hours < 10 ? '0' + hours : hours) + ':' + (minutes < 10 ? '0' + minutes : minutes) + ':' + (seconds < 10 ? '0' + seconds : seconds));
                formData.append('reason', reason);
                // formData.append('time_remaining', $scope.formatTime($scope.timeRemaining));


                const response = await $http({
                    url: window.baseUrl + '/API/exam/submit/' + $scope.examId + '/' + $scope.attemptId,
                    method: 'POST',
                    data: formData,
                    headers: { 'Content-Type': undefined }
                });

                if (response.data.status === 'success') {
                    // Clear intervals
                    $interval.cancel($scope.timerInterval);
                    $interval.cancel($scope.autoSaveInterval);

                    $scope.isSubmitted = true;
                    $scope.estimatedScore = response.data.score;
                    $scope.showSuccessModal = true;
                    $scope.$apply();
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

        let tabSwitchCount = 0;
        let fullscreenCount = 0;
        let copyCount = 0;
        let cutCount = 0;
        let pasteCount = 0;

        $scope.recordViolation = function (type, message, listMessage) {
            let count = 0;

            // Increment count and track per-type
            if (['tab-switch', 'fullscreen', 'copy', 'cut', 'paste'].includes(type)) {
                $scope.rulesViolatedCount++;
                switch (type) {
                    case 'tab-switch': tabSwitchCount++; count = tabSwitchCount; break;
                    case 'fullscreen': fullscreenCount++; count = fullscreenCount; break;
                    case 'copy': copyCount++; count = copyCount; break;
                    case 'cut': cutCount++; count = cutCount; break;
                    case 'paste': pasteCount++; count = pasteCount; break;
                }
            }

            // Add to violation list
            $scope.violations.push({
                type: type,
                message: listMessage,
                time: new Date(),
                count: count
            });

            Toast.fire({
                type: 'warning',
                title: 'Action Restricted',
                msg: `${message} Violations: (${$scope.rulesViolatedCount}/${$scope.allowedRulesViolationsCount})`,
            });

            // End exam if rules exceeded
            if ($scope.rulesViolatedCount >= $scope.allowedRulesViolationsCount) {
                $scope.endExamDueToRulesViolation();
            }
        };

        $scope.endExamDueToRulesViolation = function () {
            // Prevent multiple calls
            if ($scope.examEnded) return;
            $scope.examEnded = true;

            // Stop timers
            if ($scope.timerInterval) {
                $interval.cancel($scope.timerInterval);
                $scope.timerInterval = null;
            }
            if ($scope.autoSaveInterval) {
                $interval.cancel($scope.autoSaveInterval);
                $scope.autoSaveInterval = null;
            }

            // Remove security features
            $scope.removeAllSecurityFeatures();

            // Show **violation modal**
            $scope.showViolationModal = true;
            $scope.$apply();

            $timeout(() => {
                $scope.submitExam('rules_violation');
            }, 3000);
        };

        // ---------- STORED HANDLERS ----------
        $scope._onContextMenu = function (e) {
            e.preventDefault();
            Toast.fire({
                type: 'warning',
                title: 'Action Restricted',
                msg: 'Right click is disabled during the exam.',
            });
        };

        $scope._onCopy = function (e) {
            e.preventDefault();
            $scope.recordViolation('copy', 'Copy action is disabled during the exam.', 'Attempted to copy text');
        };

        $scope._onPaste = function (e) {
            e.preventDefault();
            $scope.recordViolation('paste', 'Paste action is disabled during the exam.', 'Attempted to paste content');
        };

        $scope._onCut = function (e) {
            e.preventDefault();
            $scope.recordViolation('cut', 'Cut action is disabled during the exam.', 'Attempted to cut text');
        };

        $scope._onFullscreenChange = function () {
            if ($scope.timeExpired) return;
            if (!document.fullscreenElement) {
                $scope.recordViolation('fullscreen', 'You must stay in fullscreen mode.', 'Exited fullscreen mode');
                $timeout(() => { document.documentElement.requestFullscreen(); }, 1000);
            }
        };

        $scope._onWindowBlur = function () {
            if ($scope.timeExpired) return;
            $scope.recordViolation('tab-switch', 'Switching tabs is not allowed.', 'Switched to another tab/window');
        };

        $scope._onWindowFocus = function () { };

        $scope._onBeforeUnload = function (e) {
            if (!$scope.isSubmitting && !$scope.showSuccessModal && $scope.answeredCount > 0) {
                e.preventDefault();
                e.returnValue = 'You have unsaved answers. Are you sure you want to leave?';
            }
        };


        // Security features for demo
        $scope.setupSecurityFeatures = function () {

            if ($scope.examData.disable_right_click) {
                document.addEventListener('contextmenu', $scope._onContextMenu);
            }

            if (!$scope.isExamRunning) return;

            if ($scope.examData.disable_copy_paste) {
                document.addEventListener('copy', $scope._onCopy);
                document.addEventListener('paste', $scope._onPaste);
                document.addEventListener('cut', $scope._onCut);
            }

            if ($scope.examData.full_screen_mode) {
                document.documentElement.requestFullscreen();
                document.addEventListener('fullscreenchange', $scope._onFullscreenChange);
            }

            $scope.tabSwitchCount = 0;
            window.addEventListener('blur', $scope._onWindowBlur);
            window.addEventListener('focus', $scope._onWindowFocus);

            window.addEventListener('beforeunload', $scope._onBeforeUnload);
        };

        // Cleanup
        $scope.removeAllSecurityFeatures = function () {

            // document.removeEventListener('contextmenu', $scope._onContextMenu);
            document.removeEventListener('copy', $scope._onCopy);
            document.removeEventListener('paste', $scope._onPaste);
            document.removeEventListener('cut', $scope._onCut);
            document.removeEventListener('fullscreenchange', $scope._onFullscreenChange);

            window.removeEventListener('blur', $scope._onWindowBlur);
            window.removeEventListener('focus', $scope._onWindowFocus);
            window.removeEventListener('beforeunload', $scope._onBeforeUnload);

            // Exit fullscreen if active
            if (document.fullscreenElement) {
                document.exitFullscreen();
            }
        };

        $scope.init();
    }
]);