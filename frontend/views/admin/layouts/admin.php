<!DOCTYPE html>
<html lang="en" ng-app="ngApp">
<?php include 'head.php' ?>
<body class="flex min-h-screen bg-gradient-to-br from-cyan-400 via-cyan-600 to-blue-600 overflow-x-hidden">
    <div <?= $this->getController() ? 'ng-controller="' . $this->getController() . '"' : '' ?>>
        <!-- load dynamic content -->
        <?= $this->section('content') ?>
    </div>
</body>
</html>