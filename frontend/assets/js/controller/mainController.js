app.controller('mainController', ["$scope", "$http", function ($scope, $http) {
    // Element selectors using jQuery
    const menuBtn = $('#menu-btn');
    const menuClose = $('#menu-close');
    const sidebar = $('#sidebar');
    const logoName = $('#logo-name');
    const userName = $('#user-name');
    const menuContainer = $('#menu-container');
    const menuLabels = $('.menu-label');
    const lists = $('.list');
    const listIcons = $('.list-icon');
    const logoContainer = $('#logo-container');
    const userContainer = $('#user-container');
    const fullScreen = $('#full-screen');
    const footer = $('#footer');
    const logout = $('#logout');

    $scope.toggleSidebar = function () {
        sidebar.toggleClass('w-0 md:w-16 w-60');
        logoName.toggleClass('hidden');
        userName.toggleClass('hidden');
        logoContainer.toggleClass('justify-center');
        userContainer.toggleClass('justify-center');
        menuContainer.toggleClass('md:mr-0 mr-3');

        menuLabels.each(function () {
            $(this).toggleClass('md:opacity-0 md:mx-2');
        });
        lists.each(function () {
            $(this).toggleClass('md:ml-2');
        });
        listIcons.each(function () {
            $(this).toggleClass('md:ml-2');
        });

        if (window.innerWidth >= 768) {
            footer.toggleClass('w-[calc(100%-4rem)] w-[calc(100%-15rem)]');
        }
    };

    // Rotate arrow when checkbox checked
    $(document).on('change', 'input[type="checkbox"].peer', function () {
        const arrow = $(this).next('label').find('.fa-chevron-down');
        if ($(this).is(':checked')) {
            arrow.addClass('rotate-180');
        } else {
            arrow.removeClass('rotate-180');
        }
    });


    // Sidebar toggle buttons
    menuBtn.off('click').on('click', function () {
        menuBtn.toggleClass('rotate-180');
        $scope.toggleSidebar();
    });
    menuClose.on('click', $scope.toggleSidebar);

    // Initial responsive check
    if (window.innerWidth <= 768) {
        footer.addClass('w-full');
        if (sidebar.hasClass('w-60')) {
            $scope.toggleSidebar();
        }
    }

    // Click outside sidebar on mobile to close
    $(document).on('click', function (e) {
        if (window.innerWidth <= 768) {
            if (sidebar.hasClass('w-60') && !sidebar.is(e.target) && sidebar.has(e.target).length === 0 && !menuBtn.is(e.target)) {
                $scope.toggleSidebar();
            }
        }
    });

    // Fullscreen toggle
    fullScreen.on('click', function (e) {
        e.preventDefault();
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            fullScreen.html('<span class="hidden md:inline-block">Exit full screen</span> <i class="fa-solid fa-minimize"></i>');
        } else {
            document.exitFullscreen();
            fullScreen.html('<span class="hidden md:inline-block">Full screen</span> <i class="fa-solid fa-maximize"></i>');
        }
    });

    // Sidebar hover for footer width adjustment
    sidebar.on('mouseenter', function () {
        if (sidebar.hasClass('md:w-16')) {
            footer.addClass('w-[calc(100%-15rem)]').removeClass('w-[calc(100%-4rem)]');
        }
    });
    sidebar.on('mouseleave', function () {
        if (!sidebar.hasClass('w-60')) {
            footer.removeClass('w-[calc(100%-15rem)]').addClass('w-[calc(100%-4rem)]');
        }
    });

    // Logout with $http
    logout.on('click', function (e) {
        e.preventDefault();
        $http.post(window.baseUrl + '/API/logout').then(function (response) {
            const data = response.data;
            if (data.status === 'success') {
                Toast.fire({ type: 'success', title: 'Logout', msg: data.msg });
                setTimeout(function () {
                    window.location.href = window.baseUrl + '/login';
                }, 1000);
            }
        }, function (error) {
            console.error('Logout Error:', error);
            Toast.fire({ type: 'error', title: 'Error', msg: 'Logout failed' });
        });
    });
}]);