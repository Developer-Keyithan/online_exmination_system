<?php $this->extend('frontend');
$this->controller('UserController'); ?>
<?php $this->start('content'); ?>
<div class="my-4 grid grid-cols-4">
    <p>{{ message }}</p>
    <div ng-repeate="user on users" class="bg-white rounded-lg shadow-lg p-4 text-black">
        <div class="">
            <h5 class="card-title">{{user.name}}</h5>
            <p class="card-text">{{user.email}}</p>
            <a href="#" class="btn btn-primary">Go somewhere</a>
        </div>
    </div>
    <button id="content-popover">Content popover test</button>
    <button id="success-popover">Success popover test</button>
</div>
<?php $this->end(); ?>