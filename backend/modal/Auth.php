<?php
namespace Backend\Modal;

class Auth {

    public function login($username, $password) {
        $userModel = new User();
        $user = $userModel->checkUser($username, $password);
        if ($user) {
            $_SESSION['user'] = $user;
        }
        return $user;
    }

    public static function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    public static function logout() {
        session_destroy();
    }

    public static function getUser() {
        $user['id'] = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        return $user;
    }
}
