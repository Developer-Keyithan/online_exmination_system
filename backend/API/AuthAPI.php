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

            // print_r($user['permission']);
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
            $rawPermissions = $user['permission'] ?? '';
            $_SESSION['permissions'] = [];
            if (!empty($rawPermissions)) {
                $fixedJson = str_replace("'", '"', $rawPermissions);
                $decoded = json_decode($fixedJson, true);

                if (is_array($decoded)) {
                    $_SESSION['permissions'] = $decoded;
                }
            }
            $_SESSION['logged_in_at'] = date('Y-m-d H:i:s');

            return json_encode([
                'status' => 'success',
                'msg' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'role' => $user['user_group'],
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
    public function getSession()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        header('Content-Type: application/json');

        try {
            if (!isset($_SESSION)) {
                throw new Exception('No session started.');
            }

            echo json_encode([
                'status' => 'success',
                'msg' => 'Session retrieved successfully',
                'user' => $_SESSION
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'msg' => $e->getMessage(),
                'user' => new stdClass()
            ]);
        }
        exit;
    }

    public function getLoggedInUser()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user'])) {
            http_response_code(401);
            return json_encode([
                'status' => 'error',
                'msg' => 'Not logged in'
            ]);
        }

        $stmt = db()->prepare(" SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);


        return json_encode([
            'status' => 'success',
            'msg' => 'User retrieved successfully',
            'user' => [
                'id' => $_SESSION['user'],
                'email' => $_SESSION['email'],
                'role' => $_SESSION['role'],
                'name' => $user['name'],
                'username' => $user['username'],
                'role_name' => $_SESSION['role_name'],
                'registered_at' => $user['created_at'],
                'last_login' => str_replace(' ', "T", $_SESSION['logged_in_at']),
                'reg_no' => $user['reg_no'] ? $user['reg_no'] : 'Not provided',
                'phone' => $user['phone'] ? $user['phone'] : 'Not provided',
                'created_at' => str_replace(' ', "T", $user['created_at']),
                'updated_at' => str_replace(' ', "T", $user['updated_at']),
                'status' => $user['status'],
            ]
        ]);
    }
    public function registerStudentsForExam()
    {
        return true;
    }
}
