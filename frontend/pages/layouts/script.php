<?php if ($this->getController()): ?>
    <script src="<?= asset('assets/js/controller/' . $this->getController() . '.js'); ?>"></script>
<?php endif; ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Element selectors using vanilla JS
        const menuBtn = document.getElementById('menu-btn');
        const menuClose = document.getElementById('menu-close');
        const sidebar = document.getElementById('sidebar');
        const logoName = document.getElementById('logo-name');
        const userName = document.getElementById('user-name');
        const menuContainer = document.getElementById('menu-container');
        const menuLabels = document.querySelectorAll('.menu-label');
        const lists = document.querySelectorAll('.list');
        const listIcons = document.querySelectorAll('.list-icon');
        const logoContainer = document.getElementById('logo-container');
        const userContainer = document.getElementById('user-container');
        const fullScreen = document.getElementById('full-screen');
        const footer = document.getElementById('footer');
        const logout = document.getElementById('logout');

        function toggleSidebar() {
            // Toggle sidebar classes
            sidebar.classList.toggle('w-0');
            sidebar.classList.toggle('md:w-16');
            sidebar.classList.toggle('w-60');

            logoName.classList.toggle('hidden');
            userName.classList.toggle('hidden');
            logoContainer.classList.toggle('justify-center');
            userContainer.classList.toggle('justify-center');
            menuContainer.classList.toggle('md:mr-0');
            menuContainer.classList.toggle('mr-3');

            // Toggle menu labels
            menuLabels.forEach(function (label) {
                label.classList.toggle('md:opacity-0');
                label.classList.toggle('md:mx-2');
            });

            // Toggle lists
            lists.forEach(function (list) {
                list.classList.toggle('md:ml-2');
            });

            // Toggle list icons
            listIcons.forEach(function (icon) {
                icon.classList.toggle('md:ml-2');
            });

            // Adjust footer width for medium screens and above
            if (window.innerWidth >= 768) {
                footer.classList.toggle('w-[calc(100%-4rem)]');
                footer.classList.toggle('w-[calc(100%-15rem)]');
            }
        }

        // Rotate arrow when checkbox checked
        document.addEventListener('change', function (e) {
            if (e.target.type === 'checkbox' && e.target.classList.contains('peer')) {
                const arrow = e.target.nextElementSibling.querySelector('.fa-chevron-right');
                if (e.target.checked) {
                    arrow.classList.add('rotate-90');
                } else {
                    arrow.classList.remove('rotate-90');
                }
            }
        });

        // Sidebar toggle buttons
        if (menuBtn) {
            menuBtn.addEventListener('click', function () {
                menuBtn.classList.toggle('rotate-180');
                toggleSidebar();
            });
        }

        if (menuClose) {
            menuClose.addEventListener('click', toggleSidebar);
        }

        // Initial responsive check
        if (window.innerWidth <= 768) {
            footer.classList.add('w-full');
            if (sidebar.classList.contains('w-60')) {
                toggleSidebar();
            }
        }

        // Click outside sidebar on mobile to close
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                const isSidebar = sidebar.contains(e.target);
                const isMenuBtn = menuBtn.contains(e.target);

                if (sidebar.classList.contains('w-60') && !isSidebar && !isMenuBtn) {
                    toggleSidebar();
                }
            }
        });

        // Fullscreen toggle
        if (fullScreen) {
            fullScreen.addEventListener('click', function (e) {
                e.preventDefault();
                const fullscreenSpan = fullScreen.querySelector('span');
                const fullscreenIcon = fullScreen.querySelector('i');

                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    if (fullscreenSpan) fullscreenSpan.textContent = 'Exit full screen';
                    if (fullscreenIcon) {
                        fullscreenIcon.classList.remove('fa-maximize');
                        fullscreenIcon.classList.add('fa-minimize');
                    }
                } else {
                    document.exitFullscreen();
                    if (fullscreenSpan) fullscreenSpan.textContent = 'Full screen';
                    if (fullscreenIcon) {
                        fullscreenIcon.classList.remove('fa-minimize');
                        fullscreenIcon.classList.add('fa-maximize');
                    }
                }
            });
        }

        // Sidebar hover for footer width adjustment
        if (sidebar) {
            sidebar.addEventListener('mouseenter', function () {
                if (sidebar.classList.contains('md:w-16')) {
                    footer.classList.add('w-[calc(100%-15rem)]');
                    footer.classList.remove('w-[calc(100%-4rem)]');
                }
            });

            sidebar.addEventListener('mouseleave', function () {
                if (!sidebar.classList.contains('w-60')) {
                    footer.classList.remove('w-[calc(100%-15rem)]');
                    footer.classList.add('w-[calc(100%-4rem)]');
                }
            });
        }

        // Logout with fetch API
        if (logout) {
            logout.addEventListener('click', function (e) {
                e.preventDefault();

                fetch(window.baseUrl + '/API/logout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            // Assuming Toast is a global object
                            if (typeof Toast !== 'undefined') {
                                Toast.fire({ type: 'success', title: 'Logout', msg: data.msg });
                            }
                            setTimeout(function () {
                                window.location.href = window.baseUrl + '/login';
                            }, 1000);
                        }
                    })
                    .catch(error => {
                        console.error('Logout Error:', error);
                        if (typeof Toast !== 'undefined') {
                            Toast.fire({ type: 'error', title: 'Error', msg: 'Logout failed' });
                        }
                    });
            });
        }
    });
</script>
<!-- laod dynamic js -->
<?= $this->stack('js') ?>