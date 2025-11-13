app.controller('UserController', [
    "$scope", "$http",
    function ($scope, $http) {
        $scope.message = "Welcome to AngularJS running inside PHP!";

        const popoverTestBtn = $('#content-popover');
        popoverTestBtn.off('click').on('click', function (e) {
            console.log('clicked');
            e.preventDefault();
            Toast.popover({
                type: 'apiContent',
                title: 'API Content',
                apiConfig: {
                    endpoint: 'test',
                    method: 'GET',
                },
                buttons: [{
                    text: 'Close',
                }]
            })
        })

        const successPopoverBtn = $('#success-popover');
        successPopoverBtn.off('click').on('click', function (e) {
            console.log('clicked');
            e.preventDefault();
            Toast.popover({
                type: 'success',
                title: 'Success',
                content: 'This is a test success popover'
            })
        })
    }
]);