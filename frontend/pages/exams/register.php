<?php $this->extend('frontend'); ?>
<?php $this->controller('ExamRegistrationController'); ?>

<?php $this->start('content'); ?>
<div class="bg-[#0003] p-6 rounded-lg mb-16" ng-cloak>
    <!-- Loading State -->
    <div ng-if="loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-spinner animate-spin text-cyan-500 text-4xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Loading Registration...</h3>
            <p class="text-gray-400">Preparing exam registration</p>
        </div>
    </div>

    <!-- Error State -->
    <div ng-if="error && !loading" class="text-center py-12">
        <div class="max-w-md mx-auto">
            <i class="fas fa-exclamation-triangle text-red-500 text-6xl mb-4"></i>
            <h3 class="text-lg font-medium text-gray-100 mb-2">Registration Failed</h3>
            <p class="text-gray-400 mb-6">{{error}}</p>
            <!-- <a href="<?php echo BASE_URL; ?>/exam/all"
                class="bg-cyan-600 hover:bg-cyan-700 text-white px-6 py-3 rounded-lg inline-flex items-center space-x-2 transition-colors">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Exams</span>
            </a> -->
        </div>
    </div>

    <!-- Main Registration Content -->
    <div ng-if="!loading && !error && examData">
        <!-- Header Section -->
        <div class="bg-[#0004] rounded-lg p-6 mb-6 border border-gray-600">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-100">Register for Exam</h1>
                    <p class="text-gray-400">Complete your registration for the following exam</p>
                </div>

                <!-- Exam Status Badge -->
                <div class="flex items-center space-x-3 capitalize">
                    <span class="px-2 py-1 rounded-full text-sm font-medium" ng-class="{
                              'bg-green-500/20 text-green-300': examData.status === 'live',
                              'bg-blue-500/20 text-blue-300': examData.status === 'scheduled' || examData.status === 'published',
                              'bg-yellow-500/20 text-yellow-300': examData.status === 'draft',
                              'bg-red-500/20 text-red-300': examData.status === 'canceled'
                          }">
                        <i class="fas fa-solid" ng-class="{
                               'fa-circle-play': examData.status === 'live',
                               'fa-check-circle': examData.status === 'published',
                               'fa-clock': examData.status === 'scheduled',
                               'fa-edit': examData.status === 'draft',
                               'fa-ban': examData.status === 'canceled'
                           }"></i>
                        {{examData.status }}
                    </span>

                    <!-- <button ng-click="viewExamDetails()"
                        class="px-4 py-2 rounded-lg border border-cyan-600 text-cyan-400 hover:bg-cyan-600/10 transition-colors">
                        <i class="fas fa-eye mr-2"></i>
                        View Exam
                    </button> -->
                </div>
            </div>
        </div>

        <!-- Two Column Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            <!-- Left Column: Exam Details -->
            <div class="lg:col-span-2">
                <!-- Exam Information Card -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 mb-6">
                    <h2 class="text-xl font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-info-circle text-cyan-400 mr-2"></i>
                        Exam Information
                    </h2>

                    <div class="space-y-4">
                        <!-- Exam Title & Code -->
                        <div>
                            <h3 class="text-lg font-bold text-gray-100 capitalize">{{examData.title}}</h3>
                            <p class="text-gray-400 uppercase">Exam Code: {{examData.code}}</p>
                        </div>

                        <!-- Exam Details Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Duration -->
                            <div class="bg-[#0005] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-clock text-cyan-400 mr-2"></i>
                                    <span class="text-gray-400">Duration</span>
                                </div>
                                <p class="text-gray-100 font-medium">{{examData.duration}} Minutes</p>
                            </div>

                            <!-- Total Questions -->
                            <div class="bg-[#0005] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-question-circle text-purple-400 mr-2"></i>
                                    <span class="text-gray-400">Total Questions</span>
                                </div>
                                <p class="text-gray-100 font-medium">{{examData.total_questions}} Questions</p>
                            </div>

                            <!-- Total Marks -->
                            <div class="bg-[#0005] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-star text-yellow-400 mr-2"></i>
                                    <span class="text-gray-400">Total Marks</span>
                                </div>
                                <p class="text-gray-100 font-medium">{{examData.total_marks}} Marks</p>
                            </div>

                            <!-- Passing Marks -->
                            <div class="bg-[#0005] p-4 rounded-lg">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-flag text-green-400 mr-2"></i>
                                    <span class="text-gray-400">Passing Marks</span>
                                </div>
                                <p class="text-gray-100 font-medium">{{examData.passing_marks}} Marks</p>
                            </div>
                        </div>

                        <!-- Schedule Information -->
                        <div ng-if="examData.schedule_type === 'scheduled'" class="bg-[#0005] p-4 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-calendar-alt text-blue-400 mr-2"></i>
                                <h4 class="text-gray-100 font-medium">Exam Schedule</h4>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <div>
                                    <p class="text-sm text-gray-400">Start Date & Time</p>
                                    <p class="text-gray-100 font-medium">
                                        {{examData.start_time | formatDateTime: 'MMM DD, YYYY'}} at
                                        {{examData.start_time | formatDateTime: 'hh:mm A'}}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-400">End Date & Time</p>
                                    <p class="text-gray-100 font-medium">
                                        {{getEndTime() | formatDateTime: 'MMM DD, YYYY'}} at {{getEndTime() |
                                        formatDateTime: 'hh:mm A'}}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 p-3 bg-blue-500/10 border border-blue-500/30 rounded">
                                <p class="text-sm text-blue-300">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    You must attempt the exam within this time window
                                </p>
                            </div>
                        </div>

                        <!-- Anytime Exam Info -->
                        <div ng-if="examData.schedule_type === 'anytime'" class="bg-[#0005] p-4 rounded-lg">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-infinity text-green-400 mr-2"></i>
                                <h4 class="text-gray-100 font-medium">Anytime Exam</h4>
                            </div>
                            <p class="text-gray-300">
                                This exam can be taken anytime at your convenience. Once registered,
                                you can start the exam whenever you're ready.
                            </p>
                        </div>

                        <!-- Exam Instructions -->
                        <div class="bg-[#0005] p-4 rounded-lg"
                            ng-if="examData.instructions && examData.instructions !== ''">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-clipboard-list text-purple-400 mr-2"></i>
                                <h4 class="text-gray-100 font-medium">Exam Instructions</h4>
                            </div>
                            <div class="text-gray-300 space-y-2 text-sm"
                                ng-bind-html="examData.instructions | safeHtml"></div>
                        </div>

                        <!-- Exam Rules -->
                        <div class="bg-[#0005] p-4 rounded-lg"
                            ng-if="examData.shuffle_questions || examData.shuffle_options || examData.full_screen_mode || examData.disable_copy_paste || examData.allow_retake">
                            <div class="flex items-center mb-3">
                                <i class="fas fa-gavel text-yellow-400 mr-2"></i>
                                <h4 class="text-gray-100 font-medium">Important Rules</h4>
                            </div>
                            <div class="space-y-2">
                                <div class="flex items-start" ng-if="examData.shuffle_questions">
                                    <i class="fas fa-random text-green-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Questions will be shuffled</span>
                                </div>
                                <div class="flex items-start" ng-if="examData.shuffle_options">
                                    <i class="fas fa-random text-green-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Options will be shuffled</span>
                                </div>
                                <div class="flex items-start" ng-if="examData.full_screen_mode">
                                    <i class="fas fa-expand text-blue-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Full screen mode required</span>
                                </div>
                                <div class="flex items-start" ng-if="examData.disable_copy_paste">
                                    <i class="fas fa-ban text-red-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Copy/paste disabled</span>
                                </div>
                                <div class="flex items-start" ng-if="examData.show_results_immediately">
                                    <i class="fas fa-chart-line text-green-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Results shown immediately</span>
                                </div>
                                <div class="flex items-start" ng-if="examData.allow_retake">
                                    <i class="fas fa-redo text-yellow-400 mt-1 mr-2"></i>
                                    <span class="text-gray-300">Retake allowed ({{examData.max_attempts}}
                                        attempts)</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Instructor Information -->
                <!-- <div class="bg-[#0004] rounded-lg p-6 border border-gray-600">
                    <h2 class="text-xl font-semibold text-gray-100 mb-4 flex items-center">
                        <i class="fas fa-user-graduate text-cyan-400 mr-2"></i>
                        Instructor Information
                    </h2>

                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 rounded-full bg-cyan-500/20 flex items-center justify-center">
                            <i class="fas fa-user text-cyan-400 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-medium text-gray-100">{{examData.instructor_name || 'Dr. John
                                Smith'}}</h3>
                            <p class="text-gray-400">{{examData.instructor_department || 'Computer Science Department'}}
                            </p>
                            <p class="text-gray-400 text-sm">{{examData.instructor_email ||
                                'instructor@university.edu'}}</p>
                        </div>
                    </div>
                </div> -->
            </div>

            <!-- Right Column: Registration Form -->
            <div class="lg:col-span-2">
                <!-- Registration Card -->
                <div class="bg-[#0004] rounded-lg p-6 border border-gray-600 top-6">
                    <h2 class="text-xl font-semibold text-gray-100 mb-6 flex items-center">
                        <i class="fas fa-user-plus text-green-400 mr-2"></i>
                        Registration Details
                    </h2>

                    <!-- Student Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-100 mb-3">Your Information</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm text-gray-400">Full Name</p>
                                <p class="text-gray-100 font-medium">{{studentInfo.name || 'Loading...'}}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Student ID</p>
                                <p class="text-gray-100 font-medium">{{studentInfo.student_id || 'N/A'}}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Email</p>
                                <p class="text-gray-100 font-medium">{{studentInfo.email || 'N/A'}}</p>
                            </div>
                            <!-- <div>
                                <p class="text-sm text-gray-400">Department</p>
                                <p class="text-gray-100 font-medium">{{studentInfo.department || 'Not specified'}}</p>
                            </div> -->
                        </div>
                    </div>

                    <!-- Registration Form -->
                    <form id="registrationForm" onsubmit="return false">
                        <input type="hidden" name="student_id" value="{{ studentInfo.id }}" />
                        <input type="hidden" name="exam_id" value="{{ examId }}" />
                        <!-- Terms & Conditions -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-100 mb-3">Terms & Conditions</h3>
                            <div class="bg-[#0005] p-4 rounded-lg overflow-y-auto">
                                <div class="space-y-3 text-sm text-gray-300">
                                    <p><strong>By registering for this exam, you agree to:</strong></p>
                                    <ol class="list-decimal pl-5 space-y-2">
                                        <li>Attempt the exam honestly without any unauthorized help</li>
                                        <li>Not share exam questions with others</li>
                                        <li>Follow all exam rules and instructions</li>
                                        <li>Accept the consequences of any violation</li>
                                        <li>Ensure stable internet connection during the exam</li>
                                        <li>Complete the exam within the allotted time</li>
                                    </ol>
                                    <p class="mt-3 text-yellow-300">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        Violation of these terms may result in disqualification.
                                    </p>
                                </div>
                            </div>

                            <!-- Agreement Checkbox -->
                            <div class="mt-4">
                                <label class="flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" ng-model="registrationData.agree_terms" name="agree_terms"
                                        required
                                        class="mt-1 rounded bg-[#0006] border-gray-600 text-green-500 focus:ring-green-500">
                                    <span class="text-gray-300 text-sm">
                                        I have read and agree to the terms and conditions
                                    </span>
                                </label>
                                <div ng-show="registrationForm.$submitted && registrationForm.$error.required"
                                    class="text-red-400 text-xs mt-1">
                                    You must agree to the terms and conditions
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information -->
                        <!-- <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-100 mb-3">Additional Information</h3> -->

                        <!-- Preferred Language -->
                        <!-- <div class="mb-4">
                                <label class="block text-sm text-gray-400 mb-2">Preferred Language</label>
                                <select ng-model="registrationData.preferred_language"
                                    class="w-full bg-[#0005] border border-gray-600 rounded-lg px-4 py-3 text-gray-100 focus:outline-none focus:border-cyan-500">
                                    <option value="en">English</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="de">German</option>
                                    <option value="other">Other</option>
                                </select>
                            </div> -->

                        <!-- Special Accommodations -->
                        <!-- <div class="mb-4">
                                <label class="block text-sm text-gray-400 mb-2">Special Accommodations Needed</label>
                                <textarea ng-model="registrationData.special_accommodations" rows="3"
                                    placeholder="If you require any special accommodations (extra time, etc.)"
                                    class="w-full bg-[#0005] border border-gray-600 rounded-lg px-4 py-3 text-gray-100 placeholder-gray-500 focus:outline-none focus:border-cyan-500"></textarea>
                            </div> -->

                        <!-- Notification Preferences -->
                        <!-- <div>
                                <label class="flex items-start space-x-3 cursor-pointer">
                                    <input type="checkbox" ng-model="registrationData.receive_notifications"
                                        class="mt-1 rounded bg-[#0006] border-gray-600 text-cyan-500 focus:ring-cyan-500">
                                    <span class="text-gray-300 text-sm">
                                        Send me email notifications about exam updates
                                    </span>
                                </label>
                            </div>
                        </div> -->


                        <!-- Notification Preferences -->
                        <div class="flex flex-col mb-4">
                            <label class="flex items-start space-x-3 cursor-pointer">
                                Enter your password
                            </label>

                            <div class="relative w-full">
                                <input type="{{ showPassword ? 'text' : 'password' }}"
                                    ng-model="registrationData.password" name="pwd" placeholder="Enter password"
                                    required
                                    class="mt-1 rounded bg-[#0006] w-full border-gray-600 text-cyan-500 focus:ring-cyan-500">
                                <i class="fas absolute top-1/2 transform -translate-y-1/2 right-3 text-gray-400 hover:text-gray-300 cursor-pointer"
                                    ng-class="showPassword ? 'fa-eye-slash' : 'fa-eye'"
                                    ng-click="showPassword = !showPassword"></i>

                            </div>
                            <span class="text-gray-300 text-sm mt-1">
                                Enter your password for confirmation
                            </span>
                            <div ng-show="registrationForm.$submitted && registrationForm.$error.required"
                                class="text-red-400 text-xs mt-1">
                                Password is required
                            </div>
                        </div>

                        <!-- Registration Summary -->
                        <div class="mb-6 p-4 bg-[#0005] rounded-lg border border-gray-600">
                            <h3 class="text-lg font-medium text-gray-100 mb-3">Registration Summary</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Exam:</span>
                                    <span class="text-gray-100 capitalize">{{examData.title}}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Code:</span>
                                    <span class="text-gray-100 uppercase">{{examData.code}}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Schedule:</span>
                                    <span class="text-gray-100">{{examData.schedule_type === 'scheduled' ? 'Scheduled' :
                                        'Anytime'}}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-400">Duration:</span>
                                    <span class="text-gray-100">{{examData.duration}} minutes</span>
                                </div>
                                <div class="pt-2 border-t border-gray-600">
                                    <div class="flex justify-between">
                                        <span class="text-gray-400">Registration Status:</span>
                                        <span class="text-yellow-400 font-medium">Pending</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="space-y-3">
                            <button type="button" ng-disabled="!registrationData.agree_terms || !registrationData.password" ng-click="submitRegistration()"
                                class="w-full py-3 rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                <i class="fas"
                                    ng-class="isSubmitting ? 'fa-spinner animate-spin' : 'fa-check-circle'"></i>
                                <span>{{isSubmitting ? 'Processing...' : 'Complete Registration'}}</span>
                            </button>

                            <button type="button" ng-click="cancelRegistration()"
                                class="w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Help & Support -->
                <div class="mt-6 bg-[#0004] rounded-lg p-4 border border-gray-600">
                    <h3 class="text-lg font-medium text-gray-100 mb-3 flex items-center">
                        <i class="fas fa-question-circle text-cyan-400 mr-2"></i>
                        Need Help?
                    </h3>
                    <div class="space-y-2 text-sm text-gray-300">
                        <p>If you encounter any issues during registration:</p>
                        <ul class="list-disc pl-5 space-y-1">
                            <!-- <li>Contact the instructor: {{examData.instructor_email || 'instructor@university.edu'}}</li> -->
                            <li>Email support: support@examsystem.edu</li>
                            <li>Call: +94 77 123-4567</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Registration Success Modal -->
    <div ng-if="showSuccessModal" class="fixed inset-0 bg-black/80 z-[9999999] flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-check-circle text-green-400 mr-2"></i>
                    Registration Successful!
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-20 h-20 mx-auto mb-4 rounded-full bg-green-500/20 border-4 border-green-500 flex items-center justify-center">
                        <i class="fas fa-check text-green-400 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">Congratulations!</h4>
                    <p class="text-gray-400">You have successfully registered for the exam.</p>
                </div>

                <!-- Registration Details -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <h5 class="text-md font-medium text-gray-100 mb-3">Registration Details:</h5>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Exam:</span>
                            <span class="text-gray-100">{{examData.title}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Registration ID:</span>
                            <span class="text-cyan-400">{{registrationId}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Date:</span>
                            <span class="text-gray-100">{{registrationDate | date:'medium'}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-green-400">Registered</span>
                        </div>
                    </div>

                    <!-- Next Steps -->
                    <div class="mt-4 p-3 bg-blue-500/10 border border-blue-500/30 rounded">
                        <p class="text-sm text-blue-300">
                            <i class="fas fa-info-circle mr-2"></i>
                            <strong>Next Steps:</strong> You can now access the exam from your dashboard.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a ng-href="<?php echo BASE_URL; ?>/exam/{{attemptUrl}}/attempt/{{examData.id}}"
                        ng-if="examData.schedule_type === 'anytime' || isExamAvailable()"
                        class="block w-full py-3 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors text-center">
                        <i class="fas fa-play mr-2"></i>
                        Start Exam Now
                    </a>

                    <a href="<?php echo BASE_URL; ?>/exam/my"
                        class="block w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-list mr-2"></i>
                        Back to Exams
                    </a>

                    <a href="<?php echo BASE_URL; ?>/dashboard"
                        class="block w-full py-3 rounded-lg border border-green-500 text-green-400 hover:bg-green-500/10 transition-colors text-center">
                        <i class="fas fa-home mr-2"></i>
                        Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Already Registered Modal -->
    <div ng-if="showAlreadyRegisteredModal" class="fixed inset-0 bg-black/80 z-[9999999] flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-user-check text-blue-400 mr-2"></i>
                    Already Registered
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-20 h-20 mx-auto mb-4 rounded-full bg-blue-500/20 border-4 border-blue-500 flex items-center justify-center">
                        <i class="fas fa-user-check text-blue-400 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">You're Already Registered</h4>
                    <p class="text-gray-400">You have already registered for this exam.</p>
                </div>

                <!-- Registration Info -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-400">Registration Date:</span>
                            <span class="text-gray-100">{{existingRegistration.date | date:'medium'}}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Status:</span>
                            <span class="text-green-400">{{existingRegistration.status | uppercase}}</span>
                        </div>
                        <div ng-if="existingRegistration.attempts > 0" class="flex justify-between">
                            <span class="text-gray-400">Attempts:</span>
                            <span class="text-yellow-400">{{existingRegistration.attempts}}/{{examData.max_attempts ||
                                1}}</span>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-3">
                    <a ng-href="<?php echo BASE_URL; ?>/exam/{{attemptUrl}}/attempt/{{attemptID}}"
                        ng-if="existingRegistration.status === 'registered'"
                        class="block w-full py-3 rounded-lg bg-cyan-600 hover:bg-cyan-700 text-white transition-colors text-center">
                        <i class="fas fa-play mr-2"></i>
                        Start Exam
                    </a>

                    <!-- <a href="<?php // echo BASE_URL; ?>/exams"
                        class="block w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
                    </a> -->

                    <a ng-href="<?php echo BASE_URL; ?>/result/review/{{attemptID}}/{{examId}}/{{studentInfo.id}}"
                        ng-if="existingRegistration.status === 'completed'"
                        class="block w-full py-3 rounded-lg border border-green-500 text-green-400 hover:bg-green-500/10 transition-colors text-center">
                        <i class="fas fa-chart-bar mr-2"></i>
                        View Results
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Exam Unavailable Modal -->
    <div ng-if="showExamUnavailableModal" class="fixed inset-0 bg-black/80 z-[9999999] flex items-center justify-center p-4">
        <div class="bg-[#0006] backdrop-blur rounded-lg border border-gray-600 w-full max-w-md">
            <div class="p-6 border-b border-gray-600">
                <h3 class="text-xl font-bold text-gray-100 flex items-center">
                    <i class="fas fa-ban text-red-400 mr-2"></i>
                    Exam Unavailable
                </h3>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <div
                        class="w-20 h-20 mx-auto mb-4 rounded-full bg-red-500/20 border-4 border-red-500 flex items-center justify-center">
                        <i class="fas fa-ban text-red-400 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-medium text-gray-100 mb-2">Cannot Register</h4>
                    <p class="text-gray-400">{{registrationError}}</p>
                </div>

                <!-- Reasons -->
                <div class="bg-[#0005] rounded-lg p-4 mb-6">
                    <h5 class="text-md font-medium text-gray-100 mb-3">Possible Reasons:</h5>
                    <ul class="list-disc pl-5 space-y-2 text-sm text-gray-300">
                        <li>Exam registration period has ended</li>
                        <li>Maximum registrations reached</li>
                        <li>Exam is not published yet</li>
                        <li>You don't meet the eligibility criteria</li>
                        <li>Exam has been canceled</li>
                    </ul>
                </div>

                <div class="space-y-3">
                    <a href="<?php echo BASE_URL; ?>/exam/my"
                        class="block w-full py-3 rounded-lg border border-gray-600 text-gray-300 hover:bg-gray-700 transition-colors text-center">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Exams
                    </a>

                    <!-- <button ng-click="contactInstructor()"
                        class="w-full py-3 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition-colors">
                        <i class="fas fa-envelope mr-2"></i>
                        Contact Instructor
                    </button> -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>