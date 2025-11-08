<?php $this->extend('frontend'); ?>

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
<div class="glass-effect rounded-3xl p-8 w-full max-w-md floating" style="animation-duration: 6s;">
    <div class="text-center mb-2">
        <div class="inline-block p-4 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 mb-4 pulse">
            <i class="fas fa-user text-white text-2xl"></i>
        </div>
        <h1 class="text-3xl font-bold text-white">LOGIN</h1>
        <p class="text-blue-100 mt-2">Welcome back! Please sign in to your account</p>
    </div>

    <form class="space-y-6 mt-6">
        <div>
            <label class="block text-white text-sm font-medium mb-2" for="email">
                <i class="fa-solid fa-envelope text-blue-300 mr-2"></i>E-mail
            </label>
            <input id="email" type="email"
                class="glass-effect w-full px-4 py-3 rounded-xl text-white placeholder-blue-200 focus:outline-none input-glow transition-all duration-300"
                placeholder="Enter your email">
        </div>

        <div>
            <label class="block text-white text-sm font-medium mb-2" for="password">
                <i class="fas fa-lock mr-2 text-blue-300"></i>Password
            </label>
            <input id="password" type="password"
                class="glass-effect w-full px-4 py-3 rounded-xl text-white placeholder-blue-200 focus:outline-none input-glow transition-all duration-300"
                placeholder="Enter your password">
        </div>

        <div class="flex items-center justify-end">
            <div class=" items-center hidden">
                <input id="remember-me" type="checkbox"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                <label for="remember-me" class="ml-2 block text-sm text-blue-200">Remember me</label>
            </div>

            <a href="#" class="text-sm text-blue-200 hover:text-white transition-colors duration-300">
                Forgot password?
            </a>
        </div>

        <button type="submit"
            class="w-full bg-gradient-to-r from-blue-500 to-indigo-600 text-white py-3 rounded-xl font-semibold btn-glow hover:from-blue-600 hover:to-indigo-700 transition-all duration-300 transform hover:-translate-y-1 mt-4">
            Sign In
        </button>
    </form>
</div>
<?php $this->end(); ?>
<?php $this->push('js'); ?>
<script>
    // Simple form validation
    document.querySelector('form').addEventListener('submit', function (e) {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        if (email && password) {
            // Add success animation
            const button = document.querySelector('button[type="submit"]');
            button.innerHTML = '<svg class="animate-spin h-5 w-5" viewBox="0 0 24 24"><circle class="opacity-50" fill="#0000" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg><span class="ml-2">Loging...</span>';
            button.classList.add('flex', 'items-center', 'justify-center', 'gap-2');

            fetch(`${window.baseUrl}/public`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
            })
                .then(response => response.text()) // or .json() if PHP returns JSON
                .then(data => {
                    // Response success handling
                    Toast({
                        type: 'success',
                        title: 'Success!',
                        msg: data || 'Login successful'
                    });
                    button.innerHTML = 'Sign In';
                    document.getElementById('email').value = '';
                    document.getElementById('password').value = '';
                })
                .catch(error => {
                    // Response error handling
                    Toast({
                        type: 'error',
                        title: 'Error!',
                        msg: 'Login failed'
                    });
                    button.innerHTML = 'Sign In';
                });

        } else {
            // Add error animation
            const inputs = document.querySelectorAll('input');
            inputs.forEach(input => {
                if (!input.value) {
                    input.classList.add('pulse-error');
                    // input.style.boxShadow = '0 0 10px rgba(239, 0, 0, 0.7)';
                    setTimeout(() => {
                        input.classList.remove('pulse-error');
                        // input.style.boxShadow = '';
                    }, 2000);
                }
            });

            // alert('Please fill in all fields');
        }
    });

    // Add focus effects to inputs
    const inputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    inputs.forEach(input => {
        input.addEventListener('focus', function () {
            this.parentElement.classList.add('transform', 'scale-105');
            this.parentElement.classList.add('transition-transform', 'duration-300');
        });

        input.addEventListener('blur', function () {
            this.parentElement.classList.remove('transform', 'scale-105');
        });
    });
</script>
<?php $this->endPush(); ?>