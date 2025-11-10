<?php
// session_start();
// if (!isset($_SESSION['user'])) {
//     header("Location: /NIT/exam/login?redirect=" . BASE_URL . currentNav());
// }
?>

<?php $this->controller('mainController') ?>

<nav id="navbar"
    class="sticky top-0 flex flex-row items-center justify-between pl-1 pr-1 py-2 bg-[#0003] border-[#fff6] border-b md:rounded-br-2xl backdrop-blur transition-all duration-200 max-h-10 w-full z-[999999]">
    <button id="menu-btn" class="text-white px-3 py-1 rounded-md hover:bg-[#fff2] transition-all duration-300">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="flex flex-row">
        <button id="notification"
            class="relative text-white rounded md:rounded-full hover:bg-[#fff2] transition-all duration-300 px-2 md:px-3 py-1 after:content-['99+'] after:bg-red-600 after:text-white after:absolute after:top-0 after:right-0 after:h-4 after:rounded-full after:text-xs after:font-bold after:px-1">
            <i class="fa-solid fa-bell"></i></button>
        <button id="full-screen"
            class="hidden md:inline-block text-white rounded md:rounded-full hover:bg-[#fff2] transition-all duration-300 px-2 md:px-3 py-1">
            <span class="hidden md:inline-block">Full screen</span> <i class="fa-solid fa-maximize"></i></button>
        <button id="logout"
            class="text-white rounded md:rounded-full  hover:bg-[#fff2] transition-all duration-300 px-2 md:px-3 py-1">
            <span class="hidden md:inline-block">Logout</span> <i class="fa-solid fa-right-from-bracket"></i>
        </button>
    </div>
</nav>

<?php $this->push('js'); ?>
<!-- <script type="module">
    const menuBtn = document.getElementById('menu-btn');
    const menuClose = document.getElementById('menu-close');
    const sidebar = document.getElementById('sidebar');
    const logoName = document.getElementById('logo-name');
    const menuContainer = document.getElementById('menu-container');
    const menuLabels = document.getElementsByClassName('menu-label');
    const lists = document.getElementsByClassName('list');
    const listIcons = document.getElementsByClassName('list-icon');
    const logoContainer = document.getElementById('logo-container');
    const fullScreen = document.getElementById('full-screen');
    const footer = document.getElementById('footer');
    const logout = document.getElementById('logout');

    function toggleSidebar() {
        sidebar.classList.toggle('w-0');
        sidebar.classList.toggle('md:w-16');
        sidebar.classList.toggle('w-60');

        logoName.classList.toggle('hidden');
        logoContainer.classList.toggle('justify-center');

        menuContainer.classList.toggle('md:mr-0');
        menuContainer.classList.toggle('mr-3');

        for (let counter = 0; counter < menuLabels.length; counter++) {
            menuLabels[counter].classList.toggle('md:opacity-0');
            menuLabels[counter].classList.toggle('md:mx-2');
        }

        for (let counter = 0; counter < lists.length; counter++) {
            lists[counter].classList.toggle('md:ml-2');
        }

        for (let counter = 0; counter < listIcons.length; counter++) {
            listIcons[counter].classList.toggle('md:ml-2');
        }

        if (window.innerWidth >= 768) {
            footer.classList.toggle('w-[calc(100%-4rem)]');
            footer.classList.toggle('w-[calc(100%-15rem)]');
        }
    }

    menuBtn.addEventListener('click', () => toggleSidebar());
    menuClose.addEventListener('click', () => toggleSidebar());

    if (window.innerWidth <= 768) {
        footer.classList.add('w-full');
        if (sidebar.classList.contains('w-60')) {
            toggleSidebar();
        }
    }

    document.addEventListener('click', (e) => {
        const sidebar = document.getElementById('sidebar');

        if (window.innerWidth <= 768) {
            if (sidebar.classList.contains('w-60') && !sidebar.contains(e.target) && !menuBtn.contains(e.target)) {
                toggleSidebar();
            }
        }
    });

    fullScreen.addEventListener('click', (e) => {
        e.preventDefault();

        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            fullScreen.innerHTML = '<span class="hidden md:inline-block">Exit full screen</span> <i class="fa-solid fa-minimize"></i>';
        } else {
            document.exitFullscreen();
            fullScreen.innerHTML = '<span class="hidden md:inline-block">Full screen</span> <i class="fa-solid fa-maximize"></i>';
        }
    });

    sidebar.addEventListener('mouseenter', () => {
        if (sidebar.classList.contains('md:w-16')) {
            footer.classList.add('w-[calc(100%-15rem)]');
            footer.classList.remove('w-[calc(100%-4rem)]');
        }
    });

    sidebar.addEventListener('mouseleave', () => {
        if (!sidebar.classList.contains('w-60')) {
            footer.classList.remove('w-[calc(100%-15rem)]');
            footer.classList.add('w-[calc(100%-4rem)]');
        }
    });

    logout.addEventListener('click', (e) => {
        e.preventDefault();
        fetch('API/logout', {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Toast({ type: 'success', title: 'Logout', msg: data.msg });
                    setTimeout(() => {
                        window.location.href = 'login';
                    }, 1fff)
                }
            })
            .catch(error => {
                console.error('Logout Error:', error);
                Toast({ type: 'error', title: 'Error', msg: 'Logout failed' });
            });
    })
</script> -->
<?php $this->endpush(); ?>