<?php
class AuthMiddleware {
    public function handle($router) {
        session_start();
        if (!isset($_SESSION['user'])) {
            $router->redirect('login'); // redirect if not logged in
        }
    }
}
