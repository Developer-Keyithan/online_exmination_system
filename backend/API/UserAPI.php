<?php
require_once './vendor/autoload.php'; // PHPMailer autoload
require_once './backend/templates/email-templates.php'; // Your welcomeMailTemplate function file
require_once './backend/helpers/mailer.php'; // Your sendMail() function file
class UserAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }

    // In UserAPI.php or route handler
    public function getAllUsersHandler()
    {
        $filter = isset($_GET['filter']) && $_GET['filter'] !== '' ? $_GET['filter'] : null;
        $status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : null;

        $users = $this->getAllUsers($filter, $status);
        echo $users;
    }

    public function getAllUsers($filter = null, $status = null)
    {
        $conditions = [];
        $params = [];

        // Logged user
        $user = user_id();

        // Role-based filtering
        if ($user == 1) {
            $conditions[] = "users.user_group != 1";
        } elseif ($user == 2) {
            $conditions[] = "users.user_group NOT IN (1,2,4,7)";
        } else {
            $conditions[] = "users.user_group NOT IN (1,2,3,4,7)";
        }

        // External filter (user_group)
        if ($filter) {

            // Role-based filter access control
            if ($user == 2 && $filter == 2) {
                throw new Exception("Access denied.");
            }

            if ($user == 3 && in_array($filter, [2, 3])) {
                throw new Exception("Access denied.");
            }

            $conditions[] = "users.user_group = ?";
            $params[] = $filter;
        }

        // 🔥 Status filter (0=active, 1=inactive, 3=suspended)
        if ($status !== null && $status !== '') {
            $conditions[] = "users.status = ?";
            $params[] = $status;
        }

        // Build query
        $sql = "SELECT users.*, user_group.name AS group_name FROM users LEFT JOIN user_group ON users.user_group = user_group.id";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as &$user) {
            $user['status'] = getUserStatusText($user['status']);
        }

        return json_encode($result);
    }

    public function getUserById($id)
    {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return json_encode([
            'user' => $stmt->fetch(PDO::FETCH_ASSOC),
            'status' => 'success'
        ]);
    }

    public function getLoggedUserAccesses()
    {
        $role = getLoggedUserRoleID();
        $permissions = getLoggedUserPermissions();
        return json_encode([
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    // Generate registration number
    function generateRegNo(PDO $db, string $prefix): string
    {
        $validPrefixes = ['TEC', 'SADMIN', 'ADMIN', 'LEC', 'STU', 'PAR', 'HOD'];
        if (!in_array($prefix, $validPrefixes)) {
            throw new Exception("Invalid prefix");
        }

        $stmt = $db->prepare("
            SELECT reg_no
            FROM users
            WHERE reg_no LIKE ?
            ORDER BY CAST(SUBSTRING(reg_no, LENGTH(?) + 1) AS UNSIGNED) DESC
            LIMIT 1
        ");
        $stmt->execute([$prefix . '%', $prefix]);
        $last = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($last) {
            $number = (int) substr($last['reg_no'], strlen($prefix)) + 1;
        } else {
            $number = 1001; // start number
        }

        return $prefix . $number;
    }

    public function createUser()
    {
        try {
            // Get POST data safely
            $fullname = $_POST['fullname'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            if (str_starts_with($phone, '0')) {
                $phone = substr($phone, 1);
            }
            $username = $_POST['username'] ?? '';
            $group_name = $_POST['userGroup'] ?? '';
            $status = $_POST['status'] ?? '';
            $pwd = $_POST['password'] ?? '';
            $cpwd = $_POST['cpassword'] ?? '';
            $note = $_POST['notes'] ?? '';

            // Validation
            if (!$fullname)
                throw new Exception("Fullname is required");
            if (!$email)
                throw new Exception("Email is required");
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
                throw new Exception("Invalid email format");
            if (!$phone)
                throw new Exception("Phone number is required");
            if (!$username)
                throw new Exception("Username is required");
            if (!$group_name)
                throw new Exception("User group is required");
            if (!$status)
                throw new Exception("Status is required");
            if (!$pwd)
                throw new Exception("Password is required");
            if ($pwd !== $cpwd)
                throw new Exception("Passwords do not match");

            $group = null;
            if ($group_name == 'Technical')
                $group = 1;
            if ($group_name == 'Administrator')
                $group = 2;
            if ($group_name == 'Admin')
                $group = 3;
            if ($group_name == 'HOD')
                $group = 4;
            if ($group_name == 'Lecturer')
                $group = 5;
            if ($group_name == 'Student')
                $group = 6;
            if ($group_name == 'Parent')
                $group = 7;

            $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0)
                throw new Exception("Email already exists");

            $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->rowCount() > 0)
                throw new Exception("Username already exists");

            $stmt = $this->db->prepare("SELECT * FROM users WHERE phone = ? AND user_group = ?");
            $stmt->execute([$username, $group]);
            if ($stmt->rowCount() > 0)
                throw new Exception("Phone number already exists on this user group $group_name");

            // Hash the password
            $hashedPwd = password_hash($pwd, PASSWORD_DEFAULT);

            if ($group == 1)
                $prefix = 'TEC';
            if ($group == 2)
                $prefix = 'SADMIN';
            if ($group == 3)
                $prefix = 'ADMIN';
            if ($group == 4)
                $prefix = 'HOD';
            if ($group == 5)
                $prefix = 'LEC';
            if ($group == 6)
                $prefix = 'STU';
            if ($group == 7)
                $prefix = 'PAR';
            // if ($group == 1 || $group == 2 || $group == 3) {
            //     $reg_no = null;

            $reg_no = $this->generateRegNo($this->db, $prefix);

            // Insert into database (example using PDO)
            $stmt = $this->db->prepare("INSERT INTO users (reg_no, name, email, phone, username, user_group, status, password, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$reg_no, $fullname, $email, $phone, $username, $group, $status, $hashedPwd, $note]);

            $toMail = $email;
            $password = $pwd;
            $token = bin2hex(random_bytes(32));
            $resetLink = BASE_URL . '/reset-password/' . $token;
            $tokenExpire = new DateTime('now', new DateTimeZone('Asia/Colombo'));
            $tokenExpire->modify('+24 hours');
            $tokenExpire = $tokenExpire->format('Y-m-d H:i:s');

            $stmt = $this->db->prepare('UPDATE users SET reset_token = ?, token_expire = ? WHERE email = ?');
            $stmt->execute([$token, $tokenExpire, $toMail]);

            // Generate email HTML using your template
            $message = welcomeMailTemplate($toMail, $resetLink, $tokenExpire, $fullname, $username, $password);

            // Send the email
            $result = sendMail($toMail, 'Reset Your Password', $message, $fullname);
            if ($result === false) {
                return json_encode([
                    'status' => 'warn',
                    'message' => 'User created successfully, but failed to send email. Please inform the user to reset their password manually.'
                ]);
            }
            return json_encode([
                'status' => 'success',
                'message' => 'User created successfully'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getStudentInfo()
    {
        try {
            $user_id = user_id();
            $studentInfo = $this->db->query("SELECT id, email, name, reg_no as student_id FROM users WHERE id = $user_id")->fetch(PDO::FETCH_ASSOC);
            return json_encode([
                'status' => 'success',
                'student_info' => $studentInfo
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Failed to fetch student info: ' . $e->getMessage()
            ]);
        }
    }
}
