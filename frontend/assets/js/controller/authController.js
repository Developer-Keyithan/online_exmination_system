app.controller('AuthController', [
    "$scope",
    "$http",
    function ($scope, $http) {
        $scope.loading = false;

        $scope.submitLogin = function () {
            $scope.loading = true;

            const email = $('#email').val();
            const password = $('#password').val();

            if (email && password) {
                const button = $('#login-btn');
                button.attr('disabled', true);
                button.html(`
                    <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
                        <circle class="opacity-50" fill="none" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="ml-2">Logging...</span>
                    `);
                button.addClass('flex items-center justify-center gap-2');
                const formData = $('#login-form').serialize();
                $http({
                    method: 'POST',
                    url: 'API/login',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    data: formData
                }).then(function (response) {
                    const data = response.data;
                    if (data.status === 'success') {
                        Toast({
                            type: 'success',
                            title: 'Success!',
                            msg: data.msg || 'Login successful'
                        });
                        setTimeout(() => {
                            window.location.href = 'dashboard';
                        }, 1000);
                    } else if (data.status === 'error') {
                        Toast({
                            type: 'error',
                            title: 'Error!',
                            msg: data.msg || 'Login failed'
                        });
                        button.html('Sign in again');
                        button.prop('disabled', false);
                    }
                }, function (error) {
                    Toast({
                        type: 'error',
                        title: 'Error!',
                        msg: 'Login failed'
                    });
                    button.html('Sign in again');
                    button.prop('disabled', false);
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
            }
        };
    }]);