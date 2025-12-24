<?php

use Backend\Modal\Auth;

class ProfileAPI
{
    private $db;
    private $user;

    public function __construct()
    {
        $this->db = db();
        $this->checkAuth();
    }

    private function checkAuth()
    {
        if (!Auth::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        $this->user = Auth::getUser();
    }

    // Get user profile
    public function getProfile()
    {
        try {
            $userId = $this->user['id'];

            $stmt = $this->db->prepare("
                SELECT id, reg_no, name, phone, username, user_group, email, note, status, 
                       created_at, updated_at
                FROM users 
                WHERE id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user) {
                return $this->errorResponse('User not found');
            }

            return $this->successResponse('Profile loaded', [
                'user' => $user
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to load profile: ' . $e->getMessage());
        }
    }

    // Update profile
    public function update()
    {
        try {
            $userId = $this->user['id'];
            $data = json_decode(file_get_contents('php://input'), true);

            if (!$data) {
                return $this->errorResponse('Invalid data');
            }

            $errors = [];

            // Validate required fields
            if (empty($data['name'])) {
                $errors['name'] = 'Name is required';
            } elseif (strlen($data['name']) < 2) {
                $errors['name'] = 'Name must be at least 2 characters long';
            }

            if (empty($data['email'])) {
                $errors['email'] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Invalid email format';
            } else {
                // Check if email exists (excluding current user)
                $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
                $stmt->execute([$data['email'], $userId]);
                if ($stmt->fetch()) {
                    $errors['email'] = 'Email already in use';
                }
            }

            if (empty($data['phone'])) {
                $errors['phone'] = 'Phone number is required';
            } elseif (!preg_match('/^\d{9,15}$/', $data['phone'])) {
                $errors['phone'] = 'Invalid phone number format (9-15 digits)';
            }

            // Validate username for students
            if ($this->user['user_group'] == 6 && empty($data['username'])) {
                $errors['username'] = 'Username is required for students';
            } elseif ($this->user['user_group'] == 6 && strlen($data['username']) < 3) {
                $errors['username'] = 'Username must be at least 3 characters long';
            }

            if (!empty($errors)) {
                return $this->errorResponse('Validation failed', $errors);
            }

            $this->db->beginTransaction();

            // Update user data
            $updateFields = [];
            $updateParams = [];

            $fields = ['name', 'email', 'phone', 'reg_no', 'username', 'note'];
            foreach ($fields as $field) {
                if (isset($data[$field])) {
                    $updateFields[] = "$field = ?";
                    $updateParams[] = $data[$field];
                }
            }

            $updateParams[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $updateFields) . ", updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($updateParams);

            $this->db->commit();

            // Update session data
            $_SESSION['user']['name'] = $data['name'];
            $_SESSION['user']['email'] = $data['email'];

            // Log activity
            $this->logActivity($userId, 'profile_update', 'Updated profile information');

            return $this->successResponse('Profile updated successfully');

        } catch (Exception $e) {
            $this->db->rollBack();
            return $this->errorResponse('Failed to update profile: ' . $e->getMessage());
        }
    }

    // Change password
    public function changePassword()
    {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            if (empty($data['current_password']) || empty($data['new_password'])) {
                return $this->errorResponse('Current and new password are required');
            }

            $userId = $this->user['id'];
            $currentPassword = $data['current_password'];
            $newPassword = $data['new_password'];

            // Verify current password
            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$user || !password_verify($currentPassword, $user['password'])) {
                return $this->errorResponse('Current password is incorrect');
            }

            // Validate new password
            if (strlen($newPassword) < 8) {
                return $this->errorResponse('Password must be at least 8 characters long');
            }
            if (!preg_match('/[A-Z]/', $newPassword)) {
                return $this->errorResponse('Password must contain at least one uppercase letter');
            }
            if (!preg_match('/[a-z]/', $newPassword)) {
                return $this->errorResponse('Password must contain at least one lowercase letter');
            }
            if (!preg_match('/[0-9]/', $newPassword)) {
                return $this->errorResponse('Password must contain at least one number');
            }

            // Hash and update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
            $stmt->execute([$hashedPassword, $userId]);

            // Log activity
            // $this->logActivity($userId, 'password_change', 'Changed password');

            return $this->successResponse('Password changed successfully');

        } catch (Exception $e) {
            return $this->errorResponse('Failed to change password: ' . $e->getMessage());
        }
    }

    // Get active sessions
    // public function getSessions()
    // {
    //     try {
    //         // print_r('Sessions');
    //         $userId = $this->user['id'];
    //         $currentSessionId = session_id();

    //         // Note: You'll need to create a user_sessions table to track sessions
    //         // For now, return dummy data
    //         $sessions = [
    //             [
    //                 'id' => 1,
    //                 'device' => 'Chrome on Windows',
    //                 'last_active' => date('Y-m-d H:i:s'),
    //                 'is_current' => true
    //             ],
    //             [
    //                 'id' => 2,
    //                 'device' => 'Safari on iPhone',
    //                 'last_active' => date('Y-m-d H:i:s', strtotime('-1 day')),
    //                 'is_current' => false
    //             ]
    //         ];

    //         return $this->successResponse('Sessions loaded', [
    //             'sessions' => $sessions
    //         ]);

    //     } catch (Exception $e) {
    //         return $this->errorResponse('Failed to load sessions');
    //     }
    // }

    // Get last login
    public function getLastLogin()
    {
        try {
            $userId = $this->user['id'];

            // Get last login from users table
            $stmt = $this->db->prepare("SELECT updated_at FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $lastUpdate = $stmt->fetchColumn();

            return $this->successResponse('Last login loaded', [
                'lastLogin' => $lastUpdate ? date('F j, Y g:i A', strtotime($lastUpdate)) : 'Never'
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to load last login');
        }
    }

    // Export user data
    // public function exportData()
    // {
    //     try {
    //         $userId = $this->user['id'];

    //         // Get user data
    //         $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
    //         $stmt->execute([$userId]);
    //         $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    //         if (!$userData) {
    //             return $this->errorResponse('User not found');
    //         }

    //         // Remove sensitive data
    //         unset($userData['password']);

    //         // Get exam attempts
    //         $stmt = $this->db->prepare("
    //             SELECT ea.*, ei.title as exam_title, ei.code as exam_code
    //             FROM exam_attempts ea
    //             LEFT JOIN exam_info ei ON ea.exam_id = ei.id
    //             WHERE ea.student_id = ?
    //             ORDER BY ea.completed_at DESC
    //         ");
    //         $stmt->execute([$userId]);
    //         $examData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //         // Compile export data
    //         $exportData = [
    //             'user_information' => $userData,
    //             'exam_history' => $examData,
    //             'exported_at' => date('Y-m-d H:i:s'),
    //             'export_id' => uniqid()
    //         ];

    //         // Set headers for JSON download
    //         header('Content-Type: application/json');
    //         header('Content-Disposition: attachment; filename="user-data-' . $userId . '-' . date('Y-m-d') . '.json"');

    //         echo json_encode($exportData, JSON_PRETTY_PRINT);
    //         exit;

    //     } catch (Exception $e) {
    //         return $this->errorResponse('Failed to export data: ' . $e->getMessage());
    //     }
    // }

    public function exportData()
    {
        try {
            $userId = $this->user['id'];
            $format = $_GET['format'] ?? 'json'; // json or pdf

            // Get user data
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$userData) {
                return $this->errorResponse('User not found');
            }

            // Mapping arrays
            $userGroups = [
                1 => 'Technical Support (Developer)',
                2 => 'Administrator',
                3 => 'Admin',
                4 => 'HOD',
                5 => 'Lecturer',
                6 => 'Student',
                7 => 'Parent'
            ];

            $statusList = [
                0 => 'Active',
                1 => 'Inactive',
                2 => 'Suspended',
                3 => 'Deleted'
            ];

            // Replace IDs with human-readable text
            $userData['user_group'] = $userGroups[$userData['user_group']] ?? 'Unknown';
            $userData['status'] = $statusList[$userData['status']] ?? 'Unknown';
            $userData['note'] = $userData['note'] ? $userData['note'] : 'N/A';

            // Remove sensitive data
            unset($userData['id']);
            unset($userData['password']);
            unset($userData['updated_at']);

            // Get exam attempts
            $stmt = $this->db->prepare("
                SELECT ea.*, ei.title as exam_title, ei.code as exam_code, ei.passing_marks
                FROM exam_attempts ea
                LEFT JOIN exam_info ei ON ea.exam_id = ei.id
                WHERE ea.student_id = ?
                ORDER BY ea.completed_at DESC
            ");
            $stmt->execute([$userId]);
            $examData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (strtolower($format) === 'json') {
                $userData['created_at'] = str_replace(' ', 'T', date('Y-m-d H:i:s', strtotime($userData['created_at'])));

                $exportData = ['user_information' => $userData];
                // Filter exam data for JSON export
                $filteredExamData = [];
                if ($userData['user_group'] == 'Student') {
                    foreach ($examData as $exam) {
                        $score = isset($exam['score']) ? floatval($exam['score']) : null;
                        if (is_numeric($score)) {
                            $score = $score == floor($score) ? floor($score) : $score;
                        }

                        $passed = (isset($exam['score'], $exam['passing_marks']) && floatval($exam['score']) >= floatval($exam['passing_marks']))
                            ? true : (isset($exam['score'], $exam['passing_marks']) ? false : '-');

                        $filteredExamData[] = [
                            'code' => isset($exam['exam_code']) ? strtoupper(str_replace(' ', '_', $exam['exam_code'])) : '-',
                            'title' => $exam['exam_title'] ?? '-',
                            'score' => $score ?? '-',
                            'passed' => $passed,
                            'completed_at' => isset($exam['completed_at']) ? str_replace(' ', 'T', $exam['completed_at']) : '-'
                        ];
                    }
                    $exportData['exam_history'] = $filteredExamData;
                }

                $exportData['exported_at'] = str_replace(' ', 'T', date('Y-m-d H:i:s'));
                $exportData['export_id'] = uniqid();

                header('Content-Type: application/json');
                header('Content-Disposition: attachment; filename="user-data-' . $userId . '-' . date('Y-m-d') . '.json"');
                echo json_encode($exportData, JSON_PRETTY_PRINT);
                exit;
            }

            if (strtolower($format) === 'pdf') {
                $userData['created_at'] = date('D, M d Y, H:i:s', strtotime($userData['created_at']));
                require_once 'vendor/autoload.php';
                $pdf = new \Dompdf\Dompdf();

                $html = '
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; color: #333; margin: 20px; }
                            h1 { text-align: center; color: #1e40af; }
                            h2 { color: #111827; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 20px; }
                            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
                            th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
                            th { background-color: #1e3a8a; color: #fff; }
                            tr:nth-child(even) { background-color: #f3f4f6; }
                            tr td:nth-child(1) { text-transform: capitalize; }
                            .status-active { color: #16a34a; font-weight: bold; }      /* green */
                            .status-inactive { color: #f59e0b; font-weight: bold; }   /* orange */
                            .status-suspended { color: #dc2626; font-weight: bold; }  /* red */
                            .status-deleted { color: #7f1d1d; font-weight: bold; }    /* dark red */
                            .export-info { font-size: 12px; color: #555; margin-top: 20px; }
                        </style>
                    </head>
                    <body>
                        <h1>User Data Export</h1>
                        <h2>User Information</h2>
                        <table>
                            <tr><th>Field</th><th>Value</th></tr>';

                foreach ($userData as $key => $value) {
                    if ($key == 'user_group') {
                        $key = 'role';
                    } else {
                        $key = str_replace('_', ' ', $key);
                    }

                    $tdClass = '';
                    if (strtolower($key) == 'status') {
                        $key = 'current status';
                        $tdClass = 'class="status-' . strtolower($value) . '"';
                    }

                    $html .= '<tr>
                        <td>' . htmlspecialchars($key) . '</td>
                        <td ' . $tdClass . '>' . htmlspecialchars($value) . '</td>
                    </tr>';
                }


                if ($userData['user_group'] == 'Student') {
                    $html .= '</table>
                        <h2>Exam History</h2>
                        <table>
                            <tr>
                                <th>Exam Code</th>
                                <th>Title</th>
                                <th>Score</th>
                                <th>Passed</th>
                                <th>Completed At</th>
                            </tr>';

                    foreach ($examData as $exam) {
                        if (isset($exam['score'], $exam['passing_marks'])) {
                            $exam['passed'] = floatval($exam['score']) >= floatval($exam['passing_marks']) ? 'Pass' : 'Fail';
                            $passedColor = $exam['passed'] === 'Pass' ? 'green' : 'red';
                        } else {
                            $exam['passed'] = '-';
                            $passedColor = 'black';
                        }


                        $score = isset($exam['score']) ? floatval($exam['score']) : '-';
                        if (is_numeric($score)) {
                            $score = $score == floor($score) ? floor($score) : $score;
                        }

                        $html .= '<tr>
                            <td style="text-transform: uppercase;">' . htmlspecialchars(str_replace(' ', "_", $exam['exam_code'])) . '</td>
                            <td style="text-transform: capitalize;">' . htmlspecialchars($exam['exam_title']) . '</td>
                            <td style="text-align: center;">' . htmlspecialchars($score) . '</td>
                            <td style="text-align:center; color:' . $passedColor . ';">' . htmlspecialchars($exam['passed']) . '</td>
                            <td>' . htmlspecialchars(isset($exam['completed_at']) ? date('D, M d Y, H:i:s', strtotime($exam['completed_at'])) : '-') . '</td>
                        </tr>';

                    }

                }

                $html .= '</table>
                        <div class="export-info">
                            Exported at: ' . date('D, M d Y,  H:i:s') . '<br>
                            Export ID: ' . uniqid() . '
                        </div>
                    </body>
                    </html>';

                $pdf->loadHtml($html);
                $pdf->setPaper('A4', 'portrait');
                $pdf->render();
                $pdf->stream('user-data-' . $userId . '-' . date('Y-m-d') . '.pdf', ['Attachment' => true]);
                exit;
            }
        } catch (Exception $e) {
            return $this->errorResponse('Failed to export data: ' . $e->getMessage());
        }
    }

    // Delete account
    public function deleteAccount()
    {
        try {
            $userId = $this->user['id'];

            $this->db->beginTransaction();

            // Deactivate account instead of deleting (for data integrity)
            $stmt = $this->db->prepare("
                UPDATE users 
                SET status = 3, email = CONCAT('deleted_', id, '_', UNIX_TIMESTAMP(), '@deleted.com'), 
                    username = CONCAT('deleted_', id), updated_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$userId]);

            // Log activity
            // $this->logActivity($userId, 'account_deletion', 'Account deactivated');

            $this->db->commit();

            // Destroy session
            session_destroy();

            return $this->successResponse('Account deactivated successfully');

        } catch (Exception $e) {
            $this->db->rollBack();
            return $this->errorResponse('Failed to delete account: ' . $e->getMessage());
        }
    }

    // Upload avatar
    public function uploadAvatar()
    {
        try {
            if (!isset($_FILES['avatar'])) {
                return $this->errorResponse('No file uploaded');
            }

            $file = $_FILES['avatar'];
            $userId = $this->user['id'];

            // Validate file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $maxSize = 5 * 1024 * 1024; // 5MB

            if (!in_array($file['type'], $allowedTypes)) {
                return $this->errorResponse('Invalid file type. Allowed: JPG, PNG, GIF');
            }

            if ($file['size'] > $maxSize) {
                return $this->errorResponse('File size too large. Maximum: 5MB');
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = 'uploads/avatars/' . $filename;

            // Create directory if not exists
            if (!is_dir('uploads/avatars')) {
                mkdir('uploads/avatars', 0777, true);
            }

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                return $this->errorResponse('Failed to upload file');
            }

            // Note: You'll need to add avatar field to users table or create a separate table
            // For now, just return success

            return $this->successResponse('Avatar uploaded successfully', [
                'avatar_url' => BASE_URL . '/' . $uploadPath
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to upload avatar: ' . $e->getMessage());
        }
    }

    // Helper methods
    private function successResponse($message, $data = [])
    {
        return json_encode(['status' => 'success', 'msg' => $message, 'data' => $data]);
    }

    private function errorResponse($message, $errors = [])
    {
        return json_encode(['status' => 'error', 'msg' => $message, 'errors' => $errors]);
    }

    private function logActivity($userId, $action, $details)
    {
        // You'll need to create an activity_log table
        // For now, just log to file
        $log = date('Y-m-d H:i:s') . " - User $userId - $action - $details\n";
        file_put_contents('logs/activity.log', $log, FILE_APPEND);
    }
}
