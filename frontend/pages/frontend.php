<!DOCTYPE html>
<!-- start html and set this is a anguler app -->
<html lang="en" ng-app="ngApp">
<!-- load head tag  -->
<?php include 'layouts/head.php' ?>
<!-- start body -->

<body
    class="flex flex-row justify-center <?php echo (currentNav() == 'login') ? 'items-center' : 'text-white' ?> min-h-[100vh] overflow-hidden relative bg-gradient-to-br from-[#0f172a] from-0% via-[#1e293b] via-50% to-[#334155] to-100%">
    <?php if (currentNav() != 'login')
        include 'layouts/sidebar.php' ?>
        <div class="w-full">
        <?php if (currentNav() != 'login')
        include 'layouts/header.php'; ?>
        <div class="w-full px-4" <?= $this->getController() ? 'ng-controller="' . $this->getController() . '"' : '' ?>>
            <?php if (currentNav() != 'login'): ?>
                <div class="mt-3 mb-4 rounded-lg">
                    <div class="flex flex-row justify-between">
                        <h4 class="text-white text-xl font-semibold capitalize">
                            <?php echo ucwords(str_replace('_', ' ', currentNav())); ?>
                        </h4>

                        <?php if (currentNav() != 'dashboard'): ?>
                            <nav class="flex px-4 py-2 text-gray-700 bg-[#0006] rounded" aria-label="Breadcrumb">
                                <ol class="inline-flex items-center space-x-1 md:space-x-3 list-none">
                                    <li class="inline-flex items-center">
                                        <a href="dashboard" class="inline-flex items-center text-gray-300 hover:text-blue-300">
                                            Home
                                        </a>
                                    </li>

                                    <?php
                                    // Dynamic breadcrumb using currentNav() and its parents
                                    $breadcrumbs = explode('/', currentNav()); // if you use slashes in URL paths
                                    $path = '';
                                    foreach ($breadcrumbs as $index => $crumb):
                                        $path .= $crumb;
                                        $isLast = ($index == count($breadcrumbs) - 1);
                                        ?>
                                        <li aria-current="<?php echo $isLast ? 'page' : ''; ?>">
                                            <div class="flex items-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                                    <path
                                                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" />
                                                </svg>
                                                <?php if ($isLast): ?>
                                                    <span
                                                        class="ml-1 text-gray-500 md:ml-2"><?php echo ucwords(str_replace('_', ' ', $crumb)); ?></span>
                                                <?php else: ?>
                                                    <a href="<?php echo $path; ?>"
                                                        class="ml-1 text-gray-700 hover:text-blue-600 md:ml-2">
                                                        <?php echo ucwords(str_replace('_', ' ', $crumb)); ?>
                                                    </a>
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
        <?php if (currentNav() != 'login')
            include 'layouts/footer.php' ?>
        </div>
        <!-- end body -->
    </body>
    <!-- load all scripts -->
<?php include 'layouts/script.php' ?>
<!-- end html -->

</html>