app.controller('ResetPasswordController', ['$scope', '$http', '$timeout', '$interval', '$location', function ($scope, $http, $timeout, $interval, $location) {
    // Initialize scope variables
    $scope.email = '';
    $scope.newPassword = '';
    $scope.confirmPassword = '';
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
    $scope.timeLeft = 900; // 15 minutes in seconds
    $scope.expired = false;
    $scope.timer = null;

    // Initialize controller
    $scope.init = function () {
        // Extract token from URL (in real app, this would come from backend)
        const token = $location.search().token || 'demo-token';

        // Simulate fetching email from token
        $scope.loading = true;
        $timeout(function () {
            // In real app: $http.get('/api/auth/verify-reset-token/' + token)
            $scope.email = 'user@example.com'; // Fetched from server
            $scope.loading = false;

            // Start countdown timer
            $scope.startTimer();
        }, 1000);
    };

    // Start countdown timer
    $scope.startTimer = function () {
        $scope.timer = $interval(function () {
            if ($scope.timeLeft > 0) {
                $scope.timeLeft--;
            } else {
                $scope.expired = true;
                $interval.cancel($scope.timer);
            }
        }, 1000);
    };

    // Format time for display
    $scope.formatTime = function (seconds) {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
    };

    // Check password strength
    $scope.checkPasswordStrength = function () {
        if (!$scope.newPassword) {
            $scope.passwordStrength = 'weak';
            return;
        }

        let score = 0;

        // Check criteria
        $scope.passwordCriteria.length = $scope.newPassword.length >= 8;
        $scope.passwordCriteria.uppercase = /[A-Z]/.test($scope.newPassword);
        $scope.passwordCriteria.lowercase = /[a-z]/.test($scope.newPassword);
        $scope.passwordCriteria.number = /[0-9]/.test($scope.newPassword);
        $scope.passwordCriteria.special = /[^A-Za-z0-9]/.test($scope.newPassword);

        // Calculate score
        if ($scope.passwordCriteria.length) score++;
        if ($scope.passwordCriteria.uppercase) score++;
        if ($scope.passwordCriteria.lowercase) score++;
        if ($scope.passwordCriteria.number) score++;
        if ($scope.passwordCriteria.special) score++;

        // Determine strength
        if (score <= 2) {
            $scope.passwordStrength = 'weak';
        } else if (score <= 4) {
            $scope.passwordStrength = 'medium';
        } else {
            $scope.passwordStrength = 'strong';
        }

        // Check password match
        $scope.checkPasswordMatch();
    };

    // Check if passwords match
    $scope.checkPasswordMatch = function () {
        $scope.passwordsMatch = $scope.newPassword === $scope.confirmPassword && $scope.newPassword !== '';
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
        return $scope.newPassword &&
            $scope.confirmPassword &&
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

        // In real app: $http.post('/api/auth/reset-password', {
        //     token: $location.search().token,
        //     password: $scope.newPassword,
        //     confirmPassword: $scope.confirmPassword
        // })

        // Simulate API call
        $timeout(function () {
            $scope.loading = false;
            $scope.success = 'Your password has been reset successfully!';

            // Clear form
            $scope.newPassword = '';
            $scope.confirmPassword = '';

            // Stop timer
            if ($scope.timer) {
                $interval.cancel($scope.timer);
            }

            // Redirect to login after 5 seconds
            $timeout(function () {
                window.location.href = window.baseUrl + '/auth/login';
            }, 5000);

        }, 2000);
    };

    // Resend reset link
    $scope.resendLink = function () {
        $scope.resendLoading = true;
        $scope.error = null;

        // In real app: $http.post('/api/auth/resend-reset-link', { email: $scope.email })

        $timeout(function () {
            $scope.resendLoading = false;

            // Reset timer
            if ($scope.timer) {
                $interval.cancel($scope.timer);
            }
            $scope.timeLeft = 900;
            $scope.expired = false;
            $scope.startTimer();

            // Show success message
            $scope.success = 'New reset link has been sent to your email!';

            // Clear success message after 5 seconds
            $timeout(function () {
                $scope.success = null;
            }, 5000);

        }, 1500);
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