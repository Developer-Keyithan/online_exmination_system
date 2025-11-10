<?php $collapse = isCollapse() ?>

<aside>
    <div id="sidebar"
        class="group h-screen bg-[#0003] border-[#fff6] border-r backdrop-blur transition-all duration-200 fixed md:sticky z-[1000000] top-0 left-0 overflow-hidden md:hover:w-60 <?php echo $collapse ? 'w-0 md:w-16' : 'w-60' ?>">
        <button id="menu-close" class="md:hidden rounded-full text-white absolute top-1 right-1 w-5 h-5 text-xs">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div id="logo-container"
            class="flex flex-row items-center gap-4 text-white p-2 border-b border-[#fff6] group-hover:justify-start <?php echo $collapse ? 'justify-center' : '' ?>">
            <a href="<?php echo BASE_URL ?>" class="text-3xl border-2 border-white rounded-full overflow-hidden">
                <?php if (file_exists(config('app.logo'))): ?>
                    <img src="<?php echo asset(config('app.logo')) ?>" alt="Logo">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif ?>
            </a>
            <a href="<?php echo BASE_URL ?>" id="logo-name"
                class="flex flex-col items-start justify-between transition-all duration-300 group-hover:block <?php echo $collapse ? 'hidden' : 'block' ?>">
                <p><?php echo config('app.app_name') ?> </p>
                <p class="text-xs">V.<?php echo config('app.version') ?></p>
            </a>
        </div>
        <div id="user-container"
            class="flex flex-row items-center gap-4 text-white bg-[#fff6] p-2 group-hover:justify-start <?php echo $collapse ? 'justify-center' : '' ?>">
            <div class="text-3xl border-2 border-white rounded-full overflow-hidden">
                <?php if (file_exists(config('app.logo'))): ?>
                    <img src="<?php echo asset(config('app.logo')) ?>" alt="Logo">
                <?php else: ?>
                    <i class="fa-solid fa-user"></i>
                <?php endif ?>
            </div>
            <div class="flex flex-col items-start justify-between transition-all duration-300 group-hover:block <?php echo $collapse ? 'hidden' : 'block' ?>">
                <p><?php echo $_SESSION['username'] ?> <small>(<?php echo $_SESSION['role_name'] ?>)</small></p>
                <p class="text-xs"><?php echo $_SESSION['email'] ?></p>
            </div>
        </div>
        <div id="menu-container"
            class="my-3 transition-all duration-300 group-hover:mr-3 overflow-x-hidden h-full <?php echo $collapse ? 'md:mr-0' : 'mr-3' ?>">
            <?php include 'menu.php' ?>
        </div>
    </div>
</aside>