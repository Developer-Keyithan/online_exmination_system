<?php
use Backend\Modal\Auth;

class PageAPI
{

    public function __construct()
    {
        $currentRoute = $_SERVER['REQUEST_URI'] ?? ''; // simple check
        if (!Auth::isLoggedIn() && !str_contains($currentRoute, '/login')) {
            redirect('login');
        }
    }

    public function login()
    {
        if (Auth::isLoggedIn()) {
            redirect('dashboard'); // if already logged in, redirect
        }
        return view('auth.login', ['title' => 'User Login']);
    }

    public function dashboard()
    {
        return view('dashboard', ['title' => 'Dashboard']);
    }

    public function createExam()
    {
        return view('exams.create', ['title' => 'Create Exam']);
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
    public function myExams()
    {
        return view('exams.my', ['title' => 'My Exams']);
    }

    public function results()
    {
        return view('results.all', ['title' => 'All Results']);
    }
    public function myResults()
    {
        return view('results.my', ['title' => 'My Results']);
    }

    public function attendance()
    {
        return view('attendance.view', ['title' => 'View Attendance']);
    }
    public function markAttendance()
    {
        return view('attendance.mark', ['title' => 'Mark Attendance']);
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

    public function examReports()
    {
        return view('reports.exam', ['title' => 'Exam Reports']);
    }
    public function studentPerformance()
    {
        return view('reports.student', ['title' => 'Student Performance']);
    }

    public function settings()
    {
        return view('settings.index', ['title' => 'Settings']);
    }
}
