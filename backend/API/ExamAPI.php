<?php

use Backend\Modal\Auth;

class ExamAPI
{
    private $db;
    public function __construct()
    {
        $this->db = db();
    }

    public function saveExamBasicInfo()
    {
        try {
            // Retrieve and validate input data
            $examTitle = $_POST['examTitle'] ?? null;
            $examCode = $_POST['examCode'] ?? null;
            $examDuration = $_POST['examDuration'] ?? null;
            $totalMarks = $_POST['totalMarks'] ?? null;
            $passingMarks = $_POST['passingMarks'] ?? null;
            $instructions = $_POST['instructions'] ?? '';
            $examStatus = $_POST['examStatus'] ?? 0;
            $totalQuestions = $_POST['totalQuestions'] ?? null;

            if (!$examTitle || !$examCode || !$examDuration || !$totalMarks || !$passingMarks || !$totalQuestions) {
                throw new Exception("All required fields must be filled.");
            }

            if ($examStatus == 'sheduled') {
                $examStatus = 2;
            } else if ($examStatus == 'published') {
                $examStatus = 1;
            } else if ($examStatus == 'draft') {
                $examStatus = 0;
            } else {
                $examStatus = 0; // default to draft
            }

            // Save exam basic info to the database
            $stmt = $this->db->prepare("INSERT INTO exam_info (title, code, total_marks, duration, total_num_of_ques, passing_marks, instructions, created_by, status) VALUES (? , ? , ? , ? , ? , ? , ? , ? , ?)");
            $stmt->execute([
                $examTitle,
                $examCode,
                $totalMarks,
                $examDuration,
                $totalQuestions,
                $passingMarks,
                $instructions,
                user_id(),
                $examStatus
            ]);
            $examId = $this->db->lastInsertId();
            $status = 'draft';
            if ($examStatus == 1) {
                $status = 'published';
            } else if ($examStatus == 2) {
                $status = 'sheduled';
            }
            $exam = [
                'id' => $examId,
                'title' => $examTitle,
                'code' => $examCode,
                'totalMarks' => $totalMarks,
                'totalQuestions' => $totalQuestions,
                'duration' => $examDuration,
                'passingMarks' => $passingMarks,
                'instructions' => $instructions,
                'status' => $status,
            ];

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam basic information saved successfully.',
                'exam_id' => $examId,
                'exam' => $exam
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function editExamBasicInfo($id)
    {
        try {

            $fields = [];
            $params = [];

            if (!empty($_POST['examTitle'])) {
                $fields[] = "title = ?";
                $params[] = $_POST['examTitle'];
            }

            if (!empty($_POST['examCode'])) {
                $fields[] = "code = ?";
                $params[] = $_POST['examCode'];
            }

            if (!empty($_POST['examDuration'])) {
                $fields[] = "duration = ?";
                $params[] = $_POST['examDuration'];
            }

            if (!empty($_POST['totalMarks'])) {
                $fields[] = "total_marks = ?";
                $params[] = $_POST['totalMarks'];
            }

            if (!empty($_POST['passingMarks'])) {
                $fields[] = "passing_marks = ?";
                $params[] = $_POST['passingMarks'];
            }

            if (isset($_POST['examInstructions'])) {
                $fields[] = "instructions = ?";
                $params[] = $_POST['examInstructions'];
            }

            if (isset($_POST['examStatus'])) {
                $fields[] = "status = ?";
                $params[] = $_POST['examStatus'];
            }

            if (!empty($_POST['totalQuestions'])) {
                $fields[] = "total_num_of_ques = ?";
                $params[] = $_POST['totalQuestions'];
            }

            $exam = [
                'id' => $id,
                'title' => $_POST['examTitle'],
                'code' => $_POST['examCode'],
                'totalMarks' => $_POST['totalMarks'],
                'totalQuestions' => $_POST['totalQuestions'],
                'duration' => $_POST['examDuration'],
                'passingMarks' => $_POST['passingMarks'],
                'instructions' => $_POST['examInstructions'],
                'status' => $_POST['examStatus'],
            ];

            if (empty($fields)) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'No fields to update',
                    'exam' => $exam
                ]);
            }

            // Build SQL safely
            $sql = "UPDATE exam_info SET " . implode(', ', $fields) . " WHERE id = ?";

            // Add ID to param list
            $params[] = $id;

            // Execute
            $statement = $this->db->prepare($sql);
            $statement->execute($params);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam updated successfully',
                'exam' => $exam
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getAllExams()
    {
        try {
            $exams = [];

            // Get all exams with settings
            $statement = $this->db->prepare("SELECT i.id, i.title, i.code, i.duration, i.total_num_of_ques AS total_questions, i.status, i.created_by, s.schedule_type, s.start_time FROM exam_info i LEFT JOIN exam_settings s ON s.exam_id = i.id ");
            $statement->execute();
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);

            foreach ($result as $exam) {
                $exam_id = $exam['id'];
                $status = $exam['status'];
                $schedule_type = $exam['schedule_type'];
                $start_time = $exam['start_time'] ? strtotime($exam['start_time']) : null;
                $duration = (int) $exam['duration'];
                $current_time = time();
                $end_time = $start_time + ($duration * 60);

                // Calculate final status
                $finalStatus = '';
                if ($status == 0) {
                    $finalStatus = 'draft';
                } else if ($status == 2) {
                    $finalStatus = 'canceled';
                } else if ($status == 1 && $schedule_type == 'anytime') {
                    $finalStatus = 'active';
                } else if ($status == 1 && $schedule_type == 'scheduled') {
                    if ($start_time > $current_time) {
                        $finalStatus = 'upcoming';
                    } else if ($current_time >= $start_time && $current_time <= $end_time) {
                        $finalStatus = 'active';
                    } else if ($current_time > $end_time) {
                        $finalStatus = 'completed';
                    }
                }
                $exam['final_status'] = $finalStatus;

                // Count participants
                $stmt = $this->db->prepare("SELECT COUNT(*) as participants_count FROM exam_registration WHERE exam_id = ?");
                $stmt->execute([$exam_id]);
                $participants_count = (int) $stmt->fetch(PDO::FETCH_ASSOC)['participants_count'];

                // Count completed attempts (completed or rules_violation)
                $stmt = $this->db->prepare("SELECT COUNT(*) as completed_count  FROM exam_attempts ea JOIN exam_registration er ON ea.registration_id = er.id WHERE er.exam_id = ? AND (ea.status = 'completed' OR ea.status = 'rules_violation') ");
                $stmt->execute([$exam_id]);
                $completed_count = (int) $stmt->fetch(PDO::FETCH_ASSOC)['completed_count'];

                $exams[] = [
                    'id' => $exam['id'],
                    'title' => $exam['title'],
                    'code' => str_replace(' ', '_', $exam['code']),
                    'schedule_type' => $exam['schedule_type'],
                    'start_time' => $start_time ? str_replace(' ', 'T', $exam['start_time']) : null,
                    'end_time' => $start_time ? date("Y-m-d\TH:i:s", $end_time) : null,
                    'duration' => $exam['duration'],
                    'status' => $finalStatus,
                    'total_questions' => $exam['total_questions'],
                    'participants_count' => $participants_count,
                    'completed_count' => $completed_count,
                    'created_by' => $exam['created_by']
                ];
            }

            return json_encode([
                'status' => 'success',
                'exams' => $exams
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamData($id)
    {
        $sql = "SELECT id, title, duration, code, total_marks, passing_marks, instructions, status, total_num_of_ques as total_questions 
            FROM exam_info WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            return json_encode([
                'status' => 'error',
                'msg' => 'Exam not found.'
            ]);
        }

        $exam['duration'] = $exam['duration'] + 0;

        $questions = [];
        $questionIds = []; // prevent duplicates
        $sections = [];
        $optionMap = ['a' => 1, 'b' => 2, 'c' => 3, 'd' => 4];
        $options = [];

        // Fetch all questions that already contain this exam
        $statment = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
        $statment->execute([$id]);
        $existingQuestions = $statment->fetchAll(PDO::FETCH_ASSOC);

        foreach ($existingQuestions as $question) {
            $options = [];
            $question['isSaved'] = true;
            foreach ($optionMap as $key => $order) {
                $options[] = [
                    'text' => $question[$key] ?? '',
                    'image' => $question[$key . '_img'] ?? '',
                    'order' => $order,
                    'op' => strtoupper($key)
                ];
            }
            $question['options'] = $options;
            $question['marks'] = $question['marks'] + 0;
            // Decode exam_ids
            $examIDs = !empty($question['exam_ids']) ? json_decode($question['exam_ids'], true) : [];
            $sectionIds = [];
            if (!empty($question['section_ids'])) {
                $decoded = json_decode($question['section_ids'], true);
                if (is_array($decoded)) {
                    $sectionIds = array_map('intval', $decoded);
                }
            }
            $question['assignedSections'] = $sectionIds;
            $question['exam_ids'] = $examIDs;
            $question['grid'] = $question['grid'] + 0;
            $questions[$question['id']] = $question;
            $questionIds[] = $question['id'];
        }

        // Fetch sections
        $statment = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
        $statment->execute([$id]);
        $scts = $statment->fetchAll(PDO::FETCH_ASSOC);

        foreach ($scts as $sct) {

            // Fetch questions that belong to this section
            $stmt = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(section_ids, JSON_QUOTE(?))");
            $stmt->execute([$sct['id']]);
            $sectionQuestions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($sectionQuestions as $q) {
                $options = [];
                // Decode exam_ids
                $examIDs = !empty($q['exam_ids']) ? json_decode($q['exam_ids'], true) : [];

                // Push this section's exam_id if missing
                if (!in_array($sct['exam_id'], $examIDs)) {
                    $examIDs[] = $sct['exam_id'];
                    $examIDsJson = json_encode(array_values(array_unique($examIDs)));

                    $updateStmt = $this->db->prepare("UPDATE questions SET exam_ids = ? WHERE id = ?");
                    $updateStmt->execute([$examIDsJson, $q['id']]);
                }

                $q['exam_ids'] = $examIDs;

                // Attach options
                foreach ($optionMap as $key => $order) {
                    $options[] = [
                        'text' => $q[$key] ?? '',
                        'image' => $q[$key . '_img'] ?? '',
                        'order' => $order,
                        'op' => strtoupper($key)
                    ];
                }
                $q['options'] = $options;
                $q['isSaved'] = true;
                $q['marks'] = $q['marks'] + 0;

                $sectionIds = [];
                if (!empty($q['section_ids'])) {
                    $decoded = json_decode($q['section_ids'], true);
                    if (is_array($decoded)) {
                        $sectionIds = array_map('intval', $decoded);
                    }
                }
                $q['assignedSections'] = $sectionIds;

                // Add to final questions if not already added
                if (!in_array($q['id'], $questionIds)) {
                    $questions[$q['id']] = $q;
                    $questionIds[] = $q['id'];
                }
            }

            // Build section info
            $sections[] = [
                'id' => $sct['id'],
                'title' => $sct['title'],
                'description' => $sct['s_des'],
                'secondDescription' => $sct['s_s_des'],
                'question_count' => $sct['num_of_ques'],
                'examID' => $sct['exam_id'],
                'assignedQuestions' => count($sectionQuestions)
            ];
        }

        $statment = $this->db->prepare("SELECT id, schedule_type, start_time, shuffle_questions, shuffle_options, immediate_results, retake, max_attempts, enable_proctoring, full_screen_mode, disable_copy_paste, disable_right_click FROM exam_settings WHERE exam_id = ?");
        $statment->execute([$id]);
        $settings = $statment->fetch(PDO::FETCH_ASSOC);

        $settings_data = [];
        if ($settings) {
            $settings_data = [
                'id' => $settings['id'] + 0,
                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] != null ? str_replace(" ", "T", $settings['start_time']) : null,
                'shuffle_questions' => $settings['shuffle_questions'] == 1 ? true : false,
                'shuffle_options' => $settings['shuffle_options'] == 1 ? true : false,
                'immediate_results' => $settings['immediate_results'] == 1 ? true : false,
                'retake' => $settings['retake'] == 1 ? true : false,
                'max_attempts' => $settings['max_attempts'] != null ? $settings['max_attempts'] + 0 : 1,
                'enable_proctoring' => $settings['enable_proctoring'] == 1 ? true : false,
                'full_screen_mode' => $settings['full_screen_mode'] == 1 ? true : false,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1 ? true : false,
                'disable_right_click' => $settings['disable_right_click'] == 1 ? true : false,
                'isDone' => true
            ];
        }

        if ($exam['status'] == 0) {
            $exam['status'] = 'draft';
        } else if ($settings['schedule_type'] == 'scheduled' && $exam['status'] == 1) {
            $exam['status'] = 'scheduled';
        } else if ($exam['status'] == 1) {
            $exam['status'] = 'published';
        } else if ($exam['status'] == 2) {
            $exam['status'] = 'canceled';
        }

        usort($questions, function ($a, $b) {
            return strtotime($a['created_at']) - strtotime($b['created_at']);
        });

        return json_encode([
            'status' => 'success',
            'exam' => $exam,
            'questions' => array_values($questions), // reindex
            'sections' => $sections,
            'exam_settings' => $settings_data
        ]);
    }

    public function saveExamSettings()
    {
        try {
            $exam_id = $_POST['exam_id'];
            $schedule_type = $_POST['scheduleType'];
            $start_date_time = $_POST['scheduleType'] == 'scheduled' ? str_replace("T", " ", $_POST['startDateTime']) : null;

            $shuffle_questions = isset($_POST['shuffleQuestions']) ? 1 : 0;
            $shuffle_options = isset($_POST['shuffleOptions']) ? 1 : 0;
            $immediate_results = isset($_POST['showResultsImmediately']) ? 1 : 0;
            $retake = isset($_POST['allowRetake']) ? 1 : 0;

            $max_attempts = isset($_POST['maxAttempts']) ? $_POST['maxAttempts'] : 1;

            $enable_proctoring = isset($_POST['enableProctoring']) ? 1 : 0;
            $full_screen_mode = isset($_POST['fullScreenMode']) ? 1 : 0;
            $disable_copy_paste = isset($_POST['disableCopyPaste']) ? 1 : 0;
            $disable_right_click = isset($_POST['disableRightClick']) ? 1 : 0;

            $statement = $this->db->prepare("INSERT INTO exam_settings (exam_id, schedule_type, start_time, shuffle_questions, shuffle_options, immediate_results, retake, max_attempts, enable_proctoring, full_screen_mode, disable_copy_paste, disable_right_click) VALUES ( ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,?, ? )");
            $statement->execute([$exam_id, $schedule_type, $start_date_time, $shuffle_questions, $shuffle_options, $immediate_results, $retake, $max_attempts, $enable_proctoring, $full_screen_mode, $disable_copy_paste, $disable_right_click]);
            $setting_id = $this->db->lastInsertId();

            $settings_data = [
                'id' => $setting_id + 0,
                'exam_id' => $exam_id + 0,
                'schedule_type' => $schedule_type,
                'start_time' => $schedule_type == 'scheduled' ? str_replace(" ", "T", $start_date_time) : null,
                'shuffle_questions' => $shuffle_questions == 1 ? true : false,
                'shuffle_options' => $shuffle_options == 1 ? true : false,
                'immediate_results' => $immediate_results == 1 ? true : false,
                'retake' => $retake == 1 ? true : false,
                'max_attempts' => $max_attempts,
                'enable_proctoring' => $enable_proctoring == 1 ? true : false,
                'full_screen_mode' => $full_screen_mode == 1 ? true : false,
                'disable_copy_paste' => $disable_copy_paste == 1 ? true : false,
                'disable_right_click' => $disable_right_click == 1 ? true : false,
                'isDone' => true
            ];

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam settings added successfully',
                'exam_settings' => $settings_data
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function editExamSettings($id)
    {
        try {
            $data = $_POST;

            $fields = [];
            $values = [];

            // Optional fields
            if (isset($data['exam_id'])) {
                $fields[] = "exam_id = ?";
                $values[] = $data['exam_id'];
            }

            if (isset($data['scheduleType'])) {
                $fields[] = "schedule_type = ?";
                $values[] = $data['scheduleType'];
            }

            if (isset(($data['scheduleType'])) && ($data['scheduleType']) == 'scheduled' && isset($data['startDateTime'])) {
                $start_time = str_replace("T", " ", $data['startDateTime']);
            } else {
                $start_time = null;
            }
            $fields[] = "start_time = ?";
            $values[] = $start_time;


            // Boolean fields
            $booleanFields = [
                'shuffleQuestions' => 'shuffle_questions',
                'shuffleOptions' => 'shuffle_options',
                'showResultsImmediately' => 'immediate_results',
                'allowRetake' => 'retake',
                'enableProctoring' => 'enable_proctoring',
                'fullScreenMode' => 'full_screen_mode',
                'disableCopyPaste' => 'disable_copy_paste',
                'disableRightClick' => 'disable_right_click',
            ];

            foreach ($booleanFields as $key => $column) {
                $fields[] = "$column = ?";
                $values[] = isset($data[$key]) && $data[$key] ? 1 : 0;
            }

            $fields[] = "max_attempts = ?";
            if (isset($data['allowRetake']) && $data['allowRetake'] && isset($data['maxAttempts'])) {
                $values[] = $data['maxAttempts'];
            } else {
                $values[] = null;
            }

            if (!empty($fields)) {
                $values[] = $id;
                $sql = "UPDATE exam_settings SET " . implode(", ", $fields) . " WHERE id = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute($values);
            }

            // Build settings_data for response
            $settings_data = [
                'id' => $id + 0,
                'exam_id' => isset($data['exam_id']) ? $data['exam_id'] + 0 : null,
                'schedule_type' => $data['scheduleType'] ?? null,
                'start_time' => isset($start_time) ? str_replace(" ", "T", $start_time) : null,
                'isDone' => true
            ];

            foreach ($booleanFields as $key => $column) {
                $settings_data[$column] = isset($data[$key]) ? ($data[$key] ? true : false) : false;
            }

            $settings_data['max_attempts'] = isset($data['maxAttempts']) ? $data['maxAttempts'] + 0 : 1;

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam settings updated successfully',
                'exam_settings' => $settings_data
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamDataForPreview($id)
    {
        try {
            $statement = $this->db->prepare("SELECT * FROM exam_info WHERE id = ?");
            $statement->execute([$id]);
            $exam = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                throw new Exception("Exam not found");
            }

            $statement = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $statement->execute([$id]);
            $questions = $statement->fetchAll(PDO::FETCH_ASSOC);

            // if (!$questions) {
            //     throw new Exception('No questions found for this exam');
            // }

            $statement = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $statement->execute([$id]);
            $sections = $statement->fetchAll(PDO::FETCH_ASSOC);

            $statement = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
            $statement->execute([$id]);
            $settings = $statement->fetch(PDO::FETCH_ASSOC);

            $finalSections = [];

            foreach ($sections as $index => $section) {
                $finalSections[] = [
                    'id' => (int) $section['id'],
                    'exam_id' => (int) $id,
                    'title' => $section['title'] ?? '',
                    'description' => $section['s_des'] ?? '',
                    'second_description' => $section['s_s_des'] ?? '',
                    'order' => $index + 1,
                    'questions' => [],
                    'created_at' => $section['created_at'] ?? '',
                    'questions_count' => $section['num_of_ques'] ?? 0
                ];
            }

            // Create a mapping of section IDs for quick lookup
            $sectionIds = array_column($finalSections, 'id');

            $finalQuestions = [];
            foreach ($questions as $question) {
                $optionsKeys = ['a', 'b', 'c', 'd'];
                $options = [];
                foreach ($optionsKeys as $order => $key) {
                    $options[] = [
                        // 'id' => $key,
                        'text' => $question[$key] ?? '',
                        'image' => $question[$key . '_img'] ?? null,
                        'order' => $order + 1,
                        'op' => strtoupper($key)
                    ];
                }

                // Get section IDs from the question (assuming there's a section_ids field)
                // If not, you may need to query a separate table for question-section relationships
                $sectionIdsForQuestion = [];

                // If your questions table has a section_ids JSON field:
                if (isset($question['section_ids'])) {
                    $sectionIdsJson = json_decode($question['section_ids'], true);
                    if (is_array($sectionIdsJson)) {
                        $sectionIdsForQuestion = array_map('intval', $sectionIdsJson);
                    }
                }

                // OR if you have a separate question_sections table, query it:
                // $stmt = $this->db->prepare("SELECT section_id FROM question_sections WHERE question_id = ?");
                // $stmt->execute([$question['id']]);
                // $sectionIdsForQuestion = $stmt->fetchAll(PDO::FETCH_COLUMN);

                $qData = [
                    'id' => (int) $question['id'],
                    'question' => $question['question'],
                    'marks' => (int) $question['marks'],
                    'created_at' => $question['created_at'],
                    'created_by' => $question['created_by'],
                    'options' => $options,
                    'correctAnswer' => strtolower($question['answer'] ?? 'a'), // Default to 'a' if empty
                    'sectionIds' => $sectionIdsForQuestion,
                    'grid' => $question['grid'] + 0,
                    'image' => $question['image'] ?? null
                ];

                $finalQuestions[] = $qData;
            }

            if ($exam['status'] == 0) {
                $exam['status'] = 'draft';
            } else if ($settings['schedule_type'] == 'scheduled' && $exam['status'] == 1) {
                $exam['status'] = 'scheduled';
            } else if ($exam['status'] == 1) {
                $exam['status'] = 'published';
            } else if ($exam['status'] == 2) {
                $exam['status'] = 'canceled';
            }

            $exam_info = [
                'id' => (int) $id,
                'code' => $exam['code'],
                'title' => $exam['title'],
                'total_marks' => (int) $exam['total_marks'],
                'total_questions' => (int) $exam['total_num_of_ques'],
                'duration' => (int) $exam['duration'],
                'instructions' => $exam['instructions'],
                'passing_marks' => (int) $exam['passing_marks'],
                'status' => $exam['status'],
                'published_at' => $exam['status'] == 'published' ? str_replace(" ", "T", $exam['published_at']) : null,
            ];

            $settings_info = [
                'id' => (int) $settings['id'],
                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] ? str_replace(" ", "T", $settings['start_time']) : null,
                'end_time' => $settings['start_time'] ? str_replace(" ", "T", $settings['start_time']) : null,
                'shuffle_questions' => $settings['shuffle_questions'] == 1,
                'shuffle_options' => $settings['shuffle_options'] == 1,
                'show_results_immediately' => $settings['immediate_results'] == 1, // Note: renamed for consistency
                'allow_retake' => $settings['retake'] == 1,
                'max_attempts' => $settings['max_attempts'] ? (int) $settings['max_attempts'] : 1,
                'enable_proctoring' => $settings['enable_proctoring'] == 1,
                'full_screen_mode' => $settings['full_screen_mode'] == 1,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1,
                'disable_right_click' => $settings['disable_right_click'] == 1,
                'allow_navigation' => true // Add if exists
            ];

            return json_encode([
                'status' => 'success',
                'exam_info' => $exam_info,
                'sections' => $finalSections,
                'questions' => $finalQuestions,
                'settings' => $settings_info
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function publishExam($exam_id)
    {
        try {
            $statement = $this->db->prepare("UPDATE exam_info SET status = ?, published_by = ?, published_at = CURDATE() WHERE id = ?");
            $statement->execute(params: [1, user_id(), $exam_id]);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam published successfully',
                'published_at' => date('Y-m-d\TH:m:s')
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function unpublishExam($exam_id)
    {
        try {
            $statement = $this->db->prepare("UPDATE exam_info SET status = ?, published_by = ?, published_at = null WHERE id = ?");
            $statement->execute(params: [2, 0, $exam_id]);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam unpublished successfully'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function cancelExam($exam_id)
    {
        try {
            $statement = $this->db->prepare("UPDATE exam_info SET status = ?, published_at = null  WHERE id = ?");
            $statement->execute(params: [2, $exam_id]);

            return json_encode([
                'status' => 'success',
                'msg' => 'The exam was suddenly stopped for all candidates.'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function deleteExam($exam_id)
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM exam_info WHERE id = ?");
            $stmt->execute([$exam_id]);

            $stmt = $this->db->prepare("DELETE FROM exam_settings WHERE exam_id = ?");
            $stmt->execute([$exam_id]);

            $stmt = $this->db->prepare("SELECT id FROM sections WHERE exam_id = ?");
            $stmt->execute([$exam_id]);
            $sections = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $stmt = $this->db->prepare("SELECT id, exam_ids, section_ids FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $stmt->execute([$exam_id]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($questions as $q) {
                $exam_ids = json_decode($q['exam_ids'], true);
                $section_ids = json_decode($q['section_ids'], true);

                if (($key = array_search($exam_id, $exam_ids)) !== false) {
                    unset($exam_ids[$key]);
                    $exam_ids = array_values($exam_ids);
                }

                $section_ids = array_filter($section_ids, function ($sid) use ($sections) {
                    return !in_array($sid, $sections);
                });
                $section_ids = array_values($section_ids);

                // Update question
                $update = $this->db->prepare("UPDATE questions SET exam_ids = ?, section_ids = ? WHERE id = ?");
                $update->execute([json_encode($exam_ids), json_encode($section_ids), $q['id']]);
            }

            foreach ($sections as $section) {
                $stmt = $this->db->prepare("DELETE FROM sections WHERE id = ?");
                $stmt->execute([$section['id']]);
            }

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam deleted successfully'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function saveExamRegistrationData($exam_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ? AND student_id = ?");
            $stmt->execute([$exam_id, $_SESSION['user_id']]);
            $registration_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($registration_data) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'You have already registered for this exam',
                    'data' => $registration_data
                ]);
            }

            $stmt = $this->db->prepare("INSERT INTO exam_registration (`exam_id`, `student_id`, `registration_date`) VALUES (?, ?, ?)");
            $stmt->execute([$exam_id, user_id(), date('Y-m-d H:i:s')]);
            $registration_id = $this->db->lastInsertId();

            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE id = ?");
            $stmt->execute([$registration_id]);
            $registration_data = $stmt->fetch(PDO::FETCH_ASSOC);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam registered successfully'
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function registerExam()
    {
        try {
            $exam_id = $_POST['exam_id'];
            $user_id = $_POST['student_id'];
            $pwd = $_POST['pwd'];
            $current_date = date('Y-m-d H:i:s');
            $terms_accepted = isset($_POST['agree_terms']) ? 1 : 0;

            $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!password_verify($pwd, $user['password'])) {
                throw new Exception("Invalid password.");
            }

            if (password_verify($pwd, $user['password'])) {
                $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ? AND student_id = ?");
                $stmt->execute([$exam_id, $user_id]);
                $found = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($found) {
                    $reg_data = [];
                    $reg_data['date'] = $found['registration_date'];
                    $reg_data['status'] = $found['status'];
                    $reg_data['attempts'] = $found['attempts_count'];
                    $stmt = $this->db->prepare("SELECT url, id FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND registration_id = ?");
                    $stmt->execute([$exam_id, $user_id, $found['id']]);
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);

                    return json_encode([
                        'status' => 'success',
                        'msg' => 'You have already registered for this exam.',
                        'existingRegistration' => $reg_data,
                        'url' => $result['url'],
                        'code' => 'ALREADY_REGISTERED',
                        'attempt_id' => $result['id']
                    ]);
                }

                $stmt = $this->db->prepare("SELECT reg_no FROM exam_registration ORDER BY id DESC LIMIT 1");
                $stmt->execute();

                $last = $stmt->fetch(PDO::FETCH_ASSOC);

                $lastNumber = 0;
                if ($last && !empty($last['reg_no'])) {
                    $lastNumber = (int) str_replace('EX_REG_', '', $last['reg_no']);
                }

                $reg_no = 'EX_REG_' . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

                $stmt = $this->db->prepare("INSERT INTO exam_registration (exam_id, student_id, reg_no, registration_date, terms_accepted) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$exam_id, $user_id, $reg_no, $current_date, $terms_accepted]);
                $registration_id = $this->db->lastInsertId();

                // Generate a unique 180-character URL for this attempt
                do {
                    $url = bin2hex(random_bytes(90)); // 180 characters
                    $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM exam_attempts WHERE url = ?");
                    $stmt->execute([$url]);
                    $exists = $stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0;
                } while ($exists);

                $stmt = $this->db->prepare("INSERT INTO exam_attempts (registration_id , exam_id, student_id, url) VALUES (?, ?, ?, ?)");
                $stmt->execute([$registration_id, $exam_id, $user_id, $url]);

                return json_encode([
                    'status' => 'success',
                    'msg' => 'Exam registered successfully.',
                    'registration_id' => $reg_no,
                    'url' => $url
                ]);
            }
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function checkExamEligibility($exam_id)
    {
        try {
            $user = user_id();

            if (($_SESSION['role'] ?? null) !== 6) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'You are not allowed to access this exam.',
                    'code' => 'UNAUTHORIZED'
                ]);
            }


            $stmt = $this->db->prepare("SELECT ei.*, es.schedule_type, es.start_time, es.max_attempts FROM exam_info ei LEFT JOIN exam_settings es ON ei.id = es.exam_id WHERE ei.id = ?");
            $stmt->execute([$exam_id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Exam not found.',
                    'code' => 'EXAM_NOT_FOUND'
                ]);
            }

            // Exam status check
            if ($exam['status'] == 0) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Exam is not published yet.',
                    'code' => 'EXAM_NOT_PUBLISHED'
                ]);
            } elseif ($exam['status'] == 2) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'Exam has been canceled.',
                    'code' => 'EXAM_CANCELED'
                ]);
            }

            // Schedule type check
            $currentDateTime = date('Y-m-d H:i:s');
            if ($exam['schedule_type'] == 'scheduled' && !empty($exam['start_time'])) {
                $start_timestamp = strtotime($exam['start_time']);
                $end_timestamp = $start_timestamp + (!empty($exam['duration']) ? $exam['duration'] * 60 : 0);
                $exam['end_date'] = date('Y-m-d H:i:s', $end_timestamp);

                if ($currentDateTime < $exam['start_time']) {
                    return json_encode([
                        'status' => 'error',
                        'msg' => 'Exam has not started yet.',
                        'code' => 'EXAM_NOT_STARTED',
                        'start_time' => $exam['start_time']
                    ]);
                }

                if (!empty($exam['duration']) && $currentDateTime > $exam['end_date']) {
                    return json_encode([
                        'status' => 'error',
                        'msg' => 'Exam has ended.',
                        'code' => 'EXAM_ENDED'
                    ]);
                }
            }

            // Registration check
            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ? AND student_id = ?");
            $stmt->execute([$exam_id, $user]);
            $register = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$register) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'You are not registered for this exam.',
                    'code' => 'NOT_REGISTERED'
                ]);
            }

            $total_attempts = $register['attempts_count'];
            // Check max attempts
            if ($total_attempts >= $exam['max_attempts']) {
                return json_encode([
                    'status' => 'error',
                    'msg' => 'You have exceeded the maximum number of attempts for this exam.',
                    'code' => 'MAX_ATTEMPTS_EXCEEDED'
                ]);
            }

            // Fetch attempts ordered by ID
            $stmt = $this->db->prepare("SELECT * FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND registration_id = ?");
            $stmt->execute([$exam_id, $user, $register['id']]);
            $attempts = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_attempts = count($attempts) - 1;

            return json_encode([
                'status' => 'success',
                'data' => [
                    'reg_no' => $register['reg_no'],
                    'total_attempts' => $register['attempts_count'],
                ],
                'isEligible' => true
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage(),
                'code' => 'SERVER_ERROR'
            ]);
        }
    }

