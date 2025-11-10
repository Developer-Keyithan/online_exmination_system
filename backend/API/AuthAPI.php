<?php
use Backend\Modal\Auth;

class AuthAPI
{

    public function showLogin()
    {
        if (Auth::isLoggedIn()) {
            redirect('dashboard'); // if already logged in, redirect
        }
        return view('auth.login', ['title' => 'User Login']);
    }

    public function login()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $email = $_POST['email'] ?? null;
            $password = $_POST['password'] ?? null;

            if (!$email || !$password) {
                throw new Exception("Email and password are required.");
            }

            $statement = db()->prepare(" SELECT u.*, g.permission, g.name as role_name FROM users u LEFT JOIN user_group g ON u.user_group = g.id WHERE u.email = ?");
            $statement->execute([$email]);
            $user = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception("User not found.");
            }

            if (!password_verify($password, $user['password'])) {
                throw new Exception("Invalid password.");
            }

            // Start session
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // Save session
            $_SESSION['user'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['user_group'];
            $_SESSION['username'] = $user['name'];
            $_SESSION['role_name'] = $user['role_name'];
            $_SESSION['permissions'] = !empty($user['permission']) ? explode(',', $user['permission']) : [];

            return json_encode([
                'status' => 'success',
                'msg' => 'Login successful',
                'user' => [
                        'id' => $user['id'],
                        'role' =>  $user['user_group'],
                        'username' => $user['name'],
                        'email' => $email
                    ]
            ]);

        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy all session data
        session_unset();
        session_destroy();

        // Return JSON response
        header('Content-Type: application/json; charset=utf-8');
        return json_encode([
            'status' => 'success',
            'msg' => 'Logged out successfully'
        ]);
    }
}
