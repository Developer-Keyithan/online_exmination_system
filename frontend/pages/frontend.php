<!DOCTYPE html>
<!-- start html and set this is a anguler app -->
<html lang="en" ng-app="ngApp">
<!-- load head tag  -->
<?php include 'layouts/head.php' ?>
<!-- start body -->
<?php
function isAuthLayout()
{
    $authPages = ['login', 'forgot-password', 'reset-password', '404', 'unauthorized', 'forbidden'];
    return in_array(currentNav(), $authPages) || in_array(currentNav(1), $authPages);
}

?>

<body class="flex flex-row justify-center <?php echo isAuthLayout() ? 'items-center' : 'text-white'; ?> min-h-[100vh] relative bg-gradient-to-br from-[#0f172a] from-0% via-[#1e293b] via-50% to-[#334155] to-100%">
    <?php if (!isAuthLayout())
        include 'layouts/sidebar.php' ?>
    <div class="w-full">
        <?php if (!isAuthLayout())
            include 'layouts/header.php'; ?>
        <div class="w-full <?php echo isAuthLayout() ? 'p-0' : 'p-4' ?>"
            <?= $this->getController() ? 'ng-controller="' . $this->getController() . '"' : '' ?>>
            <?php if (!isAuthLayout()): ?>
                <div class="mt-3 mb-4 rounded-lg">
                    <div class="flex flex-row justify-between">
                        <h4 class="text-white text-xl font-semibold capitalize">
                            <?php
                            // Uncomment if you want to show current nav title
                            // echo ucwords(str_replace('_', ' ', currentNav())); 
                            ?>
                        </h4>

                        <?php if (currentNav() != 'dashboard'): ?>
                            <nav class="flex px-4 py-2 text-gray-700 bg-[#0006] rounded" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3 list-none">
                                    <!-- Home link -->
                                    <li class="inline-flex items-center">
                                        <a href="<?= BASE_URL ?>/dashboard"
                                            class="inline-flex items-center text-gray-300 hover:text-blue-300">
                                            Home
                                        </a>
                                    </li>

                                    <?php
                                    $current = currentNav();
                                    $breadcrumbs = explode('/', $current);

                                    // Remove numeric crumbs AND crumbs longer than 50 characters
                                    $breadcrumbs = array_filter($breadcrumbs, function ($crumb) {
                                        return !is_numeric($crumb) && strlen($crumb) <= 50;
                                    });


                                    $path = '';
                                    foreach ($breadcrumbs as $index => $crumb):
                                        $path .= '/' . $crumb;
                                        $isLast = ($index == count($breadcrumbs) - 1);
                                    ?>
                                        <li aria-current="<?= $isLast ? 'page' : ''; ?>">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                                                </svg>
                                                <?php if ($isLast): ?>
                                                    <span class="ml-1 text-gray-500 md:ml-2">
                                                        <?= ucwords(str_replace('_', ' ', $crumb)) ?>
                                                    </span>
                                                <?php else: ?>
                                                    <!-- <a href="<?php // echo BASE_URL . $path 
                                                                    ?>"
                                                        class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                                                        <?php // echo ucwords(str_replace('_', ' ', $crumb)) 
                                                        ?>
                                                    </a> -->
                                                    <span class="ml-1 text-gray-500">
                                                        <?php echo ucwords(str_replace('_', ' ', $crumb)) ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ol>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- load dynamic content -->
            <?= $this->section('content') ?>
        </div>

        <?php if (!isAuthLayout())
            include 'layouts/footer.php' ?>
    </div>
    <!-- end body -->
</body>
<!-- load all scripts -->
<?php include 'layouts/script.php' ?>
<!-- end html -->

</html>