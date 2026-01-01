app.controller('ForgotPasswordController', function ($scope, $http) {

    $scope.email = '';
    $scope.loading = false;
    $scope.error = '';
    $scope.success = '';

    $scope.sendResetLink = function () {
        $scope.error = '';
        $scope.success = '';

        if (!$scope.email) {
            $scope.error = 'Email is required';
            return;
        }

        $scope.loading = true;

        $http.post(window.baseUrl + '/API/reset/send-email', {
            email: $scope.email
        }).then(function (res) {

            if (res.data.status === 'success') {
                $scope.success = res.data.message || 'Password reset link sent to your email.';
                $scope.email = '';
            } else {
                $scope.error = res.data.message || 'Unable to send reset link.';
            }

        }).catch(function () {
            $scope.error = 'Server error. Please try again later.';
        }).finally(function () {
            $scope.loading = false;
        });
    };
});
