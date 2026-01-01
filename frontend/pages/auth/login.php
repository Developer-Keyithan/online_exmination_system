<?php $this->extend('frontend'); ?>
<?php $this->controller('AuthController')?>
<?php $this->start('content'); ?>
<!-- Background lighting effects -->
<div class="light-effect light-left"></div>
<div class="light-effect light-right"></div>
<div class="light-effect light-center"></div>

<!-- Floating particles -->
<div class="absolute top-1/4 left-1/4 w-3 h-3 bg-blue-400 rounded-full opacity-60 floating"
    style="animation-delay: 0.5s;"></div>
<div class="absolute top-1/3 right-1/4 w-2 h-2 bg-purple-400 rounded-full opacity-60 floating"
    style="animation-delay: 1s;"></div>
<div class="absolute bottom-1/4 left-1/3 w-4 h-4 bg-cyan-400 rounded-full opacity-60 floating"
    style="animation-delay: 1.5s;"></div>
<div class="absolute top-2/3 right-1/3 w-3 h-3 bg-pink-400 rounded-full opacity-60 floating"
    style="animation-delay: 2s;"></div>

<!-- Login container -->
<div class="glass-effect rounded-3xl p-6 md:p-8 mx-auto w-max md:w-full max-w-md floating " style="animation-duration: 6s;">
    <div class="text-center mb-2">
        <div class="inline-block p-4 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 mb-4 pulse">
            <i class="fas fa-user text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-white">LOGIN</h1>
        <p class="text-blue-100 mt-2">Welcome back! Please sign in to your account</p>
    </div>

    <form id="login-form" class="space-y-6 mt-6">
        <div>
            <label class="block text-white text-sm font-medium mb-2" for="email">
                <i class="fa-solid fa-envelope text-blue-300 mr-2"></i>E-mail
            </label>
            <input id="email" type="email" name="email"
                class="glass-effect w-full px-4 py-3 rounded-xl text-white placeholder-blue-200 focus:outline-none input-glow transition-all duration-300"
                placeholder="Enter your email">
        </div>

        <div>
            <label class="block text-white text-sm font-medium mb-2" for="password">
                <i class="fas fa-lock mr-2 text-blue-300"></i>Password
            </label>
            <input id="password" type="password" name="password"
                class="glass-effect w-full px-4 py-3 rounded-xl text-white placeholder-blue-200 focus:outline-none input-glow transition-all duration-300"
                placeholder="Enter your password">
        </div>

        <div class="flex items-center justify-end">
            <div class=" items-center hidden">
                <input id="remember-me" type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="remember-me" class="ml-2 block text-sm text-blue-200">Remember me</label>
            </div>

            <a href="<?php echo BASE_URL ?>/forgot-password" class="text-sm text-blue-200 hover:text-white transition-colors duration-300">
                Forgot password?
            </a>
        </div>

        <button type="button" ng-click="submitLogin()" id="login-btn"
            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 rounded-xl font-semibold btn-glow hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-1 mt-4">
            Sign In
        </button>
    </form>
</div>
<?php $this->end(); ?>