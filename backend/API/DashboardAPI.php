<?php

use Backend\Modal\Auth;

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
            redirect('login');
            exit;
        }
        $this->user = Auth::getUser();
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
            $stmt = $this->db->prepare("SELECT id, name, email, user_group as role, created_at FROM users  WHERE status = 0 AND user_group != 1 ORDER BY created_at DESC  LIMIT 5 ");
            $stmt->execute();
            $recentUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($recentUsers as &$user) {
                $user['created_at'] = str_replace(' ', "T", $user['created_at']);
            }
            $stmt = $this->db->prepare("SELECT ei.id, ei.title, ei.code, ei.duration, es.schedule_type, es.start_time FROM exam_info ei JOIN exam_settings es ON es.exam_id = ei.id WHERE ei.status != 0 ");
            $stmt->execute();
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $todayCount = 0;
            $upcomingExams = [];
            $activeCount = 0;

            $now = new DateTime();
            $todayDate = $now->format('Y-m-d');

            foreach ($exams as $exam) {
                if ($exam['schedule_type'] === 'anytime') {
                    $activeCount++;
                    $todayCount++;
                    continue;
                }

                $startDateTime = new DateTime($exam['start_time']);
                $startDate = $startDateTime->format('Y-m-d');

                $endDateTime = clone $startDateTime;
                $endDateTime->modify('+' . (int) $exam['duration'] . ' minutes');
                if ($startDate === $todayDate) {
                    $todayCount++;

                    if ($now < $startDateTime) {
                        $upcomingExams[] = [
                            'id' => $exam['id'] + 0,
                            'code' => str_replace(' ', '_', $exam['code']),
                            'title' => $exam['title'],
                            'date' => str_replace(' ', 'T', $exam['start_time']),
                            'duration' => $exam['duration'] + 0
                        ];
                    } elseif ($now >= $startDateTime && $now <= $endDateTime) {
                        $activeCount++;
                    }
                } elseif ($startDate > $todayDate) {
                    $upcomingExams[] = [
                        'id' => $exam['id'] + 0,
                        'code' => str_replace(' ', '_', $exam['code']),
                        'title' => $exam['title'],
                        'date' => str_replace(' ', 'T', $exam['start_time']),
                        'duration' => $exam['duration'] + 0
                    ];
                }
            }

            $stmt = $this->db->prepare("SELECT ea.score, ei.total_marks FROM exam_attempts ea LEFT JOIN exam_info ei ON ea.exam_id = ei.id WHERE ea.status IN ('completed', 'rules_violation')");
            $stmt->execute();
            $scores = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $totalScorePresantage = 0;
            $totalCount = count($scores);

            foreach ($scores as $row) {
                $scorePresantage = ($row['score'] / $row['total_marks']) * 100;
                $totalScorePresantage += (float) $scorePresantage;
            }


            $stmt = $this->db->prepare("SELECT  COUNT(*) AS total, SUM( CASE  WHEN ea.score >= ei.passing_marks THEN 1 ELSE 0  END ) AS passed FROM exam_attempts ea LEFT JOIN exam_info ei ON ei.id = ea.exam_id WHERE ea.status IN ('completed', 'rules_violation') ");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            $total = (int) $result['total'];
            $passed = (int) $result['passed'];

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM questions");
            $stmt->execute();
            $totalQuestions = (int) $stmt->fetchColumn();

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM questions WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)");
            $stmt->execute();
            $thisWeekQuestions = (int) $stmt->fetchColumn();

            $stats['todayExams'] = (int) $todayCount;
            $stats['activeExams'] = (int) $activeCount;
            $stats['avgScore'] = $totalCount > 0 ? round($totalScorePresantage / $totalCount, 1) : 0;
            $stats['passRate'] = $total > 0 ? round(($passed / $total) * 100, 1) : 0;
            $stats['totalQuestions'] = $totalQuestions;
            $stats['thisWeekQuestions'] = $thisWeekQuestions;


            return $this->successResponse('Dashboard data loaded', [
                'stats' => $stats,
                'recentUsers' => $recentUsers,
                'upcomingExams' => $upcomingExams
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
            $stats = [
                'activeExams' => 0,
                'exams' => 0,
                'enrolledStudents' => 0,
                'questions' => 0
            ];
            $upcomingExams = [];
            $recentAttempts = [];

            $stmt = $this->db->prepare("SELECT * FROM exam_info WHERE created_by = ?");
            $stmt->execute([$userId]);
            $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $total_enrolled_students = 0;
            $active_exams = 0;
            $allAttempts = [];
            foreach ($exams as &$exam) {
                $stmt = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
                $stmt->execute([$exam['id']]);
                $settings = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($settings) {
                    $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ?");
                    $stmt->execute([$exam['id']]);
                    $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $stmt = $this->db->prepare("SELECT * FROM exam_attempts WHERE exam_id = ? AND status IN ('completed', 'rules_violation', 'in_progress', 'started')");
                    $stmt->execute([$exam['id']]);
                    $attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $enrolled_students = count($registrations);
                    $total_enrolled_students += $enrolled_students;

                    if ($attempts) {
                        foreach ($attempts as &$attempt) {
                            $attempt['exam_title'] = $exam['title'];
                        }
                        unset($attempt);
                        $allAttempts = array_merge($allAttempts, $attempts);
                    }

                    if ($settings['schedule_type'] == 'anytime') {
                        $active_exams++;
                        continue;
                    }

                    $now = new DateTime();
                    $todayDate = $now->format('Y-m-d');
                    $startDateTime = new DateTime($settings['start_time']);
                    $startDate = $startDateTime->format('Y-m-d');

                    $endDateTime = clone $startDateTime;
                    $endDateTime->modify('+' . (int) $exam['duration'] . ' minutes');
                    if ($startDate === $todayDate) {
                        if ($now < $startDateTime) {
                            $upcomingExams[] = [
                                'id' => $exam['id'] + 0,
                                'code' => str_replace(' ', '_', $exam['code']),
                                'title' => $exam['title'],
                                'date' => str_replace(' ', 'T', $settings['start_time']),
                                'students' => $enrolled_students + 0
                            ];
                        } elseif ($now >= $startDateTime && $now <= $endDateTime) {
                            $active_exams++;
                        }
                    } elseif ($startDate > $todayDate) {
                        $upcomingExams[] = [
                            'id' => $exam['id'] + 0,
                            'code' => str_replace(' ', '_', $exam['code']),
                            'title' => $exam['title'],
                            'date' => str_replace(' ', 'T', $settings['start_time']),
                            'students' => $enrolled_students + 0
                        ];
                    }
                }
            }

            $stmt = $this->db->prepare("SELECT COUNT(*) FROM questions WHERE created_by = ?");
            $stmt->execute([$userId]);
            $toatl_questions = $stmt->fetchColumn();

            usort($allAttempts, function ($a, $b) {
                return strtotime($b['started_at']) - strtotime($a['started_at']);
            });

            $convertedAttempts = [];
            foreach ($allAttempts as &$att) {
                $attempt = [
                    'attempt_id' => $att['id'],
                    'exam_id' => $att['exam_id'],
                    'student_id' => $att['student_id'],
                    'student_name' => getUserName($att['student_id']),
                    'exam_title' => $att['exam_title'],
                    'attempted_date' => $att['started_at']
                ];
                $convertedAttempts[] = $attempt;
            }

            $recentAttempts = array_slice($convertedAttempts, 0, 5);

            $stats = [
                'activeExams' => $active_exams + 0,
                'exams' => count($exams) + 0,
                'enrolledStudents' => $total_enrolled_students + 0,
                'questions' => $toatl_questions + 0
            ];

            return $this->successResponse('Dashboard data loaded', [
                'stats' => $stats,
                'upcomingExams' => $upcomingExams,
                'recentAttempts' => $recentAttempts
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

            $examTaken = 0;
            $passedCount = 0;
            $totalScore = 0;

            $upcomingExams = [];
            $allResults = [];

            // Score ranges
            $scoreDistributions = [
                ['range' => '75-100%', 'count' => 0, 'percentage' => 0],
                ['range' => '65-74%', 'count' => 0, 'percentage' => 0],
                ['range' => '55-64%', 'count' => 0, 'percentage' => 0],
                ['range' => '40-54%', 'count' => 0, 'percentage' => 0],
                ['range' => 'Below 40%', 'count' => 0, 'percentage' => 0],
            ];

            // Registrations
            $stmt = $this->db->prepare(
                "SELECT * FROM exam_registration WHERE student_id = ?"
            );
            $stmt->execute([$userId]);
            $registrations = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($registrations as $reg) {

                // Attempt
                $stmt = $this->db->prepare(
                    "SELECT * FROM exam_attempts 
                 WHERE registration_id = ? AND student_id = ?"
                );
                $stmt->execute([$reg['id'], $userId]);
                $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$attempt)
                    continue;

                // Exam info
                $stmt = $this->db->prepare(
                    "SELECT ei.id, ei.title, ei.code, ei.duration, es.start_time, ea.url AS hash
                 FROM exam_info ei
                 LEFT JOIN exam_settings es ON ei.id = es.exam_id
                 LEFT JOIN exam_attempts ea ON ea.id = ?
                 WHERE ei.id = ?"
                );
                $stmt->execute([$attempt['id'], $reg['exam_id']]);
                $exam = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$exam)
                    continue;

                /* ---------------- Upcoming Exams ---------------- */
                if ($reg['status'] === 'registered' && $attempt['status'] === 'not_started') {
                    $upcomingExams[] = [
                        'id' => $exam['id'],
                        'title' => $exam['title'],
                        'code' => str_replace(' ', "_", $exam['code']),
                        'duration' => $exam['duration'],
                        'date' => str_replace(' ', 'T', $exam['start_time']),
                        'start_time' =>str_replace(' ', 'T', $exam['start_time'])
                    ];
                }

                /* ---------------- Completed Exams ---------------- */
                if (in_array($attempt['status'], ['completed', 'rules_violation'])) {

                    $examTaken++;
                    $score = (int) $attempt['score'];
                    $totalScore += $score;

                    if ((int) $attempt['passed'] === 1) {
                        $passedCount++;
                    }

                    // Score distribution
                    if ($score >= 75)
                        $scoreDistributions[0]['count']++;
                    elseif ($score >= 65)
                        $scoreDistributions[1]['count']++;
                    elseif ($score >= 55)
                        $scoreDistributions[2]['count']++;
                    elseif ($score >= 40)
                        $scoreDistributions[3]['count']++;
                    else
                        $scoreDistributions[4]['count']++;

                    $allResults[] = [
                        'attempt_id' => $attempt['id'],
                        'exam_id' => $exam['id'],
                        'exam_title' => $exam['title'],
                        'code' => strtoupper(str_replace(' ', '_', $exam['code'])),
                        'score' => $score,
                        'passed' => (bool) $attempt['passed'],
                        'date' => str_replace(' ', 'T', $attempt['started_at'])
                    ];
                }
            }

            /* ---------------- Calculations ---------------- */
            $passRate = $examTaken > 0
                ? round(($passedCount / $examTaken) * 100)
                : 0;

            $avgScore = $examTaken > 0
                ? round($totalScore / $examTaken)
                : 0;

            foreach ($scoreDistributions as &$dist) {
                $dist['percentage'] = $examTaken > 0
                    ? round(($dist['count'] / $examTaken) * 100)
                    : 0;
            }

            // Recent results (latest 5)
            usort($allResults, function ($a, $b) {
                return strtotime($b['date']) - strtotime($a['date']);
            });
            $recentResults = array_slice($allResults, 0, 5);

            /* ---------------- Response ---------------- */
            return $this->successResponse('Dashboard data loaded successfully', [
                'stats' => [
                    'passRate' => $passRate,
                    'examsTaken' => $examTaken,
                    'avgScore' => $avgScore,
                    'upcomingExams' => count($upcomingExams)
                ],
                'upcomingExams' => $upcomingExams,
                'recentResults' => $recentResults,
                'scoreDistribution' => $scoreDistributions
            ]);

        } catch (Exception $e) {
            return $this->errorResponse(
                'Failed to load dashboard data: ' . $e->getMessage()
            );
        }
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
        return json_encode(['status' => 'error', 'msg' => $message]);
    }
}