    public function configRegistration($exam_id)
    {
        try {
            $user_id = user_id(); // or $_SESSION['user_id']

            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ? AND student_id = ?");
            $stmt->execute([$exam_id, $user_id]);
            $found = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($found) {
                $reg_data = [];
                $reg_data['date'] = $found['registration_date'];
                $reg_data['status'] = $found['status'];
                $reg_data['attempts'] = $found['attempts_count'];
                $stmt = $this->db->prepare("SELECT url, id FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND registration_id = ?");
                $stmt->execute([$exam_id, $user_id, $found['id']]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                return json_encode([
                    'status' => 'success',
                    'msg' => 'You have already registered for this exam.',
                    'existingRegistration' => $reg_data,
                    'url' => $result['url'],
                    'code' => 'ALREADY_REGISTERED',
                    'attempt_id' => $result['id']
                ]);
            }

            // Exam info
            $stmt = $this->db->prepare("SELECT id, title, code, duration, instructions, passing_marks, status, total_marks, total_num_of_ques as total_questions FROM exam_info WHERE id = ?");
            $stmt->execute([$exam_id]);
            $exam_info = $stmt->fetch(PDO::FETCH_ASSOC);

            // Exam settings
            $stmt = $this->db->prepare("SELECT max_attempts, retake, schedule_type, start_time, full_screen_mode, disable_copy_paste, immediate_results as show_results_immediately, retake as allow_retake  FROM exam_settings WHERE exam_id = ?");
            $stmt->execute([$exam_id]);
            $exam_settings = $stmt->fetch(PDO::FETCH_ASSOC);

            // Questions (multiple rows)
            $stmt = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $stmt->execute([$exam_id]);
            $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Sections (multiple rows)
            $stmt = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $stmt->execute([$exam_id]);
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Check registration
            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ? AND student_id = ?");
            $stmt->execute([$exam_id, $user_id]);
            $is_registered = $stmt->rowCount() > 0;

            $status = $exam_info['status'];
            $schedule_type = $exam_settings['schedule_type'];

            $start_time = $exam_settings['start_time'] ? strtotime($exam_settings['start_time']) : null;
            $start_time_ts = $exam_settings['start_time'] ? strtotime($exam_settings['start_time']) : null;
            $duration_minutes = $exam_info['duration'] + 0;
            $end_time = $start_time_ts ? $start_time_ts + ($duration_minutes * 60) : null;
            $current_time = time();

            if ($status == 0) {
                $finalStatus = 'draft';
            } else if ($status == 2) {
                $finalStatus = 'canceled';
            } else if ($status == 1 && $schedule_type == 'anytime') {
                $finalStatus = 'live';
            } else if ($status == 1 && $schedule_type == 'scheduled') {
                if ($start_time > $current_time) {
                    $finalStatus = 'scheduled';
                } else if ($current_time >= $start_time && $current_time <= $end_time) {
                    $finalStatus = 'live';
                } else if ($current_time > $end_time) {
                    $finalStatus = 'completed';
                }
            }

            // Merge exam info & settings
            $exam_data = array_merge($exam_info ?: [], $exam_settings ?: []);
            $exam_data['code'] = str_replace(" ", "_", $exam_data['code']);
            $exam_data['duration'] = $exam_data['duration'] + 0;
            $exam_data['already_registered'] = $is_registered;
            $exam_data['status'] = $finalStatus;
            // $exam_data['questions'] = $questions;
            // $exam_data['sections'] = $sections;

            // Insert registration only if not already registered
            // if (!$is_registered) {
            //     $stmt = $this->db->prepare("INSERT INTO exam_registration (exam_id, user_id) VALUES (?, ?)");
            //     $stmt->execute([$exam_id, $user_id]);
            //     $exam_data['already_registered'] = true;
            // }

            return json_encode([
                'status' => 'success',
                'exam_data' => $exam_data
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamRegistrationData($exam_id)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM exam_registration WHERE exam_id = ?");
            $stmt->execute([$exam_id]);

            return json_encode([
                'status' => 'success',
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamMetaData($id)
    {
        try {
            // Fetch Exam Info
            $statement = $this->db->prepare("SELECT * FROM exam_info WHERE id = ?");
            $statement->execute([$id]);
            $exam = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                throw new Exception("Exam not found");
            }

            // Fetch Questions
            $statement = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $statement->execute([$id]);
            $questions = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$questions) {
                throw new Exception('No questions found for this exam');
            }

            // Fetch Sections
            $statement = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $statement->execute([$id]);
            $sections = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Fetch Exam Settings
            $statement = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
            $statement->execute([$id]);
            $settings = $statement->fetch(PDO::FETCH_ASSOC);

            $finalSections = [];

            foreach ($sections as $section) {
                $finalSections[] = [
                    'id' => $section['id'],
                    'examID' => $id + 0,
                    'description' => $section['s_des'] ?? '',
                    'secondDescription' => $section['s_s_des'] ?? '',
                    'questions' => []
                ];
            }

            // Sort questions by created_at ASC
            usort($questions, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });

            $finalQuestions = [];
            foreach ($questions as $question) {
                $options = [];
                $o = 0;
                foreach (['a', 'b', 'c', 'd'] as $order => $key) {
                    $options[] = [
                        'text' => $question[$key] ?? '',
                        'image' => $question[$key . '_img'] ?? null,
                        'order' => $order + 1,
                        'op' => strtoupper($key)
                    ];
                }

                $qData = [
                    'id' => $question['id'],
                    'order' => $o++,
                    'question' => $question['question'],
                    'marks' => $question['marks'] + 0,
                    'options' => $options,
                    'grid' => $question['grid'] + 0,
                    'sectionIds' => $question['section_ids'] ? array_map('intval', json_decode($question['section_ids'], true)) : [],
                ];

                $finalQuestions[] = $qData;
            }

            $status = $exam['status'];
            $schedule_type = $settings['schedule_type'];

            $start_time = $settings['start_time'] ? strtotime($settings['start_time']) : null;
            $start_time_ts = $settings['start_time'] ? strtotime($settings['start_time']) : null;
            $duration_minutes = $exam['duration'] + 0;
            $end_time = $start_time_ts ? $start_time_ts + ($duration_minutes * 60) : null;

            $current_time = time();

            if ($status == 0) {
                $finalStatus = 'draft';
            } else if ($status == 2) {
                $finalStatus = 'canceled';
            } else if ($status == 1 && $schedule_type == 'anytime') {
                $finalStatus = 'active';
            } else if ($status == 1 && $schedule_type == 'scheduled') {

                if ($start_time > $current_time) {
                    $finalStatus = 'upcoming';
                } else if ($current_time >= $start_time && $current_time <= $end_time) {
                    $finalStatus = 'active';
                } else if ($current_time > $end_time) {
                    $finalStatus = 'completed';
                }
            }

            $stmt = $this->db->prepare("SELECT id, attempts_count FROM exam_registration WHERE exam_id = ?AND student_id = ?");
            $stmt->execute([$exam['id'], user_id()]);
            $registration = $stmt->fetch(PDO::FETCH_ASSOC);

            $stmt = $this->db->prepare("SELECT started_at, status as attempt_status FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND registration_id = ?");
            $stmt->execute([$exam['id'], user_id(), $registration['id']]);
            $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

            $isAlreadyTaken = false;
            $isCompleted = false;
            $isAbandoned = false;
            $isProgress = false;
            if ($attempt['attempt_status'] !== 'not_started') {
                $isAlreadyTaken = true;
            }

            if ($attempt['attempt_status'] === 'in_progress') {
                $isProgress = true;
            }

            if ($attempt['attempt_status'] === 'completed') {
                $isCompleted = true;
            }

            if ($attempt['attempt_status'] === 'abandoned') {
                $isAbandoned = true;
            }

            // ---------------------------
            // MERGE DATA (DUMMY FORMAT)
            // ---------------------------
            $mergedData = [
                'id' => $exam['id'] + 0,
                'title' => $exam['title'],
                'code' => $exam['code'],

                'duration' => $exam['duration'] + 0,
                'total_questions' => $exam['total_num_of_ques'] + 0,
                'total_marks' => $exam['total_marks'] + 0,

                'schedule_type' => $settings['schedule_type'],
                'start_time' => $settings['start_time'] ? str_replace(" ", "T", $settings['start_time']) : null,
                'started_at' => (!empty($attempt['started_at']) && $attempt['attempt_status'] === 'in_progress') ? str_replace(" ", "T", $attempt['started_at']) : null,

                'instructions' => $exam['instructions'],

                'allow_retake' => $settings['retake'] == 1,
                'max_attempts' => $settings['max_attempts'] ? $settings['max_attempts'] + 0 : 1,
                'disable_right_click' => $settings['disable_right_click'] == 1,

                'status' => $finalStatus,
                'isAlredyTaken' => $isAlreadyTaken,
                'isProgress' => $isProgress,
                'isAbandoned' => $isAbandoned,
                'isCompleted' => $isCompleted,
                'total_attempts' => $registration['attempts_count'] + 0,
            ];

            return json_encode([
                'status' => 'success',
                'exam_info' => $mergedData,
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamDataForAttempt($id)
    {
        try {
            // Fetch Exam Info
            $statement = $this->db->prepare("SELECT * FROM exam_info WHERE id = ?");
            $statement->execute([$id]);
            $exam = $statement->fetch(PDO::FETCH_ASSOC);

            if (!$exam) {
                throw new Exception("Exam not found");
            }

            // Fetch Questions
            $statement = $this->db->prepare("SELECT * FROM questions WHERE JSON_CONTAINS(exam_ids, JSON_QUOTE(?))");
            $statement->execute([$id]);
            $questions = $statement->fetchAll(PDO::FETCH_ASSOC);

            if (!$questions) {
                throw new Exception('No questions found for this exam');
            }

            // Fetch Sections
            $statement = $this->db->prepare("SELECT * FROM sections WHERE exam_id = ?");
            $statement->execute([$id]);
            $sections = $statement->fetchAll(PDO::FETCH_ASSOC);

            // Fetch Exam Settings
            $statement = $this->db->prepare("SELECT * FROM exam_settings WHERE exam_id = ?");
            $statement->execute([$id]);
            $settings = $statement->fetch(PDO::FETCH_ASSOC);

            $statement = $this->db->prepare("SELECT id, last_attempt_date FROM exam_registration WHERE exam_id = ? AND student_id = ?");
            $statement->execute([$id, user_id()]);
            $register = $statement->fetch(PDO::FETCH_ASSOC);
            $register_id = $register['id'];

            $statement = $this->db->prepare("SELECT id, answers, status FROM exam_attempts WHERE exam_id = ? AND student_id = ? AND registration_id = ?");
            $statement->execute([$id, user_id(), $register_id]);
            $attempt = $statement->fetch(PDO::FETCH_ASSOC);
            $attempt_id = $attempt['id'];
            $stored_answers = [];

            $finalSections = [];

            foreach ($sections as $section) {
                $finalSections[] = [
                    'id' => $section['id'],
                    'examID' => $id + 0,
                    'description' => $section['s_des'] ?? '',
                    'secondDescription' => $section['s_s_des'] ?? '',
                    'questions' => []
                ];
            }

            // Sort questions by created_at ASC
            usort($questions, function ($a, $b) {
                return strtotime($a['created_at']) - strtotime($b['created_at']);
            });

            $finalQuestions = [];
            foreach ($questions as $question) {
                $options = [];
                $o = 0;
                foreach (['a', 'b', 'c', 'd'] as $order => $key) {
                    $options[] = [
                        'text' => $question[$key] ?? '',
                        'image' => $question[$key . '_img'] ?? null,
                        'order' => $order + 1,
                        'op' => strtoupper($key)
                    ];
                }

                $qData = [
                    'id' => $question['id'],
                    'order' => $o++,
                    'question' => $question['question'],
                    'marks' => $question['marks'] + 0,
                    'options' => $options,
                    'grid' => $question['grid'] + 0,
                    'sectionIds' => $question['section_ids'] ? array_map('intval', json_decode($question['section_ids'], true)) : [],
                ];

                $finalQuestions[] = $qData;
            }

            $status = $exam['status'];
            $schedule_type = $settings['schedule_type'];

            $start_time = $settings['start_time'] ? strtotime($settings['start_time']) : null;
            $start_time_ts = $settings['start_time'] ? strtotime($settings['start_time']) : null;
            $duration_minutes = $exam['duration'] + 0;
            $end_time = $start_time_ts ? $start_time_ts + ($duration_minutes * 60) : null;

            $current_time = time();

            if ($status == 0) {
                $finalStatus = 'draft';
            } else if ($status == 2) {
                $finalStatus = 'canceled';
            } else if ($status == 1 && $schedule_type == 'anytime') {
                $finalStatus = 'active';
            } else if ($status == 1 && $schedule_type == 'scheduled') {

                if ($start_time > $current_time) {
                    $finalStatus = 'upcoming';
                } else if ($current_time >= $start_time && $current_time <= $end_time) {
                    $finalStatus = 'active';
                } else if ($current_time > $end_time) {
                    $finalStatus = 'completed';
                }
            }
            // ---------------------------
            // MERGE DATA (DUMMY FORMAT)
            // ---------------------------
            $mergedData = [
                'passing_marks' => $exam['passing_marks'] + 0,

                'shuffle_questions' => $settings['shuffle_questions'] == 1,
                'shuffle_options' => $settings['shuffle_options'] == 1,
                'full_screen_mode' => $settings['full_screen_mode'] == 1,
                'disable_copy_paste' => $settings['disable_copy_paste'] == 1,
                'show_results_immediately' => $settings['immediate_results'] == 1,

                'attempt_id' => $attempt_id
            ];

            $currentTime = strtotime(date('Y-m-d H:i:s'));

            if ($settings['schedule_type'] == 'scheduled') {
                $startTime = strtotime($settings['start_time']);
                $endTime = $startTime + ($exam['duration'] * 60);

                if ($currentTime >= $startTime && $currentTime <= $endTime) {
                    // last attempt date update
                    $stmt = $this->db->prepare("UPDATE exam_registration SET last_attempt_date = ? WHERE id = ?");
                    $stmt->execute([$settings['start_time'], $register['id']]);

                    // attempt start time
                    $stmt = $this->db->prepare("UPDATE exam_attempts SET status = 'in_progress', started_at = ? WHERE id = ?");
                    $stmt->execute([$settings['start_time'], $attempt['id']]);

                    $stored_answers = json_decode($attempt['answers'], true) ?: [];
                }
            } else {
                if (!$register['last_attempt_date']) {

                    $stmt = $this->db->prepare("UPDATE exam_registration SET last_attempt_date = ? WHERE id = ?");
                    $stmt->execute([date('Y-m-d H:i:s'), $register['id']]);

                    $stmt = $this->db->prepare("UPDATE exam_attempts SET status = 'in_progress', started_at = NOW() WHERE id = ?");
                    $stmt->execute([$attempt['id']]);
                } else {
                    $startTime = strtotime($register['last_attempt_date']);
                    $endTime = $startTime + ($exam['duration'] * 60);

                    if ($currentTime > $endTime) {

                        $stmt = $this->db->prepare("UPDATE exam_registration SET last_attempt_date = ? WHERE id = ?");
                        $stmt->execute([date('Y-m-d H:i:s'), $register['id']]);

                        $stmt = $this->db->prepare("UPDATE exam_attempts SET status = 'in_progress', started_at = NOW() WHERE id = ?");
                        $stmt->execute([$attempt['id']]);
                    }

                    if ($attempt['status'] == 'rules_violation') {
                        if ($currentTime < $endTime) {

                            $stmt = $this->db->prepare("UPDATE exam_registration SET last_attempt_date = ? WHERE id = ?");
                            $stmt->execute([date('Y-m-d H:i:s'), $register['id']]);

                            $stmt = $this->db->prepare("UPDATE exam_attempts SET status = 'in_progress', started_at = NOW() WHERE id = ?");
                            $stmt->execute([$attempt['id']]);
                        }
                    }

                    if ($currentTime >= $startTime && $currentTime <= $endTime) {
                        $stored_answers = json_decode($attempt['answers'], true) ?: [];
                    }
                }
            }

            foreach ($stored_answers as &$answer) {
                $answer['question_id'] = (int) $answer['question_id'];

                if (isset($answer['flagged'])) {
                    $answer['flagged'] = filter_var($answer['flagged'], FILTER_VALIDATE_BOOLEAN);
                }
            }
            unset($answer); // reference safe

            return json_encode([
                'status' => 'success',
                'rest_exam_info' => $mergedData,
                'sections' => $finalSections,
                'questions' => $finalQuestions,
                'answers' => $stored_answers
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function saveExamAnswer($exam_id, $attempt_id, $question_id)
    {
        try {
            $answer = $_POST['answer'];

            // Fetch existing answers
            $stmt = $this->db->prepare("SELECT answers FROM exam_attempts WHERE id = ? AND exam_id = ?");
            $stmt->execute([$attempt_id, $exam_id]);
            $attempt = $stmt->fetch(PDO::FETCH_ASSOC);
            $answers = json_decode($attempt['answers'], true) ?: [];

            $found = false;
            // Update if question exists
            foreach ($answers as &$a) {
                if ($a['question_id'] === $question_id) {
                    $a['answer'] = $answer ? $answer : $a['answer'];
                    $a['flagged'] = $_POST['flagged'] ? $_POST['flagged'] : $a['flagged'];
                    $found = true;
                    break;
                }
            }
            unset($a);

            // If question not found, add it
            if (!$found) {
                $answers[] = [
                    'question_id' => $question_id,
                    'answer' => $answer ? $answer : null,
                    'flagged' => isset($_POST['flagged']) ? $_POST['flagged'] : false
                ];
            }

            // Save back to DB
            $update = $this->db->prepare("UPDATE exam_attempts SET answers = ? WHERE exam_id = ? AND id = ?");
            $update->execute([json_encode($answers), $exam_id, $attempt_id]);

            $stmt = $this->db->prepare("SELECT answers FROM exam_attempts WHERE exam_id = ? AND id = ?");
            $stmt->execute([$exam_id, $attempt_id]);
            $answers = $stmt->fetch(PDO::FETCH_ASSOC)['answers'];
            $answers = json_decode($answers, true);

            foreach ($answers as $ans) {
                if ($ans['question_id'] == $question_id) {
                    $answer = $ans['answer'];
                    $flagged = $ans['flagged'];
                    break;
                }
            }


            return json_encode([
                'status' => 'success',
                'msg' => 'Answer saved',
                'answer' => $answer ? $answer : null,
                'flagged' => $flagged === 'true' ? true : false
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function submitExam($exam_id, $attempt_id)
    {
        try {
            // Get POST data safely
            $attempt_status = $_POST['reason'] ?? 'completed';
            $remaining_time = $_POST['time_remaining'] ?? 0;

            // Get exam attempt
            $stmt = $this->db->prepare("SELECT * FROM exam_attempts WHERE exam_id = ? AND id = ?");
            $stmt->execute([$exam_id, $attempt_id]);
            $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

            // Get exam info
            $stmt = $this->db->prepare("SELECT passing_marks, total_marks, total_num_of_ques as total_questions FROM exam_info WHERE id = ?");
            $stmt->execute([$exam_id]);
            $exam = $stmt->fetch(PDO::FETCH_ASSOC);

            $theAnswers = json_decode($attempt['answers'], true) ?? [];

            $score = 0;

            // Calculate score
            foreach ($theAnswers as $answer) {
                $stmt = $this->db->prepare("SELECT answer, marks FROM questions WHERE id = ?");
                $stmt->execute([$answer['question_id']]);
                $question = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($question && $question['answer'] == $answer['answer']) {
                    $score += (float) $question['marks'];
                }
            }

            $percentage = ($exam['total_marks'] > 0) ? ($score / $exam['total_marks']) * 100 : 0;
            $passed = ($score >= $exam['passing_marks']) ? 1 : 0;

            // Update exam_attempts
            $stmt = $this->db->prepare(
                "UPDATE exam_attempts SET status = ?, completed_at = NOW(), time_remaining = ?, score = ?, percentage = ?, passed = ? WHERE id = ?"
            );
            $stmt->execute([$attempt_status, $remaining_time, $score, $percentage, $passed, $attempt_id]);

            // Update attempts count in exam_registrations
            $stmt = $this->db->prepare(
                "UPDATE exam_registration SET attempts_count = attempts_count + 1, status = 'completed' WHERE id = ?"
            );
            $stmt->execute([$attempt['registration_id']]);

            return json_encode([
                'status' => 'success',
                'msg' => 'Exam submitted successfully',
                'score' => $score,
                'percentage' => $percentage,
                'passed' => $passed == 1 ? true : false
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }

    public function getExamsByRole($user_id = null)
    {
        try {
            $user_id = $user_id ?? user_id();
            if (!$user_id) {
                throw new Exception("Unauthorized");
            }

            $role = getUserRoleID($user_id);
            $params = [];
            if ($role == 5 || $role == '5') {
                $sql = "SELECT ei.*, ei.id as original_exam_id, ei.status as info_status, es.*, er.*, er.status as register_status, ea.*, es.id AS settings_id, er.id AS registration_id, ea.id AS attempt_id, ea.status AS attempt_status, ea.status AS attempt_status FROM exam_info ei LEFT JOIN exam_settings es ON ei.id = es.exam_id LEFT JOIN exam_registration er ON ei.id = er.exam_id LEFT JOIN exam_attempts ea ON er.id = ea.registration_id WHERE ei.created_by = ?";
                $params = [$user_id];
            } elseif ($role == 6 || $role == '6') {
                $sql = "SELECT ei.*, ei.id as original_exam_id, ei.status as info_status, es.*, er.*, er.status as register_status, ea.*, es.id AS settings_id, er.id AS registration_id, ea.id AS attempt_id, ea.status AS attempt_status FROM exam_registration er LEFT JOIN exam_info ei ON ei.id = er.exam_id LEFT JOIN exam_settings es ON ei.id = es.exam_id LEFT JOIN exam_attempts ea ON er.id = ea.registration_id WHERE er.student_id = ? ";
                $params = [$user_id];
            }

            /* =========================
               BASE QUERY
            ========================== */

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $lecturer = [];
            $student = [];

            /* =========================
               LECTURER / ADMIN / CREATOR
               Roles: 1,2,3,5
            ========================== */
            if (in_array($role, [5])) {
                $exams = []; // aggregated per exam

                foreach ($rows as $row) {
                    $status = '';

                    if ($row['info_status'] == 0) {
                        $status = 'draft';
                    } elseif ($row['info_status'] == 2) {
                        $status = 'canceled';
                    } elseif ($row['info_status'] == 1) {
                        if ($row['schedule_type'] === 'anytime') {
                            $status = 'live';
                        } elseif ($row['schedule_type'] === 'scheduled') {
                            date_default_timezone_set('Asia/Colombo');
                            $currentTime = new DateTime();
                            $startTime = new DateTime($row['start_time']);
                            $duration = (int)$row['duration']; // duration in minutes

                            // Add duration to start time
                            $endTime = clone $startTime;
                            $endTime->modify("+{$duration} minutes");

                            // Determine status
                            if ($currentTime < $startTime) {
                                $status = 'scheduled';
                            } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
                                $status = 'live';
                            } else {
                                $status = 'ended';
                            }
                        }
                    }

                    $exam_id = $row['original_exam_id'];

                    if (!isset($exams[$exam_id])) {
                        $exams[$exam_id] = [
                            'exam' => $row,
                            'completed_count' => 0,
                            'participants_count' => 0,
                            'status' => $status
                        ];
                    }

                    // Count completed attempts
                    if ($row['attempt_status'] === 'completed' || $row['attempt_status'] === 'rules_violation') {
                        $exams[$exam_id]['completed_count']++;
                    }

                    // Count participants (registrations)
                    if (!empty($row['registration_id'])) {
                        $exams[$exam_id]['participants_count']++;
                    }
                }

                // Push aggregated exams to $lecturer array
                foreach ($exams as $exam_id => $e) {
                    $row = $e['exam'];
                    $lecturer[] = [
                        'id' => (int) $exam_id,
                        'title' => $row['title'],
                        'code' => $row['code'],
                        'instructions' => $row['instructions'],
                        'duration' => (int) $row['duration'],
                        'total_questions' => (int) $row['total_num_of_ques'],
                        'total_marks' => (int) $row['total_marks'],
                        'passing_marks' => (int) $row['passing_marks'],
                        'status' => $e['status'],
                        'schedule_type' => $row['schedule_type'],
                        'start_time' => $row['start_time']
                            ? str_replace(' ', 'T', $row['start_time']) . 'Z'
                            : null,
                        'participants_count' => $e['participants_count'],
                        'completed_count' => $e['completed_count'],
                        'shuffle_questions' => (bool) $row['shuffle_questions'],
                        'shuffle_options' => (bool) $row['shuffle_options'],
                        'full_screen_mode' => (bool) $row['full_screen_mode'],
                        'allow_retake' => (bool) $row['retake'],
                        'created_by' => (int) $row['created_by']
                    ];
                }
            }


            /* =========================
               STUDENT
               Roles: 6,7
            ========================== */
            if (in_array($role, [6, 7])) {
                $exams = [];

                foreach ($rows as $row) {

                    // only student related records
                    if ((int) $row['student_id'] !== (int) $user_id) {
                        continue;
                    }

                    $percentage = $row['score'] !== null
                        ? round(($row['score'] / $row['total_marks']) * 100)
                        : null;

                    $currentTime = time(); // current timestamp
                    $startTime = $row['start_time'] ? strtotime($row['start_time']) : null;
                    $duration = (int) $row['duration']; // in minutes
                    $endTime = $startTime + ($duration * 60); // end time in seconds

                    $finalStatus = '';

                    if ($row['info_status'] == 0) {
                        $finalStatus = 'available'; // draft or not yet started
                    } elseif ($row['info_status'] == 2) {
                        $finalStatus = 'expired'; // canceled or expired
                    } elseif ($row['attempt_status'] === 'completed' || $row['attempt_status'] === 'rules_violation') {
                        $finalStatus = 'completed';
                    } elseif ($row['info_status'] == 1) {
                        if ($row['schedule_type'] === 'anytime') {
                            if ($currentTime < $startTime) {
                                $finalStatus = 'upcoming';
                            } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
                                $finalStatus = 'in_progress';
                            } else {
                                $finalStatus = 'completed';
                            }
                        } elseif ($row['schedule_type'] === 'scheduled') {
                            if ($currentTime < $startTime) {
                                $finalStatus = 'upcoming';
                            } elseif ($currentTime >= $startTime && $currentTime <= $endTime) {
                                $finalStatus = 'in_progress';
                            } else {
                                $finalStatus = 'completed';
                            }
                        }
                    }

                    $student[] = [
                        'id' => (int) $row['original_exam_id'],
                        'attempt_id' => (int) $row['attempt_id'],
                        'title' => $row['title'],
                        'code' => $row['code'],
                        'instructor_name' => null,
                        'duration' => (int) $row['duration'],
                        'total_questions' => (int) $row['total_num_of_ques'],
                        'total_marks' => (int) $row['total_marks'],
                        'passing_marks' => (int) $row['passing_marks'],
                        'passing_percentage' =>
                        round(($row['passing_marks'] / $row['total_marks']) * 100),
                        'schedule_type' => $row['schedule_type'],
                        'start_time' => $row['start_time']
                            ? str_replace(' ', 'T', $row['start_time']) . 'Z'
                            : null,
                        'your_score' =>
                        $row['score'] !== null ? (int) $row['score'] : null,
                        'percentage' => $percentage,
                        'is_passed' =>
                        $row['score'] !== null
                            ? $row['score'] >= $row['passing_marks']
                            : null,
                        'last_attempt_date' => $row['completed_at']
                            ? str_replace(' ', 'T', $row['completed_at']) . 'Z'
                            : null,
                        'attempts_remaining' =>
                        (int) $row['max_attempts'] - (int) $row['attempts_count'],
                        'time_remaining' => $row['time_remaining'],
                        'shuffle_questions' => (bool) $row['shuffle_questions'],
                        'shuffle_options' => (bool) $row['shuffle_options'],
                        'full_screen_mode' => (bool) $row['full_screen_mode'],
                        'allow_retake' => (bool) $row['retake'],
                        'attempt_status' => $finalStatus
                    ];
                }
            }


            return json_encode([
                'status' => 'success',
                'exams' => $role == 5 ? $lecturer : $student,
                'user' => [
                    'id' => (int) $user_id,
                    'name' => getUserName($user_id),
                    'role_id' => (int) $role
                ]
            ]);
        } catch (Exception $e) {
            return json_encode([
                'status' => 'error',
                'msg' => $e->getMessage()
            ]);
        }
    }
}
