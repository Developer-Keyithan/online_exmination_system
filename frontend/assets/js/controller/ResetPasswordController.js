app.controller('ResetPasswordController', ['$scope', '$http', '$timeout', '$interval', '$location', function ($scope, $http, $timeout, $interval, $location) {
    // Initialize scope variables
    $scope.email = '';
    $scope.form = {
        newPassword: '',
        confirmPassword: ''
    };
    $scope.loading = false;
    $scope.resendLoading = false;
    $scope.error = null;
    $scope.success = null;
    $scope.showNewPassword = false;
    $scope.showConfirmPassword = false;
    $scope.passwordStrength = 'weak';
    $scope.passwordCriteria = {
        length: false,
        uppercase: false,
        lowercase: false,
        number: false,
        special: false
    };
    $scope.passwordsMatch = false;

    // Timer variables
    $scope.timeLeft = 0;
    $scope.expired = false;
    $scope.timer = null;

    // Initialize controller
    $scope.init = function () {
        const token = getParameterFromUrl();
        $scope.loading = true;
        loadUserEmail(token);
    };

    const loadUserEmail = (token) => {
        $http.get(window.baseUrl + '/API/reset/' + token)
            .then(response => {
                if (response.data.status === 'success') {
                    $scope.infos = response.data.infos;
                    $scope.email = response.data.infos.email;
                    $scope.loading = false;

                    if ($scope.infos.tokenExpired) {
                        $scope.expired = true;
                        $scope.timeLeft = 0;
                    } else {
                        $scope.expired = false;
                        $scope.timeLeft = $scope.infos.timeLeft;
                    }
                    $scope.tokenExpire = new Date($scope.infos.tokenExpire);
                    console.log($scope.tokenExpire);
                    $scope.startTimer();
                } else {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        text: 'Failed to load reset data. Please reload the page (Ctrl/Cmd + Shift + R)'
                    });
                }
            })
            .catch(error => {
                console.error(error);
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    text: 'Failed to load reset data. Please reload the page (Ctrl/Cmd + Shift + R)'
                });
            });
    }

    // Start countdown timer
    $scope.startTimer = function () {
        $scope.timer = $interval(function () {
            if ($scope.timeLeft > 0) {
                $scope.timeLeft--;
                $scope.displayTime = $scope.formatTime($scope.timeLeft, $scope.tokenExpire);
            } else {
                $scope.expired = true;
                $interval.cancel($scope.timer);
                $scope.displayTime = "Expired";
            }
        }, 1000);
    };

    // Format time for display
    $scope.formatTime = function (seconds, tokenExpireDate) {
        if (!tokenExpireDate || seconds <= 0) {
            return "Expired";
        }

        const now = new Date();
        const expireDate = new Date(tokenExpireDate);

        // Reset time for date comparison
        const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
        const expireDay = new Date(
            expireDate.getFullYear(),
            expireDate.getMonth(),
            expireDate.getDate()
        );

        const dayDiff = Math.round(
            (expireDay - today) / (1000 * 60 * 60 * 24)
        );

        // 🔹 TODAY → show live countdown
        if (dayDiff === 0) {
            const hrs = Math.floor(seconds / 3600);
            const mins = Math.floor((seconds % 3600) / 60);
            const secs = Math.floor(seconds % 60);

            return `Today ${hrs.toString().padStart(2, '0')}:` +
                `${mins.toString().padStart(2, '0')}:` +
                `${secs.toString().padStart(2, '0')}`;
        }

        // 🔹 TOMORROW
        if (dayDiff === 1) {
            return `Tomorrow at ${expireDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            })
                }`;
        }

        // 🔹 FUTURE DATE
        return expireDate.toLocaleDateString('en-US', {
            weekday: 'short',
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        }) + ' at ' +
            expireDate.toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                hour12: true
            });
    };

    // Check password strength
    $scope.checkPasswordStrength = function () {
        console.log($scope.form.newPassword);
        if (!$scope.form.newPassword) {
            $scope.passwordStrength = 'weak';
            $scope.passwordCriteria = { length: false, uppercase: false, lowercase: false, number: false, special: false };
            $scope.passwordsMatch = false;
            return;
        }

        const pwd = $scope.form.newPassword;

        // Criteria checks
        $scope.passwordCriteria.length = pwd.length >= 8;
        $scope.passwordCriteria.uppercase = /[A-Z]/.test(pwd);
        $scope.passwordCriteria.lowercase = /[a-z]/.test(pwd);
        $scope.passwordCriteria.number = /[0-9]/.test(pwd);
        $scope.passwordCriteria.special = /[^A-Za-z0-9]/.test(pwd);

        // Score calculation
        let score = 0;
        for (let key in $scope.passwordCriteria) {
            if ($scope.passwordCriteria[key]) score++;
        }
        // Determine strength
        if (score <= 2) $scope.passwordStrength = 'weak';
        else if (score <= 4) $scope.passwordStrength = 'medium';
        else $scope.passwordStrength = 'strong';

        // Check if passwords match
        $scope.checkPasswordMatch();
    };

    // Check if passwords match
    $scope.checkPasswordMatch = function () {
        $scope.passwordsMatch = $scope.form.newPassword === $scope.form.confirmPassword && $scope.form.newPassword !== '';
    };

    // Toggle password visibility
    $scope.togglePasswordVisibility = function (field) {
        if (field === 'newPassword') {
            $scope.showNewPassword = !$scope.showNewPassword;
        } else if (field === 'confirmPassword') {
            $scope.showConfirmPassword = !$scope.showConfirmPassword;
        }
    };

    // Check if form is valid
    $scope.isFormValid = function () {
        return $scope.form.newPassword &&
            $scope.form.confirmPassword &&
            $scope.passwordsMatch &&
            $scope.passwordStrength !== 'weak' &&
            $scope.timeLeft > 0;
    };

    // Submit new password
    $scope.submitNewPassword = function () {
        if (!$scope.isFormValid()) {
            $scope.error = 'Please fill all fields correctly';
            return;
        }

        $scope.loading = true;
        $scope.error = null;
        $scope.success = null;

        const token = getParameterFromUrl(); // Get token from URL
        $http.post(
            window.baseUrl + '/API/reset/' + token,
            {
                email: $scope.email,
                newPassword: $scope.form.newPassword,
                confirmPassword: $scope.form.confirmPassword
            }
        ).then(response => {
            if (response.data.status === 'success') {
                Toast.fire({
                    type: 'success',
                    title: 'Success!',
                    msg: response.data.msg || 'Password reset successfully!'
                })

                $scope.loading = false;
                $scope.success = 'Your password has been reset successfully!';

                // Clear form
                $scope.form.newPassword = '';
                $scope.form.confirmPassword = '';

                // Stop timer
                if ($scope.timer) {
                    $interval.cancel($scope.timer);
                }

                $timeout(function () {
                    window.location.href = window.baseUrl + '/login';
                }, 1000);
            } else {
                Toast.fire({
                    type: 'error',
                    title: 'Error!',
                    msg: response.data.msg || 'Something went wrong!'
                })

                $scope.loading = false;
                $scope.error = response.data.msg || 'Faild to reset password. Please try again!';
            }
        })
    };

    // Resend reset link
    $scope.resendLink = function () {
        $scope.resendLoading = true;
        $scope.error = null;

        const token = getParameterFromUrl();
        $http.post(window.baseUrl + '/API/reset/send-email', {
            email: $scope.email
        })
            .then(response => {
                if (response.data.status === 'success') {
                    Toast.fire({
                        type: 'info',
                        title: 'Infomation!',
                        msg: response.data.msg || 'Reset link has been sent to your email!'
                    })

                    $scope.resendLoading = false;
                    $scope.success = 'Reset link has been sent to your email!';
                    $scope.tokenExpired = true;
                    // Stop timer
                    if ($scope.timer) {
                        $interval.cancel($scope.timer);
                    }
                } else {
                    Toast.fire({
                        type: 'error',
                        title: 'Error!',
                        msg: response.data.msg || 'Faild to resend reset link. Please try again!'
                    })

                    $scope.resendLoading = false;
                    $scope.error = response.data.msg || 'Faild to resend reset link. Please try again!';
                }
            })
    };

    // Clean up on destroy
    $scope.$on('$destroy', function () {
        if ($scope.timer) {
            $interval.cancel($scope.timer);
        }
    });

    // Initialize
    $scope.init();
}]);