<?php

use Backend\Modal\Auth;

require_once './vendor/autoload.php'; // PHPMailer autoload
require_once './backend/templates/email-templates.php'; // Your welcomeMailTemplate function file
require_once './backend/helpers/mailer.php'; // Your sendMail() function file
class AuthAPI
{

    private $db;
    public function __construct()
    {
        $this->db = db();
    }
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

    public function getResetInfos($token)
    {
        try {
            if (!$token) {
                throw new Exception('Token not provided');
            }

            $stmt = $this->db->prepare('SELECT * FROM users WHERE reset_token = ?');
            $stmt->execute([$token]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                throw new Exception('Invalid token');
            }

            $infos = null;
            $tokenExpire = $user['token_expire'] ? $user['token_expire'] : 0;
            if (time() > $tokenExpire) {
                $infos['tokenExpired'] = true;
                $infos['timeLeft'] = 0;
            } else {
                $infos['tokenExpired'] = false;
                $tokenExpireTimestamp = strtotime($tokenExpire); // convert to Unix timestamp
                $infos['timeLeft'] = $tokenExpireTimestamp - time();
            }
            $infos['tokenExpire'] = str_replace(' ', 'T', $tokenExpire);
            $infos['email'] = $user['email'];

            return json_encode([
                'status' => 'success',
                'infos' => $infos
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function updateResetInfos($token)
    {
        try {
            if (!$token) {
                throw new Exception('Invalid token');
            }

            $data = json_decode(file_get_contents("php://input"), true);

            $password = $data['newPassword'] ?? null;
            $email = $data['email'] ?? null;

            if (!$email || !$password) {
                throw new Exception('Missing email or password');
            }

            $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? AND reset_token = ?');
            $stmt->execute([$email, $token]);
            $user = $stmt->fetch();

            if (!$user) {
                throw new Exception('Invalid token or email. Please try again.');
            }
            $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare('UPDATE users SET password = ?, reset_token = NULL, token_expire = NULL WHERE email = ?');
            $stmt->execute([$hashedPwd, $email]);

            $this->logout();
            return json_encode([
                'status' => 'success',
                'msg' => 'Password updated successfully'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function sendResetLink()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $data = json_decode(file_get_contents('php://input'), true);
            $email = $data['email'] ?? null;
            $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return json_encode([
                    'status' => 'error',
                    'msg' => "Can't find the email, maybe it doesn't exist",
                    'email' => $email
                ]);
            }

            $toMail = $user['email'];
            $fullname = $user['name'];
            $newToken = bin2hex(random_bytes(32));
            $resetLink = BASE_URL . '/reset-password/' . $newToken;
            $tokenExpire = new DateTime('now', new DateTimeZone('Asia/Colombo'));
            $tokenExpire->modify('+5 minutes');
            $tokenExpire = $tokenExpire->format('Y-m-d H:i:s');

            $stmt = $this->db->prepare('UPDATE users SET reset_token = ?, token_expire = ? WHERE email = ?');
            $stmt->execute([$newToken, $tokenExpire, $toMail]);

            // Generate email HTML using your template
            $message = resetMailTemplate($toMail, $resetLink, $fullname);

            // Send the email
            $result = sendMail($toMail, 'Reset Your Password', $message, $fullname);
            if ($result === false) {
                return json_encode([
                    'status' => 'warn',
                    'msg' => 'Failed to sent reset password link. Please try again later.'
                ]);
            }
            return json_encode([
                'status' => 'success',
                'msg' => 'Reset password link sent to your email. Please check your email.'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Failed to send email: ' . $e->getMessage()
            ]);
        }
    }
}
