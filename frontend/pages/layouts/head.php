<!-- start head -->

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- set dynamic title  -->
    <title><?= $title ?? 'NEW APP' ?></title>
    <!-- STYLESHEET -->
    <link rel="stylesheet" href="<?= asset('assets/css/theme.min.css') ?>">
    <!-- FONT AWESOME 7.0.1 -->
    <link rel="stylesheet" href="<?php echo asset('assets/plugins/fontawesome-free-7.1.0-web/css/all.min.css') ?>">
    <!-- load dynamic css  -->
    <?= $this->stack('css') ?>
    <!-- set wanted script -->
    <script type="module">
        var baseUrl = '<?php echo BASE_URL ?>';
    </script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="<?= asset('assets/js/angular.min.js') ?>"></script>
    <script src="<?= asset('assets/js/angularApp.js') ?>"></script>
    <script type="module" src="<?= asset('assets/js/main.js') ?>"></script>
    <!-- end head -->
</head>