<?php 
Router::get('/', 'DashboardAPI@index', 'home',['auth']);
Router::get('/login', 'AuthAPI@showLogin', 'login');

Router::group(['prefix' => 'admin', 'middleware' => ['auth']], function() {
    Router::get('/dashboard', 'DashboardAPI@dashboard', 'dashboard');
});
