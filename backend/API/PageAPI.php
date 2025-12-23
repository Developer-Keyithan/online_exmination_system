<?php

use Backend\Modal\Auth;

class PageAPI
{

    public function __construct()
    {
        $currentRoute = $_SERVER['REQUEST_URI'] ?? '/';

        // Skip login redirect if already on login page OR 404 page
        if (!Auth::isLoggedIn() && !str_contains($currentRoute, 'login') && !str_contains($currentRoute, '/404')) {
            $encodedUrl = rawurlencode($currentRoute);

            $router = Router::getInstance();
            $loginUrl = $router->url('login');
            $loginUrl .= '?redirect=' . $encodedUrl;

            header("Location: $loginUrl");
            exit;
        }
    }

    private function requirePermission($permission)
    {
        if (!hasPermission($permission)) {
            header('Location: ' . BASE_URL . '/forbidden');
            exit;
        }
    }

    public function login()
    {
        if (Auth::isLoggedIn()) {
            redirect('dashboard'); // if already logged in, redirect
        }
        return view('auth.login', ['title' => 'User Login']);
    }

    public function notFound()
    {
        return view('not_found.not_found', ['title' => '404 Page Not Found']);
    }

    public function forbidden()
    {
        return view('forbidden.forbidden', ['title' => '403 Forbidden']);
    }

    public function dashboard()
    {
        $this->requirePermission('dashboard.view');
        return view('dashboard', ['title' => 'Dashboard']);
    }

    public function courses()
    {
        $this->requirePermission('courses.view');
        return view('courses.all', ['title' => 'All Courses']);
    }
    
    public function addCourse()
    {
        $this->requirePermission('courses.create');
        return view('courses.add', ['title' => 'Add Course']);
    }
    
    public function myCourses()
    {
        $this->requirePermission('courses.my_courses');
        return view('courses.my', ['title' => 'My Courses']);
    }

    public function lectures()
    {
        $this->requirePermission('lectures.view');
        return view('lectures.all', ['title' => 'All Lectures']);
    }
    
    public function myLectures()
    {
        $this->requirePermission('lectures.my');
        return view('lectures.my', ['title' => 'My Lectures']);
    }

    public function exams()
    {
        $this->requirePermission('exams.view');
        return view('exams.all', ['title' => 'All Exams']);
    }
    
    public function createExam()
    {
        $this->requirePermission('exams.create');
        return view('exams.create', ['title' => 'Create Exam']);
    }
    
    public function editExam()
    {
        $this->requirePermission('exams.edit');
        return view('exams.create', ['title' => 'Edit Exam']);
    }
    
    public function myExams()
    {
        $this->requirePermission('exams.my');
        return view('exams.my', ['title' => 'My Exams']);
    }
    
    public function previewExam($id)
    {
        $this->requirePermission('exams.view');
        return view('exams.preview', ['title' => 'Preview Exam', 'exam_id' => $id]);
    }
    
    public function examAttemptRegister($hash)
    {
        $this->requirePermission('exams.attempt');
        return view('exams.register', ['title' => 'Register For Exam', 'rest_url_hash' => $hash]);
    }
    
    public function attemptExam($hash, $id)
    {
        $this->requirePermission('exams.attempt');
        $stmt = db()->prepare("SELECT * FROM exam_attempts WHERE url = ? AND exam_id = ?");
        $stmt->execute([$hash, $id]);
        $attempt = $stmt->fetch();
        if ($attempt) {
            return view('exams.attempt', ['title' => 'Attempt Exam', 'rest_url_hash' => $hash]);
        } else {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $path = getPath();
            header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
        }
    }

    public function questionBank()
    {
        $this->requirePermission('questions.bank');
        return view('questions.bank', ['title' => 'Question Bank']);
    }
    
    public function createQuestions()
    {
        $this->requirePermission('questions.create');
        return view('questions.create', ['title' => 'Create Questions']);
    }
    
    public function myQuestions()
    {
        $this->requirePermission('questions.my');
        return view('questions.my', ['title' => 'My Questions']);
    }

    public function pastPapers()
    {
        $this->requirePermission('past_papers.view');
        return view('past_paper', ['title' => 'Past Papers']);
    }

    public function results()
    {
        $this->requirePermission('results.all');
        return view('results.all', ['title' => 'All Results']);
    }
    
    public function myResults()
    {
        $this->requirePermission('results.my');
        return view('results.my', ['title' => 'My Results']);
    }

    public function examResultsReview($attempt_id, $id, $student_id = null)
    {
        $this->requirePermission('results.review');
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $path = getPath();
        $stmt = db()->prepare("SELECT id FROM exam_attempts WHERE id = ?  AND status IN ('completed', 'rules_violation')");
        $stmt->execute([$attempt_id]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$attempt) {
            header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
        } else {
            $stmt = db()->prepare("SELECT id FROM exam_info WHERE id = ?");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$exam) {
                header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
            } else {
                $stmt = db()->prepare("SELECT id FROM users WHERE id = ?");
                $stmt->execute([$student_id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$student) {
                    header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
                } else {
                    return view('results.review', ['title' => 'Exam Results Review', 'exam_id' => $id]);
                }
            }
        }
    }

    public function attendance()
    {
        $this->requirePermission('attendance.view');
        return view('attendance.view', ['title' => 'View Attendance']);
    }
    
    public function markAttendance()
    {
        $this->requirePermission('attendance.mark');
        return view('attendance.mark', ['title' => 'Mark Attendance']);
    }
    
    public function myAttendance()
    {
        $this->requirePermission('attendance.my');
        return view('attendance.my', ['title' => 'My Attendance']);
    }

    public function notifications()
    {
        $this->requirePermission('notifications.view');
        return view('notifications.index', ['title' => 'Notifications']);
    }
    
    public function users()
    {
        $this->requirePermission('users.view');
        return view('users.list', ['title' => 'All Users']);
    }
    
    public function addUser()
    {
        $this->requirePermission('users.create');
        return view('users.add', ['title' => 'Add User']);
    }

    public function userGroup()
    {
        $this->requirePermission('groups.manage');
        return view('users.group', ['title' => 'User Groups']);
    }

    public function examReports()
    {
        $this->requirePermission('reports.exam');
        return view('reports.exam', ['title' => 'Exam Reports']);
    }
    
    public function studentPerformance()
    {
        $this->requirePermission('reports.performance');
        return view('reports.student', ['title' => 'Student Performance']);
    }

    public function profile()
    {
        $this->requirePermission('profile.view');
        return view('profile.index', ['title' => 'Profile']);
    }

    public function settings()
    {
        $this->requirePermission('settings.manage');
        return view('settings.index', ['title' => 'Settings']);
    }
}