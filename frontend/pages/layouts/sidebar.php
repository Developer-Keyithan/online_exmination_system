<?php
$collapse = isCollapse();
$user_icon = config('app.user_icon'); // assets/img/logo.png
$logo_icon = config('app.logo');      // assets/img/logo.png

// Full filesystem paths for file_exists()
$logo_path = $_SERVER['DOCUMENT_ROOT'] . '/NIT/exam/frontend/' . $logo_icon;
$user_icon_path = $_SERVER['DOCUMENT_ROOT'] . '/NIT/exam/frontend/' . $user_icon;
?>

<aside>
    <div id="sidebar"
        class="group h-screen bg-[#0003] border-[#fff6] border-r backdrop-blur transition-all duration-200 fixed md:sticky z-[1000000] top-0 left-0 overflow-hidden md:hover:w-60 <?php echo $collapse ? 'w-0 md:w-16' : 'w-60' ?>">
        <button id="menu-close" class="md:hidden rounded-full text-white absolute top-1 right-1 w-5 h-5 text-xs">
            <i class="fa-solid fa-xmark"></i>
        </button>
        <div id="logo-container"
            class="flex flex-row items-center gap-4 text-white p-2 border-b border-[#fff6] group-hover:justify-start <?php echo $collapse ? 'justify-center' : '' ?>">
            <a href="<?php echo BASE_URL ?>" class="text-3xl bg-gradient-to-br from-[#97e8ff] to-[#b0d4ff] rounded-full overflow-hidden">
                <?php if (file_exists($logo_path)): ?>
                    <img width="50px" height="50px" src="<?php echo asset($logo_icon); ?>" alt="Profile Img">
                <?php else: ?>
                    <i class="fa-solid fa-user border-2 border-white rounded-full h-[50px] w-[50px]"></i>
                <?php endif ?>
            </a>
            <a href="<?php echo BASE_URL ?>" id="logo-name"
                class="flex flex-col items-start justify-between transition-all duration-300 group-hover:block <?php echo $collapse ? 'hidden' : 'block' ?>">
                <p><?php echo config('app.app_name') ?> </p>
                <p class="text-xs">V.<?php echo config('app.version') ?></p>
            </a>
        </div>
        <div id="user-container"
            class="flex flex-row items-center gap-4 text-white  bg-gradient-to-br from-[#4dd8ff90] to-[#348fff90] p-2 group-hover:justify-start <?php echo $collapse ? 'justify-center' : '' ?>">
            <div class="text-3xl rounded-full overflow-hidden">
                <?php if (file_exists($user_icon_path)): ?>
                    <img  width="50px" height="50px" class="bg-gradient-to-br from-[#97e8ff] to-[#b0d4ff]" src="<?php echo asset($user_icon); ?>" alt="Profile Img">
                <?php else: ?>
                    <div class="border-1 border-white  bg-gradient-to-br from-[#4dd8ff] to-[#348fff] rounded-full h-[50px] w-[50px] flex flex-row items-center justify-center">
                        <i class="fa-solid fa-user "></i>
                    </div>
                <?php endif; ?>
            </div>
            <div id="user-name"
                class="flex flex-col items-start justify-between w-[calc(100% - 50px)] transition-all duration-300 group-hover:block <?php echo $collapse ? 'hidden' : 'block' ?>">
                <p><?php echo $_SESSION['username'] ?> <small>(<?php echo $_SESSION['role_name'] ?>)</small></p>
                <p class="text-xs"><?php echo $_SESSION['email'] ?></p>
            </div>
        </div>
        <div id="menu-container"
            class="mt-3 pb-32 transition-all duration-300 group-hover:mr-3 overflow-x-hidden h-full <?php echo $collapse ? 'md:mr-0' : 'mr-3' ?>">
            <?php include 'menu.php' ?>
        </div>
    </div>
</aside>