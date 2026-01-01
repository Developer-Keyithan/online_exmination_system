<?php $this->extend('frontend'); ?>
<?php $this->controller('ForgotPasswordController'); ?>

<?php $this->start('content'); ?>
<div class="min-h-screen flex items-center justify-center p-4" ng-controller="ForgotPasswordController">

    <!-- Animated background -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-10 w-64 h-64 bg-cyan-500/10 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-1/4 right-10 w-80 h-80 bg-purple-500/10 rounded-full blur-3xl animate-pulse delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-blue-500/5 rounded-full blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md z-10">
        <div class="bg-gradient-to-br from-gray-800/90 to-gray-900/90 backdrop-blur-xl rounded-2xl border border-gray-700 shadow-2xl">

            <!-- Header -->
            <div class="p-8 text-center">
                <h1 class="text-3xl font-bold bg-gradient-to-r from-cyan-300 via-white to-purple-300 bg-clip-text text-transparent mb-3">
                    Forgot Password
                </h1>
                <p class="text-gray-400 text-sm">
                    Enter your email to receive a password reset link
                </p>
            </div>

            <!-- Form -->
            <div class="px-8 pb-8">
                <form novalidate>
                    <!-- Email -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-300 mb-2">
                            <i class="fas fa-envelope text-cyan-400 mr-2"></i>Email Address
                        </label>
                        <input type="email" ng-model="email" required
                            placeholder="Enter your registered email"
                            class="w-full px-4 py-3 bg-gray-700/50 border border-gray-600 rounded-lg text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-cyan-500/50">
                    </div>

                    <!-- Error -->
                    <div ng-if="error" class="mb-4 p-4 bg-red-500/10 border border-red-500/30 rounded-lg">
                        <p class="text-red-400 text-sm">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{error}}
                        </p>
                    </div>

                    <!-- Success -->
                    <div ng-if="success" class="mb-4 p-4 bg-green-500/10 border border-green-500/30 rounded-lg">
                        <p class="text-green-400 text-sm">
                            <i class="fas fa-check-circle mr-2"></i>{{success}}
                        </p>
                    </div>

                    <!-- Submit -->
                    <button type="button"
                        ng-click="sendResetLink()"
                        ng-disabled="loading || !email"
                        class="w-full py-4 bg-gradient-to-r from-cyan-600 to-purple-600 hover:from-cyan-500 hover:to-purple-500 text-white font-semibold rounded-lg transition-all disabled:opacity-50">
                        <span ng-if="!loading">
                            <i class="fas fa-paper-plane mr-2"></i>Send Reset Link
                        </span>
                        <span ng-if="loading">
                            <i class="fas fa-spinner animate-spin mr-2"></i>Sending...
                        </span>
                    </button>
                </form>

                <!-- Back -->
                <div class="mt-6 text-center">
                    <a href="<?php echo BASE_URL ?>/login"
                        class="inline-flex items-center text-cyan-400 hover:text-cyan-300">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Login
                    </a>
                </div>
            </div>

            <!-- Footer -->
            <div class="px-8 py-5 bg-black/20 border-t border-gray-700 text-center">
                <p class="text-xs text-gray-500">© <?php echo config('app.copyright') ?></p>
            </div>
        </div>
    </div>
</div>
<?php $this->end(); ?>