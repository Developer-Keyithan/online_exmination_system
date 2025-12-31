<?php $this->extend('frontend'); ?>
<?php $this->controller('ResetPasswordController'); ?>

<?php $this->start('content'); ?>
<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 p-4">
    <div class="absolute inset-0 overflow-hidden">
        <!-- Animated background -->
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-1/4 left-10 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl animate-pulse"></div>
            <div
                class="absolute bottom-1/4 right-10 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000">
            </div>
            <div
                class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl">
            </div>
        </div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <!-- Main Card -->
        <div
            class="bg-gradient-to-br from-gray-800/90 to-gray-900/90 backdrop-blur-xl rounded-2xl border border-gray-700 shadow-2xl overflow-hidden">
            <!-- Header Section -->
            <div class="p-8">
                <div class="text-center">
                    <h1
                        class="text-3xl font-bold bg-gradient-to-r from-cyan-300 via-white to-purple-300 bg-clip-text text-transparent mb-3">
                        Set New Password
                    </h1>
                    <p class="text-gray-400 text-sm mb-3">
                        Create a strong, secure password for your account
                    </p>
                    <!-- Email Display -->
                    <div
                        class="inline-flex items-center px-4 py-2 bg-cyan-500/10 border border-cyan-500/30 rounded-full">
                        <i class="fas fa-envelope text-cyan-400 mr-2"></i>
                        <span class="text-cyan-300 text-sm" ng-bind="email || 'Fetching email...'"></span>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="px-8">
                <!-- Link Expiry Timer -->
                <div ng-if="!expired && timeLeft > 0"
                    class="mb-6 p-4 bg-yellow-500/10 border border-yellow-500/30 rounded-lg">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 rounded-lg bg-yellow-500/20 flex items-center justify-center">
                                <i class="fas fa-clock text-yellow-400"></i>
                            </div>
                            <div>
                                <h4 class="font-medium text-yellow-300">Reset Link Expires In</h4>
                                <div class="text-2xl font-bold text-white" id="countdown">
                                    {{formatTime(timeLeft)}}
                                </div>
                            </div>
                        </div>
                        <button ng-if="timeLeft <= 0" ng-click="resendLink()" ng-disabled="resendLoading"
                            class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span ng-if="!resendLoading">Resend</span>
                            <span ng-if="resendLoading" class="flex items-center">
                                <i class="fas fa-spinner animate-spin mr-2"></i>
                                Sending...
                            </span>
                        </button>
                    </div>
                    <div class="mt-3 w-full bg-gray-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-2 rounded-full transition-all duration-1000"
                            ng-style="{'width': (timeLeft / 900 * 100) + '%'}"></div>
                    </div>
                </div>

                <!-- Expired Message -->
                <div ng-if="expired" class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-lg bg-red-500/20 flex items-center justify-center mr-3">
                            <i class="fas fa-exclamation-triangle text-red-400"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-red-300">Reset Link Expired</h4>
                            <p class="text-sm text-red-400/80 mt-1">This password reset link has expired. Please request
                                a new one.</p>
                        </div>
                    </div>
                    <button ng-click="resendLink()" ng-disabled="resendLoading"
                        class="w-full mt-4 px-4 py-3 bg-gradient-to-r from-red-600 to-orange-600 hover:from-red-500 hover:to-orange-500 text-white font-medium rounded-lg transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span ng-if="!resendLoading">Send New Reset Link</span>
                        <span ng-if="resendLoading" class="flex items-center justify-center">
                            <i class="fas fa-spinner animate-spin mr-2"></i>
                            Sending...
                        </span>
                    </button>
                </div>

                <!-- Reset Password Form -->
                <form id="resetPasswordForm" ng-submit="submitNewPassword()" ng-if="!expired && timeLeft > 0">
                    <!-- New Password -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-key mr-2 text-cyan-400"></i>
                            New Password
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-cyan-500/10 to-purple-500/10 rounded-lg blur opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <input type="password" ng-model="newPassword" ng-change="checkPasswordStrength()" required
                                minlength="8" placeholder="Enter new password"
                                class="relative w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50 focus:border-transparent transition-all duration-300"
                                ng-class="{
                                    'border-red-500': passwordStrength === 'weak',
                                    'border-yellow-500': passwordStrength === 'medium',
                                    'border-green-500': passwordStrength === 'strong'
                                }">
                            <button type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300"
                                ng-click="togglePasswordVisibility('newPassword')">
                                <i class="fas" ng-class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>

                        <!-- Password Strength Meter -->
                        <div class="mt-3 space-y-2" ng-if="newPassword">
                            <div class="flex justify-between text-xs">
                                <span class="text-gray-400">Password Strength:</span>
                                <span class="font-medium" :class="{
                                          'text-red-400': passwordStrength === 'weak',
                                          'text-yellow-400': passwordStrength === 'medium',
                                          'text-green-400': passwordStrength === 'strong'
                                      }">
                                    {{passwordStrength | uppercase}}
                                </span>
                            </div>
                            <div class="w-full bg-gray-700 rounded-full h-2">
                                <div class="h-2 rounded-full transition-all duration-500" :class="{
                                         'bg-red-500 w-1/3': passwordStrength === 'weak',
                                         'bg-yellow-500 w-2/3': passwordStrength === 'medium',
                                         'bg-green-500 w-full': passwordStrength === 'strong'
                                     }"></div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-400">
                                <div class="flex items-center">
                                    <i class="fas fa-check mr-2"
                                        :class="passwordCriteria.length ? 'text-green-400' : 'text-gray-600'"></i>
                                    <span>8+ characters</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check mr-2"
                                        :class="passwordCriteria.uppercase ? 'text-green-400' : 'text-gray-600'"></i>
                                    <span>Uppercase</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check mr-2"
                                        :class="passwordCriteria.lowercase ? 'text-green-400' : 'text-gray-600'"></i>
                                    <span>Lowercase</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check mr-2"
                                        :class="passwordCriteria.number ? 'text-green-400' : 'text-gray-600'"></i>
                                    <span>Number</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-check mr-2"
                                        :class="passwordCriteria.special ? 'text-green-400' : 'text-gray-600'"></i>
                                    <span>Special char</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-key mr-2 text-purple-400"></i>
                            Confirm Password
                        </label>
                        <div class="relative group">
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-pink-500/10 rounded-lg blur opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            </div>
                            <input type="password" ng-model="confirmPassword" ng-change="checkPasswordMatch()" required
                                placeholder="Confirm new password"
                                class="relative w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-transparent transition-all duration-300"
                                :class="{'border-red-500': !passwordsMatch && confirmPassword, 'border-green-500': passwordsMatch && confirmPassword}">
                            <button type="button"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300"
                                ng-click="togglePasswordVisibility('confirmPassword')">
                                <i class="fas" ng-class="showConfirmPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>

                        <!-- Password Match Indicator -->
                        <div class="mt-2" ng-if="confirmPassword">
                            <div class="flex items-center text-sm"
                                :class="passwordsMatch ? 'text-green-400' : 'text-red-400'">
                                <i class="fas mr-2" :class="passwordsMatch ? 'fa-check-circle' : 'fa-times-circle'"></i>
                                <span>{{passwordsMatch ? 'Passwords match' : 'Passwords do not match'}}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div ng-if="error"
                        class="mb-6 p-4 bg-red-500/10 border border-red-500/30 rounded-lg animate-fadeIn">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle text-red-400 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-red-300">Failed to reset password</h4>
                                <p class="text-sm text-red-400/80 mt-1">{{error}}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Success Message -->
                    <div ng-if="success"
                        class="mb-6 p-4 bg-green-500/10 border border-green-500/30 rounded-lg animate-fadeIn">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-400 mr-3 text-xl"></i>
                            <div>
                                <h4 class="font-medium text-green-300">Password Reset Successful!</h4>
                                <p class="text-sm text-green-400/80 mt-1">{{success}}</p>
                            </div>
                        </div>
                        <div class="mt-4 text-center">
                            <a href="<?php echo BASE_URL ?>/auth/login"
                                class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 hover:from-green-500 hover:to-teal-500 text-white font-medium rounded-lg transition-colors">
                                <i class="fas fa-sign-in-alt mr-2"></i>
                                Login with New Password
                            </a>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" ng-disabled="loading || !isFormValid()"
                        class="w-full py-4 px-6 bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 text-white font-semibold rounded-lg transition-all duration-300 transform hover:-translate-y-0.5 hover:shadow-lg hover:shadow-cyan-500/25 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:shadow-none group">
                        <div class="flex items-center justify-center space-x-3">
                            <i class="fas fa-lock group-hover:rotate-12 transition-transform"></i>
                            <span ng-if="!loading">Reset Password</span>
                            <span ng-if="loading" class="flex items-center">
                                <i class="fas fa-spinner animate-spin mr-2"></i>
                                Resetting...
                            </span>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"
                                ng-if="!loading"></i>
                        </div>
                    </button>
                </form>

                <!-- Password Tips -->
                <div class="mt-8 pt-6 border-t border-gray-700" ng-if="!expired && timeLeft > 0">
                    <h4 class="text-sm font-medium text-gray-300 mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-400 mr-2"></i>
                        Password Tips
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span class="text-xs text-gray-400">Use at least 12 characters</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span class="text-xs text-gray-400">Mix letters, numbers & symbols</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span class="text-xs text-gray-400">Avoid common words & sequences</span>
                        </div>
                        <div class="flex items-start space-x-2">
                            <i class="fas fa-check text-green-400 mt-1"></i>
                            <span class="text-xs text-gray-400">Don't reuse old passwords</span>
                        </div>
                    </div>
                </div>

                <!-- Back to Login -->
                <a href="<?php echo BASE_URL ?>/login"
                    class="flex items-center text-cyan-300 hover:text-cyan-200 transition-colors group mb-8">
                    <i class="fas fa-arrow-left mr-2 group-hover:-translate-x-1 transition-transform"></i>
                    <span>Back to Login</span>
                </a>
            </div>

            <!-- Footer -->
            <div class="px-8 py-6 bg-black/20 border-t border-gray-700">
                <div class="text-center">
                    <p class="text-xs text-gray-500">
                        © <?php echo config('app.copyright') ?>
                        <!-- <br>
                        <span class="inline-flex items-center mt-1">
                            <i class="fas fa-lock text-green-400 mr-1"></i>
                            <span class="text-gray-400">256-bit SSL Encryption</span>
                        </span> -->
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    input[type="password"]::-ms-reveal,
    input[type="password"]::-ms-clear {
        display: none;
    }
</style>
<?php $this->end(); ?>