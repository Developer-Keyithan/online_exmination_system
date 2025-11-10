<?php
use Backend\Modal\Auth;

class DashboardAPI {

    public function __construct(){
        if (!Auth::isLoggedIn()) {
               redirect('login');
        }
    }

    public function index() {
        // If user is logged in, show dashboard, otherwise home
        // if (Auth::isLoggedIn()) {
        //     redirect('admin.dashboard');
        //     // return view('admin.dashboard', ['title' => 'Dashboard']);
        // }
        // return view('auth.login', ['title' => 'Login']);
        // redirect('login');
    }
}
