app.controller('ExamAttemptController', [
    "$scope", "$http", "$compile", "$timeout", "window",
    function ($scope, $http, $compile, $timeout, window) {
    
    // Initialize function
    $scope.init = function() {
        // Load dummy data
        $scope.loadDummyData();
        $scope.startTimer();
        $scope.setupEventListeners();
        $scope.startAutoSave();
        $scope.updateSteps();
    };
    
    // Load dummy data
    $scope.loadDummyData = function() {
        // Exam basic data
        $scope.examData = {
            code: 'WEBDEV-2024-FINAL',
            title: 'Web Development Final Exam',
            duration: 60,
            total_marks: 100,
            passing_marks: 40,
            instructions: 'Complete all questions within the time limit.'
        };
        
        // Questions dummy data
        $scope.questions = [
            {
                id: 1,
                question: 'What does HTML stand for?',
                marks: 2,
                image: null,
                options: [
                    { id: 'a', text: 'Hyper Text Markup Language', selected: false },
                    { id: 'b', text: 'High Tech Modern Language', selected: false },
                    { id: 'c', text: 'Hyper Transfer Markup Language', selected: false },
                    { id: 'd', text: 'Home Tool Markup Language', selected: false }
                ],
                correctAnswer: 'a',
                userAnswer: null,
                answered: false,
                markedForReview: false,
                saved: false
            },
            {
                id: 2,
                question: 'Which of the following is a CSS framework?',
                marks: 2,
                image: null,
                options: [
                    { id: 'a', text: 'React', selected: false },
                    { id: 'b', text: 'Angular', selected: false },
                    { id: 'c', text: 'Tailwind CSS', selected: false },
                    { id: 'd', text: 'Vue.js', selected: false }
                ],
                correctAnswer: 'c',
                userAnswer: null,
                answered: false,
                markedForReview: false,
                saved: false
            },
            {
                id: 3,
                question: 'What is the purpose of JavaScript?',
                marks: 3,
                image: null,
                options: [
                    { id: 'a', text: 'Style web pages', selected: false },
                    { id: 'b', text: 'Add interactivity to web pages', selected: false },
                    { id: 'c', text: 'Structure web content', selected: false },
                    { id: 'd', text: 'Store database information', selected: false }
                ],
                correctAnswer: 'b',
                userAnswer: null,
                answered: false,
                markedForReview: false,
                saved: false
            },
            {
                id: 4,
                question: 'Which HTML tag is used for the largest heading?',
                marks: 1,
                image: null,
                options: [
                    { id: 'a', text: '<h6>' },
                    { id: 'b', text: '<heading>' },
                    { id: 'c', text: '<h1>' },
                    { id: 'd', text: '<head>' }
                ],
                correctAnswer: 'c',
                userAnswer: null,
                answered: false,
                markedForReview: false,
                saved: false
            },
            {
                id: 5,
                question: 'Which CSS property controls text size?',
                marks: 2,
                image: null,
                options: [
                    { id: 'a', text: 'font-style' },
                    { id: 'b', text: 'text-size' },
                    { id: 'c', text: 'font-size' },
                    { id: 'd', text: 'text-style' }
                ],
                correctAnswer: 'c',
                userAnswer: null,
                answered: false,
                markedForReview: false,
                saved: false
            }
        ];
        
        // Current question
        $scope.currentQuestionIndex = 0;
        $scope.currentQuestion = $scope.questions[0];
        
        // Timer settings
        $scope.totalTime = 60 * 60; // 60 minutes
        $scope.timeRemaining = $scope.totalTime;
        $scope.formattedTime = '60:00';
        
        // Status
        $scope.isOnline = navigator.onLine;
        $scope.dataSynced = true;
        $scope.autoSaving = false;
        $scope.showSubmitModal = false;
        $scope.confirmSubmit = false;
        $scope.realTimeUpdates = [];
        
        // Progress steps
        $scope.steps = [
            { title: 'Start', icon: 'fa-play', completed: true, active: false },
            { title: 'Questions', icon: 'fa-list', completed: false, active: true },
            { title: 'Review', icon: 'fa-check', completed: false, active: false },
            { title: 'Submit', icon: 'fa-paper-plane', completed: false, active: false }
        ];
        
        // Add initial update
        $scope.addUpdate('system', 'Exam loaded successfully');
    };
    
    // Timer functions
    $scope.startTimer = function() {
        $scope.timerInterval = $interval(function() {
            if ($scope.timeRemaining > 0) {
                $scope.timeRemaining--;
                $scope.updateFormattedTime();
                
                // Auto-save at specific intervals
                if ($scope.timeRemaining % 300 === 0) { // Every 5 minutes
                    $scope.saveProgress();
                }
                
                // Warn when 10 minutes left
                if ($scope.timeRemaining === 600) {
                    $scope.addUpdate('warning', '10 minutes remaining!');
                }
                
                // Warn when 5 minutes left
                if ($scope.timeRemaining === 300) {
                    $scope.addUpdate('warning', '5 minutes remaining!');
                }
                
                // Auto-submit when time's up
                if ($scope.timeRemaining === 0) {
                    $scope.submitExam();
                }
            }
        }, 1000);
    };
    
    $scope.updateFormattedTime = function() {
        var minutes = Math.floor($scope.timeRemaining / 60);
        var seconds = $scope.timeRemaining % 60;
        $scope.formattedTime = minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
    };
    
    $scope.getTimeUsed = function() {
        var used = $scope.totalTime - $scope.timeRemaining;
        var minutes = Math.floor(used / 60);
        var seconds = used % 60;
        return minutes.toString().padStart(2, '0') + ':' + seconds.toString().padStart(2, '0');
    };
    
    $scope.getTimePercentage = function() {
        return (($scope.totalTime - $scope.timeRemaining) / $scope.totalTime) * 100;
    };
    
    // Question navigation
    $scope.goToQuestion = function(index) {
        if (index >= 0 && index < $scope.questions.length) {
            $scope.currentQuestionIndex = index;
            $scope.currentQuestion = $scope.questions[index];
        }
    };
    
    $scope.previousQuestion = function() {
        if ($scope.currentQuestionIndex > 0) {
            $scope.goToQuestion($scope.currentQuestionIndex - 1);
        }
    };
    
    $scope.nextQuestion = function() {
        if ($scope.currentQuestionIndex < $scope.questions.length - 1) {
            $scope.goToQuestion($scope.currentQuestionIndex + 1);
        } else {
            $scope.showSubmitModal = true;
        }
    };
    
    // Answer handling
    $scope.selectOption = function(optionIndex) {
        // For single-choice questions
        $scope.currentQuestion.options.forEach(function(opt, idx) {
            opt.selected = (idx === optionIndex);
        });
        
        $scope.currentQuestion.userAnswer = optionIndex;
        $scope.currentQuestion.answered = true;
        
        // Update the question in the array
        $scope.questions[$scope.currentQuestionIndex] = $scope.currentQuestion;
        
        // Auto-save
        $scope.saveAnswer();
    };
    
    $scope.clearAnswer = function() {
        $scope.currentQuestion.options.forEach(function(opt) {
            opt.selected = false;
        });
        
        $scope.currentQuestion.userAnswer = null;
        $scope.currentQuestion.answered = false;
        $scope.questions[$scope.currentQuestionIndex] = $scope.currentQuestion;
        
        $scope.saveAnswer();
    };
    
    $scope.toggleMarkForReview = function() {
        $scope.currentQuestion.markedForReview = !$scope.currentQuestion.markedForReview;
        $scope.questions[$scope.currentQuestionIndex] = $scope.currentQuestion;
        
        if ($scope.currentQuestion.markedForReview) {
            $scope.addUpdate('system', 'Question ' + ($scope.currentQuestionIndex + 1) + ' marked for review');
        }
        
        $scope.saveAnswer();
    };
    
    // Save functions
    $scope.saveAnswer = function() {
        if (!$scope.currentQuestion || $scope.currentQuestion.userAnswer === null) return;
        
        $scope.autoSaving = true;
        
        // Save locally
        $scope.saveLocalProgress();
        
        // Simulate server save
        $timeout(function() {
            $scope.currentQuestion.saved = true;
            $scope.addUpdate('answer', 'Answer saved for question ' + ($scope.currentQuestionIndex + 1));
            $scope.dataSynced = true;
            $scope.autoSaving = false;
        }, 500);
    };
    
    $scope.saveProgress = function() {
        $scope.saveLocalProgress();
        $scope.addUpdate('system', 'Progress saved');
    };
    
    $scope.getProgressData = function() {
        return {
            examId: 123,
            userId: 1,
            timestamp: new Date().toISOString(),
            answers: $scope.questions.map(function(q) {
                return {
                    questionId: q.id,
                    answer: q.userAnswer,
                    markedForReview: q.markedForReview,
                    saved: q.saved
                };
            }),
            currentQuestion: $scope.currentQuestionIndex,
            timeRemaining: $scope.timeRemaining
        };
    };
    
    // Local storage functions
    $scope.saveLocalProgress = function() {
        var progress = $scope.getProgressData();
        localStorage.setItem('exam_progress_123', JSON.stringify(progress));
    };
    
    $scope.loadLocalProgress = function() {
        var saved = localStorage.getItem('exam_progress_123');
        if (!saved) return;
        
        try {
            var progress = JSON.parse(saved);
            
            progress.answers.forEach(function(savedAnswer) {
                var question = $scope.questions.find(function(q) {
                    return q.id === savedAnswer.questionId;
                });
                
                if (question && savedAnswer.answer !== null) {
                    question.userAnswer = savedAnswer.answer;
                    question.answered = true;
                    question.markedForReview = savedAnswer.markedForReview || false;
                    question.saved = savedAnswer.saved || false;
                    
                    if (question.userAnswer !== null && question.options[question.userAnswer]) {
                        question.options.forEach(function(opt, idx) {
                            opt.selected = (idx === question.userAnswer);
                        });
                    }
                }
            });
            
            if (progress.currentQuestion !== undefined) {
                $scope.goToQuestion(progress.currentQuestion);
            }
            
            if (progress.timeRemaining) {
                $scope.timeRemaining = progress.timeRemaining;
                $scope.updateFormattedTime();
            }
            
            $scope.addUpdate('system', 'Progress loaded from local storage');
        } catch (error) {
            console.error('Error loading local progress:', error);
        }
    };
    
    // Manual sync
    $scope.manualSync = function() {
        $scope.dataSynced = false;
        $scope.saveProgress();
    };
    
    // Update functions
    $scope.addUpdate = function(type, message) {
        $scope.realTimeUpdates.unshift({
            type: type,
            message: message,
            timestamp: new Date()
        });
        
        // Keep only last 5 updates
        if ($scope.realTimeUpdates.length > 5) {
            $scope.realTimeUpdates.pop();
        }
    };
    
    $scope.dismissAlert = function() {
        // In a real app, you might want to hide the alert
        $scope.isOnline = true; // Simulate reconnection
    };
    
    // Utility functions
    $scope.getAnsweredCount = function() {
        return $scope.questions.filter(function(q) {
            return q.answered;
        }).length;
    };
    
    $scope.getRemainingCount = function() {
        return $scope.questions.length - $scope.getAnsweredCount();
    };
    
    $scope.getMarkedCount = function() {
        return $scope.questions.filter(function(q) {
            return q.markedForReview;
        }).length;
    };
    
    // Event listeners
    $scope.setupEventListeners = function() {
        $window.addEventListener('online', function() {
            $scope.$apply(function() {
                $scope.isOnline = true;
                $scope.addUpdate('system', 'Connection restored');
                $scope.saveProgress(); // Sync when back online
            });
        });
        
        $window.addEventListener('offline', function() {
            $scope.$apply(function() {
                $scope.isOnline = false;
                $scope.addUpdate('system', 'Lost connection - working offline');
            });
        });
        
        // Prevent accidental navigation
        $window.addEventListener('beforeunload', function(e) {
            if ($scope.timeRemaining > 0 && !$scope.confirmSubmit) {
                var message = 'You have unsaved changes. Are you sure you want to leave?';
                e.returnValue = message;
                return message;
            }
        });
    };
    
    // Auto-save interval
    $scope.startAutoSave = function() {
        $interval(function() {
            if ($scope.isOnline) {
                $scope.saveLocalProgress();
                $scope.saveProgress();
            }
        }, 30000); // Every 30 seconds
    };
    
    // Submit exam
    $scope.submitExam = function() {
        if (!$scope.confirmSubmit && $scope.timeRemaining > 0) {
            return;
        }
        
        // Save final progress
        $scope.saveProgress();
        
        var submitData = $scope.questions.map(function(q) {
            return {
                questionId: q.id,
                answer: q.userAnswer,
                markedForReview: q.markedForReview
            };
        });
        
        // Simulate API call
        $scope.autoSaving = true;
        $timeout(function() {
            $scope.autoSaving = false;
            $scope.addUpdate('system', 'Exam submitted successfully!');
            
            // Clear local storage
            localStorage.removeItem('exam_progress_123');
            
            // Show success message
            alert('Exam submitted successfully! You will be redirected to results page.');
            
            // In real app, redirect to results
            // window.location.href = window.baseUrl + '/exam/results/123';
        }, 1500);
    };
    
    $scope.pauseExam = function() {
        $scope.addUpdate('system', 'Exam paused. Timer will resume when you continue.');
        // Implement pause functionality as needed
    };
    
    // Update progress steps
    $scope.updateSteps = function() {
        var answeredPercentage = ($scope.getAnsweredCount() / $scope.questions.length) * 100;
        
        $scope.steps.forEach(function(step, index) {
            step.active = false;
            step.completed = false;
            
            if (index === 0) {
                step.completed = true;
            } else if (index === 1) {
                step.active = true;
                step.completed = answeredPercentage > 0;
            } else if (index === 2) {
                step.completed = answeredPercentage === 100;
            } else if (index === 3) {
                step.active = answeredPercentage === 100;
            }
        });
    };
    
    // Watch for changes to update steps
    $scope.$watch('getAnsweredCount()', function() {
        $scope.updateSteps();
    });
    
    // Cleanup
    $scope.$on('$destroy', function() {
        if ($scope.timerInterval) {
            $interval.cancel($scope.timerInterval);
        }
    });
    
    // Initialize controller
    $scope.init();
  }]);