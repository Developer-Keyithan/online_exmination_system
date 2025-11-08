<!DOCTYPE html>
<!-- start html and set this is a anguler app -->
<html lang="en" ng-app="ngApp">
<!-- load head tag  -->
<?php include 'layouts/head.php' ?>
<!-- start body -->

<body class="flex flex-row justify-center items-center min-h-[100vh] overflow-hidden relative bg-gradient-to-br from-[#0f172a] from-0% via-[#1e293b] via-50% to-[#334155] to-100%">
    <div class="mx-auto my-auto" <?= $this->getController() ? 'ng-controller="' . $this->getController() . '"' : '' ?>>
        <!-- load dynamic content -->
        <?= $this->section('content') ?>
    </div>
    <!-- end body -->
</body>
<!-- load all scripts -->
<?php include 'layouts/script.php' ?>
<!-- end html -->

</html>