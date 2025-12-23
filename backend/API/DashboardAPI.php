<?php
// use Backend\Modal\Auth;

// class DashboardAPI {

//     public function __construct(){
//         if (!Auth::isLoggedIn()) {
//                redirect('login');
//         }
//     }

//     public function index() {
//         // echo 'Dashboard';
//         // If user is logged in, show dashboard, otherwise home
//         // if (Auth::isLoggedIn()) {
//         //     redirect('admin.dashboard');
//             return view('dashboard', ['title' => 'Dashboard']);
//         // }
//         // return view('auth.login', ['title' => 'Login']);
//         // redirect('login');
//     }
// }

use Backend\Modal\Auth;


// Dashboard API Controller
class DashboardAPI
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

    // Tech dashboard
    public function techDashboard()
    {
        try {
            $stats = [];

            // Get total users
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE status = 1");
            $stats['totalUsers'] = $stmt->fetchColumn();

            // Get active users (logged in last 24 hours)
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE updated_at >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
            $stats['activeUsers'] = $stmt->fetchColumn();

            // Get system logs (you'll need to create a system_logs table)
            $logs = [];

            return $this->successResponse('Dashboard data loaded', [
                'stats' => $stats,
                'logs' => $logs,
                'systemStatus' => [
                    'online' => true,
                    'uptime' => '99.9%'
                ],
                'dbStats' => [
                    'size' => $this->getDatabaseSize(),
                    'tables' => $this->countTables(),
                    'lastBackup' => 'Today'
                ]
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    // Admin dashboard
    public function adminDashboard()
    {
        try {
            $stats = [];

            // Get user statistics
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE status = 0 AND user_group != 1");
            $stats['totalUsers'] = $stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE user_group = 6 AND status = 0");
            $stats['students'] = $stmt->fetchColumn();

            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE user_group = 5 AND status = 0");
            $stats['lecturers'] = $stmt->fetchColumn();

            // Get recent users
            $stmt = $this->db->prepare("SELECT id, name, email, user_group as role, created_at FROM users  WHERE status = 0 ORDER BY created_at DESC  LIMIT 5 ");
            $stmt->execute();
            $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recentUsers as &$user) {
                $user['created_at'] = str_replace(' ', "T", $user['created_at']);
            }

            $stmt = $this->db->query("
    SELECT COUNT(DISTINCT ei.id)
    FROM exam_info ei
    JOIN exam_settings es ON es.exam_id = ei.id
    WHERE ei.status = 1
      AND es.schedule_type = 'scheduled'
      AND DATE(es.start_time) = CURDATE()
");
            $todayCount = $stmt->fetchColumn();


         $stmt = $this->db->query("
    SELECT COUNT(DISTINCT ei.id)
    FROM exam_info ei
    JOIN exam_settings es ON es.exam_id = ei.id
    WHERE ei.status = 1
      AND es.schedule_type = 'scheduled'
      AND es.start_time > NOW()
");
$upcomingCount = $stmt->fetchColumn();



            $stmt = $this->db->query("
    SELECT COUNT(DISTINCT ei.id)
    FROM exam_info ei
    JOIN exam_settings es ON es.exam_id = ei.id
    WHERE ei.status = 1
      AND (
            es.schedule_type = 'anytime'
            OR (
                es.schedule_type = 'scheduled'
                AND NOW() BETWEEN es.start_time
                AND DATE_ADD(es.start_time, INTERVAL ei.duration MINUTE)
            )
          )
");
            $activeCount = $stmt->fetchColumn();


            $stats['todayExams'] = (int) $todayCount;
            $stats['upcomingExams'] = (int) $upcomingCount;
            $stats['activeExams'] = (int) $activeCount;


            return $this->successResponse('Dashboard data loaded', [
                'stats' => $stats,
                'recentUsers' => $recentUsers,
            ]);
        } catch (Exception $e) {
            return $this->errorResponse('Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    // Lecturer dashboard
    public function lecturerDashboard()
    {
        try {
            $userId = $this->user['id'];
            $stats = [];

            // Get lecturer stats (you'll need to map lecturers to courses/exams)
            // For now, return dummy data

            return $this->successResponse('Dashboard data loaded', [
                'stats' => [
                    'courses' => 3,
                    'exams' => 5,
                    'students' => 45,
                    'questions' => 78
                ],
                'upcomingExams' => [],
                'pendingReviews' => []
            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    // Student dashboard
    public function studentDashboard()
    {
        try {
            $userId = $this->user['id'];

            // Get student stats
            $stats = [];

            // Get exam attempts count
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM exam_attempts WHERE user_id = ?");
            $stmt->execute([$userId]);
            $stats['examsTaken'] = $stmt->fetchColumn();

            // Get average score
            $stmt = $this->db->prepare("SELECT AVG(percentage) FROM exam_attempts WHERE user_id = ? AND status = 'completed'");
            $stmt->execute([$userId]);
            $stats['avgScore'] = round($stmt->fetchColumn(), 1) ?: 0;

            // Get recent results
            $stmt = $this->db->prepare("
                SELECT ea.id as attempt_id, ea.exam_id, ea.percentage as score, 
                       ea.completed_date as date, ei.title as exam_title
                FROM exam_attempts ea
                LEFT JOIN exam_info ei ON ea.exam_id = ei.id
                WHERE ea.user_id = ? AND ea.status = 'completed'
                ORDER BY ea.completed_date DESC 
                LIMIT 5
            ");
            $stmt->execute([$userId]);
            $recentResults = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Add passed flag
            foreach ($recentResults as &$result) {
                $result['passed'] = $result['score'] >= 50; // Assuming 50% is passing
                $result['course'] = 'General'; // You'll need to map exams to courses
            }

            return $this->successResponse('Dashboard data loaded', [
                'stats' => $stats,
                'upcomingExams' => [],
                'recentResults' => $recentResults,
                'scoreDistribution' => [
                    ['range' => '90-100%', 'count' => 3, 'percentage' => 30],
                    ['range' => '80-89%', 'count' => 2, 'percentage' => 20],
                    ['range' => '70-79%', 'count' => 2, 'percentage' => 20],
                    ['range' => '60-69%', 'count' => 2, 'percentage' => 20],
                    ['range' => 'Below 60%', 'count' => 1, 'percentage' => 10]
                ],
                'subjectPerformance' => [
                    ['name' => 'Mathematics', 'score' => 92],
                    ['name' => 'Physics', 'score' => 85],
                    ['name' => 'Chemistry', 'score' => 78],
                    ['name' => 'Biology', 'score' => 88]
                ]

            ]);

        } catch (Exception $e) {
            return $this->errorResponse('Failed to load dashboard data: ' . $e->getMessage());
        }
    }

    private function getDatabaseSize()
    {
        // Get database size
        $stmt = $this->db->query("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as size FROM information_schema.tables WHERE table_schema = DATABASE()");
        return $stmt->fetchColumn() . ' MB';
    }

    private function countTables()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()");
        return $stmt->fetchColumn();
    }

    private function successResponse($message, $data = [])
    {
        $response = ['status' => 'success', 'msg' => $message];

        // Add each $data key-value to response
        foreach ($data as $key => $value) {
            $response[$key] = $value;
        }

        return json_encode($response);
    }


    private function errorResponse($message)
    {
        return ['status' => 'error', 'msg' => $message];
    }
}
