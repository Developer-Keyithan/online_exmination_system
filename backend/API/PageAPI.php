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

    public function dashboard()
    {
        return view('dashboard', ['title' => 'Dashboard']);
    }

    public function courses()
    {
        return view('courses.all', ['title' => 'All Courses']);
    }
    public function addCourse()
    {
        return view('courses.add', ['title' => 'Add Course']);
    }
    public function myCourses()
    {
        return view('courses.my', ['title' => 'My Courses']);
    }

    public function lectures()
    {
        return view('lectures.all', ['title' => 'All Lectures']);
    }
    public function myLectures()
    {
        return view('lectures.my', ['title' => 'My Lectures']);
    }

    public function exams()
    {
        return view('exams.all', ['title' => 'All Exams']);
    }
    public function createExam()
    {
        return view('exams.create', ['title' => 'Create Exam']);
    }
    public function editExam()
    {
        return view('exams.create', ['title' => 'Edit Exam']);
    }
    public function myExams()
    {
        return view('exams.my', ['title' => 'My Exams']);
    }
    public function previewExam($id)
    {
        return view('exams.preview', ['title' => 'Preview Exam', 'exam_id' => $id]);
    }
    public function examAttemptRegister($hash)
    {
        return view('exams.register', ['title' => 'Register For Exam', 'rest_url_hash' => $hash]);
    }
    public function attemptExam($hash, $id)
    {
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
        return view('questions.bank', ['title' => 'All Exams']);
    }
    public function createQuestions()
    {
        return view('questions.create', ['title' => 'My Exams']);
    }
    public function myQuestions()
    {
        return view('questions.my', ['title' => 'My Exams']);
    }

    public function pastPapers()
    {
        return view('past_paper', ['title' => 'My Exams']);
    }

    public function results()
    {
        return view('results.all', ['title' => 'All Results']);
    }
    public function myResults()
    {
        return view('results.my', ['title' => 'My Results']);
    }

    public function examResultsReview($attempt_id, $id, $student_id = null)
    {
        $stmt = db()->prepare("SELECT id FROM exam_attempts WHERE id = ?  AND status IN ('completed', 'rules_violation')");
        $stmt->execute([$attempt_id]);
        $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$attempt) {
            $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
            $path = getPath();
            header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
        } else {
            $stmt = db()->prepare("SELECT id FROM exam_info WHERE id = ?");
            $stmt->execute([$id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$exam) {
                $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
                $path = getPath();
                header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
            } else {
                $stmt = db()->prepare("SELECT id FROM users WHERE id = ?");
                $stmt->execute([$student_id]);
                $student = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!$student) {
                    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
                    $path = getPath();
                    header('Location: ' . BASE_URL . '/404?path=' . urlencode($path) . '&method=' . urlencode($method));
                } else {
                    return view('results.review', ['title' => 'Exam Results Review', 'exam_id' => $id]);
                }
            }
        }
    }

    public function attendance()
    {
        return view('attendance.view', ['title' => 'View Attendance']);
    }
    public function markAttendance()
    {
        return view('attendance.mark', ['title' => 'Mark Attendance']);
    }
    public function myAttendance()
    {
        return view('attendance.my', ['title' => 'Mark Attendance']);
    }

    public function notifications()
    {
        return view('notifications.index', ['title' => 'Notifications']);
    }
    public function users()
    {
        return view('users.list', ['title' => 'All Users']);
    }
    public function addUser()
    {
        return view('users.add', ['title' => 'Add User']);
    }

    public function userGroup()
    {
        return view('users.group', ['title' => 'User Groups']);
    }

    public function examReports()
    {
        return view('reports.exam', ['title' => 'Exam Reports']);
    }
    public function studentPerformance()
    {
        return view('reports.student', ['title' => 'Student Performance']);
    }

    public function profile()
    {
        return view('profile.index', ['title' => 'Settings']);
    }

    public function settings()
    {
        return view('settings.index', ['title' => 'Settings']);
    }
}
